<?php

namespace Tests\Unit;

use App\Http\Controllers\Api\AdministrareController;
use App\Models\Company;
use App\Models\DianaSoftMenuOption;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * Modulele contului, alese la crearea lui din „Administrare clienti".
 *
 * Meniul omului se citeste din legatura lui cu optiunile de meniu, per firma.
 * Fara randurile acelea, contul nou intra intr-o aplicatie fara niciun meniu si
 * n-are ce face acolo — de aceea modulele se aleg chiar cand se face contul.
 */
class ModuleContNouTest extends TestCase
{
    protected $client;

    /** Modulul de proba si pagina care sta sub el. */
    protected $modul;
    protected $pagina;

    protected $conturi = [];

    protected function setUp(): void
    {
        parent::setUp();

        $this->client = Company::create(['denumire' => 'MODULE PROBA SRL', 'cui' => '99000444']);

        $this->modul = DianaSoftMenuOption::create([
            'name' => 'Modul de probă',
            'url' => '/modul-proba',
            'slug' => 'modul-proba',
            'icon' => 'BoxIcon',
            'parent' => '\\',
            'dropdown' => 0,
            'position1' => 5000,
            'position2' => 0,
            'isdisabled' => false,
        ]);

        $this->pagina = DianaSoftMenuOption::create([
            'name' => 'Pagina din modul',
            'url' => '/modul-proba/pagina',
            'slug' => 'modul-proba-pagina',
            'icon' => 'FileIcon',
            // Legatura de parinte se tine pe numele parintelui, nu pe id.
            'parent' => 'Modul de probă',
            'dropdown' => 0,
            'position1' => 5001,
            'position2' => 0,
            'isdisabled' => false,
        ]);
    }

    protected function tearDown(): void
    {
        foreach ($this->conturi as $cont) {
            DB::table('dianasoftmenuoption_user')->where('user_id', $cont->id)->delete();
            DB::table('company_user')->where('user_id', $cont->id)->delete();
            $cont->delete();
        }

        $this->pagina->delete();
        $this->modul->delete();
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

    protected function esteActiv(int $userId, int $optiuneId): bool
    {
        return (bool) DB::table('dianasoftmenuoption_user')
            ->where('user_id', $userId)
            ->where('company_id', $this->client->id)
            ->where('dianasoftmenuoption_id', $optiuneId)
            ->value('isactive');
    }

    /** Modulul bifat se scrie in meniul contului si se intoarce inapoi. */
    public function test_modulul_ales_ajunge_in_meniul_contului()
    {
        $cont = $this->creeaza(['module' => [$this->modul->id]]);

        $this->assertContains($this->modul->id, $cont['module']);
        $this->assertTrue($this->esteActiv($cont['id'], $this->modul->id));
    }

    /**
     * Ce sta sub modul merge dupa el: altfel omul ar primi o intrare de meniu
     * goala, fara paginile din ea.
     */
    public function test_paginile_din_modul_merg_dupa_modul()
    {
        $cont = $this->creeaza(['module' => [$this->modul->id]]);

        $this->assertTrue($this->esteActiv($cont['id'], $this->pagina->id));

        // In fereastra se bifeaza doar modulele de prim rang, nu si paginile lor.
        $this->assertNotContains($this->pagina->id, $cont['module']);
    }

    /** Ce nu s-a bifat ramane inchis, dar randul se scrie: se poate bifa mai tarziu. */
    public function test_modulul_nebifat_ramane_inchis_dar_are_rand()
    {
        $cont = $this->creeaza(['module' => []]);

        $this->assertSame([], $cont['module']);
        $this->assertFalse($this->esteActiv($cont['id'], $this->modul->id));

        $randuri = DB::table('dianasoftmenuoption_user')
            ->where('user_id', $cont['id'])
            ->where('company_id', $this->client->id)
            ->count();

        $this->assertSame(DianaSoftMenuOption::count(), $randuri);
    }

    /** Modulele se schimba si dupa aceea, din aceeasi fereastra. */
    public function test_modulele_se_pot_schimba_pe_urma()
    {
        $cont = $this->creeaza(['module' => [$this->modul->id]]);
        $utilizator = User::find($cont['id']);

        $dupa = (new AdministrareController())->actualizeazaUtilizator(
            new Request(['company_id' => $this->client->id, 'module' => []]),
            $utilizator
        )->getData(true)['data'];

        $this->assertSame([], $dupa['module']);
        $this->assertFalse($this->esteActiv($utilizator->id, $this->modul->id));
        $this->assertFalse($this->esteActiv($utilizator->id, $this->pagina->id));
    }

    /** Meniul e per firma: modulele scrise aici nu ating alta firma a aceluiasi om. */
    public function test_modulele_sunt_ale_firmei_nu_ale_omului()
    {
        $cont = $this->creeaza(['module' => [$this->modul->id]]);

        $altaFirma = Company::create(['denumire' => 'ALTĂ FIRMĂ SRL', 'cui' => '99000555']);

        DB::table('dianasoftmenuoption_user')->insert([
            'user_id' => $cont['id'],
            'dianasoftmenuoption_id' => $this->modul->id,
            'company_id' => $altaFirma->id,
            'isactive' => true,
        ]);

        (new AdministrareController())->actualizeazaUtilizator(
            new Request(['company_id' => $this->client->id, 'module' => []]),
            User::find($cont['id'])
        );

        $laCealalta = DB::table('dianasoftmenuoption_user')
            ->where('user_id', $cont['id'])
            ->where('company_id', $altaFirma->id)
            ->where('dianasoftmenuoption_id', $this->modul->id)
            ->value('isactive');

        $this->assertTrue((bool) $laCealalta, 'Modulele altei firme n-au ce căuta în ștergerea aceasta.');

        DB::table('dianasoftmenuoption_user')->where('company_id', $altaFirma->id)->delete();
        $altaFirma->delete();
    }
}
