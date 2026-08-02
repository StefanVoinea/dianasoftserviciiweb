<?php

namespace App\Services\Anaf\Spv\Transport;

use App\Services\Anaf\Bridge\Punte;
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
    /** Cat se asteapta dupa o legatura care nu se face deloc. */
    protected const CONECTARE_SECUNDE = 10;

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
            ->timeout($this->rabdarea())
            /*
             * O adresa la care nu raspunde nimeni nu are de ce sa tina un minut
             * intreg: daca legatura nu se face in zece secunde, nu se mai face.
             * Asa, o adresa de retea locala scrisa pe un certificat lucrat din
             * cloud se vede indata ce e apasat butonul, nu dupa un minut.
             *
             * Se scrie ca optiune de Guzzle, nu prin connectTimeout(): metoda
             * aceea e din Laravel 9, iar aici suntem pe 8.
             */
            ->withOptions(['connect_timeout' => self::CONECTARE_SECUNDE])
            ->get($url);
    }

    /**
     * Cat asteptam raspunsul.
     *
     * Prin tunel, aceeasi rabdare de un minut nu ajunge: inauntrul ei incape si
     * drumul pana la programul de la client, si apelul lui catre ANAF — care are
     * el singur voie sa tina un minut. Cel care intreaba renunta primul, iar
     * omul primeste o eroare de retea in locul raspunsului care tocmai venea.
     *
     * De aceea, la tunel, se asteapta cu ragazul puntii peste cel al ANAF-ului.
     */
    protected function rabdarea(): int
    {
        $rabdare = (int) $this->config['timeout'];

        if (!app(Punte::class)->esteTunel($this->certificate->activ())) {
            return $rabdare;
        }

        return (int) ($this->config['timeout_tunel'] ?? $rabdare + Punte::ASTEPTARE_SECUNDE);
    }
}
