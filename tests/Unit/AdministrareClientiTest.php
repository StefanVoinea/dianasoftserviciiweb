<?php

namespace Tests\Unit;

use App\Models\AbonamentClient;
use App\Models\Company;
use App\Models\User;
use App\Support\ContextCompanie;
use App\Support\ContextUtilizator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * Administrarea clientilor: cine intra in zona, ce inseamna un abonament activ
 * si cand se inchid modulele.
 */
class AdministrareClientiTest extends TestCase
{
    protected $client;
    protected $conturi = [];

    protected function setUp(): void
    {
        parent::setUp();

        $this->client = Company::create(['denumire' => 'CLIENT DE PROBA SRL', 'cui' => '99000111']);
    }

    protected function tearDown(): void
    {
        AbonamentClient::where('company_id', $this->client->id)->delete();
        DB::table('company_user')->where('company_id', $this->client->id)->delete();

        foreach ($this->conturi as $cont) {
            $cont->delete();
        }

        $this->client->delete();

        // Utilizatorul fixat pe guard nu are voie sa treaca la testul urmator.
        Auth::forgetGuards();
        ContextCompanie::elibereaza();

        parent::tearDown();
    }

    protected function cont(string $email, bool $administrator = false): User
    {
        $user = User::create([
            'name' => 'Test ' . $email,
            'email' => $email,
            'password' => Hash::make('parola-de-proba'),
            'user_type' => 'user',
            'blocat' => 'Nu',
        ]);

        $this->conturi[] = $user;

        $this->client->users()->attach($user->id, ['administrator' => $administrator]);

        return $user;
    }

    protected function abonament(array $atribute = []): AbonamentClient
    {
        return AbonamentClient::create(array_merge([
            'company_id' => $this->client->id,
            'modul_spv' => true,
        ], $atribute));
    }

    public function test_administratorul_aplicatiei_este_doar_contul_din_configuratie(): void
    {
        $altul = $this->cont('contabil.test@example.com');

        Auth::guard('api')->setUser($altul);
        $this->assertFalse(ContextUtilizator::esteSuperAdministrator());

        $sef = User::where('email', config('app.super_admin'))->first();

        if ($sef) {
            Auth::guard('api')->setUser($sef);
            $this->assertTrue(ContextUtilizator::esteSuperAdministrator());
        }
    }

    /** Dreptul de administrator e per client, nu per persoana. */
    public function test_administratorul_firmei_se_recunoaste_dupa_client(): void
    {
        $sef = $this->cont('sef.test@example.com', true);
        $angajat = $this->cont('angajat.test@example.com');

        Auth::guard('api')->setUser($sef);
        ContextCompanie::fixeaza($this->client->id);
        $this->assertTrue(ContextUtilizator::esteAdministratorClient());

        Auth::guard('api')->setUser($angajat);
        $this->assertFalse(ContextUtilizator::esteAdministratorClient());
    }

    /** Administratorul firmei vede tot; angajatul, doar ce a lucrat el. */
    public function test_limitarea_pe_utilizator_se_ridica_pentru_administrator(): void
    {
        $sef = $this->cont('sef2.test@example.com', true);
        $angajat = $this->cont('angajat2.test@example.com');

        ContextCompanie::fixeaza($this->client->id);

        Auth::guard('api')->setUser($sef);
        $this->assertNull(ContextUtilizator::limitatLa());

        Auth::guard('api')->setUser($angajat);
        $this->assertSame($angajat->id, ContextUtilizator::limitatLa());
    }

    /** In proba se lucreaza, chiar daca nu s-a platit nimic. */
    public function test_proba_da_acces_pana_la_data_ei(): void
    {
        $abonament = $this->abonament(['proba_zile' => 30, 'proba_pana_la' => now()->addDays(10)->toDateString()]);

        $this->assertTrue($abonament->activ());
        $this->assertTrue($abonament->inProba());
        $this->assertNull($abonament->motiv());
    }

    /** Dupa proba, fara plata, accesul se opreste — cu motivul spus pe sleau. */
    public function test_dupa_proba_neplatita_accesul_se_opreste(): void
    {
        $abonament = $this->abonament(['proba_pana_la' => now()->subDay()->toDateString()]);

        $this->assertFalse($abonament->activ());
        $this->assertStringContainsString('Perioada de probă s-a încheiat', $abonament->motiv());
    }

    public function test_plata_la_zi_tine_accesul_deschis_dupa_proba(): void
    {
        $abonament = $this->abonament([
            'proba_pana_la' => now()->subMonth()->toDateString(),
            'platit_pana_la' => now()->addDays(20)->toDateString(),
        ]);

        $this->assertTrue($abonament->activ());
        $this->assertFalse($abonament->inProba());
        $this->assertSame(20, $abonament->zileRamase());
    }

    /** Oprirea din administrare are intaietate fata de orice plata. */
    public function test_oprirea_are_intaietate_fata_de_plata(): void
    {
        $abonament = $this->abonament([
            'platit_pana_la' => now()->addYear()->toDateString(),
            'blocat' => true,
            'motiv_blocare' => 'Facturi neachitate din martie.',
        ]);

        $this->assertFalse($abonament->activ());
        $this->assertSame('Facturi neachitate din martie.', $abonament->motiv());
    }

    public function test_modulele_se_acorda_separat(): void
    {
        $abonament = $this->abonament(['modul_spv' => true, 'modul_etransport' => false]);

        $this->assertTrue($abonament->areModul('spv'));
        $this->assertFalse($abonament->areModul('etransport'));
        $this->assertFalse($abonament->areModul('portal_just'));
        $this->assertFalse($abonament->areModul('inexistent'));
    }

    /** Fara abonament scris, clientul lucreaza ca inainte. */
    public function test_clientul_fara_abonament_nu_este_oprit(): void
    {
        $this->assertNull(AbonamentClient::alClientului($this->client->id));
    }
}
