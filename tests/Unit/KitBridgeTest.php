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
            // Fara ea, omul de la client n-are cu ce afla de ce nu merge
            'diagnoza.bat', 'diagnoza.ps1',
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
     * BOM-ul se pune si in fisierele din depozit, nu doar la impachetare.
     *
     * Fara el, un script copiat de-a dreptul pe un calculator (cum se face la
     * dezvoltare si la reparatii pe teren) e citit de PowerShell 5.1 ca ANSI:
     * liniuta — ajunge sa se termine cu octetul pe care PowerShell il ia drept
     * ghilimea, iar scriptul nu se mai compileaza deloc. Instalarea nu porneste
     * si nimeni nu afla de ce.
     */
    /**
     * Kitul duce fiecare script pe care programul stie sa-l cheme.
     *
     * A lipsit pdf-info.ps1: programul il chema la fiecare declaratie venita ca
     * PDF, iar la client PowerShell raspundea „the argument ... does not exist".
     * Fisierul era in depozit, dar nu si in lista kitului — o scapare pe care
     * nimeni n-avea cum s-o vada pana la primul PDF pus in dosarul urmarit.
     */
    public function test_kitul_duce_toate_scripturile_chemate_de_program(): void
    {
        $server = file_get_contents(base_path('spv-bridge/server.php'));

        preg_match_all('/[A-Za-z0-9_-]+\.ps1/', $server, $potriviri);

        $cerute = array_unique($potriviri[0]);

        // Daca tiparul nu mai gaseste nimic, testul n-ar mai pazi nimic.
        $this->assertNotEmpty($cerute, 'Nu s-a găsit niciun script chemat de program.');
        $duse = (new \ReflectionClass(KitBridge::class))->getConstant('FISIERE_BRIDGE');

        foreach ($cerute as $script) {
            $this->assertContains(
                $script,
                $duse,
                'Programul cheamă ' . $script . ', dar kitul nu-l duce la client.'
            );

            $this->assertFileExists(
                base_path('spv-bridge/' . $script),
                $script . ' e trecut în kit, dar lipsește din depozit.'
            );
        }
    }

    public function test_scripturile_din_depozit_au_bom_utf8(): void
    {
        $scripturi = array_merge(
            glob(base_path('spv-bridge/*.ps1')),
            glob(base_path('spv-bridge/kit/*.ps1'))
        );

        $this->assertNotEmpty($scripturi);

        foreach ($scripturi as $cale) {
            $this->assertStringStartsWith(
                "\xEF\xBB\xBF",
                file_get_contents($cale),
                basename($cale) . ' nu are BOM în depozit'
            );
        }
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

        // Toate scripturile kitului, nu doar cele de instalare: aceeasi
        // ghilimea rupe parsarea oriunde ar sta.
        foreach (['instaleaza.ps1', 'dezinstaleaza.ps1', 'diagnoza.ps1'] as $fisier) {
            $continut = $zip->getFromName($fisier);

            $this->assertStringNotContainsString('„', $continut, $fisier . ' conține ghilimele de deschidere');
            $this->assertStringNotContainsString('”', $continut, $fisier . ' conține ghilimele de închidere');
        }

        $zip->close();
    }

    /** Arhiva poarta numele modulului, ca omul sa stie ce a descarcat. */
    public function test_arhiva_se_numeste_dupa_modul(): void
    {
        $this->assertSame('kit_spv_curier.zip', $this->construieste()['nume']);
    }

    /**
     * Agentul se porneste la instalare, nu la urmatoarea autentificare.
     *
     * Altfel, cine tocmai a instalat incearca o operatie si i se spune ca
     * programul de pe calculatorul cu tokenul nu ruleaza.
     */
    public function test_instalarea_porneste_si_agentul(): void
    {
        $kit = $this->construieste();

        $zip = new ZipArchive();
        $zip->open($kit['cale']);
        $script = $zip->getFromName('instaleaza.ps1');
        $zip->close();

        $this->assertStringContainsString('$agentDePornit', $script);
        $this->assertStringContainsString('Start-ScheduledTask -TaskName $agentDePornit', $script);
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
