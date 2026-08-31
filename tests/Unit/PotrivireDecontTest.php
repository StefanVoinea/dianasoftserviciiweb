<?php

namespace Tests\Unit;

use App\Models\AnafDeclaratie;
use App\Services\Anaf\Declaratii\D300\PotrivireDecont;
use App\Support\ContextCompanie;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Decontul depus, pus fata in fata cu cel care iese din SAF-T.
 *
 * Amandoua declaratiile vorbesc despre aceeasi luna a aceleiasi firme: D300
 * spune cat TVA a iesit, D406 spune din ce. Daca nu se potrivesc, una din ele e
 * gresita — si mai bine se afla la validare decat peste doi ani, la control.
 */
class PotrivireDecontTest extends TestCase
{
    protected const COMPANIE = 992;

    /** @var array<int, string> */
    protected $fisiere = [];

    protected function setUp(): void
    {
        parent::setUp();

        ContextCompanie::fixeaza(self::COMPANIE);
    }

    protected function tearDown(): void
    {
        foreach ($this->fisiere as $cale) {
            Storage::delete($cale);
        }

        AnafDeclaratie::query()->toateCompaniile()->where('company_id', self::COMPANIE)->delete();
        ContextCompanie::elibereaza();

        parent::tearDown();
    }

    protected function potrivire(AnafDeclaratie $declaratie): array
    {
        return $this->app->make(PotrivireDecont::class)->pentru($declaratie);
    }

    /** Un SAF-T cu o achizitie de 1.000 lei si TVA de 190. */
    protected function saft(string $pas = 'validat'): AnafDeclaratie
    {
        $cale = 'declaratii/xml/proba-potrivire-saft.xml';
        Storage::put($cale, $this->xmlSaft());
        $this->fisiere[] = $cale;

        return $this->declaratie(['tip' => 'D406', 'cale_xml' => $cale, 'pas' => $pas]);
    }

    /** O D300 cu randurile date; ce lipseste e zero si pentru ANAF. */
    protected function d300(array $randuri, string $pas = 'validat'): AnafDeclaratie
    {
        $atribute = '';

        foreach ($randuri as $nume => $valoare) {
            $atribute .= ' ' . $nume . '="' . $valoare . '"';
        }

        $cale = 'declaratii/xml/proba-potrivire-d300.xml';
        Storage::put(
            $cale,
            '<?xml version="1.0" encoding="UTF-8"?>'
            . '<declaratie300 xmlns="mfp:anaf:dgti:d300:declaratie:v12"'
            . ' luna="6" an="2026" cui="15208744" den="DIANA SOFT SRL"' . $atribute . '/>'
        );
        $this->fisiere[] = $cale;

        return $this->declaratie(['tip' => 'D300', 'cale_xml' => $cale, 'pas' => $pas]);
    }

    protected function declaratie(array $atribute): AnafDeclaratie
    {
        return AnafDeclaratie::create(array_merge([
            'company_id' => self::COMPANIE,
            'nume_fisier' => 'proba.xml',
            'cui' => '15208744',
            'luna' => 6,
            'anul' => 2026,
        ], $atribute));
    }

    public function test_cand_cifrele_ies_la_fel_declaratiile_se_potrivesc(): void
    {
        $saft = $this->saft();

        // Randul 5 si perechea lui de la deducere, plus totalurile.
        $d300 = $this->d300([
            'R5_1' => 1000, 'R5_2' => 190,
            'R18_1' => 1000, 'R18_2' => 190,
            'R17_1' => 1000, 'R17_2' => 190,
            'R27_1' => 1000, 'R27_2' => 190,
            'R34_2' => 190, 'R37_2' => 190, 'R41_2' => 190,
        ]);

        $potrivire = $this->potrivire($d300);

        $this->assertSame('potrivit', $potrivire['stare'], print_r($potrivire['diferente'], true));
        $this->assertSame(0, $potrivire['numar']);
        $this->assertSame($saft->id, $potrivire['perechea']['id']);
    }

    /**
     * Un rand scris altfel in D300 se vede, cu amandoua cifrele si cu diferenta.
     */
    public function test_randul_care_nu_se_potriveste_se_arata_cu_amandoua_cifrele(): void
    {
        $this->saft();

        $d300 = $this->d300([
            'R5_1' => 900, 'R5_2' => 190,
            'R18_1' => 900, 'R18_2' => 190,
            'R17_1' => 900, 'R17_2' => 190,
            'R27_1' => 900, 'R27_2' => 190,
            'R34_2' => 190, 'R37_2' => 190, 'R41_2' => 190,
        ]);

        $potrivire = $this->potrivire($d300);

        $this->assertSame('diferente', $potrivire['stare']);

        $peAtribute = array_column($potrivire['diferente'], null, 'atribut');

        $this->assertArrayHasKey('R5_1', $peAtribute);
        $this->assertSame(1000, $peAtribute['R5_1']['din_saft']);
        $this->assertSame(900, $peAtribute['R5_1']['din_d300']);
        $this->assertSame(100, $peAtribute['R5_1']['diferenta']);
        $this->assertSame('5', $peAtribute['R5_1']['rand']);
    }

    /**
     * Un rand lipsa din D300 e tot zero — si tocmai asta se cauta.
     */
    public function test_randul_lipsa_din_d300_se_socoteste_zero(): void
    {
        $this->saft();

        $potrivire = $this->potrivire($this->d300([]));

        $this->assertSame('diferente', $potrivire['stare']);

        $peAtribute = array_column($potrivire['diferente'], null, 'atribut');

        $this->assertSame(0, $peAtribute['R5_2']['din_d300']);
        $this->assertSame(190, $peAtribute['R5_2']['din_saft']);
    }

