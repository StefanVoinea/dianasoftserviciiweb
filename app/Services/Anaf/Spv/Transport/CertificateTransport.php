<?php

namespace App\Services\Anaf\Spv\Transport;

use App\Services\Anaf\Spv\Contracts\SpvTransport;
use App\Services\Anaf\Spv\ProgramLocalVechiException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class CertificateTransport implements SpvTransport
{
    protected $config;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
     * Aici certificatul e un fisier de pe server, nu un token de la client, deci
     * nu exista niciun calculator al lui pe care sa fie scris documentul.
     * Lucrarea se face pe drumul dinainte, prin server.
     */
    public function descarcaInArhiva(string $id, array $destinatie): array
    {
        throw new ProgramLocalVechiException(
            'Descărcarea direct în arhiva clientului merge doar prin programul local.'
        );
    }

    public function get($path, array $query = array()): Response
    {
        $url = rtrim($this->config['base_url'], '/') . '/' . ltrim($path, '/');

        if ($query !== array()) {
            $url .= '?' . http_build_query($query, '', '&', PHP_QUERY_RFC3986);
        }

        return Http::withOptions([
            'cert' => [$this->config['cert']['path'], $this->config['cert']['password']],
            'verify' => !empty($this->config['cert']['ca']) ? $this->config['cert']['ca'] : true,
            'version' => 1.1,
        ])
            ->timeout($this->config['timeout'])
            ->withHeaders(['Accept' => '*/*'])
            ->get($url);
    }
}
