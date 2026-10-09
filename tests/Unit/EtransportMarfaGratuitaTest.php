<?php

namespace Tests\Unit;

use App\Models\Company;
use App\Models\EtransportDeclaratie;
use App\Models\EtransportGestiune;
use App\Services\Anaf\Etransport\Import\ImportArhiva;
use App\Services\Anaf\Etransport\Import\ImportDdtGratuit;
use App\Support\ContextCompanie;
use Tests\TestCase;

/**
 * Marfa gratuită (DT1_*, „Omaggio") pleacă în același camion cu facturile și
 * intră în ciorna facturii aceluiași magazin, pe linii cu valoare zero.
 *
 * Până acum documentul ei era sărit cu „nu e T02, T01 sau D01", iar marfa
 * lipsea din declarație.
 */
class EtransportMarfaGratuitaTest extends TestCase
{
    protected $client;

    /** @var array<int, string> */
    protected $temporare = [];

    protected function setUp(): void
    {
        parent::setUp();

        $this->client = Company::create(['denumire' => 'PROBA GRATUIT SRL', 'cui' => '15196216']);
        ContextCompanie::fixeaza($this->client->id);
    }

    protected function tearDown(): void
    {
        foreach ($this->temporare as $cale) {
            @unlink($cale);
        }

        EtransportDeclaratie::query()->toateCompaniile()->where('company_id', $this->client->id)->delete();
        EtransportGestiune::query()->toateCompaniile()->where('company_id', $this->client->id)->delete();
        ContextCompanie::elibereaza();
        $this->client->delete();

        parent::tearDown();
    }

    /** Documentul de marfă gratuită, așa cum vine de la furnizor. */
    protected function dt1(string $numar, string $magazin, array $articole, string $neta, string $bruta): string
    {
        $randuri = [
            'DT_TEDDY_RIMINITRN_01_DDT_OV_0' . $numar . '_2026_0029818_03_07_2026__-G_0' . $numar,
            '                                                                                 S.C. EMPORIO COM SRL',
            '                                       D.O.T OF SHIPMENT       SHIPMDDT OV       RO15196216',
            '                                         ' . $numar . '/G   03.07.2026   0029818 007      OMG  Omaggio                                         1',
            '                                                                                                                               ' . $magazin,
            '                                                                                 MAGAZIN TERRANOVA',
        ];

        foreach ($articole as [$cod, $denumire, $cantitate]) {
            $randuri[] = '  VIS ' . $cod . ' 001       ' . str_pad($denumire, 46) . ' Pri  ' . str_pad($cantitate, 15, ' ', STR_PAD_LEFT);
        }

        return implode("\r\n", array_merge($randuri, [
            '                        FREE GOODS, FREE GIFT,',
            '                        GOODS NOT FOR SALE.',
            'T                                                                                    222,000',
            '          KG         KG',
            '       ' . $neta . '      ' . $bruta,
        ]));
    }

    /** O factură pentru Magheru (NEG0000548) din 3 iulie, plus fișierele date. */
    protected function dosar(array $altele): array
    {
        $t02 = implode("\n", [
            '     Sender.............: TEDDY S.P.A.',
            '                          Italy                                          Vat N: 00953910403',
            '     Doc number.........:  10053419 of 03.07.2026',
            '     BD   Bangladesh                     61046200  Pantaloni,tute con bretelle              20,307         22,515        133  EUR           985,23',
        ]);

        $d01 = implode("\n", [
            '    Number ......:   10053419     del  3/07/2026',
            '    Persona......:  0029818 000 S.C. EMPORIO COM SRL',
            '    Destinazione.:  0029818 007 S.C. EMPORIO COM SRL MAGAZIN TERRAN',
            '    NEG0000548      BD GEN GH MAGHERU, NR 33, SECTOR 1,',
            '                    000000     BUCURESTI     RO',
        ]);

        $fisiere = [];

        foreach (array_merge([
            'T02_2_TEDDY_RIMINITRN_01FTISH_2026_10053419.TXT' => $t02,
            'D01_2_TEDDY_RIMINITRN_01FTISH_2026_10053419.TXT' => $d01,
            'FT1_2_TEDDY_RIMINITRN_01FTISH_2026_10053419.TXT' => 'detaliul de articole, nu ne trebuie',
        ], $altele) as $nume => $continut) {
            $cale = tempnam(sys_get_temp_dir(), 'grt');
            file_put_contents($cale, $continut);
            $this->temporare[] = $cale;
            $fisiere[] = ['nume' => $nume, 'cale' => $cale];
        }

        return $fisiere;
    }

