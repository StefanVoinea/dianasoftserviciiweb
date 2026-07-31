<?php

namespace App\Services\Anaf\Spv\Transport;

use App\Services\Anaf\Spv\CertificatService;
use App\Services\Anaf\Spv\Contracts\SpvTransport;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

/**
 * Trimite cererile SPV catre bridge-ul calculatorului pe care se afla
 * certificatul folosit. Ruta si amprenta vin de la CertificatService, pentru ca
 * un client poate avea mai multe certificate, pe calculatoare diferite.
 */
class BridgeTransport implements SpvTransport
{
    protected $config;
    protected $certificate;

    public function __construct(array $config, CertificatService $certificate)
    {
        $this->config = $config;
        $this->certificate = $certificate;
    }

    public function get($path, array $query = array()): Response
    {
        $bridge = $this->certificate->bridge();

        $url = rtrim($bridge['url'], '/') . '/spv' . '/' . ltrim($path, '/');

        if ($query !== array()) {
            $url .= '?' . http_build_query($query, '', '&', PHP_QUERY_RFC3986);
        }

        return Http::withToken($bridge['token'])
            ->withHeaders($bridge['thumbprint'] ? ['X-Thumbprint' => $bridge['thumbprint']] : [])
            ->timeout($this->config['timeout'])
            ->get($url);
    }
}
