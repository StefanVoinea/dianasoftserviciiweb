<?php

namespace App\Services\Anaf\Etransport;

use App\Services\Anaf\Oauth\OauthAnaf;
use App\Services\Anaf\Spv\CertificatService;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

/**
 * Serviciile e-Transport ale ANAF, apelate cu certificatul digital prin
 * programul local instalat pe calculatorul cu tokenul.
 *
 * Servicii (conform documentației ANAF):
 *   upload     — depunerea declarației de transport (XML), întoarce indexul de încărcare
 *   lista      — notificările din ultimele N zile pentru un CIF
 *   stareMesaj — starea unei declarații depuse, după indexul de încărcare
 *   info       — notificările în care un CIF este organizator de transport
 */
class EtransportClient
{
    protected $config;
    protected $certificate;
    protected $oauth;

    public function __construct(array $config, CertificatService $certificate, OauthAnaf $oauth)
    {
        $this->config = $config;
        $this->certificate = $certificate;
        $this->oauth = $oauth;
    }

    /**
     * Depune declarația de transport. Întoarce răspunsul ANAF, care conține
     * „index_incarcare” la succes sau erorile de validare.
     */
    public function upload(string $xml, string $cif, ?int $versiune = null): array
    {
        $versiune = $versiune ?: $this->config['versiune'];

        $cale = 'upload/' . $this->config['standard'] . '/' . $cif;

        // Versiunea 2 a serviciului cere componenta suplimentară în cale.
        if ($versiune) {
            $cale .= '/' . $versiune;
        }

        return $this->raspuns($this->trimite('POST', $cale, [], $xml, $cif), $cale);
    }

    /** Notificările din ultimele N zile (maximum 60, conform ANAF). */
    public function lista(int $zile, string $cif): array
    {
        $zile = max(1, min($zile, $this->config['zile_max']));

        return $this->raspuns($this->trimite('GET', 'lista/' . $zile . '/' . $cif, [], null, $cif), 'lista');
    }

    /**
     * Starea unei declarații depuse. În modul OAuth2 e nevoie și de CIF-ul
     * pentru care s-a făcut autorizarea.
     */
    public function stareMesaj(string $idIncarcare, ?string $cif = null): array
    {
        return $this->raspuns($this->trimite('GET', 'stareMesaj/' . $idIncarcare, [], null, $cif), 'stareMesaj');
    }

    /**
     * Notificările în care CIF-ul este organizator de transport / transportator.
     * Filtrele suplimentare (declarant, UIT, referință) sunt opționale.
     */
    public function info(string $cuiOperator, array $filtre = []): array
    {
        $query = array_filter([
            'cui_op' => $cuiOperator,
            'cui_decl' => $filtre['cui_decl'] ?? null,
            'uit' => $filtre['uit'] ?? null,
            'ref_decl' => $filtre['ref_decl'] ?? null,
        ]);

        return $this->raspuns($this->trimite('GET', 'info', $query, null, $cuiOperator), 'info');
    }

    /**
     * Apelul merge fie prin programul local (cu certificatul de pe token), fie
     * direct la ANAF cu autorizare OAuth2.
     */
    protected function trimite(string $metoda, string $cale, array $query = [], ?string $corp = null, ?string $cif = null): Response
    {
        return $this->foloseseOauth($cif)
            ? $this->trimiteOauth($metoda, $cale, $query, $corp, $cif)
            : $this->trimitePrinProgramLocal($metoda, $cale, $query, $corp);
    }

    public function prinOauth(): bool
    {
        return ($this->config['mod'] ?? 'certificat') === 'oauth';
    }

    /**
     * OAuth se folosește când e modul configurat, dar și de la sine, când
     * CIF-ul are o autorizare valabilă: cine a autorizat aplicația la ANAF nu
     * mai are nevoie de programul local pentru e-Transport, și nici de vreo
     * setare pe server.
     */
    public function foloseseOauth(?string $cif): bool
    {
        if ($this->prinOauth()) {
            return true;
        }

        return $cif !== null && $cif !== '' && $this->oauth->token($cif) !== null;
    }

    protected function trimitePrinProgramLocal(string $metoda, string $cale, array $query, ?string $corp): Response
    {
        $bridge = $this->certificate->bridge();

        /*
         * Fara adresa puntii, URL-ul ar iesi „/etransport/...", iar curl ar
         * cauta o gazda pe nume „etransport" si ar cadea cu o eroare criptica.
         * Mai bine se spune de aici ce lipseste si ce e de facut.
         */
        if (empty($bridge['url'])) {
            throw new EtransportException(
                'Certificatul ales nu are un program local legat, deci cererea nu are pe unde pleca. '
                . 'Alegeți certificatul cu care lucrați (dreapta sus) sau instalați kitul pe calculatorul cu tokenul.'
            );
        }

        $url = rtrim($bridge['url'], '/') . '/etransport/' . ltrim($cale, '/');

        if ($query !== []) {
            $url .= '?' . http_build_query($query, '', '&', PHP_QUERY_RFC3986);
        }

        $cerere = Http::withToken($bridge['token'])
            ->withHeaders(array_filter(['X-Thumbprint' => $bridge['thumbprint']]))
            ->timeout($this->config['timeout']);

        if ($metoda === 'POST') {
            return $cerere->withBody($corp, 'application/xml')->post($url);
        }

        return $cerere->get($url);
    }

    protected function trimiteOauth(string $metoda, string $cale, array $query, ?string $corp, ?string $cif): Response
    {
        if (!$cif) {
            throw new EtransportException('Nu se poate stabili CIF-ul pentru autorizarea OAuth2.');
        }

        $token = $this->oauth->token($cif);

        if (!$token) {
            throw new EtransportException(
                'Nu există o autorizare OAuth2 validă pentru CIF-ul ' . $cif . '. '
                . 'Apăsați „Autorizează la ANAF” pentru a o obține.'
            );
        }

        $baza = ($this->config['mediu'] ?? 'prod') === 'test'
            ? $this->config['oauth_test_url']
            : $this->config['oauth_url'];

        $url = rtrim($baza, '/') . '/' . ltrim($cale, '/');

        if ($query !== []) {
            $url .= '?' . http_build_query($query, '', '&', PHP_QUERY_RFC3986);
        }

        $cerere = Http::withToken($token)->timeout($this->config['timeout']);

        if ($metoda === 'POST') {
            return $cerere->withBody($corp, 'application/xml')->post($url);
        }

        return $cerere->get($url);
    }

    protected function raspuns(Response $raspuns, string $operatie): array
    {
        $payload = json_decode($raspuns->body(), true);

        if ($raspuns->failed()) {
            $detalii = is_array($payload)
                ? ($payload['detalii'] ?? $payload['eroare'] ?? null)
                : mb_substr(strip_tags($raspuns->body()), 0, 300);

            throw new EtransportException(
                'e-Transport (' . $operatie . '): ' . ($detalii ?: 'HTTP ' . $raspuns->status())
            );
        }

        if (!is_array($payload)) {
            throw new EtransportException(
                'Răspuns neinterpretabil de la e-Transport (' . $operatie . '): '
                . mb_substr($raspuns->body(), 0, 300)
            );
        }

        return $payload;
    }
}
