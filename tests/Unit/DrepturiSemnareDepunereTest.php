<?php

namespace Tests\Unit;

use App\Http\Controllers\Api\DeclaratiiController;
use App\Http\Controllers\Api\UtilizatoriClientController;
use App\Models\AnafDeclaratie;
use App\Models\Company;
use App\Models\User;
use App\Services\Anaf\Declaratii\DepunereService;
use App\Services\Anaf\Declaratii\SemnareService;
use App\Support\ContextCompanie;
use App\Support\ContextUtilizator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * Cine are voie sa semneze si cine sa depuna.
 *
 * Sunt drepturi date anume, pe legatura dintre om si firma: semnatura e a
 * persoanei care tine tokenul, iar depunerea la ANAF nu se mai poate lua
 * inapoi. Ascunderea butoanelor din interfata nu e o oprire — refuzul trebuie
 * sa vina de la server, pentru oricine trimite cererea de-a dreptul.
 */
class DrepturiSemnareDepunereTest extends TestCase
{
    protected $client;
    protected $sef;
    protected $semnatarul;
    protected $depunatorul;
    protected $simplu;

    protected function setUp(): void
    {
        parent::setUp();

        $this->client = Company::create(['denumire' => 'CABINET FISCAL SRL', 'cui' => '99000333']);

        $this->sef = $this->cont('sef.drepturi@example.com', ['administrator' => true]);
        $this->semnatarul = $this->cont('semnatar.drepturi@example.com', ['poate_semna' => true]);
        $this->depunatorul = $this->cont('depunator.drepturi@example.com', ['poate_depune' => true]);
        $this->simplu = $this->cont('simplu.drepturi@example.com');

        ContextCompanie::fixeaza($this->client->id);
    }

    protected function tearDown(): void
    {
        ContextUtilizator::faraLimitare(function () {
            AnafDeclaratie::query()->toateCompaniile()->where('company_id', $this->client->id)->delete();
        });

        DB::table('company_user')->where('company_id', $this->client->id)->delete();

        foreach ([$this->sef, $this->semnatarul, $this->depunatorul, $this->simplu] as $cont) {
            $cont->delete();
        }

        $this->client->delete();

        Auth::forgetGuards();
        ContextCompanie::elibereaza();

        parent::tearDown();
    }

    protected function cont(string $email, array $drepturi = []): User
    {
        $user = User::create([
            'name' => $email,
            'email' => $email,
            'password' => Hash::make('parola-de-proba'),
            'user_type' => 'user',
            'blocat' => 'Nu',
        ]);

        $this->client->users()->attach($user->id, $drepturi + [
            'administrator' => false,
            'poate_semna' => false,
            'poate_depune' => false,
        ]);

        return $user;
    }

    protected function ca(User $user): void
    {
        Auth::guard('api')->setUser($user);
    }

    /** O declaratie semnata, cu tot ce trebuie ca depunerea sa poata incepe. */
    protected function declaratie(): AnafDeclaratie
    {
        return AnafDeclaratie::create([
            'company_id' => $this->client->id,
            'nume_fisier' => 'D112.xml',
            'tip' => 'D112',
            'cui' => '15208744',
            'pas' => 'semnat',
            'semnat' => true,
            'cale_pdf' => 'anaf/proba.pdf',
            'cale_pdf_semnat' => 'anaf/proba_semnat.pdf',
            'user_id' => $this->sef->id,
        ]);
    }

    public function test_utilizatorul_fara_drept_nu_poate_semna(): void
    {
        $this->assertFalse($this->drept($this->simplu, 'poateSemna'));
        $this->assertFalse($this->drept($this->depunatorul, 'poateSemna'));
        $this->assertTrue($this->drept($this->semnatarul, 'poateSemna'));
    }

    public function test_utilizatorul_fara_drept_nu_poate_depune(): void
    {
        $this->assertFalse($this->drept($this->simplu, 'poateDepune'));
        $this->assertFalse($this->drept($this->semnatarul, 'poateDepune'));
        $this->assertTrue($this->drept($this->depunatorul, 'poateDepune'));
    }

