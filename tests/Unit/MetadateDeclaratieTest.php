<?php

namespace Tests\Unit;

use App\Services\Anaf\Declaratii\DeclaratieXml;
use Tests\TestCase;

/**
 * Citirea CUI-ului si a denumirii din XML.
 *
 * Nu toate declaratiile tin identificarea pe elementul radacina: D112 o tine pe
 * <angajator>, impreuna cu luna si anul. Numele atributelor sunt cele folosite
 * de validatoarele DUKIntegrator.
 */
class MetadateDeclaratieTest extends TestCase
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

    protected function analizeaza(string $xml): array
    {
        $cale = tempnam(sys_get_temp_dir(), 'dec') . '.xml';
        file_put_contents($cale, $xml);
        $this->fisiere[] = $cale;

        return (new DeclaratieXml())->analizeaza($cale);
    }

    /** D112 tine identificarea pe prima sectiune, nu pe radacina. */
    public function test_d112_citeste_cui_denumire_si_perioada_din_sectiunea_angajator(): void
    {
        $meta = $this->analizeaza(<<<'XML'
<?xml version="1.0" encoding="UTF-8"?>
<declaratieUnica xmlns="mfp:anaf:dgti:declaratie_unica:declaratie:v6">
  <angajator luna_r="6" an_r="2026" d_rec="1" cif="15208744" cifS="0" den="DIANA SOFT SRL" caen="6201">
    <angajatorA totalPlata_A="1234"/>
  </angajator>
  <asigurat B_cnp="1900101410011" den_b="POPESCU ION"/>
</declaratieUnica>
XML);

        $this->assertSame('D112', $meta['tip']);
        $this->assertSame('15208744', $meta['cui']);
        $this->assertSame('DIANA SOFT SRL', $meta['den_firma']);
        $this->assertSame(6, $meta['luna']);
        $this->assertSame(2026, $meta['anul']);
        $this->assertTrue($meta['rectificativa']);
    }

    /** Denumirea din D394 sta in "den", nu in "den_firma". */
    public function test_d394_ia_datele_contribuabilului_nu_pe_ale_partenerului(): void
    {
        $meta = $this->analizeaza(<<<'XML'
<?xml version="1.0" encoding="UTF-8"?>
<declaratie394 xmlns="mfp:anaf:dgti:d394:declaratie:v4" luna="6" an="2026" cui="RO15208744" den="DIANA SOFT SRL"
               cifR="9999" denR="CONTABIL SRL" cif_intocmit="123" den_intocmit="EXPERT SRL">
  <rezumat1 cuiP="33486455" denP="ALFA CONSTRUCT SRL"/>
</declaratie394>
XML);

        $this->assertSame('15208744', $meta['cui'], 'prefixul RO se taie');
        $this->assertSame('DIANA SOFT SRL', $meta['den_firma']);
    }

    /**
     * Reprezentantul, auditorul si cel care a intocmit declaratia au si ei cod
     * fiscal in XML; niciunul nu e al contribuabilului.
     */
    public function test_codurile_fiscale_ale_altor_persoane_nu_sunt_luate_drept_ale_firmei(): void
    {
        $meta = $this->analizeaza(<<<'XML'
<?xml version="1.0" encoding="UTF-8"?>
<bilant1046 xmlns="mfp:anaf:dgti:s1046:declaratie:v1" an="2025" cif_audi="111" den_audi="AUDIT SRL"
            cif_intocmit="222" cifra_afaceri="900000"/>
XML);

        $this->assertNull($meta['cui']);
        $this->assertNull($meta['den_firma']);
    }

    /** SAF-T tine identificarea in elemente, nu in atribute. */
    public function test_saf_t_citeste_identificarea_din_elemente(): void
    {
        $meta = $this->analizeaza(<<<'XML'
<?xml version="1.0" encoding="UTF-8"?>
<AuditFile xmlns="mfp:anaf:dgti:d406:declaratie:v1">
  <Header>
    <Company>
      <RegistrationNumber>33486455</RegistrationNumber>
      <Name>ALFA CONSTRUCT SRL</Name>
    </Company>
  </Header>
</AuditFile>
XML);

        $this->assertSame('D406', $meta['tip']);
        $this->assertSame('33486455', $meta['cui']);
        $this->assertSame('ALFA CONSTRUCT SRL', $meta['den_firma']);
    }

    /**
     * Un SAF-T are sute de mii de elemente si nicio identificare in atribute.
     *
     * Cautarea trebuie sa se opreasca sus, nu sa parcurga tot fisierul: altfel
     * incarcarea se blocheaza minute bune pe o declaratie mare.
     */
    public function test_documentul_urias_nu_este_parcurs_intreg(): void
    {
        $randuri = str_repeat('<Transaction><Line><AccountID>2837</AccountID></Line></Transaction>', 20000);

        $inceput = microtime(true);

        $meta = $this->analizeaza(<<<XML
<?xml version="1.0" encoding="UTF-8"?>
<AuditFile xmlns="mfp:anaf:dgti:d406:declaratie:v1">
  <Header><Company>
    <RegistrationNumber>33486455</RegistrationNumber>
    <Name>ALFA CONSTRUCT SRL</Name>
  </Company></Header>
  <GeneralLedgerEntries>{$randuri}</GeneralLedgerEntries>
</AuditFile>
XML);

        $durata = microtime(true) - $inceput;

        $this->assertSame('33486455', $meta['cui']);
        $this->assertSame('ALFA CONSTRUCT SRL', $meta['den_firma']);
        $this->assertLessThan(5, $durata, 'analiza a durat ' . round($durata, 1) . 's — se parcurge tot documentul');
    }

    /**
     * D406/SAF-T ține perioada în antet, în elemente.
     *
     * Fără ea, declarația ar apărea în listă și în arhivă fără lună și an, iar
     * validatorul ANAF alege regulile și nomenclatoarele tocmai după perioadă.
     */
    public function test_d406_citeste_perioada_din_antetul_saft(): void
    {
        $meta = $this->analizeaza(<<<'XML'
<?xml version="1.0" encoding="UTF-8"?>
<AuditFile xmlns="mfp:anaf:dgti:d406:declaratie:v1">
  <Header>
    <AuditFileVersion>2.0</AuditFileVersion>
    <AuditFileCountry>RO</AuditFileCountry>
    <Company>
      <RegistrationNumber>14385411</RegistrationNumber>
      <Name>ALFA CONSTRUCT SRL</Name>
    </Company>
    <SelectionCriteria>
      <PeriodStart>6</PeriodStart>
      <PeriodStartYear>2026</PeriodStartYear>
      <PeriodEnd>6</PeriodEnd>
      <PeriodEndYear>2026</PeriodEndYear>
    </SelectionCriteria>
  </Header>
  <MasterFiles>
    <Customers><Customer><Name>ALT CLIENT SRL</Name></Customer></Customers>
  </MasterFiles>
</AuditFile>
XML);

        $this->assertSame('D406', $meta['tip']);
        $this->assertSame('14385411', $meta['cui']);
        $this->assertSame('ALFA CONSTRUCT SRL', $meta['den_firma'], 'denumirea e a firmei, nu a unui client');
        $this->assertSame(6, $meta['luna']);
        $this->assertSame(2026, $meta['anul']);
    }

    /**
     * Antetul scris de programele de contabilitate: perioada e dată prin
     * intervalul de selecție, iar felul ei prin HeaderComment (L/T/A).
     */
    public function test_d406_ia_perioada_din_intervalul_de_selectie(): void
    {
        $meta = $this->analizeaza(<<<'XML'
<?xml version="1.0" encoding="UTF-8"?>
<AuditFile xmlns="mfp:anaf:dgti:d406:declaratie:v1">
  <Header>
    <Company>
      <RegistrationNumber>RO14385411</RegistrationNumber>
      <Name>DV CITY AUTOMOBILE SRL</Name>
    </Company>
    <SelectionCriteria>
      <SelectionStartDate>2026-06-01</SelectionStartDate>
      <SelectionEndDate>2026-06-30</SelectionEndDate>
    </SelectionCriteria>
    <HeaderComment>L</HeaderComment>
    <TaxAccountingBasis>A</TaxAccountingBasis>
  </Header>
</AuditFile>
XML);

        $this->assertSame(6, $meta['luna']);
        $this->assertSame(2026, $meta['anul']);
        $this->assertSame('L', $meta['perioada_tip']);
    }

    /** Un HeaderComment care nu e L, T sau A nu se ia drept perioadă. */
    public function test_comentariul_de_antet_strain_nu_devine_tip_de_perioada(): void
    {
        $meta = $this->analizeaza(<<<'XML'
<?xml version="1.0" encoding="UTF-8"?>
<AuditFile xmlns="mfp:anaf:dgti:d406:declaratie:v1">
  <Header>
    <Company><RegistrationNumber>14385411</RegistrationNumber><Name>X SRL</Name></Company>
    <SelectionCriteria><SelectionStartDate>2026-06-01</SelectionStartDate></SelectionCriteria>
    <HeaderComment>generat automat</HeaderComment>
  </Header>
</AuditFile>
XML);

        $this->assertNull($meta['perioada_tip']);
        $this->assertSame(6, $meta['luna']);
    }

    /** Raportarea anuală (D406 pe an) nu are lună de început în antet. */
    public function test_d406_anual_ia_doar_anul_fiscal(): void
    {
        $meta = $this->analizeaza(<<<'XML'
<?xml version="1.0" encoding="UTF-8"?>
<AuditFile xmlns="mfp:anaf:dgti:d406:declaratie:v1">
  <Header>
    <Company>
      <RegistrationNumber>RO14385411</RegistrationNumber>
      <Name>ALFA CONSTRUCT SRL</Name>
    </Company>
    <FiscalYear>2025</FiscalYear>
  </Header>
</AuditFile>
XML);

        $this->assertSame('14385411', $meta['cui'], 'prefixul RO se taie și aici');
        $this->assertNull($meta['luna']);
        $this->assertSame(2025, $meta['anul']);
    }

    /** Cand radacina are ea insasi CUI, nu se coboara in sectiuni. */
    public function test_radacina_cu_cui_are_intaietate_fata_de_sectiuni(): void
    {
        $meta = $this->analizeaza(<<<'XML'
<?xml version="1.0" encoding="UTF-8"?>
<declaratie300 xmlns="mfp:anaf:dgti:d300:declaratie:v10" luna_r="5" an_r="2026" cui="33486455" den="ALFA CONSTRUCT SRL">
  <sectiune cui="99999999" den="ALTCINEVA SRL"/>
</declaratie300>
XML);

        $this->assertSame('33486455', $meta['cui']);
        $this->assertSame('ALFA CONSTRUCT SRL', $meta['den_firma']);
    }
}
