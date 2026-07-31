<?php

namespace Tests\Unit;

use App\Services\Just\PortalJustClient;
use App\Services\Just\PortalJustException;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

/**
 * Clientul serviciului Portal Just: compunerea plicului SOAP, citirea
 * răspunsului și tratarea erorilor.
 */
class PortalJustTest extends TestCase
{
    protected function client(): PortalJustClient
    {
        return $this->app->make(PortalJustClient::class);
    }

    /** Plicul de răspuns, cu conținutul dat în interior. */
    protected function raspuns(string $metoda, string $continut): string
    {
        return '<?xml version="1.0" encoding="utf-8"?>'
            . '<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/"'
            . ' xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">'
            . '<soap:Body><' . $metoda . 'Response xmlns="portalquery.just.ro">'
            . '<' . $metoda . 'Result>' . $continut . '</' . $metoda . 'Result>'
            . '</' . $metoda . 'Response></soap:Body></soap:Envelope>';
    }

    protected function dosarExemplu(): string
    {
        return '<Dosar>'
            . '<parti>'
            . '<DosarParte><nume>POPESCU ION</nume><calitateParte>Reclamant</calitateParte></DosarParte>'
            . '<DosarParte><nume>SC EXEMPLU SRL</nume><calitateParte>Pârât</calitateParte></DosarParte>'
            . '</parti>'
            . '<sedinte><DosarSedinta>'
            . '<complet>S7 C.31A</complet><data>2025-11-28T00:00:00</data><ora>10:00</ora>'
            . '<solutie>Nefondat</solutie><solutieSumar>Respinge apelul.</solutieSumar>'
            . '<dataPronuntare>2025-11-28T00:00:00</dataPronuntare>'
            . '<documentSedinta>_Hotarare</documentSedinta><numarDocument>3907/2025</numarDocument>'
            . '<dataDocument>2025-11-28T00:00:00</dataDocument>'
            . '</DosarSedinta></sedinte>'
            . '<caiAtac><DosarCaleAtac>'
            . '<dataDeclarare>2025-01-15T00:00:00</dataDeclarare>'
            . '<parteDeclaratoare>POPESCU ION</parteDeclaratoare><tipCaleAtac>Apel</tipCaleAtac>'
            . '</DosarCaleAtac></caiAtac>'
            . '<numar>1234/3/2024</numar><numarVechi /><data>2024-02-10T00:00:00</data>'
            . '<institutie>CurteadeApelBUCURESTI</institutie><departament>Secția a VII-a</departament>'
            . '<obiect>pretenții</obiect><dataModificare>2025-11-29T14:22:31</dataModificare>'
            . '<categorieCazNume>Civil</categorieCazNume><stadiuProcesualNume>Apel</stadiuProcesualNume>'
            . '</Dosar>';
    }

    public function test_cautarea_fara_niciun_criteriu_principal_este_refuzata(): void
    {
        Http::fake();

        $this->expectException(PortalJustException::class);
        $this->client()->cautaDosare(['institutie' => 'CurteadeApelBUCURESTI']);
    }

    /**
     * Criteriile necompletate trebuie trimise explicit ca `xsi:nil`: serviciul
     * le declară obligatorii în schemă, deși le acceptă goale.
     */
    public function test_criteriile_necompletate_se_trimit_ca_nil(): void
    {
        Http::fake([
            '*' => Http::response($this->raspuns('CautareDosare', ''), 200),
        ]);

        $this->client()->cautaDosare(['nume_parte' => 'Popescu']);

        Http::assertSent(function (Request $cerere) {
            $corp = $cerere->body();

            return strpos($corp, '<numeParte>Popescu</numeParte>') !== false
                && strpos($corp, '<institutie xsi:nil="true" />') !== false
                && strpos($corp, '<dataStart xsi:nil="true" />') !== false
                && strpos($corp, '<dataStop xsi:nil="true" />') !== false
                && $cerere->header('SOAPAction')[0] === '"portalquery.just.ro/CautareDosare"';
        });
    }

