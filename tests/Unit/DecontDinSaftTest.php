<?php

namespace Tests\Unit;

use App\Services\Anaf\Declaratii\D300\CoduriD300;
use App\Services\Anaf\Declaratii\D300\DecontDinSaft;
use App\Services\Anaf\Declaratii\D300\NomenclatorTva;
use App\Services\Anaf\Declaratii\D300\RanduriD300;
use App\Services\Anaf\Declaratii\D300\ReguliD300;
use App\Services\Anaf\Declaratii\DeclaratieException;
use ReflectionClass;
use Tests\TestCase;

/**
 * Decontul de TVA socotit din jurnalele SAF-T.
 *
 * Regulile sunt mutate mecanic din aplicatia ANAF D300 (vezi tools/d300); ce se
 * cerceteaza aici e citirea fisierului si starea purtata de la o linie la alta,
 * care sunt scrise de mana.
 *
 * Cifrele asteptate nu sunt scoase din felul in care ar trebui sa arate un
 * decont, ci din felul in care lucreaza aplicatia ANAF: decontul acesta se
 * compara cu al ei, pe acelasi fisier.
 */
class DecontDinSaftTest extends TestCase
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

    public function test_achizitia_taxabila_intra_in_randul_cinci(): void
    {
        $decont = $this->decont($this->tranzactie('C1', [
            $this->linie('371', 1000.00, 0.00, '300101', 1000.00, 190.00),
            $this->linie('4426', 190.00, 0.00),
        ]));

        // Codul 300101 (cota de 19%) duce baza si taxa in randul 5…
        $this->assertSame(1000.0, $decont['randuri']['RD5_BAZA']);
        $this->assertSame(190.0, $decont['randuri']['RD5_TVA']);

        // …de unde se copiaza in randul 20 si se aduna in totalul de la 19.
        $this->assertSame(1000.0, $decont['randuri']['RD20_BAZA']);
        $this->assertSame(190.0, $decont['randuri']['RD19_TVA']);

        $this->assertSame(2, $decont['linii']);
    }

    /**
     * Semnul il da partea notei contabile din care vine suma.
     *
     * Fiecare rand e orientat asa incat operatiunea lui obisnuita sa iasa
     * pozitiva: livrarile din credit, achizitiile din debit. O livrare cu cod de
     * livrare iese cu plus…
     */
    public function test_livrarea_cu_cod_de_livrare_iese_cu_plus(): void
    {
        $decont = $this->decont($this->tranzactie('V1', [
            $this->linie('707', 0.00, 1000.00, '310309', 1000.00, 210.00),
        ]));

        $this->assertSame(1000.0, $decont['randuri']['RD9_1_BAZA']);
        $this->assertSame(210.0, $decont['randuri']['RD9_1_TVA']);
    }

    /**
     * …iar aceeasi suma pusa pe partea cealalta iese tot cu plus.
     *
     * In socoteala, ea vine cu minus: un cod de achizitie pe o linie de credit
     * inseamna ori o stornare, ori informatia de taxa pusa pe linia partenerului
     * in loc de linia de baza. Decontul se depune insa cu sume pozitive, asa ca
     * semnul se lasa deoparte dupa ce s-a terminat socoteala — o data, ca
     * fereastra si fisierul sa arate acelasi lucru.
     */
    public function test_suma_de_pe_partea_cealalta_iese_tot_cu_plus(): void
    {
        $decont = $this->decont($this->tranzactie('V1', [
            $this->linie('707', 0.00, 1000.00, '300101', 1000.00, 190.00),
        ]));

        $this->assertSame(1000.0, $decont['randuri']['RD5_BAZA']);
        $this->assertSame(190.0, $decont['randuri']['RD5_TVA']);
    }

    /** Randurile fara TVA se aduna la citirea codului, nu a sumei taxei. */
    public function test_operatiunile_fara_taxa_se_aduna_la_codul_de_taxa(): void
    {
        $decont = $this->decont($this->tranzactie('V2', [
            $this->linie('707', 0.00, 5000.00, '310301'),
        ]));

        $this->assertSame(5000.0, $decont['randuri']['RD1_BAZA']);
        $this->assertSame(0.0, $decont['randuri']['RD5_BAZA']);
    }

    /**
     * Taxarea inversa se vede abia la sfarsitul tranzactiei.
     *
     * TVA-ul ei sta si in deductibila (4426), si in neexigibila (4428), fara sa
     * vina vreo suma de taxa pe linii. Pana nu se inchide tranzactia nu se stie
     * daca asa a fost, asa ca sumele se strang deoparte.
     */
    public function test_taxarea_inversa_intra_in_decont_la_inchiderea_tranzactiei(): void
    {
        $decont = $this->decont($this->tranzactie('T1', [
            $this->linie('4426', 210.00, 0.00, '301104', null, 0.00),
            $this->linie('4428', 0.00, 210.00, '000000', null, 0.00),
        ]));

        $this->assertSame(210.0, $decont['randuri']['RD24_TVA']);
        $this->assertSame(1000.0, $decont['randuri']['RD24_BAZA']);
    }

    /**
     * Steagurile de TVA neexigibila raman aprinse, ca la ANAF.
     *
     * Aplicatia ANAF stinge la sfarsitul liniei toate steagurile de cont, in
     * afara de „Is4428" si „Is35328"; la fel ramane si codul de taxa. Are toate
     * semnele unei scapari, dar decontul acesta se compara cu al lor, asa ca se
     * pastreaza intocmai. Testul e aici ca sa se vada daca ANAF indreapta.
     */
    public function test_steagul_de_tva_neexigibila_ramane_aprins_ca_la_anaf(): void
    {
        $decont = $this->decont(
            // Prima tranzactie aprinde steagul de neexigibila…
            $this->tranzactie('T1', [
                $this->linie('4428', 0.00, 210.00, '000000', null, 0.00),
            ])
            // …iar a doua, care n-are nicio linie de 4428, se poarta ca si cum
            // ar avea: asa ajunge in randul 24 desi singura ei conditie e 4426.
            . $this->tranzactie('T2', [
                $this->linie('4426', 210.00, 0.00, '301104', null, 0.00),
            ])
        );

        $this->assertSame(210.0, $decont['randuri']['RD24_TVA']);
    }

    /** Cine depune si pe ce perioada, luate din antet. */
    public function test_firma_si_perioada_se_iau_din_antet(): void
    {
        $decont = $this->decont($this->tranzactie('V1', [
            $this->linie('707', 0.00, 100.00, '310301'),
        ]));

        $this->assertSame('15208744', $decont['cif']);
        $this->assertSame('DIANA SOFT SRL', $decont['denumire']);

        // In schema D406, PeriodEnd si PeriodEndYear vin dupa data de sfarsit,
        // asa ca ele raman.
        $this->assertSame('6', $decont['luna']);
        $this->assertSame('2026', $decont['an']);
    }

    /** Fara elementele de perioada, luna si anul se scot din data de sfarsit. */
    public function test_perioada_se_scoate_si_din_data_de_sfarsit(): void
    {
        $saft = str_replace(
            "      <PeriodEnd>6</PeriodEnd>\n      <PeriodEndYear>2026</PeriodEndYear>\n",
            '',
            $this->saft($this->tranzactie('V1', [$this->linie('707', 0.00, 100.00, '310301')]), '', '')
        );

        $decont = $this->dinText($saft);

        $this->assertSame('06', $decont['luna']);
        $this->assertSame('2026', $decont['an']);
    }

    /**
     * Randul 39: TVA-ul ramas de plata din perioada trecuta, mai putin ce s-a
     * platit intre timp.
     */
    public function test_soldul_ramas_de_plata_scade_cu_platile_facute(): void
    {
        // Ordinea e cea din schema D406: suma platii vine inaintea taxei, iar
        // aplicatia ANAF pe ea se bizuie — la citirea tipului de taxa aduna
        // suma citita mai devreme.
        $plati = <<<'XML'
  <SourceDocuments>
    <Payments>
      <Payment>
        <PaymentLine>
          <AccountID>4423</AccountID>
          <PaymentLineAmount>
            <Amount>300.00</Amount>
          </PaymentLineAmount>
          <TaxInformation>
            <TaxType>301</TaxType>
          </TaxInformation>
        </PaymentLine>
      </Payment>
    </Payments>
  </SourceDocuments>
XML;

        $decont = $this->decont(
            $this->tranzactie('V1', [$this->linie('707', 0.00, 100.00, '310301')]),
            $this->contDeSold(500.00),
            $plati
        );

        $this->assertSame(200.0, $decont['randuri']['RD39_TVA']);
    }

    /**
     * Un SAF-T impartit pe sectiuni: decontul se face numai din cel cu jurnale.
     *
     * Fara ele n-ar iesi o eroare, ci un decont de zerouri — mai rau decat
     * niciun raspuns, pentru ca arata a declaratie gata de depus.
     */
    public function test_fara_jurnale_nu_se_face_decont(): void
    {
        $this->expectException(DeclaratieException::class);
        $this->expectExceptionMessage('nu conține jurnale');

        $this->decont('');
    }

    /**
     * Paza regulilor generate: ele se refac cu tools/d300/genereaza.php, iar
     * daca ANAF schimba ceva, numerele de aici se schimba si ele. Testul nu
     * spune ca noile reguli sunt gresite — spune ca s-au schimbat.
     */
    public function test_regulile_generate_sunt_intregi(): void
    {
        $multimi = (new ReflectionClass(CoduriD300::class))->getConstants();

        $this->assertCount(61, $multimi);

        $apartenente = array_sum(array_map('count', $multimi));

        $this->assertSame(833, $apartenente);
        $this->assertCount(103, ReguliD300::RANDURI);

        // Cotele din 2025 incoace: 19, 9, 5 si, de la august, 21 si 11.
        $this->assertArrayHasKey('300101', $multimi['COD1']);
        $this->assertArrayHasKey('300104', $multimi['COD1']);
    }

    /**
     * Un decont cu cifre se spune ca atare.
     *
     * Lamurirea merge cu decontul intotdeauna, nu numai cand e gol: altfel n-ar
     * fi de crezut tocmai cand spune ca totul e in regula.
     */
    public function test_decontul_cu_cifre_e_spus_ca_atare(): void
    {
        $decont = $this->decont($this->tranzactie('C1', [
            $this->linie('371', 1000.00, 0.00, '300101', 1000.00, 190.00),
        ]));

        $this->assertSame('bun', $decont['lamurire']['stare']);
        $this->assertStringContainsString('1 cu sumă de taxă', $decont['lamurire']['explicatie']);
    }

    /**
     * Pricina cea mai des intalnita pe fisierele adevarate: programul de
     * contabilitate scrie codurile TVA numai pe facturi.
     */
    public function test_codurile_ramase_pe_facturi_sunt_aratate(): void
    {
        $facturi = <<<'XML'
  <SourceDocuments>
    <PurchaseInvoices>
      <Invoice>
        <InvoiceLine>
          <TaxInformation>
            <TaxCode>300101</TaxCode>
          </TaxInformation>
        </InvoiceLine>
      </Invoice>
    </PurchaseInvoices>
  </SourceDocuments>
XML;

        $decont = $this->decont(
            $this->tranzactie('C1', [$this->linie('371', 1000.00, 0.00, '000000', 0.00, 0.00)]),
            '',
            $facturi
        );

        $this->assertSame('coduri_doar_pe_facturi', $decont['lamurire']['stare']);
        $this->assertStringContainsString('SourceDocuments', $decont['lamurire']['explicatie']);
        $this->assertNotNull($decont['lamurire']['de_facut']);
    }

    /** Coduri bune, dar fara nicio suma de taxa. */
    public function test_lipsa_sumelor_de_taxa_e_spusa(): void
    {
        $decont = $this->decont($this->tranzactie('C1', [
            $this->linie('371', 1000.00, 0.00, '300101', 1000.00, 0.00),
        ]));

        $this->assertSame('fara_sume_de_taxa', $decont['lamurire']['stare']);
    }

    /** Coduri care nu se regasesc in regulile decontului. */
    public function test_codurile_care_nu_duc_in_niciun_rand_sunt_aratate(): void
    {
        $decont = $this->decont($this->tranzactie('C1', [
            $this->linie('371', 1000.00, 0.00, '999999', 1000.00, 190.00),
        ]));

        $this->assertSame('coduri_fara_rand', $decont['lamurire']['stare']);
        $this->assertStringContainsString('999999', $decont['lamurire']['explicatie']);
    }

    /**
     * Soldul ramas din perioada trecuta nu face decontul sa para plin.
     *
     * Randul 39 se ia din soldul contului 4423 si e acolo si cand luna n-a avut
     * nicio operatiune. Socotit drept cifra a decontului, el ascundea tocmai
     * pricina pentru care decontul era gol.
     */
    public function test_soldul_din_perioada_trecuta_nu_ascunde_un_decont_gol(): void
    {
        $decont = $this->decont(
            $this->tranzactie('C1', [$this->linie('371', 1000.00, 0.00, '000000', 0.00, 0.00)]),
            $this->contDeSold(500.00)
        );

        $this->assertSame(500.0, $decont['randuri']['RD39_TVA']);
        $this->assertNotSame('bun', $decont['lamurire']['stare']);
    }

    /**
     * Randul de pe formular nu e numarul din numele atributului.
     *
     * Cand ANAF a adaugat randuri la mijlocul decontului, a pastrat numele
     * vechi ale atributelor si le-a dat celor noi nume din coada: randul 17 sta
     * in „R64", randul 19 — totalul taxei colectate — in „R17". Cine ar scrie
     * XML-ul dupa numarul randului ar pune cifrele in alte randuri decat cele
     * bune, si declaratia ar trece de validare tocmai asa gresita.
     *
     * Legatura e scoasa din generatorul de PDF al ANAF (vezi tools/d300).
     */
    public function test_randurile_isi_pastreaza_numele_din_xml(): void
    {
        $randuri = ReguliD300::RANDURI;
        $mapate = RanduriD300::RANDURI;

        // Randurile de la inceput au acelasi numar si in nume, si pe formular…
        $this->assertSame('R5_1', $mapate['RD5_BAZA']['atribut']);
        $this->assertSame('5', $mapate['RD5_BAZA']['rand']);

        // …iar cele adaugate mai tarziu, nu.
        $this->assertSame('17', $mapate['RD17_BAZA']['rand']);
        $this->assertSame('R64_1', $mapate['RD17_BAZA']['atribut']);

        $this->assertSame('24', $mapate['RD24_BAZA']['rand']);
        $this->assertSame('R22_1', $mapate['RD24_BAZA']['atribut']);

        $this->assertSame('38', $mapate['RD39_TVA']['rand']);
        $this->assertSame('R35_2', $mapate['RD39_TVA']['atribut']);

        // Fiecare rand mapat e un rand adevarat al decontului.
        foreach (array_keys($mapate) as $camp) {
            $this->assertContains($camp, $randuri, $camp . ' nu e un rând al decontului');
        }
    }

    /** Codurile de taxa se spun pe intelesul omului, din documentatia SAF-T. */
    public function test_codurile_de_taxa_au_denumire(): void
    {
        $this->assertStringContainsString('Livrări intracomunitare', (string) NomenclatorTva::descrie('310301'));
        $this->assertStringContainsString('19%', (string) NomenclatorTva::descrie('300101'));
        $this->assertNull(NomenclatorTva::descrie('999999'));
    }

    /** Decontul, socotit dintr-un SAF-T facut pe loc. */
    protected function decont(string $tranzactii, string $conturi = '', string $plati = ''): array
    {
        return $this->dinText($this->saft($tranzactii, $conturi, $plati));
    }

    protected function dinText(string $saft): array
    {
        $cale = tempnam(sys_get_temp_dir(), 'saft') . '.xml';
        file_put_contents($cale, $saft);
        $this->fisiere[] = $cale;

        return (new DecontDinSaft())->genereaza($cale);
    }

    protected function saft(string $tranzactii, string $conturi, string $plati): string
    {
        $jurnale = $tranzactii === '' ? '' : <<<XML
  <GeneralLedgerEntries>
    <Journal>
      <JournalID>V</JournalID>
$tranzactii
    </Journal>
  </GeneralLedgerEntries>
XML;

        return <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<AuditFile xmlns="mfp:anaf:dgti:d406:declaratie:v1">
  <Header>
    <SelectionCriteria>
      <SelectionStartDate>2026-06-01</SelectionStartDate>
      <SelectionEndDate>2026-06-30</SelectionEndDate>
      <PeriodEnd>6</PeriodEnd>
      <PeriodEndYear>2026</PeriodEndYear>
    </SelectionCriteria>
    <HeaderComment>L</HeaderComment>
    <Company>
      <RegistrationNumber>15208744</RegistrationNumber>
      <Name>DIANA SOFT SRL</Name>
    </Company>
  </Header>
  <MasterFiles>
    <GeneralLedgerAccounts>
$conturi
    </GeneralLedgerAccounts>
  </MasterFiles>
$plati
$jurnale
</AuditFile>
XML;
    }

    protected function contDeSold(float $creditor): string
    {
        return <<<XML
      <Account>
        <AccountID>4423</AccountID>
        <OpeningCreditBalance>$creditor</OpeningCreditBalance>
        <OpeningDebitBalance>0.00</OpeningDebitBalance>
      </Account>
XML;
    }

    protected function tranzactie(string $id, array $linii): string
    {
        $linii = implode("\n", $linii);

        return <<<XML
      <Transaction>
        <TransactionID>$id</TransactionID>
        <Period>6</Period>
        <PeriodYear>2026</PeriodYear>
$linii
      </Transaction>
XML;
    }

    /** O linie de nota contabila, cu sau fara informatie de taxa. */
    protected function linie(
        string $cont,
        float $debit,
        float $credit,
        ?string $cod = null,
        ?float $baza = null,
        ?float $taxa = null
    ): string {
        $taxe = '';

        if ($cod !== null) {
            $bazaXml = $baza === null ? '' : "\n            <TaxBase>$baza</TaxBase>";
            $taxaXml = $taxa === null ? '' : "\n            <TaxAmount><Amount>$taxa</Amount></TaxAmount>";

            $taxe = <<<XML

          <TaxInformation>
            <TaxType>300</TaxType>
            <TaxCode>$cod</TaxCode>$bazaXml$taxaXml
          </TaxInformation>
XML;
        }

        return <<<XML
        <TransactionLine>
          <AccountID>$cont</AccountID>
          <DebitAmount><Amount>$debit</Amount></DebitAmount>
          <CreditAmount><Amount>$credit</Amount></CreditAmount>$taxe
        </TransactionLine>
XML;
    }
}
