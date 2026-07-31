<?php

namespace Tests\Unit;

use App\Http\Controllers\Api\NotificariController;
use App\Mail\NotificareEmail;
use App\Models\Company;
use App\Models\NotificareAplicatie;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

/**
 * Instiintarile trimise din administrare: cui ajung, pe ce cale si ce se
 * intampla cand emailul nu pleaca.
 */
class NotificariTest extends TestCase
{
    protected $client;
    protected $ana;
    protected $bogdan;
    protected $strain;

    protected function setUp(): void
    {
        parent::setUp();

        Mail::fake();

        $this->client = Company::create(['denumire' => 'CLIENT NOTIFICARI SRL', 'cui' => '99000333']);

        $this->ana = $this->cont('ana.notificari@example.com');
        $this->bogdan = $this->cont('bogdan.notificari@example.com');
        $this->strain = User::create([
            'name' => 'Strain',
            'email' => 'strain.notificari@example.com',
            'password' => Hash::make('parola-de-proba'),
            'user_type' => 'user',
            'blocat' => 'Nu',
        ]);
    }

    protected function tearDown(): void
    {
        NotificareAplicatie::whereIn('user_id', [$this->ana->id, $this->bogdan->id, $this->strain->id])->delete();
        DB::table('company_user')->where('company_id', $this->client->id)->delete();

        foreach ([$this->ana, $this->bogdan, $this->strain] as $cont) {
            $cont->delete();
        }

        $this->client->delete();

        Auth::forgetGuards();

        parent::tearDown();
    }

    protected function cont(string $email): User
    {
        $user = User::create([
            'name' => $email,
            'email' => $email,
            'password' => Hash::make('parola-de-proba'),
            'user_type' => 'user',
            'blocat' => 'Nu',
        ]);

        $this->client->users()->attach($user->id, ['administrator' => false]);

        return $user;
    }

    protected function controller(): NotificariController
    {
        return $this->app->make(NotificariController::class);
    }

    protected function trimite(array $date): array
    {
        return $this->controller()->trimite(new Request($date))->getData(true);
    }

    public function test_notificarea_ajunge_la_toti_utilizatorii_unui_client(): void
    {
        $raspuns = $this->trimite([
            'titlu' => 'Mentenanță sâmbătă',
            'mesaj' => 'Aplicația va fi oprită între 10:00 și 12:00.',
            'destinatari' => 'client',
            'company_id' => $this->client->id,
        ]);

        $this->assertTrue($raspuns['success']);
        $this->assertSame(2, $raspuns['data']['destinatari']);

        $this->assertSame(1, NotificareAplicatie::ale($this->ana->id)->count());
        $this->assertSame(1, NotificareAplicatie::ale($this->bogdan->id)->count());
        $this->assertSame(0, NotificareAplicatie::ale($this->strain->id)->count());
    }

    public function test_notificarea_poate_merge_doar_catre_anumiti_utilizatori(): void
    {
        $this->trimite([
            'titlu' => 'Doar pentru Ana',
            'mesaj' => 'Vă rugăm reîncărcați declarația D112.',
            'destinatari' => 'utilizatori',
            'utilizatori' => [$this->ana->id],
        ]);

        $this->assertSame(1, NotificareAplicatie::ale($this->ana->id)->count());
        $this->assertSame(0, NotificareAplicatie::ale($this->bogdan->id)->count());
    }

    /** Pe email pleaca doar daca s-a cerut. */
    public function test_emailul_pleaca_doar_cand_este_cerut(): void
    {
        $this->trimite([
            'titlu' => 'Fără email',
            'mesaj' => 'Doar în aplicație.',
            'destinatari' => 'utilizatori',
            'utilizatori' => [$this->ana->id],
        ]);

        Mail::assertNothingSent();

        $this->trimite([
            'titlu' => 'Și pe email',
            'mesaj' => 'Ajunge și în inbox.',
            'destinatari' => 'utilizatori',
            'utilizatori' => [$this->ana->id],
            'pe_email' => true,
        ]);

        Mail::assertSent(NotificareEmail::class, function (NotificareEmail $email) {
            return $email->hasTo($this->ana->email) && $email->titlu === 'Și pe email';
        });
    }

    /** Fara nicio cale aleasa nu are rost sa se trimita nimic. */
    public function test_fara_nicio_cale_aleasa_cererea_este_respinsa(): void
    {
        $raspuns = $this->trimite([
            'titlu' => 'Nicăieri',
            'mesaj' => 'Fără cale.',
            'destinatari' => 'utilizatori',
            'utilizatori' => [$this->ana->id],
            'in_aplicatie' => false,
            'pe_email' => false,
        ]);

        $this->assertFalse($raspuns['success']);
        $this->assertSame(0, NotificareAplicatie::ale($this->ana->id)->count());
    }

    public function test_importanta_se_pastreaza(): void
    {
        $this->trimite([
            'titlu' => 'Abonament expirat',
            'mesaj' => 'Vă rugăm achitați factura.',
            'destinatari' => 'utilizatori',
            'utilizatori' => [$this->bogdan->id],
            'importanta' => 'urgenta',
        ]);

        $this->assertSame('urgenta', NotificareAplicatie::ale($this->bogdan->id)->first()->importanta);
    }

