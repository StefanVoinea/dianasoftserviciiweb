<?php

namespace App\Services\Anaf\Spv;

use App\Services\Anaf\Bridge\Licente;
use ZipArchive;

/**
 * Construieste arhiva de instalare a bridge-ului: fisierele lui, configurarea
 * (cu token propriu, generat pentru calculatorul respectiv) si scripturile de
 * instalare ca sarcina programata la logon.
 */
class KitBridge
{
    /** Fisierele bridge-ului, luate din instalarea curenta. */
    protected const FISIERE_BRIDGE = [
        'server.php', 'agent.php', 'agent-functii.php',
        'cert-info.ps1', 'sign-pdf.ps1', 'merge-pdf.ps1',
        'imprimante.ps1', 'print-pdf.ps1', 'itextsharp.dll',
    ];

    /** Scripturile si documentatia, din sablonul kitului. */
    protected const FISIERE_KIT = [
        // .bat-ul e cel pe care il apasa omul: Windows nu ruleaza .ps1 la dublu clic
        'instaleaza.bat', 'instaleaza.ps1', 'dezinstaleaza.ps1', 'porneste-manual.bat',
    ];

    /**
     * Programe de tiparit PDF-uri, luate in kit daca sunt puse langa bridge.
     *
     * Nu sunt ale noastre si nu vin cu aplicatia; pus in spv-bridge/, programul
     * ajunge singur pe calculatorul clientului, iar bridge-ul il gaseste acolo
     * fara nicio configurare.
     */
    protected const PROGRAME_TIPARIRE = ['PDFtoPrinter.exe', 'SumatraPDF.exe'];

    protected $caleBridge;

    public function __construct(?string $caleBridge = null)
    {
        $this->caleBridge = $caleBridge ?: base_path('spv-bridge');
    }

    /** Adauga in arhiva un dosar intreg, cu tot ce e in el. */
    protected function adaugaDosarul(ZipArchive $zip, string $sursa, string $subdosar): void
    {
        if (!is_dir($sursa)) {
            return;
        }

        $fisiere = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($sursa, \FilesystemIterator::SKIP_DOTS)
        );

