<?php

namespace Tests\Unit;

use App\Models\AnafCertificat;
use App\Services\Anaf\Bridge\Licente;
use App\Services\Anaf\Bridge\LicentiereBridge;
use App\Support\ContextCompanie;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Licența programului local și jetoanele de comandă.
 *
 * Programul de pe calculatorul clientului poate fi citit și copiat oricând —
 * asta nu se poate opri. Se poate face însă copia nefolositoare: licența îl
 * leagă de un calculator anume și expiră, iar comenzile vin cu un jeton semnat
 * de server, pe care nici clientul, care își știe codul de acces, nu-l poate
 * face singur.
 */
class LicentaBridgeTest extends TestCase
{
    protected const COMPANIE = 977;

    protected $certificat;

    protected function setUp(): void
    {
        parent::setUp();

        // Cheile de probă nu ating storage-ul aplicației.
        Storage::fake(config('filesystems.default'));

        ContextCompanie::fixeaza(self::COMPANIE);

        $this->certificat = AnafCertificat::create([
            'company_id' => self::COMPANIE,
            'thumbprint' => strtoupper(bin2hex(random_bytes(20))),
            'cn' => 'POPESCU ION',
            'activ' => true,
            'valabil_pana_la' => now()->addYear(),
            'bridge_url' => 'http://192.168.1.55:8099',
            'bridge_token' => 'cod-de-instalare',
        ]);
    }

    protected function tearDown(): void
    {
        AnafCertificat::query()->toateCompaniile()->where('company_id', self::COMPANIE)->delete();
        ContextCompanie::elibereaza();

        parent::tearDown();
    }

    protected function licente(): Licente
    {
        $licente = $this->app->make(Licente::class);
        $licente->pregatesteCheile();

        return $licente;
    }

    public function test_licenta_este_semnata_si_verificabila_cu_cheia_publica(): void
    {
        $licente = $this->licente();

        $licenta = $licente->emite($this->certificat, 'AMPRENTA-CALCULATOR');

        $this->assertSame('AMPRENTA-CALCULATOR', $licenta['date']['masina']);
        $this->assertSame(self::COMPANIE, $licenta['date']['client']);
        $this->assertTrue($licenta['date']['jeton_semnat']);

        $this->assertTrue(
            $licente->verifica($licente->canonic($licenta['date']), $licenta['semnatura']),
            'semnătura trebuie să se verifice cu cheia publică'
        );
    }

    /** O licență cu o singură cifră schimbată nu mai trece. */
    public function test_licenta_umblata_nu_mai_trece(): void
    {
        $licente = $this->licente();

        $licenta = $licente->emite($this->certificat, 'AMPRENTA-CALCULATOR');
        $date = $licenta['date'];
        $date['expira'] = now()->addYears(5)->toIso8601String();

        $this->assertFalse($licente->verifica($licente->canonic($date), $licenta['semnatura']));
    }

    /** Jetonul de comandă ține minute, nu zile. */
    public function test_jetonul_de_comanda_are_viata_scurta(): void
    {
        $licente = $this->licente();

        $jeton = $licente->jeton();
        $bucati = explode('.', $jeton);

        $this->assertSame('v1', $bucati[0]);
        $this->assertCount(3, $bucati);

        $date = json_decode(base64_decode(strtr($bucati[1], '-_', '+/')), true);

        $this->assertLessThanOrEqual(Licente::JETON_SECUNDE, $date['expira'] - $date['emis']);
        $this->assertGreaterThan(time() - 5, $date['expira']);
    }

    /** Comenzile pleacă semnate, nu cu codul din configurare.env. */
    public function test_comenzile_pleaca_cu_jeton_semnat_nu_cu_codul_de_instalare(): void
    {
        $this->licente();
        $this->certificat->update(['licenta_pana_la' => now()->addDays(30)]);

        $bridge = $this->app->make(\App\Services\Anaf\Spv\CertificatService::class);
        $bridge->foloseste($this->certificat->fresh());

        $coordonate = $bridge->bridge();

        $this->assertStringStartsWith('v1.', $coordonate['token']);
        $this->assertSame('cod-de-instalare', $coordonate['cod_instalare'], 'codul rămâne, dar doar pentru instalare');
    }

