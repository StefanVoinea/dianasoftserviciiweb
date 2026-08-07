<?php

namespace Tests\Unit;

use App\Models\AnafDeclaratie;
use App\Models\User;
use App\Support\ContextCompanie;
use App\Support\ContextUtilizator;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * Administratorul firmei deschide si documentul lucrat de un coleg.
 *
 * Asa scrie in VizibilUtilizatorului: utilizatorul obisnuit vede doar ce a facut
 * el, iar administratorul firmei vede tot. In lista se si intampla asa.
 *
 * La butoanele care poarta id-ul in adresa nu se intampla insa. Modelul se cauta
 * cu „SubstituteBindings", iar acela rula inaintea middleware-ului care pune
 * contextul clientului. Fara client stiut, „esteAdministratorClient()" nu are
 * unde sa se uite si raspunde nu — asa ca administratorul era luat drept
 * utilizator obisnuit tocmai la cautare, si primea „No query results for model
 * [AnafDeclaratie] 3284" pentru o declaratie a firmei lui, pe care o vedea in
 * tabel cu ochii lui.
 *
 * Se vedea numai la unii oameni si numai la unele randuri — de aceea a si parut
 * multa vreme o nazdravanie a unui singur cont.
 */
class AdministratorulVedeDocumentulColeguluiTest extends TestCase
{
    protected const COMPANIE = 994;

    protected $administrator;
    protected $coleg;
    protected $declaratie;

    protected function setUp(): void
    {
        parent::setUp();

        $this->administrator = User::create([
            'name' => 'Administratorul firmei',
            'email' => 'admin994@example.test',
            'password' => bcrypt('proba'),
        ]);

        $this->coleg = User::create([
            'name' => 'Colegul',
            'email' => 'coleg994@example.test',
            'password' => bcrypt('proba'),
        ]);

        foreach ([[$this->administrator->id, true], [$this->coleg->id, false]] as [$id, $esteAdmin]) {
            DB::table('company_user')->insert([
                'user_id' => $id,
                'company_id' => self::COMPANIE,
                'administrator' => $esteAdmin,
            ]);
        }

        ContextCompanie::fixeaza(self::COMPANIE);

        // Declaratia e a colegului, nu a administratorului.
        $this->declaratie = AnafDeclaratie::create([
            'company_id' => self::COMPANIE,
            'user_id' => $this->coleg->id,
            'cui' => '15208744',
            'tip' => 'D112',
            'nume_fisier' => 'proba-d112.xml',
            'erori_validare' => 'E: eroare de probă',
        ]);

        ContextCompanie::elibereaza();
    }

    protected function tearDown(): void
    {
        ContextCompanie::elibereaza();

        AnafDeclaratie::query()->toateCompaniile()->where('company_id', self::COMPANIE)->delete();
        DB::table('company_user')->where('company_id', self::COMPANIE)->delete();
        User::whereIn('email', ['admin994@example.test', 'coleg994@example.test'])->delete();

        parent::tearDown();
    }

    /**
     * Miezul greselii: cine e administrator nu se poate sti fara client stiut.
     *
     * Cu clientul pus, administratorul nu e limitat la ce a lucrat el. Fara el,
     * acelasi om e limitat — iar cautarea dupa id se facea tocmai atunci.
     */
    public function test_administratorul_e_recunoscut_doar_cu_clientul_stiut(): void
    {
        $this->actingAs($this->administrator, 'api');

        ContextCompanie::elibereaza();
        $faraClient = ContextUtilizator::limitatLa();

        ContextCompanie::fixeaza(self::COMPANIE);
        $cuClient = ContextUtilizator::limitatLa();

        $this->assertSame(
            $this->administrator->id,
            $faraClient,
            'fără client știut, administratorul e luat drept utilizator obișnuit'
        );

        $this->assertNull($cuClient, 'cu clientul știut, administratorul vede tot ce s-a lucrat pentru firmă');
    }

    /** Cu clientul pus la vreme, declaratia colegului se gaseste. */
    public function test_declaratia_colegului_se_gaseste_pentru_administrator(): void
    {
        $this->actingAs($this->administrator, 'api');
        ContextCompanie::fixeaza(self::COMPANIE);

        $this->assertNotNull(
            AnafDeclaratie::find($this->declaratie->id),
            'administratorul firmei trebuie să găsească declarația lucrată de coleg'
        );
    }

    /** Utilizatorul obisnuit ramane insa la ce a lucrat el. */
    public function test_utilizatorul_obisnuit_nu_vede_documentul_altuia(): void
    {
        $altul = User::create([
            'name' => 'Alt utilizator',
            'email' => 'altul994@example.test',
            'password' => bcrypt('proba'),
        ]);

        DB::table('company_user')->insert([
            'user_id' => $altul->id,
            'company_id' => self::COMPANIE,
            'administrator' => false,
        ]);

        $this->actingAs($altul, 'api');
        ContextCompanie::fixeaza(self::COMPANIE);

        $this->assertNull(
            AnafDeclaratie::find($this->declaratie->id),
            'un utilizator obișnuit n-are ce căuta în documentul altuia'
        );

        $altul->delete();
    }

    /**
     * Butonul care a scos greseala la iveala: SPV Wizard, cerut de administrator
     * pentru declaratia colegului.
     */
    public function test_wizardul_raspunde_administratorului(): void
    {
        $raspuns = $this->actingAs($this->administrator, 'api')
            ->withHeader('AuthorizationHeader', (string) self::COMPANIE)
            ->get('/api/declaratii/' . $this->declaratie->id . '/erori');

        $this->assertNotSame(404, $raspuns->status(), 'wizardul nu găsește declarația firmei: ' . $raspuns->status());
    }
}