    /** Filtrele pe data ultimei modificări există doar în a doua variantă a metodei. */
    public function test_filtrul_pe_data_modificarii_foloseste_a_doua_metoda(): void
    {
        Http::fake([
            '*' => Http::response($this->raspuns('CautareDosare2', ''), 200),
        ]);

        $this->client()->cautaDosare(['nume_parte' => 'Popescu', 'modificat_de' => '01.03.2026']);

        Http::assertSent(function (Request $cerere) {
            return strpos($cerere->body(), '<CautareDosare2 xmlns="portalquery.just.ro">') !== false
                && strpos($cerere->body(), '<dataUltimaModificareStart>2026-03-01T00:00:00</dataUltimaModificareStart>') !== false
                && strpos($cerere->body(), '<dataUltimaModificareStop xsi:nil="true" />') !== false
                && $cerere->header('SOAPAction')[0] === '"portalquery.just.ro/CautareDosare2"';
        });
    }

    /** Textul introdus de utilizator nu are voie să strice plicul XML. */
    public function test_criteriile_sunt_scapate_in_xml(): void
    {
        Http::fake([
            '*' => Http::response($this->raspuns('CautareDosare', ''), 200),
        ]);

        $this->client()->cautaDosare(['nume_parte' => 'Ion & <Fiii>']);

        Http::assertSent(function (Request $cerere) {
            return strpos($cerere->body(), '<numeParte>Ion &amp; &lt;Fiii&gt;</numeParte>') !== false;
        });
    }

    public function test_dosarul_este_citit_cu_parti_termene_si_cai_de_atac(): void
    {
        Http::fake([
            '*' => Http::response($this->raspuns('CautareDosare', $this->dosarExemplu()), 200),
        ]);

        $dosare = $this->client()->cautaDosare(['numar_dosar' => '1234/3/2024']);

        $this->assertCount(1, $dosare);
        $dosar = $dosare[0];

        $this->assertSame('1234/3/2024', $dosar['numar']);
        $this->assertSame('Curtea de Apel BUCURESTI', $dosar['institutie_eticheta']);
        $this->assertSame('Secția a VII-a', $dosar['departament']);
        $this->assertSame('Apel', $dosar['stadiu']);

        // Datele se afișează zz.ll.aaaa, iar momentele cu ora.
        $this->assertSame('10.02.2024', $dosar['data']);
        $this->assertSame('29.11.2025 14:22:31', $dosar['data_modificare']);

        $this->assertCount(2, $dosar['parti']);
        $this->assertSame('POPESCU ION', $dosar['parti'][0]['nume']);
        $this->assertSame('Reclamant', $dosar['parti'][0]['calitate']);

        $this->assertCount(1, $dosar['sedinte']);
        $this->assertSame('28.11.2025', $dosar['sedinte'][0]['data']);
        $this->assertSame('Nefondat', $dosar['sedinte'][0]['solutie']);

        $this->assertCount(1, $dosar['cai_atac']);
        $this->assertSame('Apel', $dosar['cai_atac'][0]['tip']);
        $this->assertSame('15.01.2025', $dosar['cai_atac'][0]['data_declarare']);

        // Elementele goale nu trebuie să devină șiruri vide în interfață.
        $this->assertNull($dosar['numar_vechi']);
    }