    public function test_marfa_gratuita_intra_in_ciorna_facturii_aceluiasi_magazin()
    {
        $dt1 = $this->dt1('127432', 'NEG0000548', [
            ['0000186', 'SCOTCH GIALLO-YELLOW TAPE', '7,000'],
            ['0000187', 'FOGLIO GIALLO PER TRASFERIMENTI', '200,000'],
            ['0000280', 'DOCUMENT POCKET', '15,000'],
        ], '4,312', '4,592');

        $rezultat = (new ImportArhiva())->importaFisiere(
            $this->dosar(['DT1_2_TEDDY_RIMINITRN_01DDTOV_2026_0127432.TXT' => $dt1]),
            '15196216'
        );

        // O singura ciorna: marfa gratuita n-a facut alta.
        $this->assertCount(1, $rezultat['ciorne']);

        $ciorna = EtransportDeclaratie::find($rezultat['ciorne'][0]['id']);

        $this->assertCount(4, $ciorna->linii);
        $gratuite = array_slice($ciorna->linii, 1);

        foreach ($gratuite as $linie) {
            $this->assertEquals(0, $linie['valoare']);
            $this->assertEquals(0, $linie['valoare_lei']);
            $this->assertSame(301, $linie['scop_operatiune'], 'scopul e „Gratuități"');
            $this->assertSame('', $linie['cod_tarifar'], 'codul vamal îl completează omul');
            $this->assertGreaterThan(0, $linie['greutate_bruta'], 'ANAF cere greutatea brută pozitivă');
        }

        $this->assertEquals(200, $gratuite[1]['cantitate'], '„200,000" e două sute, nu două');
        $this->assertStringContainsString('VIS 0000187', $gratuite[1]['denumire']);
        // Greutatea coletului se imparte pe articole, fara sa se piarda nimic.
        $this->assertEqualsWithDelta(4.592, array_sum(array_column($gratuite, 'greutate_bruta')), 0.001);
        $this->assertEqualsWithDelta(4.312, array_sum(array_column($gratuite, 'greutate_neta')), 0.001);

        // Documentul ei e al doilea pe declaratie, ca aviz de insotire.
        $this->assertCount(2, $ciorna->documente);
        $this->assertSame(30, $ciorna->documente[1]['tip']);
        $this->assertSame('127432', $ciorna->documente[1]['numar']);
        $this->assertSame('2026-07-03', $ciorna->documente[1]['data']);

        // Se spune ce s-a facut; detaliul FT1 nu mai face zgomot.
        $text = implode("\n", $rezultat['avertismente']);
        $this->assertStringContainsString('3 linii cu valoare zero adăugate la Factura 10053419', $text);
        $this->assertStringNotContainsString('FT1_', $text);
    }

    /** Importul făcut a doua oară nu dublează liniile. */
    public function test_a_doua_oara_marfa_gratuita_nu_se_dubleaza()
    {
        $dt1 = $this->dt1('127429', 'NEG0000548', [['0000988', 'Christmas paper backgrounds', '4,000']], '0,932', '2,052');
        $fisiere = $this->dosar(['DT1_2_TEDDY_RIMINITRN_01DDTOV_2026_0127429.TXT' => $dt1]);

        (new ImportArhiva())->importaFisiere($fisiere, '15196216');
        $rezultat = (new ImportArhiva())->importaFisiere($fisiere, '15196216');

        $ciorna = EtransportDeclaratie::where('referinta_interna', 'Factura 10053419')->first();

        $this->assertCount(2, $ciorna->linii);
        $this->assertStringContainsString('127429 era deja adusă', implode("\n", $rezultat['avertismente']));
    }

    /** Fără factură pentru magazin, marfa gratuită își face ciorna ei, cu valoare zero. */
    public function test_fara_factura_a_magazinului_marfa_isi_face_ciorna_ei()
    {
        $dt1 = $this->dt1('127440', 'NEG0009999', [['0000988', 'Christmas paper backgrounds', '6,000']], '1,398', '2,518');

        $rezultat = (new ImportArhiva())->importaFisiere(
            $this->dosar(['DT1_2_TEDDY_RIMINITRN_01DDTOV_2026_0127440.TXT' => $dt1]),
            '15196216'
        );

        $this->assertCount(2, $rezultat['ciorne']);

        $proprie = EtransportDeclaratie::where('referinta_interna', 'Marfa gratuită 127440')->first();

        $this->assertNotNull($proprie);
        $this->assertTrue($proprie->valoare_zero);
        $this->assertSame('NEG0009999', $proprie->loc_final['magazin_cod']);
        $this->assertCount(1, $proprie->linii);
        $this->assertSame(30, $proprie->documente[0]['tip']);
    }

    public function test_cititorul_recunoaste_doar_marfa_gratuita()
    {
        $this->assertTrue(ImportDdtGratuit::recunoaste('DT1_2_TEDDY.TXT', 'D.O.T OF SHIPMENT ... Omaggio'));
        $this->assertTrue(ImportDdtGratuit::recunoaste('altceva.txt', "D.O.T OF SHIPMENT\nFREE GOODS, FREE GIFT,"));
        $this->assertFalse(ImportDdtGratuit::recunoaste('DT1_2_TEDDY.TXT', 'D.O.T OF SHIPMENT, marfă vândută'));
        $this->assertFalse(ImportDdtGratuit::recunoaste('T02_2_TEDDY.TXT', 'Doc number: 10053419'));
    }
}
