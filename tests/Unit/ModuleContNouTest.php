<?php

namespace Tests\Unit;

use App\Http\Controllers\Api\AdministrareController;
use App\Models\AbonamentClient;
use App\Models\Company;
use App\Models\DianaSoftMenuOption;
use App\Models\User;
use App\Support\Modul;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * Modulele contului: SPV Curier, Dispecer e-Transport si Grefier alert.
 *
 * Accesul tine de doua lucruri deodata — abonamentul firmei si darea catre om.
 * Ce nu i s-a dat, omul nu vede: nici in antet, nici la o cerere trimisa de-a
 * dreptul catre server.
 */
class ModuleContNouTest extends TestCase
{
    protected $client;
    protected $abonament;
    protected $conturi = [];

    /** Optiunile de meniu create de test, ca sa fie sterse doar ele. */
    protected $meniuriFacuteAici = [];

    protected function setUp(): void
    {
        parent::setUp();

        $this->client = Company::create(['denumire' => 'MODULE PROBA SRL', 'cui' => '99000444']);

        $this->abonament = AbonamentClient::create([
            'company_id' => $this->client->id,
            'proba_zile' => 30,
            'proba_pana_la' => now()->addDays(30)->toDateString(),
            'modul_spv' => true,
            'modul_etransport' => true,
            'modul_portal_just' => true,
        ]);

        // Intrarile de meniu ale modulelor exista in aplicatie; daca lipsesc de
        // pe serverul de proba, se fac aici si se sterg la sfarsit.
        foreach (['spv', 'vector-fiscal', 'etransport-anaf'] as $slug) {
            if (DianaSoftMenuOption::where('slug', $slug)->exists()) {
                continue;
            }

            $this->meniuriFacuteAici[] = DianaSoftMenuOption::create([
                'name' => 'Proba ' . $slug,
                'url' => '/' . $slug,
                'slug' => $slug,
                'parent' => '\\',
                'dropdown' => 0,
                'position1' => 9000,
                'position2' => 0,
                'isdisabled' => false,
            ]);
        }
    }

    protected function tearDown(): void
    {
        foreach ($this->conturi as $cont) {
            DB::table('dianasoftmenuoption_user')->where('user_id', $cont->id)->delete();
            DB::table('company_user')->where('user_id', $cont->id)->delete();
            $cont->delete();
        }

        foreach ($this->meniuriFacuteAici as $meniu) {
            $meniu->delete();
        }

        $this->abonament->delete();
        $this->client->delete();

        parent::tearDown();
    }

    /** @return array<string, mixed> contul asa cum il intoarce controllerul */
    protected function creeaza(array $date): array
    {
        $raspuns = (new AdministrareController())->creeazaUtilizator(
            new Request(array_merge([
                'nume' => 'Om Nou',
                'email' => 'om.module' . random_int(1000, 9999) . '@example.com',
                'parola' => 'parola-de-proba',
            ], $date)),
            $this->client
        )->getData(true)['data'];

        $this->conturi[] = User::find($raspuns['id']);

        return $raspuns;
    }

    protected function meniuActiv(int $userId, string $slug): bool
    {
        $optiune = DianaSoftMenuOption::where('slug', $slug)->first();

        return $optiune && (bool) DB::table('dianasoftmenuoption_user')
            ->where('user_id', $userId)
            ->where('company_id', $this->client->id)
            ->where('dianasoftmenuoption_id', $optiune->id)
            ->value('isactive');
    }

    /** Se dau doar modulele bifate, nu si celelalte. */
    public function test_contul_primeste_doar_modulele_bifate()
    {
        $cont = $this->creeaza(['module' => ['spv']]);

        $this->assertSame(['spv'], $cont['module']);
        $this->assertSame(['spv'], Modul::vazuteDe($cont['id'], $this->client->id));
    }

    /** Ce ține de modul — paginile lui din meniu — merge după el. */
    public function test_meniul_modulului_merge_dupa_modul()
    {
        $cont = $this->creeaza(['module' => ['spv']]);

        $this->assertTrue($this->meniuActiv($cont['id'], 'spv'));
        $this->assertTrue($this->meniuActiv($cont['id'], 'vector-fiscal'));
        $this->assertFalse($this->meniuActiv($cont['id'], 'etransport-anaf'));
    }

    /** Fara nicio bifa, contul nu vede niciun modul. */
    public function test_contul_fara_module_nu_vede_niciunul()
    {
        $cont = $this->creeaza(['module' => []]);

        $this->assertSame([], $cont['module']);
        $this->assertSame([], Modul::vazuteDe($cont['id'], $this->client->id));
        $this->assertFalse($this->meniuActiv($cont['id'], 'spv'));
    }

    /** Modulele se schimba si dupa aceea, din aceeasi fereastra. */
    public function test_modulele_se_pot_schimba_pe_urma()
    {
        $cont = $this->creeaza(['module' => ['spv']]);
        $utilizator = User::find($cont['id']);

        $dupa = (new AdministrareController())->actualizeazaUtilizator(
            new Request(['company_id' => $this->client->id, 'module' => ['etransport', 'portal_just']]),
            $utilizator
        )->getData(true)['data'];

        $this->assertSame(['etransport', 'portal_just'], $dupa['module']);
        $this->assertFalse($this->meniuActiv($utilizator->id, 'spv'));
        $this->assertTrue($this->meniuActiv($utilizator->id, 'etransport-anaf'));
    }

    /**
     * Darea nu trece peste abonament: modulul necumparat de firma nu se vede,
     * chiar bifat fiind. Altfel s-ar putea da din greseala ceva neplatit.
     */
    public function test_modulul_din_afara_abonamentului_tot_nu_se_vede()
    {
        $cont = $this->creeaza(['module' => ['spv', 'portal_just']]);

        $this->abonament->update(['modul_portal_just' => false]);

        $this->assertSame(['spv'], Modul::vazuteDe($cont['id'], $this->client->id));
    }

    /**
     * Conturile de dinaintea bifelor n-au nimic scris: ele raman cu tot ce
     * cuprinde abonamentul, ca sa nu se trezeasca nimeni fara acces peste noapte.
     */
    public function test_contul_vechi_ramane_cu_tot_abonamentul()
    {
        $cont = $this->creeaza(['module' => ['spv']]);

        // „Nescris" — cum arata legatura facuta inainte de coloana aceasta.
        Modul::scrie($cont['id'], $this->client->id, null);

        $this->assertSame(
            ['spv', 'etransport', 'portal_just'],
            Modul::vazuteDe($cont['id'], $this->client->id)
        );
    }

    /** Modulele sunt ale firmei: la alta firma, acelasi om poate avea altele. */
    public function test_modulele_sunt_ale_firmei_nu_ale_omului()
    {
        $cont = $this->creeaza(['module' => ['spv']]);

        $altaFirma = Company::create(['denumire' => 'ALTĂ FIRMĂ SRL', 'cui' => '99000555']);
        $altaFirma->users()->attach($cont['id'], ['administrator' => false]);
        Modul::scrie($cont['id'], $altaFirma->id, ['etransport']);

        $this->assertSame(['spv'], Modul::aleContului($cont['id'], $this->client->id));
        $this->assertSame(['etransport'], Modul::aleContului($cont['id'], $altaFirma->id));

        DB::table('company_user')->where('company_id', $altaFirma->id)->delete();
        $altaFirma->delete();
    }
}