    /** Citirea e personala: nimeni nu poate marca notificarea altcuiva. */
    public function test_notificarea_altcuiva_nu_poate_fi_marcata_citita(): void
    {
        $this->trimite([
            'titlu' => 'A Anei',
            'mesaj' => 'Mesaj.',
            'destinatari' => 'utilizatori',
            'utilizatori' => [$this->ana->id],
        ]);

        $aAnei = NotificareAplicatie::ale($this->ana->id)->first();

        $cerere = Request::create('/notificari/' . $aAnei->id . '/citita', 'POST');
        $cerere->setUserResolver(function () {
            return $this->bogdan;
        });

        $this->expectException(\Symfony\Component\HttpKernel\Exception\NotFoundHttpException::class);

        $this->controller()->marcheazaCitita($cerere, $aAnei);
    }

    /** Expeditorul primeste inapoi o instiintare cand cineva a citit. */
    public function test_expeditorul_este_anuntat_cand_notificarea_a_fost_citita(): void
    {
        Auth::guard('api')->setUser($this->strain);

        $this->trimite([
            'titlu' => 'De confirmat',
            'mesaj' => 'Vă rugăm confirmați.',
            'destinatari' => 'utilizatori',
            'utilizatori' => [$this->ana->id],
            'confirma_citirea' => true,
        ]);

        $this->citeste($this->ana);

        $confirmare = NotificareAplicatie::ale($this->strain->id)->first();

        $this->assertNotNull($confirmare, 'expeditorul trebuie să primească înștiințarea');
        $this->assertTrue($confirmare->este_confirmare);
        $this->assertStringContainsString('a citit înștiințarea', $confirmare->mesaj);
        $this->assertStringContainsString($this->ana->email, $confirmare->mesaj);
    }

    /** Fara bifa, nu se intoarce nicio confirmare. */
    public function test_fara_confirmare_ceruta_expeditorul_nu_primeste_nimic(): void
    {
        Auth::guard('api')->setUser($this->strain);

        $this->trimite([
            'titlu' => 'Fără confirmare',
            'mesaj' => 'Simplu.',
            'destinatari' => 'utilizatori',
            'utilizatori' => [$this->ana->id],
        ]);

        $this->citeste($this->ana);

        $this->assertSame(0, NotificareAplicatie::ale($this->strain->id)->count());
    }

    /** Citirea unei confirmari nu naste alta — altfel s-ar merge la nesfarsit. */
    public function test_confirmarea_nu_naste_alta_confirmare(): void
    {
        Auth::guard('api')->setUser($this->strain);

        $this->trimite([
            'titlu' => 'Bucla',
            'mesaj' => 'Mesaj.',
            'destinatari' => 'utilizatori',
            'utilizatori' => [$this->ana->id],
            'confirma_citirea' => true,
        ]);

        $this->citeste($this->ana);

        // Expeditorul citeste confirmarea primita
        $this->citeste($this->strain);

        $this->assertSame(1, NotificareAplicatie::ale($this->strain->id)->count());
        $this->assertSame(0, NotificareAplicatie::ale($this->ana->id)->necitite()->count());
    }

    /** Randurile plecate deodata poarta acelasi lot. */
    public function test_trimiterea_leaga_randurile_intr_un_lot(): void
    {
        $this->trimite([
            'titlu' => 'Un lot',
            'mesaj' => 'Mesaj.',
            'destinatari' => 'client',
            'company_id' => $this->client->id,
        ]);

        $loturi = NotificareAplicatie::whereIn('user_id', [$this->ana->id, $this->bogdan->id])
            ->pluck('lot')
            ->unique();

        $this->assertCount(1, $loturi);
        $this->assertNotNull($loturi->first());
    }

    /** Citeste toate notificarile necitite ale unui utilizator. */
    protected function citeste(User $user): void
    {
        foreach (NotificareAplicatie::ale($user->id)->necitite()->get() as $notificare) {
            $cerere = Request::create('/notificari/' . $notificare->id . '/citita', 'POST');
            $cerere->setUserResolver(function () use ($user) {
                return $user;
            });

            $this->controller()->marcheazaCitita($cerere, $notificare);
        }
    }

    public function test_marcarea_ca_citita_scoate_notificarea_din_necitite(): void
    {
        $this->trimite([
            'titlu' => 'De citit',
            'mesaj' => 'Mesaj.',
            'destinatari' => 'utilizatori',
            'utilizatori' => [$this->ana->id],
        ]);

        $notificare = NotificareAplicatie::ale($this->ana->id)->first();

        $cerere = Request::create('/notificari/' . $notificare->id . '/citita', 'POST');
        $cerere->setUserResolver(function () {
            return $this->ana;
        });

        $this->controller()->marcheazaCitita($cerere, $notificare);

        $this->assertSame(0, NotificareAplicatie::ale($this->ana->id)->necitite()->count());
    }
}
