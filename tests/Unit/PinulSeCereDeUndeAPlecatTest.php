<?php

namespace Tests\Unit;

use App\Http\Controllers\Api\CertificateController;
use App\Models\AnafCertificat;
use App\Models\Company;
use App\Models\User;
use App\Support\Aplicatia;
use App\Support\ContextCompanie;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * PIN-ul se cere de unde a plecat lucrarea.
 *
 * Cine a apăsat butonul pe telefon trebuie întrebat pe telefon, nu într-o filă
 * din browser pe care poate n-o are nimeni în față — și nici invers. Altfel,
 * fereastra de cod apărea oriunde se uita cineva, iar codul rămânea nescris
 * tocmai acolo unde aștepta cineva după el.
 *
 * Fac excepție lucrările pornite de la sine — dosarul urmărit, sarcina de
 * noapte —, care n-au pe nimeni în spate: acelea se arată oriunde, fiindcă
 * oricine e prin preajmă le poate dezlega.
 */
class PinulSeCereDeUndeAPlecatTest extends TestCase
{
    protected $client;
    protected $omul;
    protected $altul;

    protected function setUp(): void
    {
        parent::setUp();

        $this->client = Company::create(['denumire' => 'BIROU PIN SRL', 'cui' => '99000666']);

        $this->omul = $this->cont('pin.unul@example.com');
        $this->altul = $this->cont('pin.altul@example.com');

        ContextCompanie::fixeaza($this->client->id);
        Auth::login($this->omul);
    }

    protected function tearDown(): void
    {
        Auth::logout();

        AnafCertificat::query()->toateCompaniile()->where('company_id', $this->client->id)->delete();

        $this->client->users()->detach();
        $this->client->delete();
        $this->omul->delete();
        $this->altul->delete();

        ContextCompanie::elibereaza();

        parent::tearDown();
    }

    protected function cont(string $email): User
    {
        $user = User::create([
            'name' => $email,
            'email' => $email,
            'password' => Hash::make(bin2hex(random_bytes(8))),
        ]);

        $this->client->users()->attach($user->id, ['administrator' => true]);

        return $user;
    }

    /** Un token care își așteaptă codul, cerut de cineva, de undeva. */
    protected function tokenCareAsteapta(?int $deCine, ?string $deUnde): AnafCertificat
    {
        return AnafCertificat::create([
            'company_id' => $this->client->id,
            'thumbprint' => strtoupper(bin2hex(random_bytes(20))),
            'cn' => 'TOKEN ' . ($deUnde ?: 'fără'),
            'activ' => true,
            'valabil_pana_la' => now()->addYear(),
            'pin_de_la_distanta' => true,
            'pin_stare' => 'asteapta',
            'pin_verificat_la' => now(),
            'pin_cerut_de' => $deCine,
            'pin_cerut_din' => $deUnde,
        ]);
    }

    /** Ce i se arată celui care întreabă din locul acesta. */
    protected function ceVede(string $deUnde): array
    {
        $cerere = Request::create('/api/anaf-certificate/pin/asteptare', 'GET');

        if ($deUnde === Aplicatia::MOBIL) {
            $cerere->headers->set(Aplicatia::ANTETUL, Aplicatia::MOBIL);
        }

        app()->instance('request', $cerere);

        $raspuns = (new CertificateController())->pinInAsteptare();

        return array_map(function ($rand) {
            return $rand['cn'];
        }, $raspuns->getData(true)['data']);
    }

    /** Cererea pornită de pe telefon se cere tot pe telefon. */
    public function test_cererea_de_pe_telefon_nu_ajunge_in_fila(): void
    {
        $this->tokenCareAsteapta($this->omul->id, Aplicatia::MOBIL);

        $this->assertSame(['TOKEN mobil'], $this->ceVede(Aplicatia::MOBIL));
        $this->assertSame([], $this->ceVede(Aplicatia::WEB));
    }

    /** Și invers: ce s-a cerut din filă nu tulbură telefonul. */
    public function test_cererea_din_fila_nu_ajunge_pe_telefon(): void
    {
        $this->tokenCareAsteapta($this->omul->id, Aplicatia::WEB);

        $this->assertSame(['TOKEN web'], $this->ceVede(Aplicatia::WEB));
        $this->assertSame([], $this->ceVede(Aplicatia::MOBIL));
    }

    /** Nici colegului nu i se cere codul pentru lucrarea altuia. */
    public function test_colegul_nu_e_intrebat_pentru_lucrarea_altuia(): void
    {
        $this->tokenCareAsteapta($this->altul->id, Aplicatia::WEB);

        $this->assertSame([], $this->ceVede(Aplicatia::WEB));
    }

    /**
     * Lucrarea pornită de la sine se arată oriunde.
     *
     * N-a apăsat nimeni nimic — dosarul urmărit, sarcina de noapte —, deci nu e
     * nimeni anume de întrebat, iar oricine e prin preajmă o poate dezlega.
     */
    public function test_lucrarea_pornita_de_la_sine_se_arata_oriunde(): void
    {
        $this->tokenCareAsteapta(null, Aplicatia::FUNDAL);

        $this->assertSame(['TOKEN fundal'], $this->ceVede(Aplicatia::WEB));
        $this->assertSame(['TOKEN fundal'], $this->ceVede(Aplicatia::MOBIL));
    }

    /** Tokenele mai vechi, fără însemnare, se arată tot oriunde. */
    public function test_tokenul_fara_insemnare_se_arata_oriunde(): void
    {
        $this->tokenCareAsteapta(null, null);

        $this->assertCount(1, $this->ceVede(Aplicatia::WEB));
        $this->assertCount(1, $this->ceVede(Aplicatia::MOBIL));
    }

    /** Fără alegerea omului, tokenul nu se arată nicăieri. */
    public function test_fara_alegerea_omului_nu_se_cere_nicaieri(): void
    {
        $this->tokenCareAsteapta($this->omul->id, Aplicatia::WEB)
            ->update(['pin_de_la_distanta' => false]);

        $this->assertSame([], $this->ceVede(Aplicatia::WEB));
        $this->assertSame([], $this->ceVede(Aplicatia::MOBIL));
    }
}
