<?php

namespace Tests\Unit;

use App\Http\Controllers\Api\SpvController;
use App\Models\SpvMesaj;
use App\Services\Anaf\Spv\SpvStorage;
use App\Support\ContextCompanie;
use Illuminate\Http\Request;
use Tests\TestCase;

/**
 * Documentele lipsa se aduc lot dupa lot, pana nu mai ramane nimic.
 *
 * O cerere aduce cel mult atatea cate incap in rabdarea serverului de web:
 * fiecare document are pauza ceruta de ANAF si drumul pana la tokenul
 * clientului. Restul se aduceau la urmatoarea apasare — dar nimeni n-are de ce
 * sa apese de cinci ori pentru o suta de mesaje, si nici n-avea de unde sti ca
 * trebuie. Acum fila cere loturile singura, iar lista nu se mai cere de la ANAF
 * la fiecare lot: mesajele sunt deja in baza de date.
 */
class DescarcareInLoturiTest extends TestCase
{
    protected const COMPANIE = 992;

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

    protected function mesaje(int $cate, array $peste = []): void
    {
        for ($i = 0; $i < $cate; $i++) {
            SpvMesaj::create(array_merge([
                'company_id' => self::COMPANIE,
                'mesaj_id' => 'M' . self::COMPANIE . '-' . $i . '-' . random_int(1000, 9999),
                'cif' => '15208744',
                'tip' => 'DECIZIE',
            ], $peste));
        }
    }

    /**
     * Cate mesaje ar intra in lotul urmator si cate raman, fara sa se atinga
     * ANAF: se cheama chiar cantarirea din controler.
     *
     * @return array{descarcate: int, ramase: int, erori: array}
     */
    protected function cantareste(): array
    {
        $controller = $this->app->make(SpvController::class);

        $mesaje = SpvMesaj::query()
            ->whereNull('arhiva_cale')
            ->where('incercari', '<', (int) config('anaf.spv.incercari_max'))
            ->get()
            ->all();

        // Un depozit care nu aduce nimic: aici se numara loturile, nu se descarca.
        $storage = \Mockery::mock(SpvStorage::class);
        $storage->shouldReceive('aduce')->andReturn([]);

        $metoda = new \ReflectionMethod($controller, 'descarcaFisiereLipsa');
        $metoda->setAccessible(true);

        return $metoda->invoke($controller, $mesaje, $storage);
    }

    /** Lotul e marginit, iar ce ramane se spune raspicat — nu se pierde. */
    public function test_lotul_e_marginit_si_restul_se_numara(): void
    {
        $limita = (int) config('anaf.spv.limita_descarcari');

        $this->mesaje($limita + 7);

        $rezultat = $this->cantareste();

        $this->assertSame($limita, $rezultat['descarcate'], 'lotul a trecut peste limită');
        $this->assertSame(7, $rezultat['ramase'], 'restul nu e numărat');
    }

    /** Cand incape tot intr-un lot, nu mai ramane nimic de cerut. */
    public function test_cand_incape_tot_nu_mai_ramane_nimic(): void
    {
        $this->mesaje(3);

        $rezultat = $this->cantareste();

        $this->assertSame(3, $rezultat['descarcate']);
        $this->assertSame(0, $rezultat['ramase']);
    }

    /**
     * Mesajele care au esuat de prea multe ori nu mai intra in numaratoare: ele
     * ar tine „ramase" mereu peste zero, iar fila ar cere loturi la nesfarsit.
     */
    public function test_cele_esuate_de_prea_multe_ori_nu_mai_tin_loturile_deschise(): void
    {
        $this->mesaje(4, ['incercari' => (int) config('anaf.spv.incercari_max')]);

        $rezultat = $this->cantareste();

        $this->assertSame(0, $rezultat['descarcate']);
        $this->assertSame(0, $rezultat['ramase']);
    }

    /** Documentele deja duse in arhiva clientului nu se mai cer o data. */
    public function test_cele_duse_in_arhiva_nu_se_mai_cer(): void
    {
        $this->mesaje(5, ['arhiva_cale' => 'firme/proba/decizie.pdf']);

        $rezultat = $this->cantareste();

        $this->assertSame(0, $rezultat['descarcate']);
        $this->assertSame(0, $rezultat['ramase']);
    }

    /** Ruta pe care fila cere loturile urmatoare exista si nu cere lista de la ANAF. */
    public function test_ruta_pentru_loturile_urmatoare_exista(): void
    {
        $this->assertTrue(
            collect(app('router')->getRoutes())->contains(function ($ruta) {
                return $ruta->uri() === 'api/spv/descarca-lipsa';
            }),
            'lipsește ruta prin care fila cere loturile rămase'
        );

        $controler = file_get_contents(app_path('Http/Controllers/Api/SpvController.php'));

        $inceput = strpos($controler, 'function descarcaLipsa');
        $sfarsit = strpos($controler, 'function istoric');

        $this->assertNotFalse($inceput);
        $this->assertStringNotContainsString(
            'listaMesaje',
            substr($controler, $inceput, $sfarsit - $inceput),
            'lotul următor n-are de ce să mai ceară lista de la ANAF'
        );
    }

    /** Fila cere singura loturile ramase: omul apasa o data. */
    public function test_fila_cere_singura_loturile_ramase(): void
    {
        $fila = file_get_contents(base_path('resources/js/src/views/app_pages/spv/Mesaje.vue'));

        $this->assertStringContainsString('aduceRestul', $fila);
        $this->assertStringContainsString('/spv/descarca-lipsa', $fila);
        $this->assertStringNotContainsString(
            'rămase pentru următoarea încărcare',
            $fila,
            'nu mai e nimic de apăsat a doua oară'
        );
    }
}
