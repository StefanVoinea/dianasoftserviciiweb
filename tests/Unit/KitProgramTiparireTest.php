<?php

namespace Tests\Unit;

use App\Services\Anaf\Spv\KitBridge;
use Tests\TestCase;
use ZipArchive;

/**
 * Programul de tiparit PDF-uri intra in kit daca a fost pus langa bridge, iar
 * lipsa lui nu opreste construirea kitului.
 */
class KitProgramTiparireTest extends TestCase
{
    protected $dosar;

    protected function setUp(): void
    {
        parent::setUp();

        $this->dosar = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'kit-proba-' . bin2hex(random_bytes(4));
        mkdir($this->dosar);
        mkdir($this->dosar . DIRECTORY_SEPARATOR . 'kit');

        // Fisierele fara de care kitul nu se poate construi
        foreach (['server.php', 'curl-talcuri.php', 'agent.php', 'agent-functii.php', 'agent-lucreaza.php', 'agent-actualizare.php', 'cert-info.ps1', 'pin-test.ps1', 'sign-pdf.ps1', 'merge-pdf.ps1', 'pdf-info.ps1', 'imprimante.ps1', 'print-pdf.ps1', 'itextsharp.dll'] as $fisier) {
            file_put_contents($this->dosar . DIRECTORY_SEPARATOR . $fisier, 'proba');
        }
    }

    protected function tearDown(): void
    {
        $this->stergeDosarul($this->dosar);

        parent::tearDown();
    }

    protected function stergeDosarul(string $dosar): void
    {
        foreach (glob($dosar . DIRECTORY_SEPARATOR . '*') as $fisier) {
            is_dir($fisier) ? $this->stergeDosarul($fisier) : @unlink($fisier);
        }

        @rmdir($dosar);
    }

    /** @return array<int, string> numele fisierelor din arhiva */
    protected function continutulKitului(): array
    {
        $rezultat = (new KitBridge($this->dosar))->construieste('token-de-proba');

        $zip = new ZipArchive();
        $zip->open($rezultat['cale']);

        $nume = [];

        for ($i = 0; $i < $zip->numFiles; $i++) {
            $nume[] = $zip->getNameIndex($i);
        }

        $zip->close();
        @unlink($rezultat['cale']);

        return $nume;
    }

    public function test_kitul_se_construieste_si_fara_program_de_tiparit(): void
    {
        $fisiere = $this->continutulKitului();

        $this->assertContains('print-pdf.ps1', $fisiere);
        $this->assertNotContains('PDFtoPrinter.exe', $fisiere);
        $this->assertNull((new KitBridge($this->dosar))->programTiparire());
    }

    /** Pus langa bridge, programul ajunge singur pe calculatorul clientului. */
    public function test_programul_de_tiparit_intra_in_kit_daca_exista(): void
    {
        file_put_contents($this->dosar . DIRECTORY_SEPARATOR . 'PDFtoPrinter.exe', 'MZ program');

        $this->assertSame('PDFtoPrinter.exe', (new KitBridge($this->dosar))->programTiparire());
        $this->assertContains('PDFtoPrinter.exe', $this->continutulKitului());
    }

    /** Cu PHP-ul pregatit, kitul il duce intreg pe calculatorul clientului. */
    public function test_php_ul_pregatit_intra_in_kit_cu_tot_ce_are(): void
    {
        mkdir($this->dosar . DIRECTORY_SEPARATOR . 'php');
        mkdir($this->dosar . DIRECTORY_SEPARATOR . 'php' . DIRECTORY_SEPARATOR . 'ext');

        file_put_contents($this->dosar . DIRECTORY_SEPARATOR . 'php' . DIRECTORY_SEPARATOR . 'php.exe', 'MZ');
        file_put_contents($this->dosar . DIRECTORY_SEPARATOR . 'php' . DIRECTORY_SEPARATOR . 'php.ini', 'extension=mbstring');
        file_put_contents(
            $this->dosar . DIRECTORY_SEPARATOR . 'php' . DIRECTORY_SEPARATOR . 'ext' . DIRECTORY_SEPARATOR . 'php_mbstring.dll',
            'MZ'
        );

        $fisiere = $this->continutulKitului();

        $this->assertContains('php/php.exe', $fisiere);
        $this->assertContains('php/php.ini', $fisiere);
        $this->assertContains('php/ext/php_mbstring.dll', $fisiere, 'extensiile trebuie să meargă cu subdosar cu tot');
    }

    /** Fara PHP pregatit, kitul se face la fel — se cauta unul din sistem. */
    public function test_kitul_se_construieste_si_fara_php(): void
    {
        $fisiere = $this->continutulKitului();

        $this->assertContains('server.php', $fisiere);
        $this->assertNotContains('php/php.exe', $fisiere);
    }

    /** Configurarea ramane goala: bridge-ul gaseste programul singur. */
    public function test_configurarea_nu_cere_calea_scrisa_de_mana(): void
    {
        file_put_contents($this->dosar . DIRECTORY_SEPARATOR . 'PDFtoPrinter.exe', 'MZ program');

        $rezultat = (new KitBridge($this->dosar))->construieste('token-de-proba');

        $zip = new ZipArchive();
        $zip->open($rezultat['cale']);
        $configurare = $zip->getFromName('configurare.env');
        $zip->close();
        @unlink($rezultat['cale']);

        $this->assertStringContainsString('IMPRIMARE_EXE=', $configurare);
        $this->assertStringNotContainsString('IMPRIMARE_EXE=C:', $configurare);
    }

    /**
     * Curl-ul din kit are intaietate fata de cel din Windows.
     *
     * Cel din Windows e vechi de cati ani are calculatorul, iar in el s-au
     * indreptat de la o versiune la alta necazuri ale lui Schannel cu
     * certificatele de pe token: la un client cu 8.13 legatura cu ANAF cadea, la
     * altul cu 8.21 mergea. Nu putem cere nimanui sa-si innoiasca Windows-ul.
     */
    public function test_curlul_pus_langa_bridge_intra_in_kit(): void
    {
        $this->assertNull((new KitBridge($this->dosar))->curlPropriu());
        $this->assertNotContains('curl.exe', $this->continutulKitului());

        file_put_contents($this->dosar . DIRECTORY_SEPARATOR . 'curl.exe', 'MZ program');

        $this->assertSame('curl.exe', (new KitBridge($this->dosar))->curlPropriu());
        $this->assertContains('curl.exe', $this->continutulKitului());
    }

    /**
     * Curl-ul chiar se afla langa bridge, in depozit.
     *
     * Fara el, kitul pleaca fara curl si fiecare client ramane cu cel din
     * Windows-ul lui — adica exact cu deosebirea din care s-a nascut necazul.
     */
    public function test_curlul_e_pus_langa_bridge_in_depozit(): void
    {
        $cale = base_path('spv-bridge/curl.exe');

        $this->assertFileExists($cale, 'kitul trebuie să-și ducă propriul curl');
        $this->assertStringStartsWith('MZ', file_get_contents($cale, false, null, 0, 2));
    }

    /** Iar programul il alege pe el, cand e acolo. */
    public function test_programul_alege_curlul_de_langa_el(): void
    {
        $server = file_get_contents(base_path('spv-bridge/server.php'));

        $inceput = strpos($server, "'curl'       =>");
        $bucata = substr($server, $inceput, 400);

        $this->assertStringContainsString("__DIR__ . DIRECTORY_SEPARATOR . 'curl.exe'", $bucata);
        $this->assertStringContainsString('System32', $bucata, 'fără el, se ia tot cel din Windows');
    }
}
