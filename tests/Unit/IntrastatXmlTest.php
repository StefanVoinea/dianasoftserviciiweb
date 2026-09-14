<?php

namespace Tests\Unit;

use App\Models\Company;
use App\Models\EtransportDeclaratie;
use App\Services\Anaf\Etransport\EtransportException;
use App\Services\Anaf\Etransport\IntrastatXml;
use App\Support\ContextCompanie;
use Tests\TestCase;

/**
 * Declarația Intrastat din declarațiile e-Transport cu UIT.
 *
 * Sosirile ies din achizițiile intracomunitare, cu liniile adunate pe cod NC8
 * și țară, valorile în lei întregi și greutățile rotunjite — cum cere INS.
 */
class IntrastatXmlTest extends TestCase
{
    protected $client;

    protected function setUp(): void
    {
        parent::setUp();

        $this->client = Company::create(['denumire' => 'PROBA INTRASTAT SRL', 'cui' => '15196216']);
        ContextCompanie::fixeaza($this->client->id);
    }

    protected function tearDown(): void
    {
        EtransportDeclaratie::query()->toateCompaniile()->where('company_id', $this->client->id)->delete();
        ContextCompanie::elibereaza();
        $this->client->delete();

        parent::tearDown();
    }

    public function test_sosirile_se_aduna_pe_cod_si_tara_si_trec_in_xmlul_ins()
    {
        // Doua transporturi in aceeasi luna; codul comun se aduna.
        $this->declaratie('3E3G8N2TARTF4A48', [
            ['cod_tarifar' => '61046200', 'valoare_lei' => 4162.15, 'greutate_neta' => 20.3, 'tara_origine' => 'BD'],
            ['cod_tarifar' => '61091000', 'valoare_lei' => 1000.4, 'greutate_neta' => 0.4],
        ]);
        $this->declaratie('4A48TARTF3E3G8N2', [
            ['cod_tarifar' => '61046200', 'valoare_lei' => 837.85, 'greutate_neta' => 4.7, 'tara_origine' => 'BD'],
        ]);
        /*
         * [2026-09-14] Si fara cod UIT intra: e-Transport si Intrastat sunt doua
         * obligatii deosebite. Un transport nedeclarat la ANAF — sau declarat
         * prea tarziu ca sa mai primeasca UIT — tot trebuie declarat la
         * statistica. In centralizator se vede starea lui, si se poate scoate.
         */
        $this->declaratie(null, [
            ['cod_tarifar' => '61046200', 'valoare_lei' => 1000, 'greutate_neta' => 0.3, 'tara_origine' => 'BD'],
        ]);

        $rezultat = (new IntrastatXml())->genereaza(8, 2026, 'sosiri', $this->antet());

        $this->assertSame(2, $rezultat['linii']);
        $this->assertSame(3, $rezultat['declaratii']);
        $this->assertSame('intrastat_sosiri_2026_08_15196216.xml', $rezultat['nume']);

        $xml = $rezultat['xml'];

        $this->assertStringContainsString('<InsNewArrival', $xml);
        $this->assertStringContainsString('xmlns="http://www.intrastat.ro/xml/InsSchema"', $xml);
        // CIF-ul pe 10 cifre, cum cere schema INS.
        $this->assertStringContainsString('<VatNr>0015196216</VatNr>', $xml);
        $this->assertStringContainsString('<RefPeriod>2026-08</RefPeriod>', $xml);
        $this->assertStringContainsString('<Cn8Code>61046200</Cn8Code>', $xml);
        // 4162.15 + 837.85 + 1000 = 6000, intreg.
        $this->assertStringContainsString('<InvoiceValue>6000</InvoiceValue>', $xml);
        // 20.3 + 4.7 + 0.3 = 25.3, rotunjit 25 kg.
        $this->assertStringContainsString('<NetMass>25</NetMass>', $xml);
        // Sub un kilogram se scrie 1.
        $this->assertStringContainsString('<NetMass>1</NetMass>', $xml);
        // Originea marfii din fisierul furnizorului; fara ea, tara partenerului.
        $this->assertStringContainsString('<CountryOfOrigin>BD</CountryOfOrigin>', $xml);
        $this->assertStringContainsString('<CountryOfOrigin>IT</CountryOfOrigin>', $xml);
        $this->assertStringContainsString('<CountryOfConsignment>IT</CountryOfConsignment>', $xml);
        $this->assertStringContainsString('<DeliveryTermsCode>EXW</DeliveryTermsCode>', $xml);
        $this->assertStringContainsString('<ModeOfTransportCode>3</ModeOfTransportCode>', $xml);
    }

