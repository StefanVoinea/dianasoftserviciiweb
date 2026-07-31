<?php

namespace App\Services\Anaf\Spv\Transport;

use App\Services\Anaf\Spv\Contracts\SpvTransport;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class CertificateTransport implements SpvTransport
{
    protected $config;

    public function __construct(array $config)
    {
        $this->config = $config;
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
