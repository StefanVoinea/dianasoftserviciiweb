<?php

namespace Tests\Unit;

use App\Http\Controllers\Api\SpvController;
use App\Models\SpvMesaj;
use Tests\TestCase;

class SpvDescarcareTest extends TestCase
{
    /** Expune metodele protejate ale controllerului pentru verificare. */
    protected function controller()
    {
        return new class extends SpvController {
            public function testLipseste(SpvMesaj $mesaj): bool
            {
                return $this->lipsesteFisierul($mesaj);
            }

            public function testPrezinta(SpvMesaj $mesaj): array
            {
                return $this->prezinta($mesaj);
            }
        };
    }

    public function test_mesajul_fara_cale_este_considerat_nedescarcat(): void
    {
        $mesaj = new SpvMesaj(['mesaj_id' => '1', 'cif' => '123', 'tip' => 'RECIPISA']);

        $this->assertTrue($this->controller()->testLipseste($mesaj));
    }

    public function test_mesajul_cu_cale_inexistenta_este_considerat_nedescarcat(): void
    {
        $mesaj = new SpvMesaj([
            'mesaj_id' => '2',
            'cif' => '123',
            'tip' => 'RECIPISA',
            'cale_fisier' => 'spv/downloads/fisier-inexistent.pdf',
        ]);

        $this->assertTrue($this->controller()->testLipseste($mesaj));
    }

    public function test_prezentarea_include_starea_descarcarii(): void
    {
        $mesaj = new SpvMesaj([
            'mesaj_id' => '900039926',
            'cif' => '15208744',
            'tip' => 'RECIPISA',
            'detalii' => 'recipisa pentru CIF 15208744',
            'ultima_eroare' => 'SPV indisponibil',
        ]);

        $prezentat = $this->controller()->testPrezinta($mesaj);

        $this->assertSame('900039926', $prezentat['id']);
        $this->assertSame('RECIPISA', $prezentat['tip']);
        $this->assertFalse($prezentat['descarcat']);
        $this->assertSame('SPV indisponibil', $prezentat['ultima_eroare']);
    }

    public function test_configurarea_descarcarii_automate_este_definita(): void
    {
        $this->assertTrue(config('anaf.spv.descarcare_automata'));
        $this->assertGreaterThan(0, config('anaf.spv.limita_descarcari'));
        $this->assertGreaterThan(0, config('anaf.spv.incercari_max'));
    }
}
