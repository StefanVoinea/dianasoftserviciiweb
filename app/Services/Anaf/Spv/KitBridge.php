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
        'server.php', 'curl-talcuri.php', 'agent.php', 'agent-functii.php', 'agent-lucreaza.php',
        'agent-actualizare.php',
        'cert-info.ps1', 'pin-test.ps1', 'sign-pdf.ps1', 'merge-pdf.ps1',
        // Fara ea, declaratiile venite ca PDF nu pot fi citite deloc
        'pdf-info.ps1',
        'imprimante.ps1', 'print-pdf.ps1', 'itextsharp.dll',
    ];

    /** Scripturile si documentatia, din sablonul kitului. */
    protected const FISIERE_KIT = [
        // .bat-urile sunt cele pe care le apasa omul: Windows nu ruleaza .ps1 la dublu clic
        'instaleaza.bat', 'dezinstaleaza.bat',
        'porneste-manual.bat', 'opreste-manual.bat', 'porneste-ascuns.vbs', 'porneste-agent.bat',
        'instaleaza.ps1', 'dezinstaleaza.ps1',
        // Verificarea de pe calculatorul clientului, cand ceva nu merge
        'diagnoza.bat', 'diagnoza.ps1',
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
    /** Curl-ul dus la client, daca a fost pus langa bridge. */
    public function curlPropriu(): ?string
    {
        return is_file($this->caleBridge . DIRECTORY_SEPARATOR . 'curl.exe') ? 'curl.exe' : null;
    }

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

        /*
         * Curl-ul, daca a fost pus langa bridge.
         *
         * Cel din Windows e vechi de cati ani are calculatorul, iar in el s-au
         * indreptat, de la o versiune la alta, felurite necazuri ale lui
         * Schannel cu certificatele de pe token: la un client cu 8.13 legatura
         * cu ANAF cadea, la altul cu 8.21 mergea. Nu putem cere nimanui sa-si
         * innoiasca Windows-ul, dar putem duce noi programul cu care lucram.
         *
         * Lipsa lui nu opreste kitul: fara el se ia cel din Windows, ca pana
         * acum.
         */
        $curlPropriu = $this->caleBridge . DIRECTORY_SEPARATOR . 'curl.exe';

        if (is_file($curlPropriu)) {
            $zip->addFile($curlPropriu, 'curl.exe');
        }

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

            /*
             * Nu toate fisierele suporta BOM-ul.
             *
             *   .bat — cmd.exe l-ar afisa ca text parazit, la fiecare rulare;
             *   .vbs — motorul VBScript se opreste din primul caracter, cu
             *          „Invalid character (1, 1)", iar sarcina programata iese
             *          cu codul 1 fara sa porneasca nimic. Asa a ramas un
             *          calculator intreg fara program local, si in jurnal scria
             *          doar „nu asculta nimeni pe portul acela".
             */
            $faraBom = ['bat', 'vbs'];

            $zip->addFromString(
                $fisier,
                in_array(pathinfo($fisier, PATHINFO_EXTENSION), $faraBom, true)
                    ? $continut
                    : $this->cuBom($continut)
            );
        }

        /*
         * Versiunea programului merge cu kitul: dupa ea stie agentul, la prima
         * panda, daca are ce innoi. Fara ea, orice instalare noua ar cere o
         * innoire pe care tocmai a primit-o.
         */
        $zip->addFromString('versiune.txt', app(\App\Services\Anaf\Bridge\ActualizareBridge::class)->versiunea());

        $zip->addFromString('configurare.env', $this->configurare($token));
        $zip->addFromString('CITESTE-MA.txt', $this->cuBom($this->documentatie($token, $port)));

        $zip->close();

        return [
            'cale' => $cale,
            // Numele modulului, ca omul sa stie ce a descarcat si de unde vine.
            'nume' => 'kit_spv_curier.zip',
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

    /** Jetonul de inrolare al clientului pentru care se face kitul. */
    protected function jetonInrolare(): string
    {
        $client = \App\Support\ContextCompanie::curenta();

        return $client ? app(Licente::class)->jetonInrolare($client) : '';
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
            '# Cu jetonul de mai jos, programul spune serverului al cui client este,',
            '# la prima pornire, și își anunță singur certificatele de pe token — nu',
            '# aveți nimic de tastat în aplicație. E semnat de server și nu folosește',
            '# nimănui altcuiva.',
            'PUNTE_INROLARE=' . $this->jetonInrolare(),
            '',
            '# Cât de nou poate fi TLS-ul către ANAF.',
            '#',
            '# Cu certificat de pe token, Schannel-ul Windows și TLS 1.3 se înțeleg prost:',
            '# cheile se schimbă în mijlocul răspunsului, iar sesiunea se stinge înainte de',
            '# capătul lui (SEC_E_CONTEXT_EXPIRED). De aceea se ține pe 1.2, pe care ANAF îl',
            '# vorbește de ani de zile. Gol = fără margine, dacă ANAF trece cândva pe 1.3.',
            'SPV_TLS_MAX=1.2',
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