    /** Administratorul firmei le are pe amandoua: el le si da celorlalti. */
    public function test_administratorul_are_amandoua_drepturile(): void
    {
        $this->assertTrue($this->drept($this->sef, 'poateSemna'));
        $this->assertTrue($this->drept($this->sef, 'poateDepune'));
    }

    /** Fara firma aleasa nu se poate vorbi de drepturi in firma. */
    public function test_fara_companie_nu_exista_drept(): void
    {
        ContextCompanie::elibereaza();
        $this->ca($this->semnatarul);

        $this->assertFalse(ContextUtilizator::poateSemna());

        ContextCompanie::fixeaza($this->client->id);
    }

    /** Cererea de semnare a celui fara drept se opreste inaintea tokenului. */
    public function test_cererea_de_semnare_e_respinsa_fara_drept(): void
    {
        $this->ca($this->simplu);

        $semnare = $this->createMock(SemnareService::class);
        $semnare->expects($this->never())->method('semneaza');

        $raspuns = (new DeclaratiiController())->semneaza($this->declaratie(), $semnare);

        $this->assertSame(403, $raspuns->getStatusCode());
        $this->assertFalse($raspuns->getData()->success);
    }

    /** La fel depunerea: nu se ajunge la ANAF fara dreptul dat. */
    public function test_cererea_de_depunere_e_respinsa_fara_drept(): void
    {
        $this->ca($this->semnatarul);

        $depunere = $this->createMock(DepunereService::class);
        $depunere->expects($this->never())->method('depune');

        $raspuns = (new DeclaratiiController())->depune($this->declaratie(), $depunere);

        $this->assertSame(403, $raspuns->getStatusCode());
        $this->assertFalse($raspuns->getData()->success);
    }

    /** Drepturile stau pe legatura cu firma: acelasi om, alta firma, alt drept. */
    public function test_dreptul_e_pe_firma_nu_pe_om(): void
    {
        $alta = Company::create(['denumire' => 'ALT BIROU SRL', 'cui' => '99000334']);
        $alta->users()->attach($this->semnatarul->id, [
            'administrator' => false,
            'poate_semna' => false,
            'poate_depune' => false,
        ]);

        $this->ca($this->semnatarul);

        ContextCompanie::fixeaza($alta->id);
        $inAltaFirma = ContextUtilizator::poateSemna();

        ContextCompanie::fixeaza($this->client->id);
        $inFirmaLui = ContextUtilizator::poateSemna();

        DB::table('company_user')->where('company_id', $alta->id)->delete();
        $alta->delete();

        $this->assertFalse($inAltaFirma);
        $this->assertTrue($inFirmaLui);
    }

    /** Drepturile se scriu la creare si se intorc de unde le citeste interfata. */
    public function test_drepturile_se_salveaza_la_creare_si_se_modifica(): void
    {
        $this->ca($this->sef);

        $controller = new UtilizatoriClientController();

        $creat = $controller->store(new Request([
            'nume' => 'Maria Noua',
            'email' => 'maria.drepturi@example.com',
            'parola' => 'parola-de-proba',
            'poate_semna' => true,
            'poate_depune' => false,
        ]))->getData(true)['data'];

        $this->assertTrue($creat['poate_semna']);
        $this->assertFalse($creat['poate_depune']);

        $utilizator = User::find($creat['id']);

        // Se da si dreptul de depunere, iar cel de semnare se ia inapoi.
        $modificat = $controller->update(new Request([
            'poate_semna' => false,
            'poate_depune' => true,
        ]), $utilizator)->getData(true)['data'];

        $this->assertFalse($modificat['poate_semna']);
        $this->assertTrue($modificat['poate_depune']);

        $this->ca($utilizator);
        $this->assertFalse(ContextUtilizator::poateSemna());
        $this->assertTrue(ContextUtilizator::poateDepune());

        DB::table('company_user')->where('user_id', $utilizator->id)->delete();
        $utilizator->delete();
    }

    protected function drept(User $user, string $metoda): bool
    {
        $this->ca($user);

        return ContextUtilizator::$metoda();
    }
}
