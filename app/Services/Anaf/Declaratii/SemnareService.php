<?php

namespace App\Services\Anaf\Declaratii;

use App\Services\Anaf\Spv\CertificatService;
use Illuminate\Support\Facades\Http;

/**
 * Semnarea PDF-ului se face pe calculatorul unde e conectat tokenul: bridge-ul
 * primeste PDF-ul si il intoarce semnat. Ruta se alege in functie de
 * certificatul cerut, pentru ca acesta poate fi pe alt calculator din retea.
 */
class SemnareService
{
    protected $config;
    protected $certificate;

    public function __construct(array $config, CertificatService $certificate)
    {
        $this->config = $config;
        $this->certificate = $certificate;
    }

    public function semneaza(string $calePdf, string $calePdfSemnat): string
    {
        if (!is_file($calePdf)) {
            throw new DeclaratieException('PDF-ul de semnat nu există: ' . $calePdf);
        }

        $bridge = $this->certificate->bridge();

        $raspuns = Http::withToken($bridge['token'])
            ->withHeaders(array_filter(array_merge([
                'Content-Type' => 'application/pdf',
                'X-Thumbprint' => $bridge['thumbprint'],
            ], $this->anteteCaseta())))
            ->timeout($this->config['timeout'])
            ->withBody(file_get_contents($calePdf), 'application/pdf')
            ->post(rtrim($bridge['url'], '/') . '/semnare');

        if ($raspuns->failed()) {
            $payload = json_decode($raspuns->body(), true);
            throw new DeclaratieException(
                'Semnarea a eșuat: ' . ($payload['detalii'] ?? $payload['eroare'] ?? 'HTTP ' . $raspuns->status())
            );
        }

        file_put_contents($calePdfSemnat, $raspuns->body());

        return $calePdfSemnat;
    }

    /**
     * Poziția casetei vizibile a semnaturii se hotărăște aici, nu pe
     * calculatorul cu tokenul: ține de formular, nu de stația de lucru.
     *
     * Un program de acces mai vechi, care nu cunoaște aceste antete, le ignoră
     * și folosește valorile lui implicite.
     */
    protected function anteteCaseta(): array
    {
        $caseta = config('anaf.declaratii.semnatura', []);

        if (!$caseta) {
            return [];
        }

        return [
            'X-Semnatura-Pagina' => (string) ($caseta['pagina'] ?? '1'),
            'X-Semnatura-X' => (string) ($caseta['x'] ?? ''),
            'X-Semnatura-Y' => (string) ($caseta['y'] ?? ''),
            'X-Semnatura-Latime' => (string) ($caseta['latime'] ?? ''),
            'X-Semnatura-Inaltime' => (string) ($caseta['inaltime'] ?? ''),
            'X-Semnatura-Motiv' => (string) ($caseta['motiv'] ?? ''),
        ];
    }
}
