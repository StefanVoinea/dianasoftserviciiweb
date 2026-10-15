<?php

namespace Tests\Unit;

use App\Services\Anaf\Etransport\CoduriNcVechi;
use PHPUnit\Framework\TestCase;

/**
 * [2026-10-15] Codurile NC de dinainte de 2022, pe care furnizorul le scrie încă
 * și pe care INS le respinge, se aduc la cele de azi.
 */
class CoduriNcVechiTest extends TestCase
{
    public function test_hanoracul_de_bumbac_se_desparte_dupa_greutatea_pe_bucata()
    {
        // Pozitia 40 din expedierile pe septembrie.
        $this->assertSame('62013010', CoduriNcVechi::actual('62019200', 0.8));
        $this->assertSame('62013090', CoduriNcVechi::actual('62019200', 1.4));
        // Fara greutate pe bucata, cazul obisnuit: sub un kilogram.
        $this->assertSame('62013010', CoduriNcVechi::actual('62019200'));
    }

    public function test_codurile_fara_greutate_au_un_singur_inlocuitor()
    {
        $this->assertSame('62012000', CoduriNcVechi::actual('62019100', 2.0));
        $this->assertSame('62029000', CoduriNcVechi::actual('62029900'));
        $this->assertSame('62024090', CoduriNcVechi::actual('62029300', 1.2));
    }

    public function test_codul_de_azi_ramane_cum_e()
    {
        $this->assertSame('62013010', CoduriNcVechi::actual('62013010', 3.0));
        $this->assertSame('61046200', CoduriNcVechi::actual('61046200'));
    }
}
