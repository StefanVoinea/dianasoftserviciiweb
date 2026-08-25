<?php

namespace Tests\Unit;

use App\Models\EtransportCodVamal;
use App\Services\Anaf\Etransport\Import\ImportExcelDetalii;
use App\Services\Anaf\Etransport\Import\ImportFisiere;
use App\Services\Anaf\Etransport\Import\ImportRaportText;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Tests\TestCase;

/**
 * Liniile declarației e-Transport, citite din fișierele furnizorului.
 *
 * Fișierele vin cum le scoate programul furnizorului: un raport text la
 * imprimantă, cu numere italiene, sau un Excel cu coloanele pe italiană.
 * Din amândouă trebuie să iasă aceleași linii: cod vamal, cantitate,
 * greutăți și valoare.
 */
class EtransportImportTest extends TestCase
{
    /** Raportul text: liniile se citesc, totalurile raman pe dinafara. */
    public function test_raportul_text_se_citeste_cu_numerele_italiene()
    {
        $cale = tempnam(sys_get_temp_dir(), 'etr');
        file_put_contents($cale, $this->raportText());

        $rezultat = (new ImportRaportText())->citeste($cale);
        unlink($cale);

        $this->assertCount(3, $rezultat['linii']);

        $prima = $rezultat['linii'][0];
        $this->assertSame('61046200', $prima['cod_tarifar']);
        $this->assertSame(20.307, $prima['greutate_neta']);
        $this->assertSame(22.515, $prima['greutate_bruta']);
        $this->assertSame(133.0, $prima['cantitate']);
        $this->assertSame(985.23, $prima['valoare']);
        $this->assertSame('BD', $prima['tara_origine']);

        // 1.595,96 pe italiana inseamna 1595.96
        $this->assertSame(1595.96, $rezultat['linii'][2]['valoare']);

        $this->assertSame('TEDDY S.P.A.', $rezultat['antet']['partener_denumire']);
        $this->assertSame('00953910403', $rezultat['antet']['partener_cod']);
        // Tara partenerului sta pe randul cu Vat N: „Italy ... Vat N: ...".
        $this->assertSame('IT', $rezultat['antet']['partener_tara']);
        $this->assertSame('10038435', $rezultat['antet']['document_numar']);
        $this->assertSame('2026-05-08', $rezultat['antet']['document_data']);
        $this->assertSame('EUR', $rezultat['antet']['valuta']);
    }

    /** Excelul cu detaliile facturii: coloanele se gasesc dupa nume. */
    public function test_excelul_cu_detalii_se_citeste_dupa_numele_coloanelor()
    {
        $cale = $this->excelDetalii();

        $rezultat = (new ImportExcelDetalii())->citeste($cale);
        unlink($cale);

        $this->assertCount(2, $rezultat['linii']);

        $prima = $rezultat['linii'][0];
        $this->assertSame('39269097', $prima['cod_tarifar']);
        $this->assertSame(23.0, $prima['cantitate']);
        $this->assertSame(326.6, $prima['greutate_neta']);
        $this->assertSame(3377.55, $prima['valoare']);
        $this->assertSame('IT', $prima['tara_origine']);
        // Excelul nu are kg brut pe linie; ramane de completat in formular.
        $this->assertNull($prima['greutate_bruta']);

        // Randul de total, fara cod vamal, nu devine linie.
        $this->assertSame('85285900', $rezultat['linii'][1]['cod_tarifar']);
    }

    /** Liniile cu acelasi cod vamal se aduna, iar denumirea vine din nomenclator. */
    public function test_liniile_se_grupeaza_pe_cod_vamal_cu_denumirea_din_nomenclator()
    {
        // Nomenclatorul adevarat ramane cum era: randul se scoate si se pune la loc.
        $existent = EtransportCodVamal::where('cod', '61046300')->first();
        EtransportCodVamal::where('cod', '61046300')->delete();
        EtransportCodVamal::create([
            'cod' => '61046300',
            'denumire' => 'Pantaloni si pantaloni scurti tricotati, din fibre sintetice, pentru femei',
        ]);

        $cale = tempnam(sys_get_temp_dir(), 'etr');
        file_put_contents($cale, $this->raportText());

        $rezultat = (new ImportFisiere())->importa([['nume' => 'raport.txt', 'cale' => $cale]]);
        unlink($cale);

        EtransportCodVamal::where('cod', '61046300')->delete();
        if ($existent) {
            EtransportCodVamal::create($existent->only(['cod', 'denumire', 'denumire_scurta']));
        }

        // 61046300 apare la Bangladesh si la Cambodgia: o singura linie, adunata.
        $this->assertCount(2, $rezultat['linii']);

        $grupata = collect($rezultat['linii'])->firstWhere('cod_tarifar', '61046300');
        $this->assertSame(104.0, $grupata['cantitate']);
        $this->assertSame(round(22.36 + 3.096, 3), $grupata['greutate_neta']);
        $this->assertSame(round(807.73 + 1595.96, 2), $grupata['valoare']);
        $this->assertStringContainsString('Pantaloni si pantaloni scurti', $grupata['denumire']);
    }

    protected function raportText(): string
    {
        return implode("\n", [
            '     Sender.............: TEDDY S.P.A.',
            '                          VIA CORIANO, 58',
            '                          Italy                                          Vat N: 00953910403',
            '     Receiver...........: S.C. EMPORIO COM SRL',
            '     Doc number.........:  10038435 of 08.05.2026',
            '     Made In____________________________ _________ Taric_____________________   Net_weight     Gross_weig  Quantity__ Val. Price__________',
            '     BD   Bangladesh                     61046200  Pantaloni,tute con bretelle,pantaloni che scendono        20,307         22,515        133  EUR           985,23',
            '     BD   Bangladesh                     61046300  Pantaloni,tute con bretelle,pantaloni che scendono        22,360         24,791         86  EUR           807,73',
            '                                                                                                      -------------- -------------- ----------          ------------',
            '                                       Total Made IN..........:   Bangladesh                                42,667         47,306        219  EUR         1.792,96',
            '     KH   Cambodia                       61046300  Magliette,T-shirt e camiciole, a maglia, di cotone         3,096          3,433         18  EUR         1.595,96',
            '                                       Total ..................:                                             45,763         50,739        237  EUR         3.388,92',
        ]);
    }

    protected function excelDetalii(): string
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->fromArray([
            ['Articolo', 'Articolo CPA(CAD)', 'Quantità', 'Descr.Articolo', 'ID dogan.', 'M.In', 'Composizione', 'Kg uni', 'Tot.Kg', 'Prz.Netto', 'Tot.Netto'],
            ['ARR0008207007', 'PS-L38', 23, 'Hanger TRN Jeans', '39269097', 'IT', 'Plastic', 14.2, 326.6, 146.85, 3377.55],
            ['ARR0007328007', 'TVL-RTL', 4, 'ledwall cabinet', '85285900', 'CN', 'Plastic', 9.5, 38, 278.16, 1112.64],
            ['', '', '', '', '', '', '', '', 364.6, '', 4490.19],
        ], null, 'A1');

        $cale = tempnam(sys_get_temp_dir(), 'etr') . '.xlsx';
        (new Xlsx($spreadsheet))->save($cale);

        return $cale;
    }
}
