<?php

namespace Tests\Unit;

use App\Models\AnafSocietate;
use App\Services\Anaf\Declaratii\D300\DecontXml;
use App\Services\Anaf\Declaratii\D300\RanduriD300;
use App\Services\Anaf\Declaratii\DeclaratieException;
use App\Services\Anaf\Declaratii\DukIntegrator;
use Tests\TestCase;

/**
 * Decontul scos din SAF-T, scris ca declaratie D300.
 *
 * Fisierul se incarca in formularul inteligent al ANAF („soft A"). Ca sa fie
 * primit acolo, el trebuie sa treaca mai intai de validatorul ANAF — iar
 * validatorul nu se uita numai la forma: cantareste si adunarile dintre randuri,
 * si numarul de evidenta a platii, cifra cu cifra.
 */
class DecontXmlTest extends TestCase
{
    /** @var array<int, string> */
    protected $fisiere = [];

    protected function tearDown(): void
    {
        foreach ($this->fisiere as $cale) {
            @unlink($cale);
        }

        parent::tearDown();
    }

    /** Firma cu datele de declaratie intregi. */
    protected function societate(array $peste = []): AnafSocietate
    {
        return new AnafSocietate(array_merge([
            'cif' => '15208744',
            'denumire' => 'DIANA SOFT SRL',
            'adresa' => 'Str. Bradului nr. 13, Năvodari',
            'banca' => 'Banca Transilvania',
            'cont' => 'RO49AAAA1B31007593840000',
            'caen' => '6201',
            'nume_declarant' => 'Voinea',
            'prenume_declarant' => 'Ștefan',
            'functie_declarant' => 'Administrator',
            'd300_tip_decont' => 'L',
        ], $peste));
    }

    /** Un decont cu o achizitie taxabila de 1.000 lei si TVA de 190. */
    protected function decont(array $randuri = [], array $peste = []): array
    {
        return array_merge([
            'cif' => '15208744',
            'denumire' => 'DIANA SOFT SRL',
            'luna' => '6',
            'an' => '2026',
            'linii' => 2,
            'randuri' => array_merge(['RD5_BAZA' => 1000.0, 'RD5_TVA' => 190.0], $randuri),
        ], $peste);
    }

    /** @return array<string, string> atributele declaratiei scrise */
    protected function atributele(string $xml): array
    {
        $declaratie = simplexml_load_string($xml);

        $atribute = [];

        foreach ($declaratie->attributes() as $nume => $valoare) {
            $atribute[$nume] = (string) $valoare;
        }

        return $atribute;
    }

    public function test_randurile_intra_sub_numele_lor_din_schema(): void
    {
        $xml = (new DecontXml())->scrie($this->decont(), $this->societate());
        $atribute = $this->atributele($xml);

        $this->assertSame('mfp:anaf:dgti:d300:declaratie:v12', (string) simplexml_load_string($xml)->getNamespaces()['']);

        // Randul 5 al formularului sta in „R5_1" (baza) si „R5_2" (taxa)…
        $this->assertSame('1000', $atribute['R5_1']);
        $this->assertSame('190', $atribute['R5_2']);

        // …iar randul 20, care e acelasi vazut din partea deducerii, in „R18".
        $this->assertSame('1000', $atribute['R18_1']);
        $this->assertSame('190', $atribute['R18_2']);
    }

    /**
     * Totalurile ies dupa formulele validatorului, nu dupa ale noastre.
     *
     * Randul 19 — totalul taxei colectate — sta in „R17"; cel al taxei
     * deductibile, randul 30, in „R27".
     */
    public function test_totalurile_se_socotesc_dupa_regulile_validatorului(): void
    {
        $atribute = $this->atributele((new DecontXml())->scrie($this->decont(), $this->societate()));

        $this->assertSame('1000', $atribute['R17_1']);
        $this->assertSame('190', $atribute['R17_2']);
        $this->assertSame('1000', $atribute['R27_1']);
        $this->assertSame('190', $atribute['R27_2']);

        // Suma de control aduna tot ce s-a scris.
        $scrise = array_filter($atribute, function ($valoare, $nume) {
            return $nume !== 'totalPlata_A' && preg_match('/^R\d/', $nume) === 1;
        }, ARRAY_FILTER_USE_BOTH);

        $this->assertSame((string) array_sum(array_map('intval', $scrise)), $atribute['totalPlata_A']);
    }

    /** Randurile ramase la zero nu se scriu: pentru ANAF, lipsa e tot zero. */
    public function test_randurile_goale_nu_se_scriu(): void
    {
        $atribute = $this->atributele((new DecontXml())->scrie($this->decont(), $this->societate()));

        $this->assertArrayNotHasKey('R13_1', $atribute);
        $this->assertArrayNotHasKey('R9_1', $atribute);
    }