    public function test_sedintele_sunt_citite_cu_dosarele_lor(): void
    {
        $continut = '<Sedinta>'
            . '<departament>Secția I civilă</departament><complet>C1</complet>'
            . '<data>2026-03-10T00:00:00</data><ora>09:00</ora>'
            . '<dosare><SedintaDosar>'
            . '<numar>50/3/2026</numar><numar_vechi /><data>2026-01-05T00:00:00</data><ora>09:30</ora>'
            . '<categorieCazNume>Civil</categorieCazNume><stadiuProcesualNume>Fond</stadiuProcesualNume>'
            . '</SedintaDosar></dosare>'
            . '</Sedinta>';

        Http::fake([
            '*' => Http::response($this->raspuns('CautareSedinte', $continut), 200),
        ]);

        $sedinte = $this->client()->cautaSedinte('10.03.2026', 'TribunalulBUCURESTI');

        $this->assertCount(1, $sedinte);
        $this->assertSame('10.03.2026', $sedinte[0]['data']);
        $this->assertSame('Secția I civilă', $sedinte[0]['departament']);
        $this->assertCount(1, $sedinte[0]['dosare']);
        $this->assertSame('50/3/2026', $sedinte[0]['dosare'][0]['numar']);
        $this->assertSame('Fond', $sedinte[0]['dosare'][0]['stadiu']);

        Http::assertSent(function (Request $cerere) {
            return strpos($cerere->body(), '<dataSedinta>2026-03-10T00:00:00</dataSedinta>') !== false
                && strpos($cerere->body(), '<institutie>TribunalulBUCURESTI</institutie>') !== false;
        });
    }

    /** Erorile serviciului vin ca soap:Fault, cu HTTP 500. */
    public function test_eroarea_soap_devine_exceptie_cu_mesajul_serviciului(): void
    {
        $fault = '<?xml version="1.0" encoding="utf-8"?>'
            . '<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">'
            . '<soap:Body><soap:Fault>'
            . '<faultcode>soap:Server</faultcode>'
            . '<faultstring>Prea multe rezultate</faultstring>'
            . '</soap:Fault></soap:Body></soap:Envelope>';

        Http::fake(['*' => Http::response($fault, 500)]);

        $this->expectException(PortalJustException::class);
        $this->expectExceptionMessageMatches('/Prea multe rezultate/');

        $this->client()->cautaDosare(['nume_parte' => 'Popescu']);
    }

    public function test_lista_instantelor_se_citeste_din_wsdl(): void
    {
        $wsdl = '<?xml version="1.0" encoding="utf-8"?>'
            . '<wsdl:definitions xmlns:wsdl="http://schemas.xmlsoap.org/wsdl/"'
            . ' xmlns:s="http://www.w3.org/2001/XMLSchema">'
            . '<wsdl:types><s:schema>'
            . '<s:simpleType name="Institutie"><s:restriction base="s:string">'
            . '<s:enumeration value="TribunalulBUCURESTI" />'
            . '<s:enumeration value="CurteadeApelALBAIULIA" />'
            . '<s:enumeration value="JudecatoriaSECTORUL4BUCURESTI" />'
            . '<s:enumeration value="" />'
            . '</s:restriction></s:simpleType>'
            . '</s:schema></wsdl:types></wsdl:definitions>';

        $institutii = $this->client()->institutiiDinWsdl($wsdl);

        // Valoarea goală din schemă nu are ce căuta în listă.
        $this->assertCount(3, $institutii);

        $etichete = array_column($institutii, 'eticheta');
        $this->assertSame(['Curtea de Apel ALBAIULIA', 'Judecătoria SECTORUL 4 BUCURESTI', 'Tribunalul BUCURESTI'], $etichete);

        // Valoarea trimisă serviciului rămâne cea din schemă.
        $this->assertSame('CurteadeApelALBAIULIA', $institutii[0]['valoare']);
    }

    public function test_denumirile_compuse_ale_instantelor_nu_sunt_ciuntite(): void
    {
        $client = $this->client();

        $this->assertSame('Curtea Militară de Apel BUCURESTI', $client->etichetaInstitutie('CurteaMilitaradeApelBUCURESTI'));
        $this->assertSame('Tribunalul Militar Teritorial BUCURESTI', $client->etichetaInstitutie('TribunalulMilitarTeritorialBUCURESTI'));
        $this->assertSame('Tribunalul Comercial ARGES', $client->etichetaInstitutie('TribunalulComercialARGES'));
        $this->assertSame('Tribunalul pentru Minori și Familie BRASOV', $client->etichetaInstitutie('TribunalulpentruminoriSifamilieBRASOV'));
    }
}
