<?php

namespace Tests\Unit;

use App\Models\Company;
use App\Models\EtransportDeclaratie;
use App\Models\EtransportGestiune;
use App\Services\Anaf\Etransport\Import\ImportArhiva;
use App\Support\ContextCompanie;
use Tests\TestCase;

/**
 * [2026-10-30] Magazinul cunoscut: numele din gestiune, adresa din ultima lui
 * declarație validată — nu cea tăiată, ori în italiană, din distinta D01.
 */
class GestiuneaMagazinuluiTest extends TestCase
{
    protected $client;

    protected function setUp(): void
    {
        parent::setUp();

        $this->client = Company::create(['denumire' => 'PROBA GESTIUNE SRL', 'cui' => '15196216']);
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

    /** Factura de la Mega Mall, cu adresa din distinta, în italiană și tăiată. */
    protected function dosar(): array
    {
        $fisiere = [
            'T02_2_TEDDY_RIMINITRN_01FTISH_2026_10095145.TXT' => implode("\n", [
                '     Sender.............: TEDDY S.P.A.',
                '                          Italy                                          Vat N: 00953910403',
                '     Doc number.........:  10095145 of 28.10.2026',
                '     BD   Bangladesh                     61046200  Pantaloni,tute con bretelle              20,307         22,515        133  EUR           985,23',
            ]),
            'D01_2_TEDDY_RIMINITRN_01FTISH_2026_10095145.TXT' => implode("\n", [
                '    Number ......:   10095145     del 28/10/2026',
                '    Persona......:  0029818 000 S.C. EMPORIO COM SRL',
                '    Destinazione.:  0029818 040 S.C. EMPORIO COM SRL',
                '    NEG0002534      CENTRO COMMERCIALE MEGA MALL BULEVA',
                '                    040055     BUCHARESTI     RO',
            ]),
        ];

        $iesire = [];

        foreach ($fisiere as $nume => $continut) {
            $cale = tempnam(sys_get_temp_dir(), 'gst');
            file_put_contents($cale, $continut);
            $iesire[] = ['nume' => $nume, 'cale' => $cale];
        }

        return $iesire;
    }

    public function test_magazinul_cunoscut_ia_numele_din_gestiune_si_adresa_validata()
    {
        EtransportGestiune::create([
            'company_id' => $this->client->id,
            'cod_furnizor' => 'NEG0002534',
            'cod' => '2534',
            'denumire' => '2534 MEGA MALL BUCURESTI',
            'prescurtare' => 'Mega Mall Bucuresti',
        ]);

        EtransportDeclaratie::create([
            'company_id' => $this->client->id,
            'stare' => 'validata',
            'tip_operatiune' => 10,
            'referinta_interna' => '10090000',
            'loc_final' => [
                'tip' => 'adresa', 'cod_judet' => 40, 'localitate' => 'BUCURESTI',
                'strada' => 'BDUL PIERRE DE COUBERTIN', 'numar' => '3-5',
                'alte_info' => 'Centrul Comercial Mega Mall Bucuresti',
                'magazin_cod' => 'NEG0002534', 'magazin_denumire' => 'MEGA MALL BUCURESTI',
            ],
        ]);

        $rezultat = (new ImportArhiva())->importaFisiere($this->dosar(), '15196216');

        $ciorna = EtransportDeclaratie::where('referinta_interna', 'Factura 10095145')->first();

        $this->assertNotNull($ciorna, json_encode($rezultat['avertismente']));
        $this->assertSame('2534 MEGA MALL BUCURESTI', $ciorna->loc_final['magazin_denumire']);
        $this->assertSame('NEG0002534', $ciorna->loc_final['magazin_cod']);
        $this->assertSame('BDUL PIERRE DE COUBERTIN', $ciorna->loc_final['strada']);
        $this->assertSame('3-5', $ciorna->loc_final['numar']);
        $this->assertSame('BUCURESTI', $ciorna->loc_final['localitate']);
        $this->assertSame(40, $ciorna->loc_final['cod_judet']);
    }

    /** Fără declarație validată, rămâne adresa din distintă. */
    public function test_fara_declaratie_validata_ramane_adresa_din_distinta()
    {
        EtransportGestiune::create([
            'company_id' => $this->client->id,
            'cod_furnizor' => 'NEG0002534',
            'denumire' => '2534 MEGA MALL BUCURESTI',
        ]);

        (new ImportArhiva())->importaFisiere($this->dosar(), '15196216');

        $ciorna = EtransportDeclaratie::where('referinta_interna', 'Factura 10095145')->first();

        $this->assertStringContainsString('MEGA MALL', $ciorna->loc_final['strada']);
    }

    /** Migrarea îndreaptă gestiunea salvată cu numele firmei, și ciornele ei. */
    public function test_migrarea_indreapta_gestiunea_si_ciornele()
    {
        EtransportGestiune::create([
            'company_id' => $this->client->id,
            'cod_furnizor' => 'NEG0002534',
            'cod' => '2534',
            'denumire' => 'S.C. EMPORIO COM SRL',
            'prescurtare' => 'MEGA MALL BUCURESTI',
        ]);

        $loc = ['tip' => 'adresa', 'cod_judet' => 40, 'localitate' => 'BUCURESTI', 'strada' => 'BDUL PIERRE DE COUBERTIN',
            'numar' => '3-5', 'magazin_cod' => 'NEG0002534', 'magazin_denumire' => 'MEGA MALL BUCURESTI'];

        $validata = EtransportDeclaratie::create([
            'company_id' => $this->client->id, 'stare' => 'validata', 'tip_operatiune' => 10,
            'referinta_interna' => 'V1', 'loc_final' => $loc,
        ]);
        $ciorna = EtransportDeclaratie::create([
            'company_id' => $this->client->id, 'stare' => 'ciorna', 'tip_operatiune' => 10,
            'referinta_interna' => 'C1',
            'loc_final' => ['tip' => 'adresa', 'strada' => 'CENTRO COMMERCIALE MEGA MALL BULEVA', 'localitate' => 'BUCHARESTI',
                'cod_judet' => 40, 'magazin_cod' => 'NEG0002534', 'magazin_denumire' => 'S.C. EMPORIO COM SRL'],
        ]);

        $migrare = require database_path('migrations/2026_10_30_120000_gestiunea_mega_mall_bucuresti.php');
        $migrare->up();

        $gestiune = EtransportGestiune::query()->toateCompaniile()
            ->where('company_id', $this->client->id)->where('cod_furnizor', 'NEG0002534')->first();

        $this->assertSame('2534 MEGA MALL BUCURESTI', $gestiune->denumire);
        $this->assertSame('MEGA MALL BUCURESTI', $gestiune->prescurtare);

        $ciorna->refresh();
        $this->assertSame('2534 MEGA MALL BUCURESTI', $ciorna->loc_final['magazin_denumire']);
        $this->assertSame('BDUL PIERRE DE COUBERTIN', $ciorna->loc_final['strada']);
        $this->assertSame('BUCURESTI', $ciorna->loc_final['localitate']);
        $this->assertSame('NEG0002534', $ciorna->loc_final['magazin_cod']);
        $this->assertSame('adresa', $ciorna->loc_final['tip']);

        // Cea validata ramane cum a primit-o ANAF.
        $this->assertSame('MEGA MALL BUCURESTI', $validata->fresh()->loc_final['magazin_denumire']);
    }
}
