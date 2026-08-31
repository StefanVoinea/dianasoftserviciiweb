<?php

namespace Tests\Unit;

use App\Services\Anaf\Declaratii\DeclaratieException;
use App\Services\Anaf\Declaratii\TesteSaft;
use App\Services\Anaf\Declaratii\VerificareSaft;
use Tests\TestCase;

/**
 * Verificarea de consistenta a D406, cu unealta ANAF.
 *
 * DUKIntegrator spune daca declaratia e bine intocmita; unealta aceasta spune
 * daca cifrele din ea se potrivesc intre ele. Testele de aici nu cheama java:
 * ele pun in locul rularii chiar fisierele CSV pe care le-ar scrie unealta, ca
 * sa se vada ce se alege din ele.
 */
class ConsistentaSaftTest extends TestCase
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

    /** Antetul, asa cum il scrie unealta: delimitator „#", numere cu virgula. */
    protected const ANTET = "RegistrationNumber#Name#SelectionStartDate#SelectionEndDate#PeriodStart#PeriodStartYear#PeriodEnd#PeriodEndYear#HeaderComment#SegmentIndex#TotalSegmentsInsequence#NumberOfEntries#Totaldebit#Totalcredit#TransactionLine#DebitAmount#CreditAmount\n"
        . "15208744#DIANA SOFT SRL#2025-07-01#2025-07-31#7#2025#7#2025#L###2#1,190#1,190#2#0#1,190\n";

    protected const CAP_ERORI = "RegistrationNumber#Period#TransactionID#SystemID#RecordID#AccountID#CustomerID#SupplierID#DebitAmount#CreditAmount#TaxType#TaxCode#TaxPercentage#TaxBase#TaxAmount#Stare\n";

    /**
     * Serviciul, cu rularea inlocuita de fisierele pe care le-ar lasa unealta.
     *
     * Se intoarce ca „object", nu ca VerificareSaft: dosarele prin care a
     * trecut se citesc de pe clasa anonima, iar ele nu exista pe cea de baza.
     */
    protected function serviciu(array $config, ?string $antet, ?string $erori, string $iesire = ''): object
    {
        return new class($config, $antet, $erori, $iesire) extends VerificareSaft {
            /** @var array<int, string> */
            public $dosare = [];

            protected $antet;

            protected $erori;

            protected $iesire;

            public function __construct(array $config, ?string $antet, ?string $erori, string $iesire)
            {
                parent::__construct($config);

                $this->antet = $antet;
                $this->erori = $erori;
                $this->iesire = $iesire;
            }

            protected function ruleaza(string $jar, string $dosar): string
            {
                $this->dosare[] = $dosar;

                if ($this->antet !== null) {
                    file_put_contents($dosar . DIRECTORY_SEPARATOR . 'Header-saft-20260827_090000.csv', $this->antet);
                }

                if ($this->erori !== null) {
                    file_put_contents($dosar . DIRECTORY_SEPARATOR . 'Err-saft-20260827_090000.csv', $this->erori);
                }

                return $this->iesire;
            }
        };
    }

    protected function config(array $peste = []): array
    {
        return array_merge([
            'java' => 'java',
            'jar' => $this->fisier('TestSaftT.jar', 'jar de probă'),
            'timeout' => 60,
            'dosar_lucru' => sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'verificare_saft_test',
        ], $peste);
    }

    protected function fisier(string $nume, string $continut): string
    {
        $cale = sys_get_temp_dir() . DIRECTORY_SEPARATOR . $nume;
        file_put_contents($cale, $continut);
        $this->fisiere[] = $cale;

        return $cale;
    }

    /** Declaratia de verificat; continutul n-are insemnatate aici. */
    protected function declaratie(): string
    {
        return $this->fisier('saft-de-proba.xml', '<AuditFile/>');
    }

    public function test_liniile_gresite_se_citesc_din_csv_ul_uneltei(): void
    {
        $erori = self::CAP_ERORI
            . "15208744#202507#V1#1#1#4427#C1##0#190#300#000000#19#1,000#190#NOK-05\n"
            . "15208744#202507#V1#1#2#707###0#1,000#300#000000#19#1,000#190#NOK-01\n";

        $rezultat = $this->serviciu($this->config(), self::ANTET, $erori)->verifica($this->declaratie());

        $this->assertSame('erori', $rezultat['stare']);
        $this->assertSame(2, $rezultat['numar']);
        $this->assertFalse($rezultat['trunchiat']);

        // Cheile vin din capul fisierului, scrise cu litere mici.
        $this->assertSame('4427', $rezultat['erori'][0]['accountid']);
        $this->assertSame('NOK-05', $rezultat['erori'][0]['stare']);
        $this->assertSame('707', $rezultat['erori'][1]['accountid']);

        // Antetul spune a cui e declaratia si pe ce perioada.
        $this->assertSame('15208744', $rezultat['antet']['registrationnumber']);
        $this->assertSame('2025-07-31', $rezultat['antet']['selectionenddate']);

        // Numaratoarea pe teste arata de unde se apuca omul.
        $this->assertSame(['NOK-05' => 1, 'NOK-01' => 1], $rezultat['pe_teste']);
    }

    /** Fara fisier de erori, declaratia e curata: unealta il scrie doar cand are ce. */
    public function test_fara_fisier_de_erori_declaratia_e_curata(): void
    {
        $rezultat = $this->serviciu($this->config(), self::ANTET, null)->verifica($this->declaratie());

        $this->assertSame('curata', $rezultat['stare']);
        $this->assertSame(0, $rezultat['numar']);
        $this->assertSame([], $rezultat['erori']);
    }

    /**
     * Un SAF-T cu zeci de mii de linii marcate nu trece intreg prin baza de
     * date si prin pagina: se tin cate incap, iar numarul intreg ramane scris.
     */
    public function test_liniile_peste_masura_se_taie_dar_se_numara_toate(): void
    {
        $erori = self::CAP_ERORI;

        for ($i = 1; $i <= 5; $i++) {
            $erori .= "15208744#202507#V$i#1#1#707###0#1,000#300#000000#19#1,000#190#NOK-01\n";
        }

        $rezultat = $this->serviciu($this->config(['linii_pastrate' => 2]), self::ANTET, $erori)
            ->verifica($this->declaratie());

        $this->assertSame(5, $rezultat['numar']);
        $this->assertCount(2, $rezultat['erori']);
        $this->assertTrue($rezultat['trunchiat']);
    }

    /**
     * Unealta cade cu NumberFormatException cand o tranzactie n-are luna sau an,
     * fara sa spuna la care. Omului i se spune ce are de cautat, nu urma din java.
     */
    public function test_caderea_pe_perioada_lipsa_e_spusa_pe_inteles(): void
    {
        $serviciu = $this->serviciu(
            $this->config(),
            null,
            null,
            'Exception in thread "main" java.lang.NumberFormatException: For input string: ""'
        );

        $this->expectException(DeclaratieException::class);
        $this->expectExceptionMessage('fără lună sau fără an');

        $serviciu->verifica($this->declaratie());
    }

    /** Fara unealta instalata nu se verifica pe tacute: se spune ce lipseste. */
    public function test_lipsa_uneltei_este_spusa_limpede(): void
    {
        $serviciu = new VerificareSaft($this->config([
            'jar' => sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'lipsa_TestSaftT.jar',
        ]));

        $this->expectException(DeclaratieException::class);
        $this->expectExceptionMessage('nu a fost găsită');

        $serviciu->verifica($this->declaratie());
    }

    /**
     * Fiecare verificare isi are dosarul ei, si el nu ramane in urma.
     *
     * Unealta prelucreaza tot ce gaseste in dosarul primit si isi lasa acolo
     * fisierele CSV: cu un dosar comun, a doua verificare ar lua-o si pe prima.
     */
    public function test_dosarul_de_lucru_se_sterge_dupa_verificare(): void
    {
        $serviciu = $this->serviciu($this->config(), self::ANTET, null);

        $serviciu->verifica($this->declaratie());
        $serviciu->verifica($this->declaratie());

        $this->assertCount(2, $serviciu->dosare);
        $this->assertNotSame($serviciu->dosare[0], $serviciu->dosare[1]);

        foreach ($serviciu->dosare as $dosar) {
            $this->assertDirectoryDoesNotExist($dosar);
        }
    }

    /** Chiar si cand verificarea cade, dosarul de lucru nu ramane in urma. */
    public function test_dosarul_se_sterge_si_cand_verificarea_cade(): void
    {
        $serviciu = $this->serviciu($this->config(), null, null, 'NumberFormatException');

        try {
            $serviciu->verifica($this->declaratie());
        } catch (DeclaratieException $e) {
            // Motivul se cerceteaza in alt test; aici conteaza ce ramane in urma.
        }

        $this->assertDirectoryDoesNotExist($serviciu->dosare[0]);
    }

    /**
     * Cu unealta ANAF adevarata, pe o declaratie in care se stie ce e gresit.
     *
     * Testul acesta tine legatura cu ea: daca ANAF ii schimba fisierele de
     * iesire sau numele coloanelor, aici se vede intai. Acolo unde unealta nu e
     * instalata, se sare peste — restul testelor n-au nevoie de ea.
     */
    public function test_unealta_anaf_gaseste_liniile_gresite(): void
    {
        $jar = config('anaf.declaratii.saft.jar')
            ?: dirname((string) config('anaf.declaratii.duk.jar')) . DIRECTORY_SEPARATOR . 'TestSaftT.jar';

        if (!is_file($jar)) {
            $this->markTestSkipped('TestSaftT.jar nu e instalat pe mașina aceasta.');
        }

        $declaratie = $this->fisier('saft-adevarat.xml', $this->saftDeProba());

        $rezultat = (new VerificareSaft($this->config(['jar' => $jar])))->verifica($declaratie);

        $this->assertSame('erori', $rezultat['stare']);
        $this->assertSame('15208744', $rezultat['antet']['registrationnumber']);

        $stari = array_column($rezultat['erori'], 'stare', 'accountid');

        // Informația de taxă pusă pe linia contului de TVA colectată
        $this->assertSame('NOK-05', $stari['4427'] ?? null);

        // Cod TVA generic (000000) pe linia de venit
        $this->assertSame('NOK-01', $stari['707'] ?? null);
    }

    /**
     * O factura de 1.000 lei cu TVA 19%, inregistrata gresit in doua feluri.
     *
     * Liniile stau in „TransactionLine", iar fiecare nota isi poarta luna si
     * anul: fara ele unealta ANAF cade, in loc sa raporteze.
     */
    protected function saftDeProba(): string
    {
        return <<<'XML'
<?xml version="1.0" encoding="UTF-8"?>
<AuditFile xmlns="mfp:anaf:dgti:d406:declaratie:v1">
  <Header>
    <SelectionCriteria>
      <SelectionStartDate>2025-07-01</SelectionStartDate>
      <SelectionEndDate>2025-07-31</SelectionEndDate>
      <PeriodStart>7</PeriodStart>
      <PeriodStartYear>2025</PeriodStartYear>
      <PeriodEnd>7</PeriodEnd>
      <PeriodEndYear>2025</PeriodEndYear>
    </SelectionCriteria>
    <HeaderComment>L</HeaderComment>
    <Company>
      <RegistrationNumber>15208744</RegistrationNumber>
      <Name>DIANA SOFT SRL</Name>
    </Company>
  </Header>
  <GeneralLedgerEntries>
    <NumberOfEntries>2</NumberOfEntries>
    <TotalDebit>1190.00</TotalDebit>
    <TotalCredit>1190.00</TotalCredit>
    <Journal>
      <Transaction>
        <TransactionID>V1</TransactionID>
        <Period>7</Period>
        <PeriodYear>2025</PeriodYear>
        <SystemID>1</SystemID>
        <TransactionLine>
          <RecordID>1</RecordID>
          <AccountID>4427</AccountID>
          <CustomerID>C1</CustomerID>
          <DebitAmount><Amount>0.00</Amount></DebitAmount>
          <CreditAmount><Amount>190.00</Amount></CreditAmount>
          <TaxInformation>
            <TaxType>300</TaxType>
            <TaxCode>000000</TaxCode>
            <TaxPercentage>19</TaxPercentage>
            <TaxBase>1000.00</TaxBase>
            <TaxAmount><Amount>190.00</Amount></TaxAmount>
          </TaxInformation>
        </TransactionLine>
        <TransactionLine>
          <RecordID>2</RecordID>
          <AccountID>707</AccountID>
          <DebitAmount><Amount>0.00</Amount></DebitAmount>
          <CreditAmount><Amount>1000.00</Amount></CreditAmount>
          <TaxInformation>
            <TaxType>300</TaxType>
            <TaxCode>000000</TaxCode>
            <TaxPercentage>19</TaxPercentage>
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

    /** Codul testului ANAF ajunge la om ca explicatie, nu ca „NOK-07". */
    public function test_testele_anaf_sunt_talmacite(): void
    {
        $test = TesteSaft::descrie('NOK-11');

        $this->assertSame('NOK-11', $test['cod']);
        $this->assertStringContainsString('taxabil', mb_strtolower($test['titlu']));
        $this->assertNotSame('', $test['de_facut']);

        // Un cod nou, pe care ANAF l-ar adauga maine, nu se inventeaza.
        $necunoscut = TesteSaft::descrie('NOK-99');

        $this->assertSame('NOK-99', $necunoscut['cod']);
        $this->assertStringContainsString('ANAF', $necunoscut['verifica']);
    }
}
