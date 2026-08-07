<?php

namespace Tests\Unit;

use Tests\TestCase;

/**
 * Programul de la client se descurca cu PHP-ul mic din kit.
 *
 * PHP-ul acela e mic dinadins: are mbstring si openssl, atat cat ii trebuie
 * programului, si nu se instaleaza nimic pe calculatorul clientului. Orice
 * altceva — zip, dom, sqlite, gd — nu exista acolo.
 *
 * Lipsa lor nu se vede aici: pe serverul de dezvoltare si pe cel al aplicatiei
 * PHP-ul e intreg, deci un „new ZipArchive" trece toate probele si cade abia la
 * client, in mijlocul unei innoiri. Asa s-a si intamplat: innoirea automata a
 * cazut cu „Class ZipArchive not found" chiar pe calculatoarele pentru care
 * fusese facuta. De aceea proba asta citeste fisierele clientului si se uita ce
 * cer de la PHP.
 */
class PhpDeLaClientTest extends TestCase
{
    /** Extensiile care chiar sunt in php.ini din kit. */
    protected const INGADUITE = ['mbstring', 'openssl'];

    /**
     * Ce se cauta, si de la ce extensie vine.
     *
     * Nu e lista intreaga a PHP-ului — e lista lucrurilor pe care cineva le-ar
     * scrie fara sa se gandeasca, fiindca pe calculatorul lui merg.
     */
    protected const SEMNE = [
        'zip' => '/\bnew\s+ZipArchive\b|\bzip_open\s*\(/i',
        'dom' => '/\bnew\s+DOM(Document|XPath)\b/i',
        'simplexml' => '/\bsimplexml_load_(string|file)\s*\(|\bnew\s+SimpleXMLElement\b/i',
        'curl' => '/\bcurl_(init|setopt|exec|multi_init)\s*\(/i',
        'gd' => '/\bimagecreate[a-z]*\s*\(|\bimagepng\s*\(/i',
        'intl' => '/\bnew\s+(NumberFormatter|IntlDateFormatter|Collator)\b/i',
        'sqlite3' => '/\bnew\s+SQLite3\b/i',
        'pdo' => '/\bnew\s+PDO\b/i',
        'bcmath' => '/\bbc(add|sub|mul|div|comp)\s*\(/i',
        'gmp' => '/\bgmp_[a-z_]+\s*\(/i',
        'imagick' => '/\bnew\s+Imagick\b/i',
        'iconv' => '/\biconv\s*\(/i',
        'fileinfo' => '/\bfinfo_open\s*\(|\bnew\s+finfo\b/i',
        'soap' => '/\bnew\s+SoapClient\b/i',
    ];

    /** @return array<string, string> numele fisierului => cuprinsul lui */
    protected function fisiereleClientului(): array
    {
        $fisiere = [];

        foreach (glob(base_path('spv-bridge/*.php')) as $cale) {
            $fisiere[basename($cale)] = file_get_contents($cale);
        }

        return $fisiere;
    }

    /** Ce scrie in php.ini din kit e chiar ce ne bizuim aici. */
    public function test_php_ul_din_kit_are_doar_extensiile_stiute(): void
    {
        $ini = base_path('spv-bridge/php/php.ini');

        if (!is_file($ini)) {
            $this->markTestSkipped('PHP-ul pregătit nu se află lângă bridge pe acest calculator.');
        }

        preg_match_all('/^\s*extension\s*=\s*([a-z0-9_]+)/mi', file_get_contents($ini), $potriviri);

        $gasite = array_map('strtolower', $potriviri[1]);

        sort($gasite);
        $asteptate = self::INGADUITE;
        sort($asteptate);

        $this->assertSame(
            $asteptate,
            $gasite,
            'php.ini din kit s-a schimbat: potriviți și lista din această probă, ca ea să însemne ceva'
        );
    }

    /** Niciun fisier al clientului nu cere o extensie pe care el n-o are. */
    public function test_nimic_nu_cere_o_extensie_pe_care_clientul_n_o_are(): void
    {
        $vinovate = [];

        foreach ($this->fisiereleClientului() as $nume => $cuprins) {
            // Comentariile pot pomeni orice — chiar si de ce nu se foloseste.
            $cod = preg_replace('!/\*.*?\*/|//[^\n]*|\#[^\n]*!s', '', $cuprins);

            foreach (self::SEMNE as $extensie => $tipar) {
                if (in_array($extensie, self::INGADUITE, true)) {
                    continue;
                }

                if (preg_match($tipar, $cod)) {
                    $vinovate[] = $nume . ' cere extensia „' . $extensie . '"';
                }
            }
        }

        $this->assertSame(
            [],
            $vinovate,
            "Programul de la client cere extensii pe care PHP-ul lui nu le are:\n  "
                . implode("\n  ", $vinovate)
                . "\nOri se scrie altfel, ori extensia intră în php.ini din kit — dar atunci"
                . ' calculatoarele deja instalate rămân fără ea.'
        );
    }

    /**
     * Legatura cu ANAF si cu aplicatia merge prin curl.exe, program aparte, nu
     * prin extensia curl: ea lipseste, iar programul are nevoie de Schannel ca
     * sa ajunga la cheia de pe token.
     */
    public function test_legatura_merge_prin_programul_curl_nu_prin_extensie(): void
    {
        $server = file_get_contents(base_path('spv-bridge/server.php'));

        $this->assertStringNotContainsString('curl_init(', $server);
        $this->assertStringContainsString('curl', $server, 'programul curl.exe rămâne calea către ANAF');
    }
}
