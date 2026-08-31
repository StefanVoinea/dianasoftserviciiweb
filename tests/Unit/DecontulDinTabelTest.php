<?php

namespace Tests\Unit;

use App\Http\Controllers\Api\DeclaratiiController;
use App\Models\AnafDeclaratie;
use App\Services\Anaf\Declaratii\D300\DecontDinSaft;
use App\Support\ContextCompanie;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Butonul „Decont TVA" din tabelul de declaratii.
 *
 * Decontul nu se tine minte: se socoteste de fiecare data din fisierul
 * declaratiei. E o privire asupra lui, nu o declaratie care sa aiba nevoie de
 * istoric — depunerea vine abia dupa ce se scrie XML-ul D300.
 */
class DecontulDinTabelTest extends TestCase
{
    protected const COMPANIE = 994;

    /** @var string */
    protected $caleXml = 'declaratii/xml/proba-decont.xml';

    protected function setUp(): void
    {
        parent::setUp();

        ContextCompanie::fixeaza(self::COMPANIE);
    }

    protected function tearDown(): void
    {
        Storage::delete($this->caleXml);

        AnafDeclaratie::query()->toateCompaniile()->where('company_id', self::COMPANIE)->delete();
        ContextCompanie::elibereaza();

        parent::tearDown();
    }

    /** @return array raspunsul, asa cum pleaca spre tabel */
    protected function cere(AnafDeclaratie $declaratie): array
    {
        $controller = $this->app->make(DeclaratiiController::class);

        return $controller->decont($declaratie, new DecontDinSaft())->getData(true);
    }

    protected function declaratie(array $atribute = []): AnafDeclaratie
    {
        return AnafDeclaratie::create(array_merge([
            'company_id' => self::COMPANIE,
            'nume_fisier' => 'saft.xml',
            'tip' => 'D406',
            'cui' => '15208744',
            'luna' => 6,
            'anul' => 2026,
            'cale_xml' => $this->caleXml,
        ], $atribute));
    }

    public function test_decontul_vine_cu_randurile_numite_ca_pe_formular(): void
    {
        Storage::put($this->caleXml, $this->saft());

        $raspuns = $this->cere($this->declaratie());

        $this->assertTrue($raspuns['success']);
        $this->assertSame('DIANA SOFT SRL', $raspuns['data']['denumire']);
        $this->assertSame('bun', $raspuns['data']['lamurire']['stare']);

        $randuri = collect($raspuns['data']['randuri'])->keyBy('camp');

        // Codul 300101 duce achizitia in randul 5, cu baza si cu taxa.
        // Numarul trece prin json, de unde iese intreg cand n-are zecimale.
        $this->assertSame(1000.0, (float) $randuri['RD5_BAZA']['valoare']);
        // Randul de pe formular, si sub ce nume intra valoarea in XML-ul D300
        $this->assertSame('5', $randuri['RD5_BAZA']['rand']);
        $this->assertSame('R5_1', $randuri['RD5_BAZA']['atribut']);
        $this->assertSame('R5_2', $randuri['RD5_TVA']['atribut']);
        $this->assertStringContainsString(
            'Achizitii intracomunitare',
            $randuri['RD5_BAZA']['denumire']
        );

        // Randurile ramase la zero n-au ce cauta in tabel.
        $this->assertArrayNotHasKey('RD13_BAZA', $randuri);
    }

    /**
     * Cand decontul iese gol, omul primeste pricina, nu o pagina de zerouri.
     */
    public function test_decontul_gol_vine_cu_pricina_lui(): void
    {
        Storage::put($this->caleXml, $this->saft('000000', 0.00));

        $raspuns = $this->cere($this->declaratie());

        $this->assertTrue($raspuns['success']);
        $this->assertSame('numai_cod_generic', $raspuns['data']['lamurire']['stare']);
        $this->assertSame([], $raspuns['data']['randuri']);
    }

    /** Decontul se scoate numai din SAF-T; la restul, butonul nici nu apare. */
    public function test_din_alta_declaratie_nu_se_scoate_decont(): void
    {
        $raspuns = $this->cere($this->declaratie(['tip' => 'D394']));

        $this->assertFalse($raspuns['success']);
        $this->assertStringContainsString('D406', $raspuns['message']);
    }

    /** Fara fisier pe server nu se poate socoti nimic, si se spune limpede. */
    public function test_fara_fisier_se_spune_limpede(): void
    {
        $raspuns = $this->cere($this->declaratie(['cale_xml' => 'declaratii/xml/nu-exista.xml']));

        $this->assertFalse($raspuns['success']);
        $this->assertStringContainsString('nu mai are fișierul', $raspuns['message']);
    }

    /** O achizitie de 1.000 lei cu TVA 19%, cu codul cerut. */
    protected function saft(string $cod = '300101', float $taxa = 190.00): string
    {
        return <<<XML
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
            <TaxCode>$cod</TaxCode>
            <TaxBase>1000.00</TaxBase>
            <TaxAmount><Amount>$taxa</Amount></TaxAmount>
          </TaxInformation>
        </TransactionLine>
      </Transaction>
    </Journal>
  </GeneralLedgerEntries>
</AuditFile>
XML;
    }
}
