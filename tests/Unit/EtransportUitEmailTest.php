<?php

namespace Tests\Unit;

use App\Mail\EtransportUitEmail;
use App\Models\EtransportDeclaratie;
use Tests\TestCase;

/**
 * Emailul cu codul UIT: șoferul trebuie să primească codul limpede,
 * cu vehiculul și data transportului lângă el.
 */
class EtransportUitEmailTest extends TestCase
{
    public function test_emailul_poarta_uitul_vehiculul_si_data()
    {
        $email = new EtransportUitEmail(new EtransportDeclaratie([
            'uit' => '3E3G8N2TARTF4A48',
            'cif_declarant' => '15196216',
            'tip_operatiune' => 10,
            'partener_denumire' => 'TEDDY S.p.A.',
            'transportator_denumire' => 'RUTILLI ADOLFO S.R.L.',
            'nr_vehicul' => 'BH93BPT',
            'nr_remorca1' => 'CJ06RYL',
            'data_transport' => '2026-08-14',
            'linii' => [['cod_tarifar' => '61046200'], ['cod_tarifar' => '61046300']],
        ]));

        $email->build();

        $this->assertStringContainsString('3E3G8N2TARTF4A48', $email->subject);
        $this->assertStringContainsString('BH93BPT', $email->subject);

        $continut = $email->render();

        $this->assertStringContainsString('3E3G8N2TARTF4A48', $continut);
        $this->assertStringContainsString('BH93BPT + CJ06RYL', $continut);
        $this->assertStringContainsString('14.08.2026', $continut);
        $this->assertStringContainsString('TEDDY S.p.A.', $continut);
        $this->assertStringContainsString('2 feluri', $continut);
    }
}
