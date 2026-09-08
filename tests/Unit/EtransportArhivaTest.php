<?php

namespace Tests\Unit;

use App\Models\Company;
use App\Models\EtransportDeclaratie;
use App\Models\EtransportGestiune;
use App\Services\Anaf\Etransport\FormularTransportator;
use App\Services\Anaf\Etransport\Import\ImportArhiva;
use App\Support\ContextCompanie;
use Tests\TestCase;

/**
 * Arhiva zilnică a furnizorului: câte o ciornă pe fiecare factură, cu
 * destinația (magazinul) din distinta D01 — apoi formularul cu codurile UIT
 * pentru transportator, câte o foaie pe magazin.
 */
class EtransportArhivaTest extends TestCase
{
    protected $client;

    protected function setUp(): void
    {
        parent::setUp();

        $this->client = Company::create(['denumire' => 'PROBA ARHIVA SRL', 'cui' => '15196216']);
        ContextCompanie::fixeaza($this->client->id);
    }

    protected function tearDown(): void
    {
        EtransportDeclaratie::query()->toateCompaniile()->where('company_id', $this->client->id)->delete();
        EtransportGestiune::query()->toateCompaniile()->where('company_id', $this->client->id)->delete();
        ContextCompanie::elibereaza();
        $this->client->delete();

        parent::tearDown();
    }

    public function test_din_arhiva_iese_cate_o_ciorna_pe_factura_cu_destinatia_din_d01()
    {
        $rezultat = (new ImportArhiva())->importa($this->arhiva(), '15196216');

        // Si factura fara T02 primeste ciorna ei — fara linii, cu avertisment.
        $this->assertCount(3, $rezultat['ciorne']);
        $this->assertCount(1, $rezultat['avertismente']);
        $this->assertStringContainsString('fără linii', $rezultat['avertismente'][0]);

        // Codul de magazin nu e in gestiunile clientului: e propus spre stocare, o singura data.
        $this->assertCount(1, $rezultat['gestiuni_noi']);
        $this->assertSame('NEG0000548', $rezultat['gestiuni_noi'][0]['cod_furnizor']);

        $faraT02 = EtransportDeclaratie::where('referinta_interna', 'Factura 10053430')->first();

        $this->assertSame([], $faraT02->linii);
        $this->assertSame('10053430', $faraT02->documente[0]['numar']);
        // Data facturii vine atunci din antetul D01 („del 3/07/2026").
        $this->assertSame('2026-07-03', $faraT02->documente[0]['data']);
        $this->assertSame('BUCURESTI', $faraT02->loc_final['localitate']);

        $prima = EtransportDeclaratie::find($rezultat['ciorne'][0]['id']);

        $this->assertSame('ciorna', $prima->stare);
        $this->assertSame('Factura 10053419', $prima->referinta_interna);
        $this->assertSame(10, $prima->tip_operatiune);
        $this->assertSame('IT', $prima->partener_tara);
        $this->assertSame('TEDDY S.P.A.', $prima->partener_denumire);
        // Destinatia din D01: magazinul, codul lui, strada si orasul cu judetul dedus.
        $this->assertStringContainsString('MAGAZIN TERRAN', $prima->loc_final['magazin_denumire']);
        $this->assertSame('NEG0000548', $prima->loc_final['magazin_cod']);
        $this->assertStringContainsString('MAGHERU', $prima->loc_final['strada']);
        $this->assertSame('BUCURESTI', $prima->loc_final['localitate']);
        $this->assertSame(40, $prima->loc_final['cod_judet']);
        // Factura la documente, cu data ei.
        $this->assertSame('10053419', $prima->documente[0]['numar']);
        $this->assertSame('2026-07-03', $prima->documente[0]['data']);
        // Liniile au scopul si valoarea; PTF-ul implicit e Bors 2 - A3.
        $this->assertSame(101, $prima->linii[0]['scop_operatiune']);
        $this->assertSame(38, $prima->loc_start['cod_ptf']);
    }

