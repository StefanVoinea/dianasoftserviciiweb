<?php

namespace Tests\Unit;

use App\Services\Anaf\Declaratii\DeclaratieXml;
use Tests\TestCase;

/**
 * Identificarea intregita din numele fisierului, cand XML-ul n-a spus-o.
 *
 * PDF-urile de D406 scoase de alte programe nu tin CUI-ul unde il cauta
 * analiza, dar numele fisierului il poarta — la fel si perioada.
 */
class DeclaratieDinNumeTest extends TestCase
{
    public function test_cuiul_si_perioada_se_citesc_din_numele_fisierului()
    {
        $meta = (new DeclaratieXml())->completeazaDinNume(
            ['tip' => 'D406', 'cui' => null, 'luna' => null, 'anul' => null],
            'SILVIU_D406_47587115_31-07-2026_Inf-semnat.pdf'
        );

        $this->assertSame('47587115', $meta['cui']);
        $this->assertSame(7, $meta['luna']);
        $this->assertSame(2026, $meta['anul']);
    }

    public function test_ce_a_spus_xmlul_nu_se_rescrie()
    {
        $meta = (new DeclaratieXml())->completeazaDinNume(
            ['tip' => 'D406', 'cui' => '15196216', 'luna' => 6, 'anul' => 2026],
            'SILVIU_D406_47587115_31-07-2026_Inf-semnat.pdf'
        );

        $this->assertSame('15196216', $meta['cui']);
        $this->assertSame(6, $meta['luna']);
        $this->assertSame(2026, $meta['anul']);
    }

    public function test_un_nume_fara_tipar_lasa_totul_neatins()
    {
        $meta = (new DeclaratieXml())->completeazaDinNume(
            ['tip' => 'D300', 'cui' => null, 'luna' => null, 'anul' => null],
            'declaratie.pdf'
        );

        $this->assertNull($meta['cui']);
        $this->assertNull($meta['luna']);
    }
}
