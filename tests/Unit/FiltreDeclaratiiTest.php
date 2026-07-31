<?php

namespace Tests\Unit;

use App\Http\Controllers\Api\DeclaratiiController;
use App\Models\AnafDeclaratie;
use App\Support\ContextCompanie;
use Illuminate\Http\Request;
use Tests\TestCase;

/**
 * Filtrele din tabelul de declarații: cele scrise de mână caută pe bucată de
 * text, ca să nu fie nevoie de valoarea întreagă și scrisă identic.
 */
class FiltreDeclaratiiTest extends TestCase
{
    protected const COMPANIE = 995;

    protected function setUp(): void
    {
        parent::setUp();

        ContextCompanie::fixeaza(self::COMPANIE);

        $this->creeaza(['tip' => 'D394', 'cui' => '15208744', 'den_firma' => 'SC DIANA SOFT SRL', 'luna' => 6, 'anul' => 2026, 'pas' => 'semnat', 'index_recipisa' => '912239948']);
        $this->creeaza(['tip' => 'D300', 'cui' => '33486455', 'den_firma' => 'ALFA CONSTRUCT SRL', 'luna' => 5, 'anul' => 2026, 'pas' => 'eroare_semnare']);
        $this->creeaza(['tip' => 'D406', 'cui' => '33486455', 'den_firma' => 'ALFA CONSTRUCT SRL', 'luna' => 6, 'anul' => 2025, 'pas' => 'depus', 'index_recipisa' => '880012345']);
    }

    protected function tearDown(): void
    {
        AnafDeclaratie::query()->toateCompaniile()->where('company_id', self::COMPANIE)->delete();
        ContextCompanie::elibereaza();

        parent::tearDown();
    }

    protected function creeaza(array $atribute): AnafDeclaratie
    {
        return AnafDeclaratie::create(array_merge([
            'company_id' => self::COMPANIE,
            'nume_fisier' => 'proba.xml',
        ], $atribute));
    }

    /** @return array<int, array> rândurile întoarse de listă */
    protected function filtreaza(array $filtre): array
    {
        $controller = $this->app->make(DeclaratiiController::class);

        return $controller->index(new Request($filtre))->getData(true)['data'];
    }

    public function test_filtrul_dupa_tip_prinde_si_o_bucata_din_denumire(): void
    {
        $this->assertCount(1, $this->filtreaza(['tip' => 'D394']));
        $this->assertCount(3, $this->filtreaza(['tip' => 'D']));
        $this->assertCount(0, $this->filtreaza(['tip' => 'D999']));
    }

    public function test_filtrul_dupa_denumire_nu_cere_denumirea_intreaga(): void
    {
        $gasite = $this->filtreaza(['den_firma' => 'ALFA']);

        $this->assertCount(2, $gasite);
        $this->assertSame(['ALFA CONSTRUCT SRL', 'ALFA CONSTRUCT SRL'], array_column($gasite, 'den_firma'));
    }

    public function test_filtrul_dupa_cui_prinde_si_o_bucata(): void
    {
        $this->assertCount(2, $this->filtreaza(['cui' => '33486']));
        $this->assertCount(1, $this->filtreaza(['cui' => '15208744']));
    }

    public function test_filtrul_dupa_perioada(): void
    {
        $this->assertCount(2, $this->filtreaza(['luna' => 6]));
        $this->assertCount(2, $this->filtreaza(['anul' => 2026]));

        // Luna si anul se strang impreuna
        $this->assertCount(1, $this->filtreaza(['luna' => 6, 'anul' => 2026]));
    }

    public function test_filtrul_dupa_index_de_incarcare(): void
    {
        $gasite = $this->filtreaza(['index_recipisa' => '9122']);

        $this->assertCount(1, $gasite);
        $this->assertSame('912239948', $gasite[0]['index_recipisa']);
    }

    /** Starea se alege dintr-o listă, deci se compară exact. */
    public function test_filtrul_dupa_stare_este_potrivire_exacta(): void
    {
        $this->assertCount(1, $this->filtreaza(['pas' => 'eroare_semnare']));
        $this->assertCount(0, $this->filtreaza(['pas' => 'eroare']));
    }

    public function test_filtrele_se_string_intre_ele(): void
    {
        $gasite = $this->filtreaza(['den_firma' => 'ALFA', 'anul' => 2026]);

        $this->assertCount(1, $gasite);
        $this->assertSame('D300', $gasite[0]['tip']);
    }

    /** Un filtru gol nu trebuie să restrângă nimic. */
    public function test_filtrele_goale_nu_restrang_lista(): void
    {
        $this->assertCount(3, $this->filtreaza([]));
        $this->assertCount(3, $this->filtreaza(['tip' => '', 'cui' => '', 'den_firma' => '']));
    }
}
