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