    public function test_fara_declaratii_se_spune_pe_romaneste()
    {
        $this->expectException(EtransportException::class);

        (new IntrastatXml())->genereaza(1, 2020, 'expedieri', $this->antet());
    }

    /** Luna fara nimic de declarat isi are declaratia ei, cu radacina INS pentru nule. */
    public function test_declaratia_nula_de_expedieri_are_radacina_ins_nill_dispatch()
    {
        $rezultat = (new IntrastatXml())->genereaza(8, 2026, 'expedieri', $this->antet(), ['nula' => true]);

        $this->assertTrue($rezultat['nula']);
        $this->assertSame(0, $rezultat['linii']);
        $this->assertSame(0, $rezultat['declaratii']);
        $this->assertSame(0, $rezultat['valoare']);
        $this->assertSame('intrastat_nula_expedieri_2026_08_15196216.xml', $rezultat['nume']);

        $xml = $rezultat['xml'];

        $this->assertStringContainsString('<InsNillDispatch', $xml);
        $this->assertStringNotContainsString('<InsNewDispatch', $xml);
        $this->assertStringContainsString('xmlns="http://www.intrastat.ro/xml/InsSchema"', $xml);
        $this->assertStringContainsString('SchemaVersion="1.0"', $xml);
        // Cuprinsul cerut de schema: versiunile si antetul, atat.
        $this->assertStringContainsString('<InsCodeVersions>', $xml);
        $this->assertStringContainsString('<VatNr>0015196216</VatNr>', $xml);
        $this->assertStringContainsString('<RefPeriod>2026-08</RefPeriod>', $xml);
        $this->assertStringContainsString('<LastName>Popescu</LastName>', $xml);
        // Nicio linie de marfa.
        $this->assertStringNotContainsString('<InsDispatchItem', $xml);
        $this->assertStringNotContainsString('<Cn8Code>', $xml);
    }

    /** Aceeasi socoteala pe celalalt flux. */
    public function test_declaratia_nula_de_sosiri_are_radacina_ins_nill_arrival()
    {
        // Pe o luna in care nu s-a miscat nimic: cele din proba sunt pe august.
        $rezultat = (new IntrastatXml())->genereaza(3, 2026, 'sosiri', $this->antet(), ['nula' => true]);

        $this->assertStringContainsString('<InsNillArrival', $rezultat['xml']);
        $this->assertStringNotContainsString('<InsArrivalItem', $rezultat['xml']);
    }

    /** Nu se declara „nimic" pe o luna care a avut miscari: ar fi o minciuna. */
    public function test_declaratia_nula_se_refuza_cand_luna_are_miscari()
    {
        $this->declaratie('3E3G8N2TARTF4A48', [
            ['cod_tarifar' => '61046200', 'valoare_lei' => 100, 'greutate_neta' => 1],
        ]);

        $this->expectException(EtransportException::class);
        $this->expectExceptionMessageMatches('/luna nu e goală/u');

        // Proba pune declaratii pe august 2026, flux sosiri.
        (new IntrastatXml())->genereaza(8, 2026, 'sosiri', $this->antet(), ['nula' => true]);
    }

    /** Cand luna e goala, mesajul spune ce are omul de facut. */
    public function test_lipsa_declaratiilor_indruma_spre_declaratia_nula()
    {
        $this->expectException(EtransportException::class);
        $this->expectExceptionMessageMatches('/declarație nulă/u');

        (new IntrastatXml())->genereaza(1, 2020, 'expedieri', $this->antet());
    }

    /**
     * Luna se ia după data documentului, nu a transportului.
     *
     * Cazul care a scos augustul pe jumătate: marfa facturată pe 31 august a
     * plecat pe 3 septembrie. Socotită după transport, ea cădea în septembrie.
     */
    public function test_luna_se_ia_dupa_data_documentului_nu_a_transportului()
    {
        $this->declaratie('UIT-AUG-1', $this->linie(5000), [
            'data_transport' => '2026-09-03',
            'documente' => [['tip' => 20, 'numar' => '10073333', 'data' => '2026-08-31']],
        ]);

        $august = (new IntrastatXml())->centralizator(8, 2026, 'sosiri');
        $septembrie = (new IntrastatXml())->centralizator(9, 2026, 'sosiri');

        $this->assertSame(1, $august['totaluri']['documente'], 'Factura de august trebuie să cadă în august');
        $this->assertSame(0, $septembrie['totaluri']['documente']);
        $this->assertSame('2026-08-31', $august['documente'][0]['data']);
        $this->assertSame('2026-09-03', $august['documente'][0]['data_transport']);
    }

