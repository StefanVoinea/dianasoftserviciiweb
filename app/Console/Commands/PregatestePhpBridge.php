<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

/**
 * Pregateste un PHP mic, numai cat ii trebuie bridge-ului, ca sa intre in kit.
 *
 * Fara el, clientul trebuie sa instaleze PHP pe fiecare calculator cu token.
 * Cu el, kitul se dezarhiveaza si merge — programul foloseste PHP-ul de langa
 * el, nu unul din sistem.
 *
 * Se copiaza doar ce foloseste programul: php.exe, biblioteca lui si extensia
 * mbstring. Restul (php-cgi, phpdbg, celelalte extensii) ar umfla arhiva
 * degeaba.
 */
class PregatestePhpBridge extends Command
{
    protected $signature = 'anaf:kit-php
                            {--sursa= : dosarul unei instalari PHP pentru Windows (implicit, cel care ruleaza acum)}
                            {--forteaza : rescrie dosarul php din spv-bridge, daca exista}';

    protected $description = 'Pregătește un PHP minimal în spv-bridge/php, ca să intre în kitul de instalare';

    /**
     * Extensiile de care are nevoie programul: mbstring (mb_substr) si openssl,
     * cu care verifica licenta si jetoanele de comanda primite de la server.
     */
    protected const EXTENSII = ['php_mbstring.dll', 'php_openssl.dll'];

    /**
     * Bibliotecile de sistem ale compilatorului. Windows le are aproape mereu,
     * dar copiate langa program merge si acolo unde lipsesc.
     */
    protected const RUNTIME = [
        'vcruntime140.dll', 'vcruntime140_1.dll', 'msvcp140.dll',
        // Bibliotecile openssl, fara de care extensia nu porneste
        'libcrypto-1_1-x64.dll', 'libssl-1_1-x64.dll', 'libcrypto-3-x64.dll', 'libssl-3-x64.dll',
    ];

    public function handle(): int
    {
        $sursa = rtrim($this->option('sursa') ?: dirname(PHP_BINARY), '\\/');
        $destinatie = base_path('spv-bridge' . DIRECTORY_SEPARATOR . 'php');

        if (!is_file($sursa . DIRECTORY_SEPARATOR . 'php.exe')) {
            $this->error('Nu găsesc php.exe în ' . $sursa);
            $this->line('Dați dosarul unei instalări PHP pentru Windows: --sursa="C:\\php"');

            return 1;
        }

        if (is_dir($destinatie) && !$this->option('forteaza')) {
            $this->warn('Dosarul ' . $destinatie . ' există deja. Folosiți --forteaza ca să-l rescrieți.');

            return 1;
        }

        $this->pregatesteDosarul($destinatie);

        $copiate = $this->copiaza($sursa, $destinatie);

        if ($copiate === []) {
            $this->error('Nu s-a putut copia nimic din ' . $sursa);

            return 1;
        }

        file_put_contents($destinatie . DIRECTORY_SEPARATOR . 'php.ini', $this->configurare());

        return $this->verifica($destinatie, $sursa);
    }

    protected function pregatesteDosarul(string $destinatie): void
    {
        foreach ([$destinatie, $destinatie . DIRECTORY_SEPARATOR . 'ext'] as $dosar) {
            if (!is_dir($dosar)) {
                mkdir($dosar, 0777, true);
            }
        }

        // La rescriere se sterge ce era, ca sa nu ramana fisiere de la o versiune veche.
        foreach (glob($destinatie . DIRECTORY_SEPARATOR . '*.*') as $fisier) {
            @unlink($fisier);
        }

        foreach (glob($destinatie . DIRECTORY_SEPARATOR . 'ext' . DIRECTORY_SEPARATOR . '*.*') as $fisier) {
            @unlink($fisier);
        }
    }