    /**
     * Numarul de evidenta a platii nu e un numar liber.
     *
     * Validatorul il desface bucata cu bucata: pozitii fixe, perioada raportata,
     * scadenta de 25 ale lunii urmatoare si o cifra de control.
     */
    public function test_numarul_de_evidenta_e_alcatuit_dupa_regula(): void
    {
        $atribute = $this->atributele((new DecontXml())->scrie($this->decont(), $this->societate()));
        $nrEvid = $atribute['nr_evid'];

        $this->assertSame(23, strlen($nrEvid));
        $this->assertSame('10301010626250726000042', $nrEvid);

        // Pozitiile fixe, asa cum le cere regula R25
        $this->assertSame('10010000', substr($nrEvid, 0, 2) . substr($nrEvid, 5, 2) . substr($nrEvid, 17, 4));

        // Cifra de control e suma celorlalte
        $this->assertSame(
            (int) substr($nrEvid, 21, 2),
            array_sum(str_split(substr($nrEvid, 0, 21)))
        );
    }

    /** La decontul lunii decembrie, scadenta cade in ianuarie, anul urmator. */
    public function test_scadenta_din_decembrie_trece_in_anul_urmator(): void
    {
        $decont = $this->decont([], ['luna' => '12', 'an' => '2026']);
        $atribute = $this->atributele((new DecontXml())->scrie($decont, $this->societate()));

        $this->assertSame('250127', substr($atribute['nr_evid'], 11, 6));
    }

    /** Felul decontului schimba si codul impozitului din numarul de evidenta. */
    public function test_felul_decontului_schimba_codul_impozitului(): void
    {
        $decont = $this->decont([], ['luna' => '3']);
        $societate = $this->societate(['d300_tip_decont' => 'T']);

        $atribute = $this->atributele((new DecontXml())->scrie($decont, $societate));

        $this->assertSame('302', substr($atribute['nr_evid'], 2, 3));
    }

    /** Decontul se depune in lei intregi, iar totalurile ies din sumele rotunjite. */
    public function test_sumele_se_rotunjesc_la_leu(): void
    {
        $decont = $this->decont(['RD5_BAZA' => 1000.6, 'RD5_TVA' => 190.4]);
        $atribute = $this->atributele((new DecontXml())->scrie($decont, $this->societate()));

        $this->assertSame('1001', $atribute['R5_1']);
        $this->assertSame('190', $atribute['R5_2']);
        $this->assertSame('1001', $atribute['R17_1']);
    }

    /** Codul fiscal se curata: in SAF-T vine si cu „RO", si cu zerouri in fata. */
    public function test_codul_fiscal_se_curata(): void
    {
        $decont = $this->decont([], ['cif' => 'RO0014385411']);
        $atribute = $this->atributele((new DecontXml())->scrie($decont, $this->societate()));

        $this->assertSame('14385411', $atribute['cui']);
    }

    /** Fara datele din fisa firmei nu se scrie nimic, si se spune ce lipseste. */
    public function test_fara_datele_firmei_nu_se_scrie_declaratia(): void
    {
        $this->expectException(DeclaratieException::class);
        $this->expectExceptionMessage('Banca');

        (new DecontXml())->scrie($this->decont(), $this->societate(['banca' => null]));
    }

    public function test_numele_fisierului_poarta_firma_si_perioada(): void
    {
        $this->assertSame('D300_15208744_202606.xml', (new DecontXml())->numeFisier($this->decont()));
    }

    /**
     * Proba cea mare: declaratia trece de validatorul ANAF.
     *
     * Aici se vede daca tot lantul tine — randurile sub numele lor, totalurile
     * dupa formulele lui, numarul de evidenta cifra cu cifra. Acolo unde
     * DUKIntegrator nu e instalat, se sare peste.
     */
    public function test_declaratia_trece_de_validatorul_anaf(): void
    {
        $jar = config('anaf.declaratii.duk.jar');

        if (!is_file((string) $jar)) {
            $this->markTestSkipped('DUKIntegrator nu e instalat pe mașina aceasta.');
        }

        $xml = (new DecontXml())->scrie($this->decont(['RD39_TVA' => 500.0]), $this->societate());

        $cale = tempnam(sys_get_temp_dir(), 'd300') . '.xml';
        $pdf = $cale . '.pdf';
        file_put_contents($cale, $xml);
        $this->fisiere[] = $cale;
        $this->fisiere[] = $pdf;

        $rezultat = $this->app->make(DukIntegrator::class)->valideazaSiGenereazaPdf($cale, 'D300', $pdf);

        $this->assertTrue(
            $rezultat['valid'],
            'validatorul ANAF a respins declarația: ' . mb_substr((string) $rezultat['erori'], 0, 600)
        );

        $this->assertFileExists($pdf, 'din declarația validă iese și PDF-ul oficial');
    }

    /**
     * Paza legaturii cu schema: randul 19 sta in „R17", nu in „R19".
     *
     * Daca o regenerare ar strica legatura, cifrele ar intra pe alte randuri
     * decat cele bune, iar declaratia ar trece de validare tocmai asa gresita.
     */
    public function test_legatura_cu_schema_ramane_intreaga(): void
    {
        $this->assertSame('R17_1', RanduriD300::TOTALURI ? array_key_first(RanduriD300::TOTALURI) : null);
        $this->assertContains('R5_1', RanduriD300::TOTALURI['R17_1']);
        $this->assertSame(['R37_2', 'R40_2'], RanduriD300::DIFERENTE['R41_2']);
    }
}
