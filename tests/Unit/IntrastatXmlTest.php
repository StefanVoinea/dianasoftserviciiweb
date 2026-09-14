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
        // Doua declaratii cu UIT in aceeasi luna; codul comun se aduna.
        $this->declaratie('3E3G8N2TARTF4A48', [
            ['cod_tarifar' => '61046200', 'valoare_lei' => 5162.15, 'greutate_neta' => 20.3, 'tara_origine' => 'BD'],
            ['cod_tarifar' => '61091000', 'valoare_lei' => 1000.4, 'greutate_neta' => 0.4],
        ]);
        $this->declaratie('4A48TARTF3E3G8N2', [
            ['cod_tarifar' => '61046200', 'valoare_lei' => 837.85, 'greutate_neta' => 4.7, 'tara_origine' => 'BD'],
        ]);
        // Fara UIT nu e depusa: nu intra.
        $this->declaratie(null, [
            ['cod_tarifar' => '61046200', 'valoare_lei' => 99999, 'greutate_neta' => 99],
        ]);

        $rezultat = (new IntrastatXml())->genereaza(8, 2026, 'sosiri', $this->antet());

        $this->assertSame(2, $rezultat['linii']);
        $this->assertSame(2, $rezultat['declaratii']);
        $this->assertSame('intrastat_sosiri_2026_08_15196216.xml', $rezultat['nume']);

        $xml = $rezultat['xml'];

        $this->assertStringContainsString('<InsNewArrival', $xml);
        $this->assertStringContainsString('xmlns="http://www.intrastat.ro/xml/InsSchema"', $xml);
        // CIF-ul pe 10 cifre, cum cere schema INS.
        $this->assertStringContainsString('<VatNr>0015196216</VatNr>', $xml);
        $this->assertStringContainsString('<RefPeriod>2026-08</RefPeriod>', $xml);
        $this->assertStringContainsString('<Cn8Code>61046200</Cn8Code>', $xml);
        // 5162.15 + 837.85 = 6000, intreg.
        $this->assertStringContainsString('<InvoiceValue>6000</InvoiceValue>', $xml);
        // 20.3 + 4.7 = 25 kg.
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
        $rezultat = (new IntrastatXml())->genereaza(8, 2026, 'expedieri', $this->antet(), true);

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
        $rezultat = (new IntrastatXml())->genereaza(3, 2026, 'sosiri', $this->antet(), true);

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
        (new IntrastatXml())->genereaza(8, 2026, 'sosiri', $this->antet(), true);
    }

    /** Cand luna e goala, mesajul spune ce are omul de facut. */
    public function test_lipsa_declaratiilor_indruma_spre_declaratia_nula()
    {
        $this->expectException(EtransportException::class);
        $this->expectExceptionMessageMatches('/declarație nulă/u');

        (new IntrastatXml())->genereaza(1, 2020, 'expedieri', $this->antet());
    }

    /** Declaratia nula n-are linii, deci nu cere nici conditie de livrare. */
    public function test_declaratia_nula_nu_cere_incoterm()
    {
        $antet = $this->antet();
        unset($antet['incoterm']);

        $rezultat = (new IntrastatXml())->genereaza(8, 2026, 'expedieri', $antet, true);

        $this->assertTrue($rezultat['nula']);
        $this->assertStringNotContainsString('<DeliveryTermsCode>', $rezultat['xml']);
    }

    protected function declaratie(?string $uit, array $linii): EtransportDeclaratie
    {
        return EtransportDeclaratie::create([
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
