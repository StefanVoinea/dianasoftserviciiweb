<?php

namespace App\Services\Anaf\Spv;

use App\Services\Anaf\Spv\Contracts\SpvTransport;
use Illuminate\Http\Client\Response;

class SpvClient
{
    protected $transport;
    protected $config;

    public function __construct(SpvTransport $transport, array $config)
    {
        $this->transport = $transport;
        $this->config = $config;
    }

    public function listaMesaje(int $zile = 60, ?string $cif = null): array
    {
        $query = ['zile' => min($zile, $this->config['zile_max'])];

        if ($cif !== null) {
            $query['cif'] = $cif;
        }

        return $this->json('/listaMesaje', $query);
    }

    /**
     * Raspunsul complet la listaMesaje, fara a trata "nu exista mesaje" ca eroare.
     * Contine si campurile cnp, cui (CIF-urile accesibile certificatului) si serial,
     * utile chiar cand nu exista niciun mesaj in intervalul cerut.
     */
    public function listaMesajeBrut(int $zile = 60): array
    {
        $response = $this->call('/listaMesaje', ['zile' => min($zile, $this->config['zile_max'])]);

        $payload = json_decode($response->body(), true);

        if (!is_array($payload)) {
            throw new SpvException(
                'Răspuns non-JSON de la SPV: ' . mb_substr($response->body(), 0, 300)
            );
        }

        return $payload;
    }

    /**
     * Solicita un document din SPV (vector fiscal, fisa rol, situatie sintetica etc.).
     * Parametrii suplimentari acceptati de ANAF, in functie de tip:
     * an, luna, motiv (Adeverinte Venit), numar_inregistrare (Duplicat Recipisa),
     * cui_pui (Fisa Rol pentru un punct de lucru).
     */
    public function cerere(string $tip, string $cui, array $optiuni = []): array
    {
        $query = ['tip' => $tip, 'cui' => $cui];

        foreach (['an', 'luna', 'motiv', 'numar_inregistrare', 'cui_pui'] as $parametru) {
            if (isset($optiuni[$parametru]) && $optiuni[$parametru] !== '' && $optiuni[$parametru] !== null) {
                $query[$parametru] = $optiuni[$parametru];
            }
        }

        return $this->json('/cerere', $query);
    }

    public function descarcare(string $id): SpvFisier
    {
        $response = $this->call('/descarcare', ['id' => $id]);

        $contentType = strtolower($response->header('Content-Type') ?? '');

        if (str_contains($contentType, 'json') || $this->looksLikeJson($response->body())) {
            $payload = json_decode($response->body(), true) ?: [];
            throw new SpvException($payload['eroare'] ?? 'Descărcare eșuată pentru mesajul ' . $id);
        }

        $extensie = str_contains($contentType, 'zip') ? 'zip' : 'pdf';

        return new SpvFisier($id, $response->body(), $extensie, $contentType);
    }

    private function json(string $path, array $query): array
    {
        $response = $this->call($path, $query);

        $payload = json_decode($response->body(), true);

        if (!is_array($payload)) {
            throw new SpvException(
                'Răspuns non-JSON de la SPV (' . $path . '): ' . mb_substr($response->body(), 0, 300)
            );
        }

        if (isset($payload['eroare'])) {
            throw new SpvException($payload['eroare']);
        }

        return $payload;
    }

    private function call(string $path, array $query): Response
    {
        usleep($this->config['throttle_ms'] * 1000);

        $response = $this->transport->get($path, $query);

        if ($response->failed()) {
            /*
             * Pe drumul până la ANAF sunt acum mai multe verigi: puntea,
             * agentul, programul local. Fiecare știe să spună ce a pățit, iar
             * vorba lui e mai de folos decât un cod de stare — altfel omul își
             * caută zadarnic vinovăția în certificat.
             */
            $payload = json_decode($response->body(), true);

            if (!empty($payload['eroare'])) {
                throw new SpvException(trim($payload['eroare'] . ' ' . ($payload['detalii'] ?? '')));
            }

            if ($response->status() === 401 || $response->status() === 403) {
                throw new SpvException('Autentificare SPV respinsă (certificat expirat sau fără drepturi).');
            }

            throw new SpvException('SPV HTTP ' . $response->status() . ' pe ' . $path);
        }

        return $response;
    }

    private function looksLikeJson(string $body): bool
    {
        return str_starts_with(ltrim($body), '{');
    }
}
