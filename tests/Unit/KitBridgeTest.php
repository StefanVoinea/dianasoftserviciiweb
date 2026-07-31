<?php

namespace Tests\Unit;

use App\Services\Anaf\Spv\KitBridge;
use Tests\TestCase;
use ZipArchive;

class KitBridgeTest extends TestCase
{
    protected $arhive = [];

    protected function tearDown(): void
    {
        foreach ($this->arhive as $cale) {
            @unlink($cale);
        }

        parent::tearDown();
    }

    protected function construieste(?string $token = null, int $port = 8099): array
    {
        $kit = $this->app->make(KitBridge::class)->construieste($token, $port);
        $this->arhive[] = $kit['cale'];

        return $kit;
    }

    public function test_arhiva_contine_tot_ce_trebuie_pentru_instalare(): void
    {
        $kit = $this->construieste();

        $zip = new ZipArchive();
        $this->assertTrue($zip->open($kit['cale']) === true);

        $asteptate = [
            'server.php', 'cert-info.ps1', 'sign-pdf.ps1', 'itextsharp.dll',
            'configurare.env', 'instaleaza.ps1', 'dezinstaleaza.ps1', 'porneste-manual.bat', 'CITESTE-MA.txt',
        ];

        foreach ($asteptate as $fisier) {
            $this->assertNotFalse($zip->locateName($fisier), 'Lipsește ' . $fisier . ' din kit');
        }

        $zip->close();
    }

    public function test_fiecare_kit_primeste_token_propriu(): void
    {
        $unu = $this->construieste();
        $doi = $this->construieste();

        $this->assertNotSame($unu['token'], $doi['token']);
        $this->assertSame(64, strlen($unu['token']));
    }

    public function test_codul_de_acces_ajunge_in_configurare_si_in_instructiuni(): void
    {
        $kit = $this->construieste('token-de-test-1234', 8099);

        $zip = new ZipArchive();
        $zip->open($kit['cale']);

        $this->assertStringContainsString('SPV_BRIDGE_TOKEN=token-de-test-1234', $zip->getFromName('configurare.env'));
        $this->assertStringContainsString('token-de-test-1234', $zip->getFromName('CITESTE-MA.txt'));

        $zip->close();
    }

    /** Amprenta ramane goala: aplicatia o trimite la fiecare operatie. */
    public function test_configurarea_nu_fixeaza_un_singur_token_usb(): void
    {
        $kit = $this->construieste();

        $zip = new ZipArchive();
        $zip->open($kit['cale']);
        $env = $zip->getFromName('configurare.env');
        $zip->close();

        $this->assertStringContainsString('SPV_CERT_THUMBPRINT=', $env);
        $this->assertMatchesRegularExpression('/SPV_CERT_THUMBPRINT=\s*$/m', $env);
    }

    /**
     * PowerShell 5.1 citeste fisierele fara BOM ca ANSI, ceea ce ar strica
     * diacriticele din mesajele scripturilor.
     */
    public function test_scripturile_powershell_au_bom_utf8(): void
    {
        $kit = $this->construieste();

        $zip = new ZipArchive();
        $zip->open($kit['cale']);

        foreach (['instaleaza.ps1', 'dezinstaleaza.ps1', 'CITESTE-MA.txt'] as $fisier) {
            $this->assertStringStartsWith("\xEF\xBB\xBF", $zip->getFromName($fisier), $fisier . ' nu are BOM');
        }

        // Fisierele .bat trebuie sa ramana fara BOM.
        $this->assertStringStartsNotWith("\xEF\xBB\xBF", $zip->getFromName('porneste-manual.bat'));

        $zip->close();
    }

    /**
     * PowerShell trateaza ghilimelele romanesti drept delimitatori de sir, deci
     * scripturile nu trebuie sa le contina.
     */
    public function test_scripturile_nu_contin_ghilimele_care_rup_parsarea(): void
    {
        $kit = $this->construieste();

        $zip = new ZipArchive();
        $zip->open($kit['cale']);

        foreach (['instaleaza.ps1', 'dezinstaleaza.ps1'] as $fisier) {
            $continut = $zip->getFromName($fisier);

            $this->assertStringNotContainsString('„', $continut, $fisier . ' conține ghilimele de deschidere');
            $this->assertStringNotContainsString('”', $continut, $fisier . ' conține ghilimele de închidere');
        }

        $zip->close();
    }

    /** Textele vizibile utilizatorului nu mai folosesc jargonul „bridge”. */
    public function test_instructiunile_nu_folosesc_jargon(): void
    {
        $kit = $this->construieste();

        $zip = new ZipArchive();
        $zip->open($kit['cale']);

        foreach (['CITESTE-MA.txt', 'instaleaza.ps1', 'dezinstaleaza.ps1', 'porneste-manual.bat'] as $fisier) {
            $this->assertStringNotContainsStringIgnoringCase(
                'bridge',
                $zip->getFromName($fisier),
                $fisier . ' încă folosește cuvântul „bridge”'
            );
        }

        $zip->close();
    }
}