    /** Fără dată pe document rămâne ziua transportului, ca rândul să nu se piardă. */
    public function test_fara_data_pe_document_ramane_ziua_transportului()
    {
        $this->declaratie('UIT-FARA-DOC', $this->linie(), ['data_transport' => '2026-08-14']);

        $this->assertSame(1, (new IntrastatXml())->centralizator(8, 2026, 'sosiri')['totaluri']['documente']);
    }

    /**
     * Întârziații: factura unei luni trecute, neintrată în nicio declarație,
     * se ia din urmă la luna următoare.
     */
    public function test_facturile_mai_vechi_nedeclarate_apar_ca_intarziate()
    {
        $iulie = $this->declaratie('UIT-IUL', $this->linie(3000), [
            'data_transport' => '2026-07-31',
            'documente' => [['tip' => 20, 'numar' => 'F-IULIE', 'data' => '2026-07-31']],
        ]);
        $august = $this->declaratie('UIT-AUG', $this->linie(4000), [
            'data_transport' => '2026-08-20',
            'documente' => [['tip' => 20, 'numar' => 'F-AUGUST', 'data' => '2026-08-20']],
        ]);

        $centralizator = (new IntrastatXml())->centralizator(8, 2026, 'sosiri');

        $intarziate = array_values(array_filter($centralizator['documente'], function ($d) {
            return $d['intarziat'];
        }));

        $this->assertCount(1, $intarziate);
        $this->assertSame('F-IULIE', $intarziate[0]['numar']);
        // Propunerea e luna curata: intarziatul apare, dar nebifat.
        $this->assertFalse($intarziate[0]['bifat']);
        $this->assertSame(1, $centralizator['totaluri']['documente']);

        // Bifat, intra si el in socoteala si in fisier.
        $cuIntarziat = (new IntrastatXml())->centralizator(8, 2026, 'sosiri', [$august->id, $iulie->id]);
        $this->assertSame(2, $cuIntarziat['totaluri']['documente']);
        $this->assertSame(7000, $cuIntarziat['totaluri']['valoare']);
    }

    /**
     * Cazul Emporio: arhivele lui iulie, importate acum, intră în august.
     *
     * Importul de arhivă lasă ciorne fără dată de transport — ea se completează
     * la depunerea la ANAF, care aici nu mai are rost: transportul a trecut, iar
     * ANAF nu mai dă cod UIT pentru el. Rămâne data facturii, și după ea se
     * așază: facturile lui iulie, nedeclarate nicăieri, se iau la august.
     */
    public function test_arhivele_lui_iulie_importate_acum_intra_in_august()
    {
        $iulie = $this->declaratie(null, $this->linie(2500), [
            'stare' => 'ciorna',
            'data_transport' => null,
            'documente' => [['tip' => 20, 'numar' => '10068001', 'data' => '2026-07-31']],
        ]);

        $centralizator = (new IntrastatXml())->centralizator(8, 2026, 'sosiri');

        $this->assertCount(1, $centralizator['documente']);
        $this->assertTrue($centralizator['documente'][0]['intarziat']);
        $this->assertSame('10068001', $centralizator['documente'][0]['numar']);
        $this->assertNull($centralizator['documente'][0]['data_transport']);

        $rezultat = (new IntrastatXml())->genereaza(8, 2026, 'sosiri', $this->antet(), [
            'declaratii' => [$iulie->id],
        ]);

        $this->assertSame(1, $rezultat['declaratii']);
        $this->assertSame(2500, $rezultat['valoare']);
        $this->assertStringContainsString('<RefPeriod>2026-08</RefPeriod>', $rezultat['xml']);
        $this->assertSame('2026-08', $iulie->fresh()->intrastat_perioada);
    }