        foreach ($fisiere as $fisier) {
            if (!$fisier->isFile()) {
                continue;
            }

            $relativa = substr($fisier->getPathname(), strlen($sursa) + 1);

            $zip->addFile($fisier->getPathname(), $subdosar . '/' . str_replace('\\', '/', $relativa));
        }
    }

    /** Versiunea PHP-ului din kit, daca a fost pregatit. */
    public function versiuneaPhp(): ?string
    {
        $exe = $this->caleBridge . DIRECTORY_SEPARATOR . 'php' . DIRECTORY_SEPARATOR . 'php.exe';

        if (!is_file($exe)) {
            return null;
        }

        exec('"' . $exe . '" -n -r "echo PHP_VERSION;" 2>&1', $iesire, $cod);

        return $cod === 0 ? trim(implode('', $iesire)) : null;
    }

    /** Programul de tiparit gasit langa bridge, daca a fost pus acolo. */
    public function programTiparire(): ?string
    {
        foreach (self::PROGRAME_TIPARIRE as $fisier) {
            if (is_file($this->caleBridge . DIRECTORY_SEPARATOR . $fisier)) {
                return $fisier;
            }
        }

        return null;
    }

    /**
     * @return array{cale: string, nume: string, token: string}
     */
    public function construieste(?string $token = null, int $port = 8099): array
    {
        $token = $token ?: bin2hex(random_bytes(32));

        $cale = tempnam(sys_get_temp_dir(), 'kit') . '.zip';
        $zip = new ZipArchive();

        if ($zip->open($cale, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            throw new SpvException('Arhiva kitului nu a putut fi creată.');
        }

        foreach (self::FISIERE_BRIDGE as $fisier) {
            $sursa = $this->caleBridge . DIRECTORY_SEPARATOR . $fisier;

            if (!is_file($sursa)) {
                $zip->close();
                @unlink($cale);

                throw new SpvException('Lipsește fișierul „' . $fisier . '” necesar în kitul de instalare.');
            }

            $zip->addFile($sursa, $fisier);
        }

        /*
         * Cheia publica cu care programul verifica licenta si jetoanele de
         * comanda. Fara ea, programul merge nelicentiat, ca instalarile vechi —
         * de aceea kiturile noi o poarta mereu.
         */
        $cheiePublica = app(Licente::class)->cheiePublica();

        if ($cheiePublica !== null) {
            $zip->addFromString('cheie-publica.pem', $cheiePublica);
        }

        /*
         * PHP-ul din kit, pregatit cu `php artisan anaf:kit-php`. Cu el,
         * clientul nu mai instaleaza nimic: dezarhiveaza si merge. Fara el,
         * scripturile cauta un PHP din sistem, ca pana acum.
         */
        $this->adaugaDosarul($zip, $this->caleBridge . DIRECTORY_SEPARATOR . 'php', 'php');

        // Programul de tiparit, daca a fost pus langa bridge. Lipsa lui nu
        // opreste kitul: fara el se tipareste prin programul asociat PDF-urilor.
        foreach (self::PROGRAME_TIPARIRE as $fisier) {
            $sursa = $this->caleBridge . DIRECTORY_SEPARATOR . $fisier;

            if (is_file($sursa)) {
                $zip->addFile($sursa, $fisier);
            }
        }

        foreach (self::FISIERE_KIT as $fisier) {
            $sursa = $this->caleBridge . DIRECTORY_SEPARATOR . 'kit' . DIRECTORY_SEPARATOR . $fisier;

            if (!is_file($sursa)) {
                continue;
            }

            $continut = file_get_contents($sursa);

            // Fisierele .bat raman fara BOM: cmd.exe l-ar afisa ca text parazit.
            $zip->addFromString(
                $fisier,
                pathinfo($fisier, PATHINFO_EXTENSION) === 'bat' ? $continut : $this->cuBom($continut)
            );
        }

        $zip->addFromString('configurare.env', $this->configurare($token));
        $zip->addFromString('CITESTE-MA.txt', $this->cuBom($this->documentatie($token, $port)));

        $zip->close();

        return [
            'cale' => $cale,
            'nume' => 'kit-acces-token-anaf-' . now()->format('Ymd-His') . '.zip',
            'token' => $token,
        ];
    }

    /**
     * Windows PowerShell 5.1 si Notepad citesc fisierele ca ANSI daca nu au BOM,
     * ceea ce ar strica diacriticele din mesaje si documentatie.
     */
    protected function cuBom(string $continut): string
    {
        $bom = "\xEF\xBB\xBF";

        return strncmp($continut, $bom, 3) === 0 ? $continut : $bom . $continut;
    }

    protected function configurare(string $token): string
    {
        $linii = [
            '# Configurare — valabilă doar pentru acest calculator.',
            '# Codul de acces de mai jos trebuie introdus și în aplicație,',
            '# la SPV -> Certificate digitale.',
            '',
            'SPV_BRIDGE_TOKEN=' . $token,
            '',
            '# Amprenta certificatului implicit. Poate rămâne goală: aplicația trimite',
            '# amprenta cerută la fiecare operație, deci pe același calculator se pot',
            '# folosi mai multe tokene, pe rând.',
            'SPV_CERT_THUMBPRINT=',
            '',
            '# Dosarul în care se strâng documentele fiscale: declarațiile semnate,',
            '# recipisele și documentele aduse din SPV. Se poate pune pe orice disc',
            '# sau pe un folder din rețea. Structura dinăuntru o face aplicația:',
            '#   <dosar>\\<Denumire firmă (CUI)>\\<Tip declarație>\\...',
            '# Lăsat gol, se folosește subdosarul "arhiva" de lângă program.',
            'ARHIVA_CALE=',
            '',
            '# Program care tipărește PDF-uri (PDFtoPrinter.exe sau SumatraPDF.exe).',
            '# Lăsat gol — cum e mai jos — programul se caută singur în dosarul în',
            '# care a fost instalat acest fișier. Kitul îl aduce acolo, deci de',
            '# obicei nu aveți nimic de completat aici.',
            'IMPRIMARE_EXE=',
            '',
            '# Adresa aplicației. Cu ea, programul întreabă singur serverul ce are de',
            '# făcut — pe 443, ca orice pagină de internet — deci nu trebuie deschis',
            '# niciun port pe routerul dumneavoastră. Ca să meargă așa, certificatul',
            '# trebuie pus în aplicație pe legătura „prin tunel".',
            'PUNTE_SERVER=' . rtrim(config('app.url'), '/'),
            '',
            '# Adresele serviciilor ANAF (se modifică doar dacă ANAF le schimbă).',
            'SPV_BASE_URL=' . config('anaf.spv.base_url'),
            'ANAF_URL_DEPUNERE=' . config('anaf.declaratii.url_depunere'),
            '',
        ];

        return implode("\r\n", $linii);
    }

    protected function documentatie(string $token, int $port): string
    {
        $sablon = $this->caleBridge . DIRECTORY_SEPARATOR . 'kit' . DIRECTORY_SEPARATOR . 'CITESTE-MA.txt';
        $text = is_file($sablon) ? file_get_contents($sablon) : 'Kit bridge SPV.';

        $inlocuiri = [
            '{{APLICATIE}}' => config('app.name'),
            '{{DATA}}' => now()->format('d.m.Y H:i'),
            '{{TOKEN}}' => $token,
            '{{PORT}}' => (string) $port,
            '{{IP_EXEMPLU}}' => '192.168.1.20',
        ];

        return str_replace(array_keys($inlocuiri), array_values($inlocuiri), $text);
    }
}