    /**
     * Arhiva de retur („RESO"): in loc de T02 vine T01, lista pe articole, iar
     * numele fisierelor nu poarta numarul documentului — se ia dinauntru.
     */
    public function test_din_arhiva_de_retur_liniile_vin_din_lista_t01_si_numarul_din_fisier()
    {
        $t01 = implode("\n", [
            '     Sender.............: TEDDY S.P.A.',
            '                          Italy                                          Vat N: 00953910403',
            '     Customer...........: S.C. EMPORIO COM SRL',
            '     Documents..........:  10074615 of 02.09.2026',
            '     Item_________ Lot Description_of_clotMade In__________________ Taric____ ______________________________________________________________    Net_weight Quantity__ Val_Unit_price__ Price__________',
            '     SAB0066865001   1 Pants              BD   Bangladesh           61046200  Pantaloni,tute con bretelle,pantaloni che scendono sino al gin         5,282         38 EUR         2,44           92,54',
            '     SAB0066865001   2 Pants              BD   Bangladesh           61046200  Pantaloni,tute con bretelle,pantaloni che scendono sino al gin         0,834          6 EUR         2,44           14,61',
            '     SAB0076471001   1 Short Jacket       MM   Myanmar              62024010  Cappotti, giacconi, mantelli, anorak (comprese le giacche da s         2,144          4 EUR         9,74           38,94',
            '     Total gross weight.:    KG               10,000',
        ]);

        $d01 = implode("\n", [
            '    Document ....:  01 ACC SH CREDIT NOTE                             Doc. mittente:     8025000    del  2/09/2026',
            '    Number ......:   10074615     del  2/09/2026                      Doc. riferim.:  01 RCL SS     006\\025',
            '    Persona......:  0029818 000 S.C. EMPORIO COM SRL',
            '    Destinazione.:  0000004 000 TEDDY S.P.A.                          Rata 4 ........:',
            '    RIMINITRN       VIA CORIANO, 58                                   Sconto test. 1 :        34,36-',
            '                    47924      RIMINI RN  IT                          Sconto test. 2 :',
            '                                 Ref. Doc',
            '                                 From  S.C. EMPORIO COM SRL                     NEG0001474',
            '                                 To    TEDDY S.P.A.                             RIMINITRN',
        ]);

        $cale = tempnam(sys_get_temp_dir(), 'arh') . '.zip';
        $arhiva = new \ZipArchive();
        $arhiva->open($cale, \ZipArchive::CREATE);
        $arhiva->addFromString('T01_2_NEG0001474_2026_006_025_RCLSS.TXT', $t01);
        $arhiva->addFromString('D01_2_NEG0001474_2026_006_025_RCLSS.TXT', $d01);
        $arhiva->close();

        // Magazinul a mai primit o livrare: de acolo i se stie adresa.
        EtransportDeclaratie::create([
            'stare' => 'validata', 'tip_operatiune' => 10, 'referinta_interna' => 'Factura 10053419',
            'loc_start' => ['tip' => 'ptf', 'cod_ptf' => 38],
            'loc_final' => ['tip' => 'adresa', 'cod_judet' => 8, 'localitate' => 'BRASOV', 'strada' => 'ZAHARIA STANCU',
                'numar' => '1', 'magazin_cod' => 'NEG0001474', 'magazin_denumire' => '1474 Brasov Coresi'],
        ]);
        EtransportGestiune::create(['cod_furnizor' => 'NEG0001474', 'denumire' => 'Brasov Coresi']);

        $rezultat = (new ImportArhiva())->importa($cale, '15196216');
        unlink($cale);

        $this->assertCount(1, $rezultat['ciorne']);
        $this->assertSame([], $rezultat['avertismente']);
        $this->assertSame([], $rezultat['gestiuni_noi']);
        $this->assertSame('10074615', $rezultat['ciorne'][0]['factura']);
        $this->assertSame('Brasov Coresi', $rezultat['ciorne'][0]['magazin']);

        $ciorna = EtransportDeclaratie::find($rezultat['ciorne'][0]['id']);

        // Returul e livrare intracomunitara, cu traseul intors: de la magazin la frontiera.
        $this->assertSame('Retur 10074615', $ciorna->referinta_interna);
        $this->assertSame(20, $ciorna->tip_operatiune);
        $this->assertSame('adresa', $ciorna->loc_start['tip']);
        $this->assertSame('NEG0001474', $ciorna->loc_start['magazin_cod']);
        $this->assertSame('Brasov Coresi', $ciorna->loc_start['magazin_denumire']);
        $this->assertSame('BRASOV', $ciorna->loc_start['localitate']);
        $this->assertSame('ZAHARIA STANCU', $ciorna->loc_start['strada']);
        $this->assertSame(8, $ciorna->loc_start['cod_judet']);
        $this->assertSame(['tip' => 'ptf', 'cod_ptf' => 38], $ciorna->loc_final);
        $this->assertSame('loc_start', $ciorna->campul_magazinului);

        $this->assertSame('10074615', $ciorna->documente[0]['numar']);
        $this->assertSame('2026-09-02', $ciorna->documente[0]['data']);
        $this->assertSame('TEDDY S.P.A.', $ciorna->partener_denumire);

        // Cele doua loturi de pantaloni s-au adunat pe codul vamal; brutul e impartit din total.
        $this->assertCount(2, $ciorna->linii);
        $pantaloni = collect($ciorna->linii)->firstWhere('cod_tarifar', '61046200');
        // Prin JSON, 44.0 iese inapoi 44.
        $this->assertEquals(44, $pantaloni['cantitate']);
        $this->assertEquals(10, round(array_sum(array_column($ciorna->linii, 'greutate_bruta')), 3));
        $this->assertSame(101, $pantaloni['scop_operatiune']);

        // Un retur de la un magazin fara nicio declaratie anterioara: ciorna iese, cu avertisment.
        $cale = tempnam(sys_get_temp_dir(), 'arh') . '.zip';
        $arhiva = new \ZipArchive();
        $arhiva->open($cale, \ZipArchive::CREATE);
        $arhiva->addFromString('T01_2_NEG0009999_2026_006_026_RCLSS.TXT', str_replace('10074615', '10074616', $t01));
        $arhiva->addFromString('D01_2_NEG0009999_2026_006_026_RCLSS.TXT', str_replace(['10074615', 'NEG0001474'], ['10074616', 'NEG0009999'], $d01));
        $arhiva->close();

        $rezultat = (new ImportArhiva())->importa($cale, '15196216');
        unlink($cale);

        $this->assertCount(1, $rezultat['ciorne']);
        $this->assertCount(1, $rezultat['avertismente']);
        $this->assertStringContainsString('NEG0009999 nu are nicio declarație anterioară', $rezultat['avertismente'][0]);
        $this->assertSame('NEG0009999', $rezultat['gestiuni_noi'][0]['cod_furnizor']);

        $faraAdresa = EtransportDeclaratie::find($rezultat['ciorne'][0]['id']);
        $this->assertSame(['tip' => 'adresa', 'magazin_cod' => 'NEG0009999'], $faraAdresa->loc_start);
    }