    /** Fără chei pe server, totul merge ca înainte: instalările vechi nu se opresc. */
    public function test_fara_chei_se_trimite_codul_de_instalare(): void
    {
        $bridge = $this->app->make(\App\Services\Anaf\Spv\CertificatService::class);
        $bridge->foloseste($this->certificat);

        $this->assertSame('cod-de-instalare', $bridge->bridge()['token']);
    }

    /**
     * Un program încă nelicențiat primește tot codul de instalare: unul vechi
     * n-ar recunoaște jetonul semnat și ar refuza orice comandă.
     */
    public function test_programul_nelicentiat_primeste_tot_codul_de_instalare(): void
    {
        $this->licente();

        $bridge = $this->app->make(\App\Services\Anaf\Spv\CertificatService::class);
        $bridge->foloseste($this->certificat);

        $this->assertSame('cod-de-instalare', $bridge->bridge()['token']);
    }

    /** Licențierea: se cere amprenta calculatorului, apoi i se trimite licența. */
    public function test_licentierea_leaga_programul_de_calculatorul_lui(): void
    {
        $this->licente();

        Http::fake([
            '192.168.1.55:8099/identitate' => Http::response([
                'masina' => 'AMPRENTA-CALCULATOR',
                'licentiat' => false,
                'licenta' => null,
            ], 200),
            '192.168.1.55:8099/licenta' => Http::response(['primita' => true], 200),
        ]);

        $rezultat = $this->app->make(LicentiereBridge::class)->reinnoieste($this->certificat);

        $this->assertTrue($rezultat['emisa']);
        $this->assertNotNull($this->certificat->fresh()->licenta_pana_la);

        Http::assertSent(function (Request $cerere) {
            if ($cerere->url() !== 'http://192.168.1.55:8099/licenta') {
                return true;
            }

            $trimisa = json_decode($cerere->body(), true);

            return $trimisa['date']['masina'] === 'AMPRENTA-CALCULATOR'
                && !empty($trimisa['semnatura'])
                // Licențierea merge pe codul de instalare: licență încă nu are.
                && $cerere->hasHeader('Authorization', 'Bearer cod-de-instalare');
        });
    }

    /** O licență care mai are destule zile nu se reînnoiește degeaba. */
    public function test_licenta_valabila_nu_se_reemite(): void
    {
        $this->licente();

        Http::fake([
            '192.168.1.55:8099/identitate' => Http::response([
                'masina' => 'AMPRENTA-CALCULATOR',
                'licentiat' => true,
                'licenta' => ['client' => self::COMPANIE, 'expira' => now()->addDays(25)->toIso8601String()],
            ], 200),
        ]);

        $rezultat = $this->app->make(LicentiereBridge::class)->reinnoieste($this->certificat);

        $this->assertFalse($rezultat['emisa']);

        Http::assertNotSent(function (Request $cerere) {
            return $cerere->url() === 'http://192.168.1.55:8099/licenta';
        });
    }

    /** Programele dinaintea licențierii nu se opresc: nu știu de rută, merg mai departe. */
    public function test_programul_vechi_nu_este_oprit(): void
    {
        $this->licente();

        Http::fake(['192.168.1.55:8099/identitate' => Http::response('', 404)]);

        $rezultat = $this->app->make(LicentiereBridge::class)->reinnoieste($this->certificat);

        $this->assertFalse($rezultat['emisa']);
        $this->assertStringContainsString('program vechi', $rezultat['motiv']);
    }

    /** Calculatorul închis nu strică nimic: se încearcă mâine. */
    public function test_calculatorul_inchis_este_raportat_nu_aruncat(): void
    {
        $this->licente();

        Http::fake(['192.168.1.55:8099/identitate' => Http::response(['eroare' => 'ceva'], 500)]);

        $rezultat = $this->app->make(LicentiereBridge::class)->reinnoieste($this->certificat);

        $this->assertFalse($rezultat['emisa']);
        $this->assertNotNull($rezultat['motiv']);
    }
}