    /** Ce a intrat într-o declarație se însemnează și nu mai apare data viitoare. */
    public function test_ce_a_intrat_intr_o_declaratie_nu_se_mai_propune()
    {
        $declaratie = $this->declaratie('UIT-AUG', $this->linie(4000), [
            'data_transport' => '2026-08-20',
            'documente' => [['tip' => 20, 'numar' => 'F-AUGUST', 'data' => '2026-08-20']],
        ]);

        (new IntrastatXml())->genereaza(8, 2026, 'sosiri', $this->antet());

        $this->assertSame('2026-08', $declaratie->fresh()->intrastat_perioada);

        // La septembrie nu mai e intarziat: a intrat in august.
        $septembrie = (new IntrastatXml())->centralizator(9, 2026, 'sosiri');
        $this->assertSame([], $septembrie['documente']);

        // Augustul deschis din nou se vede la fel: insemnarea e a lui.
        $dinNou = (new IntrastatXml())->centralizator(8, 2026, 'sosiri');
        $this->assertSame(1, $dinNou['totaluri']['documente']);
    }

    /** În fișier intră exact documentele bifate, nu tot ce e în lună. */
    public function test_in_fisier_intra_doar_documentele_bifate()
    {
        $unul = $this->declaratie('UIT-1', $this->linie(1000, '61046200'), [
            'documente' => [['tip' => 20, 'numar' => 'F-1', 'data' => '2026-08-10']],
        ]);
        $altul = $this->declaratie('UIT-2', $this->linie(2000, '61091000'), [
            'documente' => [['tip' => 20, 'numar' => 'F-2', 'data' => '2026-08-11']],
        ]);

        $rezultat = (new IntrastatXml())->genereaza(8, 2026, 'sosiri', $this->antet(), [
            'declaratii' => [$unul->id],
        ]);

        $this->assertSame(1, $rezultat['declaratii']);
        $this->assertSame(1000, $rezultat['valoare']);
        $this->assertStringContainsString('<Cn8Code>61046200</Cn8Code>', $rezultat['xml']);
        $this->assertStringNotContainsString('<Cn8Code>61091000</Cn8Code>', $rezultat['xml']);

        // Cel nebifat ramane nedeclarat, deci se propune luna viitoare.
        $this->assertNull($altul->fresh()->intrastat_perioada);
        $this->assertSame('2026-08', $unul->fresh()->intrastat_perioada);
    }

    /** Declarația respinsă de ANAF nu intră: transportul s-a redepus cu alta. */
    public function test_declaratia_respinsa_nu_intra_in_intrastat()
    {
        $this->declaratie('UIT-RESP', $this->linie(9999), [
            'stare' => 'respinsa',
            'documente' => [['tip' => 20, 'numar' => 'F-RESP', 'data' => '2026-08-10']],
        ]);

        $this->assertSame([], (new IntrastatXml())->centralizator(8, 2026, 'sosiri')['documente']);
    }

    /** Declaratia nula n-are linii, deci nu cere nici conditie de livrare. */
    public function test_declaratia_nula_nu_cere_incoterm()
    {
        $antet = $this->antet();
        unset($antet['incoterm']);

        $rezultat = (new IntrastatXml())->genereaza(8, 2026, 'expedieri', $antet, ['nula' => true]);

        $this->assertTrue($rezultat['nula']);
        $this->assertStringNotContainsString('<DeliveryTermsCode>', $rezultat['xml']);
    }

    protected function declaratie(?string $uit, array $linii, array $campuri = []): EtransportDeclaratie
    {
        return EtransportDeclaratie::create($campuri + [
            'company_id' => $this->client->id,
            'stare' => $uit ? 'validata' : 'ciorna',
            'cif_declarant' => '15196216',
            'tip_operatiune' => 10,
            'partener_tara' => 'IT',
            'partener_cod' => '00953910403',
            'partener_denumire' => 'TEDDY S.p.A.',
            'data_transport' => '2026-08-14',
            'linii' => $linii,
            'uit' => $uit,
        ]);
    }

    /** O linie oarecare, cand nu conteaza ce marfa e. */
    protected function linie(float $lei = 1000, string $cod = '61046200'): array
    {
        return [['cod_tarifar' => $cod, 'valoare_lei' => $lei, 'greutate_neta' => 5, 'tara_origine' => 'BD']];
    }

    protected function antet(): array
    {
        return [
            'cif' => '15196216',
            'firma' => 'PROBA INTRASTAT SRL',
            'nume' => 'Popescu',
            'prenume' => 'Camelia',
            'telefon' => '0722000000',
            'email' => 'camelia@firma.ro',
            'incoterm' => 'EXW',
        ];
    }
}
