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
     * @return array{semnat: bool, semnatari: array, xml: ?string, nume_xml: ?string}
     */
    public function citeste(string $calePdf): array
    {
        if (!is_file($calePdf)) {
            throw new DeclaratieException('PDF-ul nu există: ' . $calePdf);
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
