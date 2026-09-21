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
        $this->assertSame('Retur 10074615', $rezultat['ciorne'][0]['factura']);
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

    /**
     * [2026-09-16] Importul unui dosar întreg: arhive, fișiere răzlețe și ce nu
     * se potrivește, toate deodată.
     *
     * Furnizorul nu trimite totul la fel — o zi vine ca arhivă, alta ca fișiere
     * puse în dosar. Ce nu e de citit nu se pierde în tăcere, ci se spune.
     */
    public function test_dosarul_se_importa_cu_arhive_si_fisiere_razlete_deodata()
    {
        $dosar = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'dosar' . uniqid();
        mkdir($dosar);

        // Fisierele unei facturi, razlete in dosar, ca si cum ar fi fost dezarhivate.
        $t02 = implode("\n", [
            '     Sender.............: TEDDY S.P.A.',
            '                          Italy                                          Vat N: 00953910403',
            '     Doc number.........:  10099001 of 03.07.2026',
            '     BD   Bangladesh                     61046200  Pantaloni                20,307         22,515        133  EUR           985,23',
        ]);
        file_put_contents($dosar . '/T02_2_TEDDY_2026_10099001.TXT', $t02);
        file_put_contents($dosar . '/D01_2_TEDDY_2026_10099001.TXT', implode("\n", [
            '    Number ......:   10099001     del  3/07/2026',
            '    Destinazione.:  0029818 007 S.C. EMPORIO COM SRL MAGAZIN TERRAN',
            '    NEG0000548      BD GEN GH MAGHERU, NR 33, SECTOR 1,',
            '                    000000     BUCURESTI     RO',
        ]));
        // Un fisier care nu ne priveste: trebuie spus, nu inghitit.
        file_put_contents($dosar . '/Factura 10099001.pdf', 'nu conteaza');

        $fisiere = [
            ['nume' => 'zilnica.zip', 'cale' => $this->arhiva()],
            ['nume' => 'T02_2_TEDDY_2026_10099001.TXT', 'cale' => $dosar . '/T02_2_TEDDY_2026_10099001.TXT'],
            ['nume' => 'D01_2_TEDDY_2026_10099001.TXT', 'cale' => $dosar . '/D01_2_TEDDY_2026_10099001.TXT'],
            ['nume' => 'Factura 10099001.pdf', 'cale' => $dosar . '/Factura 10099001.pdf'],
        ];

        $rezultat = (new ImportArhiva())->importaFisiere($fisiere, '15196216');

        foreach ($fisiere as $fisier) {
            if ($fisier['nume'] !== 'zilnica.zip') {
                @unlink($fisier['cale']);
            }
        }
        @rmdir($dosar);

        // Trei facturi din arhiva, plus cea razleata din dosar.
        $facturi = array_column($rezultat['ciorne'], 'factura');
        sort($facturi);

        $this->assertCount(4, $rezultat['ciorne']);
        $this->assertContains('Factura 10099001', $facturi);
        $this->assertContains('Factura 10053419', $facturi);

        $razleata = EtransportDeclaratie::where('referinta_interna', 'Factura 10099001')->first();
        $this->assertNotNull($razleata);
        $this->assertCount(1, $razleata->linii);
        $this->assertSame('BUCURESTI', $razleata->loc_final['localitate']);

        // PDF-ul e spus pe nume, ca omul sa stie ca n-a intrat.
        $spuse = implode(' | ', $rezultat['avertismente']);
        $this->assertStringContainsString('Factura 10099001.pdf', $spuse);
        $this->assertStringContainsString('fel de fișier necunoscut', $spuse);
    }

    /**
     * [2026-09-21] Arhiva încărcată din buton ajunge fără nume, și tot trebuie citită.
     *
     * Laravel pune fișierul încărcat într-un fișier trecător — „/tmp/phpA1B2C3"
     * —, care n-are nicio extensie. Felul fișierului se alegea numai după
     * extensie, așa că arhiva nu mai era recunoscută ca arhivă: importul se
     * oprea cu „Nu s-a găsit nimic de citit", deși înăuntru erau toate
     * rapoartele.
     */
    public function test_arhiva_fara_extensie_in_nume_se_citeste_dupa_continut()
    {
        $cale = tempnam(sys_get_temp_dir(), 'incarcat');

        $arhiva = new \ZipArchive();
        $arhiva->open($cale, \ZipArchive::OVERWRITE);
        $arhiva->addFromString('TARIC 01 ACC SH 10076193.dat', $this->articoleDat());
        $arhiva->addFromString('236203000002.txt', $this->distinctaDat());
        $arhiva->close();

        $this->magazinulCunoscut();

        // Exact cum cheama controlerul: calea fisierului trecator, fara extensie.
        $rezultat = (new ImportArhiva())->importa($cale, '15196216', null, true);

        @unlink($cale);

        $spuse = implode(' | ', $rezultat['avertismente']);
        $this->assertStringNotContainsString('fel de fișier necunoscut', $spuse);

        $ttn = EtransportDeclaratie::where('referinta_interna', 'Retur 10076193 (TTN)')->first();

        $this->assertNotNull($ttn, 'arhiva trebuia citită și fără extensie în nume');
        $this->assertCount(1, $ttn->linii);
    }

    /**
     * [2026-09-21] Aceleași fișiere, dar închise într-o arhivă.
     *
     * Furnizorul le trimite și așa: arhiva zilnică ține acum „TARIC ....dat" în
     * loc de „T01_...". Recunoașterea după conținut se făcea numai pentru
     * fișierele lăsate de-a dreptul în dosar, iar în arhivă se căuta tot după
     * nume: distincta intra, lista pe articole nu, și ieșea o ciornă fără nicio
     * linie de marfă.
     */
    public function test_taricurile_dintr_o_arhiva_se_recunosc_si_ele_din_continut()
    {
        $cale = tempnam(sys_get_temp_dir(), 'arh') . '.zip';
        $arhiva = new \ZipArchive();
        $arhiva->open($cale, \ZipArchive::CREATE);
        $arhiva->addFromString('TARIC 01 ACC SH 10076193.dat', $this->articoleDat());
        $arhiva->addFromString('236203000002.txt', $this->distinctaDat());
        $arhiva->close();

        $this->magazinulCunoscut();

        $rezultat = (new ImportArhiva())->importaFisiere(
            [['nume' => 'zilnica.zip', 'cale' => $cale]],
            '15196216',
            null,
            true
        );

        @unlink($cale);

        $spuse = implode(' | ', $rezultat['avertismente']);
        $this->assertStringNotContainsString('nu are fișiere', $spuse);

        $ttn = EtransportDeclaratie::where('referinta_interna', 'Retur 10076193 (TTN)')->first();

        $this->assertNotNull($ttn, 'ciorna trebuia făcută');
        $this->assertCount(1, $ttn->linii, 'ciorna a ieșit fără linii de marfă');
        $this->assertSame('61046200', $ttn->linii[0]['cod_tarifar']);
        $this->assertSame('BAIA MARE', $ttn->loc_start['localitate']);
    }

    /** Lista pe articole, așa cum o trimite furnizorul în „.dat". */
    protected function articoleDat(): string
    {
        return implode("
", [
            '     Sender.............: TEDDY S.P.A.',
            '                          Italy                                          Vat N: 00953910403',
            '     Documents..........:  10076193 of 07.09.2026',
            '     Item_________ Lot Description_of_clotMade In__________________ Taric____ ____    Net_weight Quantity__ Val_Unit_price__ Price__________',
            '     SAB0066865001   1 Pants              BD   Bangladesh           61046200  Pantaloni,tute con bretelle         9,730         70 EUR         2,45          171,48',
            '     Total gross weight.:    KG              11,500',
        ]);
    }

    /** Distinta ei, cu numărul facturii și magazinul. */
    protected function distinctaDat(): string
    {
        return implode("
", [
            '    DISTINTA CON LISTINI VENDITA',
            '    Document ....:  01 ACC SH CREDIT NOTE',
            '    Number ......:   10076193     del  7/09/2026',
            '    Destinazione.:  0000004 000 TEDDY S.P.A.',
            '                                 From  S.C. EMPORIO COM SRL                     NEG0002521',
        ]);
    }

    /** Magazinul are o declarație anterioară, din care își ia adresa. */
    protected function magazinulCunoscut(): void
    {
        EtransportDeclaratie::create([
            'stare' => 'validata', 'tip_operatiune' => 10, 'referinta_interna' => 'Livrare veche',
            'loc_start' => ['tip' => 'ptf', 'cod_ptf' => 38],
            'loc_final' => [
                'tip' => 'adresa', 'cod_judet' => 25, 'localitate' => 'BAIA MARE', 'strada' => 'BD UNIRII',
                'numar' => '1', 'magazin_cod' => 'NEG0002521', 'magazin_denumire' => '2521 Baia Mare',
            ],
        ]);
    }

    /**
     * [2026-09-16] Fișierele al căror nume nu spune nimic se recunosc din ce scrie în ele.
     *
     * Furnizorul le mai trimite și dezarhivate, botezate altfel și cu altă
     * extensie: „TARIC 01 ACC SH 10076193.dat" e lista pe articole, iar
     * „236203000002.txt" e distinta ei. Numele nu leagă una de alta — numărul
     * facturii din antet o face.
     */
    public function test_fisierele_botezate_altfel_se_recunosc_din_continut()
    {
        $dosar = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'dat' . uniqid();
        mkdir($dosar);

        $articole = implode("\n", [
            '     Sender.............: TEDDY S.P.A.',
            '                          Italy                                          Vat N: 00953910403',
            '     Documents..........:  10076193 of 07.09.2026',
            '     Item_________ Lot Description_of_clotMade In__________________ Taric____ ____    Net_weight Quantity__ Val_Unit_price__ Price__________',
            '     SAB0066865001   1 Pants              BD   Bangladesh           61046200  Pantaloni,tute con bretelle         9,730         70 EUR         2,45          171,48',
            '     Total gross weight.:    KG              11,500',
        ]);
        $distinta = implode("\n", [
            '    DISTINTA CON LISTINI VENDITA',
            '    Document ....:  01 ACC SH CREDIT NOTE',
            '    Number ......:   10076193     del  7/09/2026',
            '    Destinazione.:  0000004 000 TEDDY S.P.A.',
            '                                 From  S.C. EMPORIO COM SRL                     NEG0002521',
        ]);

        file_put_contents($dosar . '/TARIC 01 ACC SH 10076193.dat', $articole);
        file_put_contents($dosar . '/236203000002.txt', $distinta);

        // Magazinul are o declaratie anterioara, din care isi ia adresa.
        EtransportDeclaratie::create([
            'stare' => 'validata', 'tip_operatiune' => 10, 'referinta_interna' => 'Livrare veche',
            'loc_start' => ['tip' => 'ptf', 'cod_ptf' => 38],
            'loc_final' => [
                'tip' => 'adresa', 'cod_judet' => 25, 'localitate' => 'BAIA MARE', 'strada' => 'BD UNIRII',
                'numar' => '1', 'magazin_cod' => 'NEG0002521', 'magazin_denumire' => '2521 Baia Mare',
            ],
        ]);

        $fisiere = [
            ['nume' => 'TARIC 01 ACC SH 10076193.dat', 'cale' => $dosar . '/TARIC 01 ACC SH 10076193.dat'],
            ['nume' => '236203000002.txt', 'cale' => $dosar . '/236203000002.txt'],
        ];

        $rezultat = (new ImportArhiva())->importaFisiere($fisiere, '15196216', null, true);

        foreach ($fisiere as $fisier) {
            @unlink($fisier['cale']);
        }
        @rmdir($dosar);

        // Cele doua fisiere sunt ale aceleiasi facturi: un retur, deci doua declaratii.
        $this->assertCount(2, $rezultat['ciorne']);

        // Nu s-a plans de fisiere neintelese; doar de depozitul pe care nu-l stie de nicaieri.
        $spuse = implode(' | ', $rezultat['avertismente']);
        $this->assertStringNotContainsString('fel de fișier necunoscut', $spuse);
        $this->assertStringContainsString('adresa depozitului', $spuse);

        $ttn = EtransportDeclaratie::where('referinta_interna', 'Retur 10076193 (TTN)')->first();
        $this->assertNotNull($ttn);

        // Liniile vin din „.dat", iar magazinul de plecare din „.txt".
        $this->assertCount(1, $ttn->linii);
        $this->assertSame('61046200', $ttn->linii[0]['cod_tarifar']);
        $this->assertSame('BAIA MARE', $ttn->loc_start['localitate']);
        $this->assertSame('NEG0002521', $ttn->loc_start['magazin_cod']);
        $this->assertSame('10076193', $ttn->documente[0]['numar']);
        $this->assertSame('2026-09-07', $ttn->documente[0]['data']);
    }

    /**
     * [2026-09-16] Marfa de retur face două drumuri, deci două declarații.
     *
     * Din magazin la depozitul transportatorului, pe teritoriul național, apoi
     * de acolo afară din țară. Fiecare drum e un transport deosebit, cu UIT-ul
     * lui, chiar dacă marfa și factura sunt aceleași.
     */
    public function test_marfa_retur_face_doua_declaratii_pe_factura()
    {
        // Adresa depozitului se ia din ultima declaratie de transport national.
        EtransportDeclaratie::create([
            'stare' => 'validata', 'tip_operatiune' => 30, 'referinta_interna' => 'Retur vechi',
            'loc_start' => ['tip' => 'adresa', 'localitate' => 'IASI'],
            'loc_final' => [
                'tip' => 'adresa', 'cod_judet' => 5, 'localitate' => 'ORADEA', 'strada' => 'PETRE CARP',
                'numar' => '11', 'alte_info' => 'Depozitul transportatorului',
                'magazin_cod' => 'NEG0002360', 'magazin_denumire' => '2360 Moldova Mall Iasi',
            ],
        ]);

        $rezultat = (new ImportArhiva())->importa($this->arhiva(), '15196216', null, true);

        // Trei facturi in arhiva, cate doua declaratii fiecare.
        $this->assertCount(6, $rezultat['ciorne']);

        $ttn = EtransportDeclaratie::where('referinta_interna', 'Retur 10053419 (TTN)')->first();
        $lic = EtransportDeclaratie::where('referinta_interna', 'Retur 10053419 (LIC)')->first();

        $this->assertNotNull($ttn);
        $this->assertNotNull($lic);

        // Drumul dinauntrul tarii: din magazin la depozit, cu clientul drept partener.
        $this->assertSame(30, $ttn->tip_operatiune);
        $this->assertSame('RO', $ttn->partener_tara);
        $this->assertSame('15196216', $ttn->partener_cod);
        $this->assertSame('PROBA ARHIVA SRL', $ttn->partener_denumire);
        $this->assertSame('BUCURESTI', $ttn->loc_start['localitate']);
        $this->assertSame('ORADEA', $ttn->loc_final['localitate']);
        $this->assertSame('PETRE CARP', $ttn->loc_final['strada']);
        // Magazinul vechi nu se ia odata cu adresa depozitului.
        $this->assertArrayNotHasKey('magazin_cod', $ttn->loc_final);

        // Drumul afara din tara: din depozit la frontiera, cu furnizorul partener.
        $this->assertSame(20, $lic->tip_operatiune);
        $this->assertSame('IT', $lic->partener_tara);
        $this->assertSame('TEDDY S.P.A.', $lic->partener_denumire);
        $this->assertSame('ORADEA', $lic->loc_start['localitate']);
        $this->assertSame(38, $lic->loc_final['cod_ptf']);

        /*
         * Depozitul poarta numele magazinului, ca sa se vada a cui e declaratia,
         * dar nu si codul lui: altfel adresa depozitului ar trece drept adresa
         * magazinului la urmatoarea factura de la acelasi magazin.
         */
        $this->assertArrayNotHasKey('magazin_cod', $lic->loc_start);
        $this->assertSame($ttn->loc_start['magazin_denumire'], $lic->loc_start['magazin_denumire']);

        // Si a doua, si a treia factura a magazinului pleaca tot de la el, nu din depozit.
        foreach (EtransportDeclaratie::where('tip_operatiune', 30)->where('referinta_interna', 'like', 'Retur 10%')->get() as $drum) {
            $this->assertSame('BUCURESTI', $drum->loc_start['localitate'], $drum->referinta_interna);
        }

        // Aceeasi marfa si aceeasi factura pe amandoua.
        $this->assertSame($ttn->linii, $lic->linii);
        $this->assertSame('10053419', $lic->documente[0]['numar']);

        // Marfa care se intoarce nu mai merge spre comercializare: scopul e „Altele".
        $this->assertSame(9901, $ttn->linii[0]['scop_operatiune']);
        $this->assertSame(9901, $lic->linii[0]['scop_operatiune']);

        // Importat a doua oara, nu se dubleaza.
        $dinNou = (new ImportArhiva())->importa($this->arhiva(), '15196216', null, true);
        $this->assertSame([], $dinNou['ciorne']);
    }

    /** Fără nicio declarație de transport național, omul e trimis să pună adresa. */
    public function test_marfa_retur_fara_depozit_stiut_spune_ce_lipseste()
    {
        $rezultat = (new ImportArhiva())->importa($this->arhiva(), '15196216', null, true);

        $spuse = implode(' | ', $rezultat['avertismente']);

        $this->assertStringContainsString('depozitului transportatorului', $spuse);
        $this->assertCount(6, $rezultat['ciorne']);
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
