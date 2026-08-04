<?php

namespace Tests\Unit;

use App\Http\Controllers\Api\SpvController;
use App\Models\SpvMesaj;
use App\Support\ContextCompanie;
use Tests\TestCase;

/**
 * „Descarcă mesaje" aduce documentele mesajelor din SPV, dar nu si pe cele care
 * vin din alte file: recipisele odata cu declaratiile, raspunsurile odata cu
 * solicitarile. Cerute din doua locuri, ar consuma de doua ori din limita de
 * apeluri catre ANAF.
 */
class DescarcareMesajeSpvTest extends TestCase
{
    protected const COMPANIE = 985;

    protected function setUp(): void
    {
        parent::setUp();

        ContextCompanie::fixeaza(self::COMPANIE);
    }

    protected function tearDown(): void
    {
        SpvMesaj::query()->toateCompaniile()->where('company_id', self::COMPANIE)->delete();
        ContextCompanie::elibereaza();

        parent::tearDown();
    }

    protected function mesaj(string $tip): SpvMesaj
    {
        return SpvMesaj::create([
            'company_id' => self::COMPANIE,
            'mesaj_id' => (string) random_int(1000000, 9999999),
            'cif' => '15208744',
            'tip' => $tip,
        ]);
    }

    /** @return string|null fila din care se aduce documentul */
    protected function fila(SpvMesaj $mesaj): ?string
    {
        $controller = $this->app->make(SpvController::class);

        $metoda = new \ReflectionMethod($controller, 'filaCareAduce');
        $metoda->setAccessible(true);

        return $metoda->invoke($controller, $mesaj);
    }

    public function test_recipisele_vin_din_fila_declaratiilor(): void
    {
        $this->assertSame('Declarații fiscale', $this->fila($this->mesaj('RECIPISA')));
    }

    public function test_raspunsurile_vin_din_fila_solicitarilor(): void
    {
        $this->assertSame('Solicitări ANAF', $this->fila($this->mesaj('RASPUNS SOLICITARE')));
    }

    /** ANAF nu scrie tipurile mereu la fel; potrivirea e pe bucata de text. */
    public function test_potrivirea_nu_tine_cont_de_scriere(): void
    {
        $this->assertSame('Declarații fiscale', $this->fila($this->mesaj('Recipisa depunere declaratie')));
        $this->assertSame('Solicitări ANAF', $this->fila($this->mesaj('raspuns solicitare fisa rol')));
    }

    /** Restul documentelor se aduc chiar de aici. */
    public function test_celelalte_documente_se_aduc_din_mesaje(): void
    {
        $this->assertNull($this->fila($this->mesaj('SOMATIE')));
        $this->assertNull($this->fila($this->mesaj('EXTRAS DE CONT')));
        $this->assertNull($this->fila($this->mesaj('Situatie Sintetica')));
    }

    public function test_mesajul_fara_tip_nu_este_trimis_in_alta_fila(): void
    {
        $this->assertNull($this->fila($this->mesaj('')));
    }

    /** @return \Illuminate\Support\Collection randurile din fila de mesaje */
    protected function randurile()
    {
        $controller = $this->app->make(SpvController::class);

        $metoda = new \ReflectionMethod($controller, 'istoric');
        $metoda->setAccessible(true);

        return collect($metoda->invoke($controller, new \Illuminate\Http\Request()));
    }

    /**
     * Documentele care se aduc din alta fila nu se mai arata si aici: locul lor
     * e langa documentul la care raspund, iar in lista asta ar fi doar zgomot.
     */
    public function test_recipisele_si_raspunsurile_nu_se_arata_in_fila_de_mesaje(): void
    {
        $this->mesaj('RECIPISA');
        $this->mesaj('Recipisa depunere declaratie');
        $this->mesaj('RASPUNS SOLICITARE');
        $this->mesaj('SOMATIE');

        $tipuri = $this->randurile()->pluck('tip');

        $this->assertContains('SOMATIE', $tipuri);
        $this->assertNotContains('RECIPISA', $tipuri);
        $this->assertNotContains('Recipisa depunere declaratie', $tipuri);
        $this->assertNotContains('RASPUNS SOLICITARE', $tipuri);
    }

    /** Fara tip nu se stie ce e mesajul, deci nu se ascunde. */
    public function test_mesajul_fara_tip_ramane_in_lista(): void
    {
        $mesaj = $this->mesaj('');

        $this->assertContains($mesaj->mesaj_id, $this->randurile()->pluck('id'));
    }
}
