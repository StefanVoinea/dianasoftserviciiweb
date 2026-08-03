<?php

namespace Tests\Unit;

use App\Services\Anaf\Declaratii\CurataXml;
use Tests\TestCase;

/**
 * Repararea XML-urilor de declaratie inainte de validarea cu DUKIntegrator.
 *
 * Cazul care a nascut serviciul: un D112 cu adresa „Str Bradului & Nr.13" in
 * atributul adrFisc — „&"-ul neescapat facea fisierul de necitit, iar
 * validatorul il respingea cu „Fisierul nu este un XML valid".
 */
class CurataXmlTest extends TestCase
{
    protected function curata(string $continut): string
    {
        return (new CurataXml())->curata($continut);
    }

    public function test_un_fisier_valid_ramane_neatins_octet_cu_octet(): void
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n"
            . '<declaratie100 cui="123" den="A &amp; B" text="a &lt; b"><a>1</a></declaratie100>';

        $this->assertSame($xml, $this->curata($xml));
    }

    public function test_ampersandul_neescapat_din_atribut_se_escapeaza(): void
    {
        $reparat = $this->curata(
            '<?xml version="1.0"?>'
            . '<declaratieUnica luna_r="6" an_r="2026">'
            . '<angajator cif="15208744" adrFisc="Str Bradului &amp; Nr.13" adrSoc="Alfa & Beta &amp; Gama"/>'
            . '</declaratieUnica>'
        );

        $citit = simplexml_load_string($reparat);

        $this->assertNotFalse($citit);
        // Entitatea scrisa deja corect nu se dubleaza in „&amp;amp;".
        $this->assertSame('Str Bradului & Nr.13', (string) $citit->angajator['adrFisc']);
        $this->assertSame('Alfa & Beta & Gama', (string) $citit->angajator['adrSoc']);
    }

    public function test_ampersandul_neescapat_din_text_se_escapeaza(): void
    {
        $reparat = $this->curata('<declaratie100><den>Popescu & Fiii</den></declaratie100>');

        $citit = simplexml_load_string($reparat);

        $this->assertNotFalse($citit);
        $this->assertSame('Popescu & Fiii', (string) $citit->den);
    }

    public function test_semnul_mai_mic_din_valoarea_de_atribut_se_escapeaza(): void
    {
        $reparat = $this->curata('<declaratie100 den="A < B" cui="123"/>');

        $citit = simplexml_load_string($reparat);

        $this->assertNotFalse($citit);
        $this->assertSame('A < B', (string) $citit['den']);
        $this->assertSame('123', (string) $citit['cui']);
    }

    public function test_diacriticele_scrise_in_codificarea_windows_se_aduc_la_utf8(): void
    {
        // „Navodari, str. Bradului" cu ă (0xE3) si ş (0xBA) din Windows-1250,
        // desi fisierul lasa sa se inteleaga ca e UTF-8.
        $reparat = $this->curata(
            "<declaratie100 den=\"N\xE3vodari \xBAos.\" cui=\"123\"/>"
        );

        $citit = simplexml_load_string($reparat);

        $this->assertNotFalse($citit);
        $this->assertSame('Năvodari şos.', (string) $citit['den']);
    }

    public function test_caracterele_de_control_interzise_se_scot(): void
    {
        $reparat = $this->curata("<declaratie100 den=\"AB\x1A\x00C\" cui=\"123\"/>");

        $citit = simplexml_load_string($reparat);

        $this->assertNotFalse($citit);
        $this->assertSame('ABC', (string) $citit['den']);
    }

    public function test_intr_un_cdata_nu_se_umbla(): void
    {
        $reparat = $this->curata(
            '<declaratie100 den="A & B"><nota><![CDATA[a & b < c]]></nota></declaratie100>'
        );

        $citit = simplexml_load_string($reparat);

        $this->assertNotFalse($citit);
        $this->assertSame('a & b < c', (string) $citit->nota);
        $this->assertSame('A & B', (string) $citit['den']);
    }

    public function test_un_fisier_ireparabil_ramane_cum_a_venit(): void
    {
        // Element neinchis: nu e de reparat prin escapare, iar omul trebuie sa
        // vada fisierul chiar asa cum l-a produs programul lui.
        $stricat = '<declaratie100 cui="123"><a>1</declaratie100>';

        $this->assertSame($stricat, $this->curata($stricat));
    }
}
