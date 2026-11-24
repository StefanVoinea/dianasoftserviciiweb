<?php

namespace App\Services\Anaf\Declaratii;

use App\Services\Anaf\Spv\CertificatService;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;

/**
 * Citirea unui PDF de declarație ANAF: dacă este deja semnat și XML-ul original
 * atașat în el. Declarațiile ANAF poartă XML-ul în interiorul PDF-ului, deci
 * acesta poate fi validat cu DUKIntegrator fără fișierul XML separat.
 *
 * Citirea se face prin programul local (bridge), care are biblioteca de PDF.
 */
class PdfDeclaratie
{
    protected $config;
    protected $certificate;

    public function __construct(array $config, CertificatService $certificate)
    {
        $this->config = $config;
        $this->certificate = $certificate;
    }

    /**
     * [2026-11-24] Citirea PDF-ului chiar pe server, fără drumul până la
     * calculatorul cu tokenul.
     *
     * Un SAF-T mare încape într-un PDF mic: declarația de 1,7 MB a unei firme
     * purta un XML de 61 MB. Programul local îl desfăcea, îl scria ca base64
     * (82 MB) și îl trimitea înapoi prin tunel — nu apuca în cele 60 de
     * secunde, iar omul vedea „s-a pierdut legătura". Declarațiile mari nu se
     * puteau încărca deloc și se depuneau cu mâna.
     *
     * PDF-ul e însă deja aici, iar desfacerea lui ține sub o secundă. Drumul
     * prin programul local rămâne de rezervă, pentru PDF-urile pe care
     * biblioteca de aici nu le poate citi: atunci se întoarce null.
     *
     * @return array{semnat: bool, semnatari: array, xml: ?string, nume_xml: ?string}|null
     */
    protected function citesteAici(string $calePdf): ?array
    {
        // XML-ul desfacut cere memorie cat el de vreo doua ori; limita obisnuita nu ajunge.
        $limita = $this->inOcteti((string) ini_get('memory_limit'));

        if ($limita > 0 && $limita < 1024 * 1048576) {
            @ini_set('memory_limit', '1024M');
        }

        try {
            $document = (new \Smalot\PdfParser\Parser())->parseFile($calePdf);
            $xml = null;

            foreach ($document->getObjects() as $obiect) {
                $antet = $obiect->getHeader();

                if (!$antet) {
                    continue;
                }

                /*
                 * [2026-11-24] „PDF-ul inteligent" (formular XFA, cu drepturi
                 * Adobe Reader) nu se citeste aici.
                 *
                 * Bucatile formularului — „xdp", „datasets", „form" — sunt
                 * tot fluxuri care incep cu „<", si prima dintre ele trecea
                 * drept declaratie: D300, D390 si D394 cadeau la incarcare cu
                 * „Tip de declarație nesuportat (element rădăcină: datasets)".
                 * In plus, drepturile Reader sunt si ele o semnatura cu
                 * „/ByteRange", deci un PDF nesemnat de om ar fi parut semnat.
                 *
                 * Aceste PDF-uri sunt mici si merg, ca inainte, la programul
                 * local, care stie arborele de atasamente. Aici raman cele
                 * simple, scoase de DUKIntegrator — printre ele SAF-T-ul mare.
                 */
                if ($antet->has('XFA') || $antet->has('Perms')) {
                    return null;
                }

                if ($xml !== null || (string) $antet->get('Type') !== 'EmbeddedFile') {
                    continue;
                }

                $continut = (string) $obiect->getContent();
                $inceput = ltrim(substr($continut, 0, 200), "\xEF\xBB\xBF \t\r\n");

                // Atasamentul se cunoaste dupa continut, ca in programul local:
                // XML-ul declaratiei incepe cu „<"; arhiva ZIP a bilantului nu.
                if ($inceput !== '' && $inceput[0] === '<') {
                    $xml = $continut;
                }
            }

            // Inca o plasa: bucata de formular nu e declaratie, oricum ar fi ajuns aici.
            if ($xml !== null && preg_match('/<(xdp:xdp|xfa:datasets|form\s|config\s|template\s)/', substr($xml, 0, 400))) {
                return null;
            }
        } catch (\Throwable $e) {
            return null;
        }

        if ($xml === null) {
            return null;
        }

        return [
            // O semnatura isi scrie intinderea in „/ByteRange"; campul gol de semnatura nu.
            'semnat' => preg_match('/\/ByteRange\s*\[/', (string) file_get_contents($calePdf)) === 1,
            'semnatari' => [],
            'nume_xml' => null,
            'xml' => $xml,
        ];
    }

    /** „512M" → octeți; „-1" (nelimitat) → 0. */
    protected function inOcteti(string $limita): int
    {
        $limita = trim($limita);

        if ($limita === '' || $limita === '-1') {
            return 0;
        }

        $numar = (int) $limita;

        switch (strtoupper(substr($limita, -1))) {
            case 'G':
                return $numar * 1024 * 1048576;
            case 'M':
                return $numar * 1048576;
            case 'K':
                return $numar * 1024;
            default:
                return $numar;
        }
    }

    /**
     * @return array{semnat: bool, semnatari: array, xml: ?string, nume_xml: ?string}
     */
    public function citeste(string $calePdf): array
    {
        if (!is_file($calePdf)) {
            throw new DeclaratieException('PDF-ul nu există: ' . $calePdf);
        }

        // [2026-11-24] Intai aici, pe server: PDF-ul a ajuns oricum la noi.
        $aici = $this->citesteAici($calePdf);

        if ($aici !== null) {
            return $aici;
        }

        $bridge = $this->certificate->bridge();

        try {
            $raspuns = Http::withToken($bridge['token'])
                ->withHeaders(array_filter([
                    'Content-Type' => 'application/pdf',
                    'X-Thumbprint' => $bridge['thumbprint'],
                ]))
                ->timeout($this->config['timeout'])
                ->withBody(file_get_contents($calePdf), 'application/pdf')
                ->post(rtrim($bridge['url'], '/') . '/pdf/info');
        } catch (ConnectionException $e) {
            /*
             * Calculatorul cu tokenul inchis, programul oprit, reteaua cazuta:
             * viata de zi cu zi, nu o defectiune la noi.
             *
             * Lasata sa urce, poticnirea asta darama toata incarcarea — si
             * celelalte fisiere din acelasi teanc, care n-aveau nicio vina —,
             * omul ramanea cu un „cURL error 28" in fata dupa un minut de
             * asteptare, iar la noi pleca si un email de alerta. Spusa pe
             * intelesul lui, ea opreste doar fisierul acesta.
             */
            throw new DeclaratieException(
                'Programul de pe calculatorul cu tokenul nu a răspuns în '
                . (int) $this->config['timeout'] . ' de secunde. Verificați dacă e pornit acolo'
                . ' și dacă tokenul e conectat, apoi încercați din nou.'
            );
        }

        if ($raspuns->failed()) {
            $payload = json_decode($raspuns->body(), true);

            throw new DeclaratieException(
                'PDF-ul nu a putut fi citit: ' . ($payload['detalii'] ?? $payload['eroare'] ?? 'HTTP ' . $raspuns->status())
            );
        }

        $date = $raspuns->json();

        return [
            'semnat' => (bool) ($date['semnat'] ?? false),
            'semnatari' => $date['semnatari'] ?? [],
            'nume_xml' => $date['nume_xml'] ?? null,
            'xml' => !empty($date['xml_base64']) ? base64_decode($date['xml_base64']) : null,
        ];
    }
}
