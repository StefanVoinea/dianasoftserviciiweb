<?php

namespace Tests\Unit;

use App\Models\DispozitivNotificare;
use App\Models\PortalJustDosar;
use App\Models\PortalJustModificare;
use App\Models\PortalJustMonitorizare;
use App\Services\Notificari\Fcm;
use App\Support\ContextCompanie;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

/**
 * Alertele pe telefon trimise de comanda de monitorizare: cui ajung, ce se
 * întâmplă cu tokenele moarte și de ce nu se repetă.
 */
class PortalJustAlerteTest extends TestCase
{
    protected const COMPANIE = 992;
    protected const UTILIZATOR = 8801;

    protected function tearDown(): void
    {
        $monitorizari = PortalJustMonitorizare::query()->toateCompaniile()
            ->where('company_id', self::COMPANIE)->pluck('id');

        PortalJustModificare::query()->toateCompaniile()->whereIn('monitorizare_id', $monitorizari)->delete();
        PortalJustDosar::query()->toateCompaniile()->whereIn('monitorizare_id', $monitorizari)->delete();
        PortalJustMonitorizare::query()->toateCompaniile()->whereIn('id', $monitorizari)->delete();
        DispozitivNotificare::where('user_id', self::UTILIZATOR)->delete();

        ContextCompanie::elibereaza();

        parent::tearDown();
    }

    /** Un Fcm care nu iese pe rețea, dar reține ce i s-a cerut. */
    protected function fcmFals(string $rezultat): Fcm
    {
        $fals = new class($rezultat) extends Fcm {
            public $trimiteri = [];
            protected $rezultat;

            public function __construct(string $rezultat)
            {
                $this->rezultat = $rezultat;
            }

            public function activ(): bool
            {
                return true;
            }

            public function trimite(string $token, string $titlu, string $corp, array $date = []): string
            {
                $this->trimiteri[] = compact('token', 'titlu', 'corp', 'date');

                return $this->rezultat;
            }
        };

        $this->app->instance(Fcm::class, $fals);

        return $fals;
    }

    /** Pregătește o monitorizare cu o modificare neanunțată încă. */
    protected function pregateste(): PortalJustMonitorizare
    {
        return ContextCompanie::pentru(self::COMPANIE, function () {
            $monitorizare = PortalJustMonitorizare::create([
                'company_id' => self::COMPANIE,
                'user_id' => self::UTILIZATOR,
                'tip' => PortalJustMonitorizare::TIP_DOSAR,
                'valoare' => '1234/3/2024',
                'email' => 'avocat@exemplu.ro',
                'activ' => false, // inactivă: comanda nu o va reinteroga
                'ultima_verificare' => now()->subHour(),
            ]);

            PortalJustModificare::create([
                'company_id' => self::COMPANIE,
                'monitorizare_id' => $monitorizare->id,
                'dosar_numar' => '1234/3/2024',
                'institutie' => 'Curtea de Apel BUCURESTI',
                'tip' => 'termen_nou',
                'descriere' => 'Termen nou: 10.02.2026, ora 09:00',
            ]);

            return $monitorizare;
        });
    }

    protected function dispozitiv(string $token): DispozitivNotificare
    {
        return DispozitivNotificare::create([
            'user_id' => self::UTILIZATOR,
            'company_id' => self::COMPANIE,
            'token' => $token,
            'platforma' => 'android',
        ]);
    }

    public function test_alerta_ajunge_pe_telefonul_utilizatorului(): void
    {
        Mail::fake();

        $this->pregateste();
        $this->dispozitiv('token-telefon-1');

        $fcm = $this->fcmFals(Fcm::TRIMIS);

        Artisan::call('portaljust:monitorizeaza');

        $this->assertCount(1, $fcm->trimiteri);
        $this->assertSame('token-telefon-1', $fcm->trimiteri[0]['token']);
        $this->assertSame('Dosar 1234/3/2024', $fcm->trimiteri[0]['titlu']);
        $this->assertStringContainsString('10.02.2026', $fcm->trimiteri[0]['corp']);

        // Aplicația are nevoie de id, ca să nu anunțe a doua oară la verificarea ei.
        $this->assertArrayHasKey('modificare_id', $fcm->trimiteri[0]['date']);

        ContextCompanie::pentru(self::COMPANIE, function () {
            $this->assertSame(0, PortalJustModificare::faraPush()->count());
        });
    }

    /** Aceeași modificare nu se anunță de două ori, oricât de des rulează comanda. */
    public function test_alerta_nu_se_repeta_la_rularea_urmatoare(): void
    {
        Mail::fake();

        $this->pregateste();
        $this->dispozitiv('token-telefon-1');

        $fcm = $this->fcmFals(Fcm::TRIMIS);

        Artisan::call('portaljust:monitorizeaza');
        Artisan::call('portaljust:monitorizeaza');

        $this->assertCount(1, $fcm->trimiteri);
    }

    /** Aplicația dezinstalată: tokenul se șterge, ca să nu se reîncerce la nesfârșit. */
    public function test_tokenul_mort_este_sters(): void
    {
        Mail::fake();

        $this->pregateste();
        $this->dispozitiv('token-mort');

        $this->fcmFals(Fcm::TOKEN_INVALID);

        Artisan::call('portaljust:monitorizeaza');

        $this->assertSame(0, DispozitivNotificare::where('token', 'token-mort')->count());
    }

    /** Fără telefon înregistrat, modificarea rămâne doar cu emailul. */
    public function test_fara_dispozitiv_nu_se_incearca_nimic(): void
    {
        Mail::fake();

        $this->pregateste();

        $fcm = $this->fcmFals(Fcm::TRIMIS);

        Artisan::call('portaljust:monitorizeaza');

        $this->assertCount(0, $fcm->trimiteri);

        // Se marchează oricum, altfel s-ar relua din oră în oră.
        ContextCompanie::pentru(self::COMPANIE, function () {
            $this->assertSame(0, PortalJustModificare::faraPush()->count());
        });
    }

    /** Fără Firebase configurat nu se atinge nimic: alertele rămân pe seama aplicației. */
    public function test_fara_firebase_modificarile_raman_neatinse(): void
    {
        Mail::fake();

        $this->pregateste();
        $this->dispozitiv('token-telefon-1');

        config(['firebase.proiect' => null, 'firebase.cont_serviciu' => null]);

        Artisan::call('portaljust:monitorizeaza');

        ContextCompanie::pentru(self::COMPANIE, function () {
            $this->assertSame(1, PortalJustModificare::faraPush()->count());
        });
    }
}