    /** Comparatia se face in amandoua sensurile, cu acelasi raspuns. */
    public function test_potrivirea_se_face_si_dinspre_saft(): void
    {
        $saft = $this->saft();
        $d300 = $this->d300(['R5_1' => 900]);

        $dinsprSaft = $this->potrivire($saft);
        $dinspreD300 = $this->potrivire($d300);

        $this->assertSame('diferente', $dinsprSaft['stare']);
        $this->assertSame($dinspreD300['numar'], $dinsprSaft['numar']);
        $this->assertSame($d300->id, $dinsprSaft['perechea']['id']);
    }

    /** Fara declaratia pereche nu e nimic de comparat, si nu e o greseala. */
    public function test_fara_pereche_nu_e_o_greseala(): void
    {
        $potrivire = $this->potrivire($this->d300(['R5_1' => 1000]));

        $this->assertSame('fara_pereche', $potrivire['stare']);
        $this->assertNull($potrivire['perechea']);
        $this->assertSame([], $potrivire['diferente']);
    }

    /**
     * O declaratie respinsa la validare n-are ce spune despre alta.
     *
     * Cifrele ei n-au trecut nici macar de ANAF, deci nu pot fi masura pentru
     * nimeni.
     */
    public function test_declaratia_cu_erori_nu_se_ia_drept_pereche(): void
    {
        $this->saft('eroare_validare');

        $potrivire = $this->potrivire($this->d300(['R5_1' => 1000]));

        $this->assertSame('fara_pereche', $potrivire['stare']);
    }

    /** Perechea se cauta pe aceeasi firma si aceeasi luna, nu oriunde. */
    public function test_perechea_e_a_aceleiasi_luni(): void
    {
        $this->saft();

        $altaLuna = $this->d300(['R5_1' => 1000]);
        $altaLuna->update(['luna' => 5]);

        $this->assertSame('fara_pereche', $this->potrivire($altaLuna->fresh())['stare']);
    }

    /**
     * Raspunsul se scrie pe amandoua declaratiile, nu numai pe cea validata.
     *
     * Perechea a fost validata mai demult, cand cealalta inca nu era in
     * aplicatie: fara asta, ea ar ramane pe veci cu „n-are cu ce fi comparata",
     * desi tocmai i-a venit perechea.
     */
    public function test_raspunsul_se_scrie_pe_amandoua_declaratiile(): void
    {
        $saft = $this->saft();
        $d300 = $this->d300(['R5_1' => 900]);

        // Controllerul, cu potrivirea scoasa la vedere: aici se cantareste ce
        // ramane scris, nu drumul prin validatorul ANAF.
        $controller = new class extends \App\Http\Controllers\Api\DeclaratiiController {
            public function potriveste(AnafDeclaratie $declaratie): AnafDeclaratie
            {
                return $this->potrivesteDecontul($declaratie);
            }
        };

        $controller->potriveste($d300);

        $this->assertSame('diferente', $d300->fresh()->potrivire_stare);
        $this->assertSame('diferente', $saft->fresh()->potrivire_stare, 'și perechea trebuie însemnată');
        $this->assertNotNull($saft->fresh()->potrivire_la);
    }

    /** Cat timp declaratia n-a trecut de validare, nu se compara cu nimic. */
    public function test_declaratia_nevalidata_nu_se_compara(): void
    {
        $this->saft();

        $d300 = $this->d300(['R5_1' => 900], 'eroare_validare');

        $controller = new class extends \App\Http\Controllers\Api\DeclaratiiController {
            public function potriveste(AnafDeclaratie $declaratie): AnafDeclaratie
            {
                return $this->potrivesteDecontul($declaratie);
            }
        };

        $controller->potriveste($d300);

        $this->assertNull($d300->fresh()->potrivire_stare);
    }

    protected function xmlSaft(): string
    {
        return <<<'XML'
<?xml version="1.0" encoding="UTF-8"?>
<AuditFile xmlns="mfp:anaf:dgti:d406:declaratie:v1">
  <Header>
    <SelectionCriteria>
      <SelectionEndDate>2026-06-30</SelectionEndDate>
      <PeriodEnd>6</PeriodEnd>
      <PeriodEndYear>2026</PeriodEndYear>
    </SelectionCriteria>
    <Company>
      <RegistrationNumber>15208744</RegistrationNumber>
      <Name>DIANA SOFT SRL</Name>
    </Company>
  </Header>
  <GeneralLedgerEntries>
    <Journal>
      <Transaction>
        <TransactionID>C1</TransactionID>
        <Period>6</Period>
        <PeriodYear>2026</PeriodYear>
        <TransactionLine>
          <AccountID>371</AccountID>
          <DebitAmount><Amount>1000.00</Amount></DebitAmount>
          <CreditAmount><Amount>0.00</Amount></CreditAmount>
          <TaxInformation>
            <TaxType>300</TaxType>
            <TaxCode>300101</TaxCode>
            <TaxBase>1000.00</TaxBase>
            <TaxAmount><Amount>190.00</Amount></TaxAmount>
          </TaxInformation>
        </TransactionLine>
      </Transaction>
    </Journal>
  </GeneralLedgerEntries>
</AuditFile>
XML;
    }
}
