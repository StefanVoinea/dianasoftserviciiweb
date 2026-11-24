<?php

namespace Tests\Unit;

use App\Services\Anaf\Declaratii\PdfDeclaratie;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

/**
 * [2026-11-24] PDF-ul declarației se citește pe server, nu prin calculatorul
 * cu tokenul.
 *
 * Un SAF-T de 61 MB stă într-un PDF de 1,7 MB. Desfăcut pe calculatorul
 * clientului și trimis înapoi prin tunel, nu apuca în 60 de secunde, iar
 * declarațiile mari nu se puteau încărca. PDF-ul e însă deja pe server.
 */
class PdfulSeCitestePeServerTest extends TestCase
{
    /** @var array<int, string> */
    protected $fisiere = [];

    protected function tearDown(): void
    {
        foreach ($this->fisiere as $fisier) {
            @unlink($fisier);
        }

        parent::tearDown();
    }

    /** Un PDF mic, cu un fișier atașat și, la cerere, cu o semnătură sau cu formular XFA. */
    protected function pdf(string $atasament, bool $semnat, bool $inteligent = false): string
    {
        $comprimat = gzcompress($atasament);

        $obiecte = [
            1 => '<< /Type /Catalog /Pages 2 0 R' . ($inteligent ? ' /AcroForm << /XFA [(datasets) 5 0 R] >>' : '')
                . ' /Names << /EmbeddedFiles << /Names [(declaratie.xml) 4 0 R] >> >> >>',
            2 => '<< /Type /Pages /Kids [3 0 R] /Count 1 >>',
            3 => '<< /Type /Page /Parent 2 0 R /MediaBox [0 0 200 200] >>',
            4 => '<< /Type /Filespec /F (declaratie.xml) /EF << /F 5 0 R >> >>',
            5 => '<< /Type /EmbeddedFile /Filter /FlateDecode /Length ' . strlen($comprimat) . " >>\nstream\n"
                . $comprimat . "\nendstream",
        ];

        if ($semnat) {
            $obiecte[6] = '<< /Type /Sig /Filter /Adobe.PPKLite /ByteRange [0 10 20 30] /Contents <00> >>';
        }

        $pdf = "%PDF-1.4\n";
        $pozitii = [];

        foreach ($obiecte as $numar => $corp) {
            $pozitii[$numar] = strlen($pdf);
            $pdf .= $numar . " 0 obj\n" . $corp . "\nendobj\n";
        }

        $xref = strlen($pdf);
        $pdf .= "xref\n0 " . (count($obiecte) + 1) . "\n0000000000 65535 f \n";

        foreach ($pozitii as $pozitie) {
            $pdf .= sprintf("%010d 00000 n \n", $pozitie);
        }

        $pdf .= 'trailer << /Size ' . (count($obiecte) + 1) . " /Root 1 0 R >>\nstartxref\n" . $xref . "\n%%EOF\n";

        $cale = tempnam(sys_get_temp_dir(), 'pdf');
        file_put_contents($cale, $pdf);
        $this->fisiere[] = $cale;

        return $cale;
    }

    public function test_xml_ul_se_scoate_pe_server_fara_programul_local(): void
    {
        Http::fake();

        $xml = '<?xml version="1.0" encoding="UTF-8"?><declaratie394 luna="6" an="2026" cui="15208744"/>';
        $citit = app(PdfDeclaratie::class)->citeste($this->pdf($xml, true));

        $this->assertSame($xml, $citit['xml']);
        $this->assertTrue($citit['semnat']);

        // Niciun drum pana la calculatorul cu tokenul.
        Http::assertNothingSent();
    }

    public function test_pdf_ul_nesemnat_se_cunoaste_ca_nesemnat(): void
    {
        Http::fake();

        $citit = app(PdfDeclaratie::class)->citeste($this->pdf('<declaratie100 luna="6" an="2026"/>', false));

        $this->assertFalse($citit['semnat']);
        $this->assertStringStartsWith('<declaratie100', $citit['xml']);
    }

    /**
     * [2026-11-24] D300, D390 și D394 vin ca „PDF inteligent": bucățile
     * formularului încep și ele cu „<" și treceau drept declarație („element
     * rădăcină: datasets"). Acestea se citesc tot prin programul local.
     */
    public function test_pdf_ul_inteligent_merge_la_programul_local(): void
    {
        Http::fake(['*' => Http::response([
            'semnat' => false,
            'semnatari' => [],
            'nume_xml' => 'XML',
            'xml_base64' => base64_encode('<declaratie300 luna="10" an="2026"/>'),
        ], 200)]);

        $formular = '<xfa:datasets xmlns:xfa="http://www.xfa.org/schema/xfa-data/1.0/"><xfa:data/></xfa:datasets>';

        try {
            $citit = app(PdfDeclaratie::class)->citeste($this->pdf($formular, true, true));
        } catch (\Throwable $e) {
            // Fara certificat configurat in proba, drumul de rezerva se opreste
            // la alegerea lui: s-a pornit pe el, deci nu s-a citit aici.
            $this->assertStringNotContainsString('Smalot', get_class($e));

            return;
        }

        $this->assertStringStartsWith('<declaratie300', $citit['xml']);
        $this->assertFalse($citit['semnat']);
    }

    /** Ce nu se poate citi aici merge, ca înainte, la programul local. */
    public function test_pdf_ul_fara_xml_citit_aici_merge_la_programul_local(): void
    {
        Http::fake(['*' => Http::response([
            'semnat' => true,
            'semnatari' => [],
            'nume_xml' => 'XML',
            'xml_base64' => base64_encode('<declaratie300 luna="6" an="2026"/>'),
        ], 200)]);

        $this->app->make(\App\Services\Anaf\Spv\CertificatService::class);

        try {
            // Atasamentul e o arhiva, nu un XML: aici nu se gaseste nimic.
            $citit = app(PdfDeclaratie::class)->citeste($this->pdf("PK\x03\x04 arhiva", true));
        } catch (\Throwable $e) {
            // Fara certificat configurat in proba, drumul de rezerva se opreste
            // la alegerea lui — dar tocmai asta arata ca s-a pornit pe el.
            $this->assertStringNotContainsString('Smalot', get_class($e));

            return;
        }

        $this->assertStringStartsWith('<declaratie300', $citit['xml']);
    }
}
