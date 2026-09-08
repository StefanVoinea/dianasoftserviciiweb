<?php

namespace Tests\Unit;

use App\Models\EtransportCodVamal;
use App\Services\Anaf\Etransport\Import\ImportExcelDetalii;
use App\Services\Anaf\Etransport\Import\ImportFisiere;
use App\Services\Anaf\Etransport\Import\ImportRaportArticole;
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

    /**
     * Lista pe articole (T01), cea de la retururi: un rand pe articol si lot,
     * cu brutul doar la total — impartit pe linii dupa net.
     */
    public function test_lista_pe_articole_se_citeste_cu_brutul_impartit_dupa_net()
    {
        $cale = tempnam(sys_get_temp_dir(), 'etr');
        file_put_contents($cale, $this->listaArticole());

        $rezultat = (new ImportRaportArticole())->citeste($cale);
        unlink($cale);

        $this->assertCount(4, $rezultat['linii']);

        $prima = $rezultat['linii'][0];
        $this->assertSame('61046200', $prima['cod_tarifar']);
        $this->assertSame('BD', $prima['tara_origine']);
        $this->assertSame(5.282, $prima['greutate_neta']);
        $this->assertSame(38.0, $prima['cantitate']);
        $this->assertSame(92.54, $prima['valoare']);
        $this->assertStringContainsString('Pantaloni', $prima['denumire']);

        // Jacheta din Myanmar, cu pretul unitar 9,74 si valoarea 38,94.
        $this->assertSame('62024010', $rezultat['linii'][2]['cod_tarifar']);
        $this->assertSame('MM', $rezultat['linii'][2]['tara_origine']);
        $this->assertSame(38.94, $rezultat['linii'][2]['valoare']);

        // Brutul total (12,000 kg) se imparte dupa net (5,282 + 0,834 + 2,144 + 2,890 = 11,150)
        // si da inapoi exact totalul.
        $this->assertSame(round(12 * 5.282 / 11.15, 3), $prima['greutate_bruta']);
        $this->assertSame(12.0, round(array_sum(array_column($rezultat['linii'], 'greutate_bruta')), 3));

        $this->assertSame('TEDDY S.P.A.', $rezultat['antet']['partener_denumire']);
        $this->assertSame('00953910403', $rezultat['antet']['partener_cod']);
        $this->assertSame('IT', $rezultat['antet']['partener_tara']);
        $this->assertSame('10074615', $rezultat['antet']['document_numar']);
        $this->assertSame('2026-09-02', $rezultat['antet']['document_data']);
        $this->assertSame('EUR', $rezultat['antet']['valuta']);
    }

    /** Fisierul T01 se recunoaste dupa continut, nu dupa nume, si iese grupat pe cod vamal. */
    public function test_lista_pe_articole_se_recunoaste_dupa_continut_si_se_grupeaza()
    {
        $cale = tempnam(sys_get_temp_dir(), 'etr');
        file_put_contents($cale, $this->listaArticole());

        $rezultat = (new ImportFisiere())->importa([['nume' => 'oarecare.txt', 'cale' => $cale]]);
        unlink($cale);

        // Doua loturi de pantaloni pe 61046200 se aduna intr-o singura linie.
        $this->assertCount(3, $rezultat['linii']);

        $pantaloni = collect($rezultat['linii'])->firstWhere('cod_tarifar', '61046200');
        $this->assertSame(44.0, $pantaloni['cantitate']);
        $this->assertSame(round(5.282 + 0.834, 3), $pantaloni['greutate_neta']);
        $this->assertSame(round(92.54 + 14.61, 2), $pantaloni['valoare']);

        // Recapitulatia T02 merge in continuare pe parserul ei.
        file_put_contents($cale, $this->raportText());
        $rezultat = (new ImportFisiere())->importa([['nume' => 'oarecare.txt', 'cale' => $cale]]);
        unlink($cale);

        $this->assertSame('10038435', $rezultat['antet']['document_numar']);
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

    /** Lista pe articole T01, cum vine la nota de credit (retur), cu antetul ei. */
    protected function listaArticole(): string
    {
        return implode("\n", [
            '     Sender.............: TEDDY S.P.A.',
            '                          VIA CORIANO, 58',
            '                          Italy                                          Vat N: 00953910403',
            '     Customer...........: S.C. EMPORIO COM SRL',
            '                          Romania',
            '     Documents..........:  10074615 of 02.09.2026',
            '     Item_________ Lot Description_of_clotMade In__________________ Taric____ ______________________________________________________________    Net_weight Quantity__ Val_Unit_price__ Price__________',
            '     SAB0066865001   1 Pants              BD   Bangladesh           61046200  Pantaloni,tute con bretelle,pantaloni che scendono sino al gin         5,282         38 EUR         2,44           92,54',
            '     SAB0066865001   2 Pants              BD   Bangladesh           61046200  Pantaloni,tute con bretelle,pantaloni che scendono sino al gin         0,834          6 EUR         2,44           14,61',
            '     SAB0076471001   1 Short Jacket       MM   Myanmar              62024010  Cappotti, giacconi, mantelli, anorak (comprese le giacche da s         2,144          4 EUR         9,74           38,94',
            '     SAB0081325001   1 Pullover           MM   Myanmar              61103099  Maglioni,pullover,cardigan,gilè a maglia,di fibre sintetiche o         2,890         10 EUR         3,65           36,43',
            '                                                                                                                                            --------------  ---------                     ------------',
            '                                       Total...................:                                                                                    11,150         58 EUR                       182,52',
            '     Packing_______________________ N°________',
            '     Boxes                                     2',
            '     Total gross weight.:    KG               12,000',
            '     Total net weight...:    KG               11,150',
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
