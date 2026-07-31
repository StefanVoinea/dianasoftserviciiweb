<?php

namespace App\Services\Anaf\Declaratii;

use App\Services\Anaf\Spv\CertificatService;
use Illuminate\Support\Facades\Http;

/**
 * Depunerea declaratiei semnate pe decl.anaf.mfinante.gov.ro. Autentificarea
 * mTLS si upload-ul trec prin bridge-ul calculatorului pe care se afla
 * certificatul folosit.
 */
class DepunereService
{
    protected $config;
    protected $certificate;

    public function __construct(array $config, CertificatService $certificate)
    {
        $this->config = $config;
        $this->certificate = $certificate;
    }

    public function autentificare(): void
    {
        $bridge = $this->certificate->bridge();

        $raspuns = Http::withToken($bridge['token'])
            ->withHeaders($this->anteteCertificat($bridge))
            ->timeout($this->config['timeout'])
            ->post(rtrim($bridge['url'], '/') . '/decl/login');

        if ($raspuns->failed()) {
            $payload = json_decode($raspuns->body(), true);

            throw new DeclaratieException(
                'Autentificarea la ANAF a eșuat: ' . ($payload['detalii'] ?? $payload['eroare'] ?? 'HTTP ' . $raspuns->status())
            );
        }
    }

    /**
     * @return array{index_recipisa: ?string, eroare: ?string, raspuns: string}
     */
    public function depune(string $calePdfSemnat): array
    {
        if (!is_file($calePdfSemnat)) {
            throw new DeclaratieException('PDF-ul semnat nu există: ' . $calePdfSemnat);
        }

        $bridge = $this->certificate->bridge();

        $raspuns = Http::withToken($bridge['token'])
            ->withHeaders(array_merge($this->anteteCertificat($bridge), [
                'Content-Type' => 'application/pdf',
                'X-Filename' => basename($calePdfSemnat),
            ]))
            ->timeout($this->config['timeout'])
            ->withBody(file_get_contents($calePdfSemnat), 'application/pdf')
            ->post(rtrim($bridge['url'], '/') . '/decl/upload');

        if ($raspuns->failed()) {
            $payload = json_decode($raspuns->body(), true);

            throw new DeclaratieException(
                'Depunerea a eșuat: ' . ($payload['detalii'] ?? $payload['eroare'] ?? 'HTTP ' . $raspuns->status())
            );
        }

        return $this->extrageIndice($raspuns->body());
    }

    protected function anteteCertificat(array $bridge): array
    {
        return $bridge['thumbprint'] ? ['X-Thumbprint' => $bridge['thumbprint']] : [];
    }

    /**
     * Indicele de incarcare vine intr-un <b style="color: #000000">, iar erorile
     * intr-un element rosu — la fel ca in raspunsul parsat de aplicatia desktop.
     */
    public function extrageIndice(string $html): array
    {
        if (preg_match('/red"\s*>(.*?)</s', $html, $m)) {
            return [
                'index_recipisa' => null,
                'eroare' => trim(html_entity_decode(strip_tags($m[1]))),
                'raspuns' => $html,
            ];
        }

        if (preg_match('/<b style="color: ?#000000">(.*?)</s', $html, $m)) {
            return [
                'index_recipisa' => trim(html_entity_decode(strip_tags($m[1]))),
                'eroare' => null,
                'raspuns' => $html,
            ];
        }

        return [
            'index_recipisa' => null,
            'eroare' => 'Eroare la depunere declarație — răspuns ANAF neinterpretabil.',
            'raspuns' => $html,
        ];
    }
}
