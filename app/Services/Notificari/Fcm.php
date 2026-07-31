<?php

namespace App\Services\Notificari;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Trimiterea alertelor prin Firebase Cloud Messaging (HTTP v1).
 *
 * Autorizarea se face cu un cont de serviciu: cheia lui semnează un JWT, care
 * se preschimbă la Google într-un token de acces valabil o oră. Tokenul se
 * păstrează în cache, ca să nu fie cerut la fiecare mesaj.
 *
 * Când Firebase nu este configurat, serviciul raportează asta și nu încearcă
 * nimic: aplicația mobilă are oricum verificarea periodică, deci alertele nu
 * se pierd, doar întârzie.
 */
class Fcm
{
    /** Rezultatele posibile ale unei trimiteri. */
    public const TRIMIS = 'trimis';
    public const TOKEN_INVALID = 'token_invalid';
    public const ESEC = 'esec';

    protected const SCOP = 'https://www.googleapis.com/auth/firebase.messaging';
    protected const CHEIE_CACHE = 'fcm.token_acces';

    public function activ(): bool
    {
        $cale = config('firebase.cont_serviciu');

        return !empty(config('firebase.proiect')) && !empty($cale) && is_readable($cale);
    }

    /**
     * Trimite o alertă către un dispozitiv.
     *
     * @param array $date perechi text-text, trimise aplicației odată cu alerta
     *
     * @return string una dintre constantele TRIMIS / TOKEN_INVALID / ESEC
     */
    public function trimite(string $token, string $titlu, string $corp, array $date = []): string
    {
        if (!$this->activ()) {
            return self::ESEC;
        }

        $acces = $this->tokenDeAcces();

        if ($acces === null) {
            return self::ESEC;
        }

        $mesaj = [
            'message' => [
                'token' => $token,
                'notification' => ['title' => $titlu, 'body' => $corp],
                // Valorile din „data” trebuie să fie text, altfel FCM refuză mesajul.
                'data' => array_map(function ($valoare) {
                    return (string) $valoare;
                }, $date),
                'android' => [
                    'priority' => 'high',
                    'notification' => ['channel_id' => 'modificari_dosare'],
                ],
            ],
        ];

        try {
            $raspuns = Http::timeout((int) config('firebase.timeout'))
                ->withToken($acces)
                ->post($this->urlTrimitere(), $mesaj);
        } catch (\Exception $e) {
            Log::warning('FCM: trimitere eșuată: ' . $e->getMessage());

            return self::ESEC;
        }

        if ($raspuns->successful()) {
            return self::TRIMIS;
        }

        if ($this->tokenulEsteMort($raspuns->status(), $raspuns->body())) {
            return self::TOKEN_INVALID;
        }

        Log::warning('FCM: răspuns ' . $raspuns->status() . ': ' . mb_substr($raspuns->body(), 0, 300));

        return self::ESEC;
    }

    /**
     * Aplicația dezinstalată sau tokenul înlocuit — dispozitivul trebuie scos
     * din listă, altfel se reîncearcă la nesfârșit.
     */
    protected function tokenulEsteMort(int $status, string $corp): bool
    {
        if ($status === 404) {
            return true;
        }

        if ($status !== 400) {
            return false;
        }

        return strpos($corp, 'UNREGISTERED') !== false
            || strpos($corp, 'INVALID_ARGUMENT') !== false;
    }

    protected function urlTrimitere(): string
    {
        return str_replace('{proiect}', (string) config('firebase.proiect'), (string) config('firebase.url'));
    }

    /** Tokenul de acces, valabil o oră; se reține până aproape de expirare. */
    protected function tokenDeAcces(): ?string
    {
        $existent = Cache::get(self::CHEIE_CACHE);

        if (is_string($existent) && $existent !== '') {
            return $existent;
        }

        $cont = $this->contDeServiciu();

        if ($cont === null) {
            return null;
        }

        $jwt = $this->construiesteJwt($cont);

        if ($jwt === null) {
            return null;
        }

        try {
            $raspuns = Http::timeout((int) config('firebase.timeout'))
                ->asForm()
                ->post($cont['token_uri'], [
                    'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                    'assertion' => $jwt,
                ]);
        } catch (\Exception $e) {
            Log::error('FCM: obținerea tokenului a eșuat: ' . $e->getMessage());

            return null;
        }

        if ($raspuns->failed()) {
            Log::error('FCM: token refuzat de Google: ' . mb_substr($raspuns->body(), 0, 300));

            return null;
        }

        $token = $raspuns->json('access_token');
        $secunde = (int) $raspuns->json('expires_in', 3600);

        if (!is_string($token) || $token === '') {
            return null;
        }

        // Se reține cu marjă, ca să nu fie folosit chiar la expirare.
        Cache::put(self::CHEIE_CACHE, $token, now()->addSeconds(max(60, $secunde - 120)));

        return $token;
    }

    /** @return array{client_email:string, private_key:string, token_uri:string}|null */
    protected function contDeServiciu(): ?array
    {
        $cale = config('firebase.cont_serviciu');

        if (empty($cale) || !is_readable($cale)) {
            return null;
        }

        $date = json_decode((string) file_get_contents($cale), true);

        if (!is_array($date) || empty($date['client_email']) || empty($date['private_key'])) {
            Log::error('FCM: fișierul contului de serviciu nu conține datele așteptate.');

            return null;
        }

        return [
            'client_email' => $date['client_email'],
            'private_key' => $date['private_key'],
            'token_uri' => $date['token_uri'] ?? 'https://oauth2.googleapis.com/token',
        ];
    }

    protected function construiesteJwt(array $cont): ?string
    {
        $acum = time();

        $antet = ['alg' => 'RS256', 'typ' => 'JWT'];
        $pretentii = [
            'iss' => $cont['client_email'],
            'scope' => self::SCOP,
            'aud' => $cont['token_uri'],
            'iat' => $acum,
            'exp' => $acum + 3600,
        ];

        $deSemnat = $this->base64Url(json_encode($antet)) . '.' . $this->base64Url(json_encode($pretentii));

        $semnatura = '';

        if (!openssl_sign($deSemnat, $semnatura, $cont['private_key'], 'sha256WithRSAEncryption')) {
            Log::error('FCM: semnarea JWT a eșuat — cheia contului de serviciu este invalidă.');

            return null;
        }

        return $deSemnat . '.' . $this->base64Url($semnatura);
    }

    protected function base64Url(string $date): string
    {
        return rtrim(strtr(base64_encode($date), '+/', '-_'), '=');
    }
}