    public function test_formularul_transportatorului_are_cate_o_foaie_pe_magazin()
    {
        $rezultat = (new ImportArhiva())->importa($this->arhiva(), '15196216');

        // Cu gestiunea stiuta, foile poarta prescurtarea ei, nu denumirea furnizorului.
        EtransportGestiune::create([
            'cod_furnizor' => 'NEG0000548',
            'denumire' => '548 Magheru',
            'cod' => '0548',
            'prescurtare' => 'Magheru',
        ]);

        foreach ($rezultat['ciorne'] as $pozitie => $ciorna) {
            EtransportDeclaratie::find($ciorna['id'])->update([
                'uit' => '3E3G8N2TARTF4A4' . $pozitie,
                'stare' => 'validata',
                'nr_vehicul' => 'BH18BPT',
                'transportator_denumire' => 'RUTILLI ADOLFO SRL',
                'transportator_cod' => '13569610',
                'data_transport' => '2026-07-04',
            ]);
        }

        $formular = (new FormularTransportator())->genereaza(array_column($rezultat['ciorne'], 'id'));

        $this->assertSame(3, $formular['foi']);
        $this->assertStringContainsString('TEDDY', $formular['nume']);

        // Fisierul e un XLSX intreg, cu foile si codurile in el.
        $cale = tempnam(sys_get_temp_dir(), 'frm') . '.xlsx';
        file_put_contents($cale, $formular['continut']);

        $registru = \PhpOffice\PhpSpreadsheet\IOFactory::load($cale);
        unlink($cale);

        $this->assertSame(3, $registru->getSheetCount());
        $this->assertSame('Magheru', $registru->getSheet(0)->getTitle());

        $foaie = $registru->getSheet(0);
        $continut = json_encode($foaie->toArray());

        $this->assertStringContainsString('BORS 2 - A3', $continut);
        $this->assertStringContainsString('BH18BPT', $continut);
        $this->assertStringContainsString('RUTILLI ADOLFO SRL', $continut);
        $this->assertStringContainsString('ITALIA', $continut);
        // json_encode scapa bara ca \/; se verifica bucatile pe rand.
        $this->assertStringContainsString('COD UIT for Invoice number 10053419', $continut);
        $this->assertStringContainsString('03.07.2026', $continut);
        $this->assertStringContainsString('3E3G8N2TARTF4A40', $continut);
    }

    /** O arhiva mica, in forma celei zilnice: T02 + D01 pe doua facturi. */
    protected function arhiva(): string
    {
        $t02 = implode("\n", [
            '     Sender.............: TEDDY S.P.A.',
            '                          Italy                                          Vat N: 00953910403',
            '     Doc number.........:  %NUMAR% of 03.07.2026',
            '     BD   Bangladesh                     61046200  Pantaloni,tute con bretelle              20,307         22,515        133  EUR           985,23',
        ]);

        $d01 = implode("\n", [
            '    Number ......:   %NUMAR%     del  3/07/2026',
            '    Persona......:  0029818 000 S.C. EMPORIO COM SRL',
            '    Destinazione.:  0029818 007 S.C. EMPORIO COM SRL MAGAZIN TERRAN',
            '    NEG0000548      BD GEN GH MAGHERU, NR 33, SECTOR 1,',
            '                    000000     BUCURESTI     RO',
        ]);

        $cale = tempnam(sys_get_temp_dir(), 'arh') . '.zip';
        $arhiva = new \ZipArchive();
        $arhiva->open($cale, \ZipArchive::CREATE);

        foreach (['10053419', '10053420'] as $factura) {
            $arhiva->addFromString(
                'T02_2_TEDDY_RIMINITRN_01FTISH_2026_' . $factura . '.TXT',
                str_replace('%NUMAR%', $factura, $t02)
            );
            $arhiva->addFromString(
                'D01_2_TEDDY_RIMINITRN_01FTISH_2026_' . $factura . '.TXT',
                str_replace('%NUMAR%', $factura, $d01)
            );
        }

        // O factura careia furnizorul nu i-a pus recapitulatia T02 in arhiva.
        $arhiva->addFromString(
            'D01_2_TEDDY_RIMINITRN_01FTISH_2026_10053430.TXT',
            str_replace('%NUMAR%', '10053430', $d01)
        );

        $arhiva->close();

        return $cale;
    }
}