    /** @return array<int, string> fisierele copiate */
    protected function copiaza(string $sursa, string $destinatie): array
    {
        $copiate = [];

        // php.exe si biblioteca lui (php8ts.dll / php7ts.dll, dupa versiune)
        $deLuat = array_merge(
            [$sursa . DIRECTORY_SEPARATOR . 'php.exe'],
            glob($sursa . DIRECTORY_SEPARATOR . 'php?ts.dll') ?: [],
            glob($sursa . DIRECTORY_SEPARATOR . 'php?.dll') ?: []
        );

        foreach (self::RUNTIME as $biblioteca) {
            $langaPhp = $sursa . DIRECTORY_SEPARATOR . $biblioteca;
            $dinSistem = getenv('SystemRoot') . DIRECTORY_SEPARATOR . 'System32' . DIRECTORY_SEPARATOR . $biblioteca;

            if (is_file($langaPhp)) {
                $deLuat[] = $langaPhp;
            } elseif (is_file($dinSistem)) {
                $deLuat[] = $dinSistem;
            }
        }

        /*
         * Licenta PHP si nota despre bibliotecile redistribuite. Licenta PHP
         * cere ca ele sa insoteasca binarele, iar noi le trimitem la fiecare
         * client, in kit — deci se copiaza de fiecare data, nu se tin de mana.
         */
        foreach (['license.txt' => 'LICENSE.txt', 'readme-redist-bins.txt' => 'README-redistribuire.txt'] as $din => $in) {
            $cale = $sursa . DIRECTORY_SEPARATOR . $din;

            if (is_file($cale) && copy($cale, $destinatie . DIRECTORY_SEPARATOR . $in)) {
                $copiate[] = $in;
            }
        }

        foreach ($deLuat as $fisier) {
            if (is_file($fisier) && copy($fisier, $destinatie . DIRECTORY_SEPARATOR . basename($fisier))) {
                $copiate[] = basename($fisier);
            }
        }

        foreach (self::EXTENSII as $extensie) {
            $fisier = $sursa . DIRECTORY_SEPARATOR . 'ext' . DIRECTORY_SEPARATOR . $extensie;

            if (is_file($fisier) && copy($fisier, $destinatie . DIRECTORY_SEPARATOR . 'ext' . DIRECTORY_SEPARATOR . $extensie)) {
                $copiate[] = 'ext/' . $extensie;
            }
        }

        return $copiate;
    }

    /**
     * Configurarea PHP-ului din kit.
     *
     * Valorile implicite ale PHP-ului nu sunt bune aici: un lot de declaratii
     * inseamna zeci de fisiere intr-o singura cerere, iar „max_file_uploads = 20"
     * le-ar taia tacut pe cele de peste, fara nicio eroare.
     */
    protected function configurare(): string
    {
        return implode("\r\n", [
            '; Configurare pentru programul de acces la token. Nu o folosiți pentru altceva.',
            'extension_dir = "ext"',
            'extension = mbstring',
            'extension = openssl',
            '',
            '; Un lot de tipărit poate avea zeci de documente, într-o singură cerere.',
            'max_file_uploads = 300',
            'upload_max_filesize = 64M',
            'post_max_size = 512M',
            'memory_limit = 512M',
            '',
            '; Semnarea și tipărirea așteaptă răspuns de la token și de la imprimantă.',
            'max_execution_time = 0',
            'default_socket_timeout = 300',
            '',
            'date.timezone = Europe/Bucharest',
            'display_errors = Off',
            'log_errors = On',
            '',
        ]);
    }

    protected function verifica(string $destinatie, string $sursa): int
    {
        $exe = $destinatie . DIRECTORY_SEPARATOR . 'php.exe';

        // Se cer amândouă: fără openssl, programul nu poate verifica licența.
        $verificare = 'echo PHP_VERSION, \'|\', (int) function_exists(\'mb_substr\'),'
            . ' (int) function_exists(\'openssl_verify\');';

        exec('"' . $exe . '" -n -c "' . $destinatie . DIRECTORY_SEPARATOR . 'php.ini" -r "'
            . $verificare . '" 2>&1', $iesire, $cod);

        $raspuns = trim(implode(' ', $iesire));

        if ($cod !== 0 || strpos($raspuns, '|11') === false) {
            $this->error('PHP-ul copiat nu funcționează: ' . $raspuns);
            $this->line('Lipsesc bibliotecile Visual C++ sau cele de openssl de pe acest calculator.');

            return 1;
        }

        [$versiune] = explode('|', $raspuns);

        $marime = 0;

        foreach (['*.*', 'ext' . DIRECTORY_SEPARATOR . '*.*'] as $tipar) {
            foreach (glob($destinatie . DIRECTORY_SEPARATOR . $tipar) as $fisier) {
                $marime += filesize($fisier);
            }
        }

        $this->info('PHP ' . $versiune . ' pregătit în spv-bridge/php (' . round($marime / 1048576, 1) . ' MB).');
        $this->line('Luat din: ' . $sursa);
        $this->line('Intră singur în kitul de instalare, iar programul îl folosește pe el.');

        return 0;
    }
}
