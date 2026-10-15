<?php

namespace Tests\Unit;

use App\Http\Controllers\Api\EtransportDeclaratiiController;
use App\Models\EtransportDeclaratie;
use App\Services\Anaf\Etransport\DeclaratieXml;
use App\Services\Anaf\Etransport\EtransportClient;
use App\Services\Anaf\Etransport\Import\ImportFacturaPdf;
use Tests\TestCase;

/**
 * [2026-10-14] Factura PDF a furnizorului, pentru marfa venită prin curier, și
 * bifa „doar pentru Intrastat" care o ține departe de e-Transport.
 */
class EtransportFacturaPdfTest extends TestCase
{
    /** Textul unei facturi, cu spațiile „non-breaking" pe care le scoate PDF-ul. */
    protected function text(): string
    {
        $randuri = [
            'TEDDY S.p.a.',
            '  INVOICE                           FTI MV',
            '   10076871     08.09.2026   0029818 028',
            ' S.C. EMPORIO COM SRL',
            ' NEG0001628',
            ' GEN 0000143 001 A 5   SERVIZIO GARANZIA 5ANNI KASKO PALMARE          Pri              3,000         190,00                  570,00',
            ' ITC 0000917 001 1     KIT PATHFINDER 6140/BATTERIA/POLSIERA/CLEANIN  NR               1,000       1.700,00                1.700,00',
            ' ITC 0000917 001 1     KIT PATHFINDER 6140/BATTERIA/POLSIERA/CLEANIN  NR               1,000       1.700,00                1.700,00',
            ' ITC 0001109 001 1 1   MEMOR 17 PROTECTIVE BOOT                       NR               3,000          11,00                   33,00',
            ' EUR',
            '         Kg           Kg',
            '     12,000       15,000',
        ];

        return str_replace(' ', "\u{00A0}", implode("\n", $randuri));
    }

    public function test_factura_se_citeste_si_serviciul_ramane_deoparte()
    {
        $this->assertTrue(ImportFacturaPdf::recunoaste($this->text()));

        $factura = (new ImportFacturaPdf())->citeste($this->text());

        $this->assertSame('10076871', $factura['numar']);
        $this->assertSame('2026-09-08', $factura['data']);
        $this->assertSame('NEG0001628', $factura['magazin_cod']);
        $this->assertSame('EUR', $factura['valuta']);

        // Cele doua randuri ale aceluiasi articol se aduna; garantia nu e marfa.
        $this->assertCount(2, $factura['linii']);
        $this->assertEquals(2, $factura['linii'][0]['cantitate']);
        $this->assertEquals(3400, $factura['linii'][0]['valoare']);
        $this->assertCount(1, $factura['servicii']);
        $this->assertStringContainsString('GARANZIA', $factura['servicii'][0]);

        // Greutatea coletului se imparte dupa valoare, fara sa se piarda nimic.
        $this->assertEqualsWithDelta(12, array_sum(array_column($factura['linii'], 'greutate_neta')), 0.02);
        $this->assertEqualsWithDelta(15, array_sum(array_column($factura['linii'], 'greutate_bruta')), 0.02);

        foreach ($factura['linii'] as $linie) {
            $this->assertSame('', $linie['cod_tarifar'], 'factura nu are cod vamal');
            $this->assertSame(101, $linie['scop_operatiune']);
        }
    }

    public function test_un_pdf_oarecare_nu_e_luat_drept_factura()
    {
        $this->assertFalse(ImportFacturaPdf::recunoaste('Extras de cont BCR, septembrie 2026'));
        $this->assertFalse(ImportFacturaPdf::recunoaste("INVOICE\n 10076871 08.09.2026\n de la alt furnizor"));
    }

    /** Declarația „doar pentru Intrastat" nu pleacă la ANAF, nici apăsând butonul. */
    public function test_declaratia_doar_pentru_intrastat_nu_se_depune()
    {
        $declaratie = new EtransportDeclaratie(['stare' => 'ciorna', 'doar_intrastat' => true]);

        $client = $this->createMock(EtransportClient::class);
        $client->expects($this->never())->method('upload');

        $raspuns = (new EtransportDeclaratiiController())->depune(
            $declaratie,
            $this->createMock(DeclaratieXml::class),
            $client
        );

        $this->assertSame(422, $raspuns->status());
        $this->assertStringContainsString('doar pentru Intrastat', $raspuns->getData(true)['message']);
    }
}
