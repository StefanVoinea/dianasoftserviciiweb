<?php

namespace App\Services\Anaf\Oauth;

use App\Models\Efacturaparams;
use App\Models\Etransporttokens;
use App\Support\ContextCompanie;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;

/**
 * Autorizarea OAuth2 la ANAF pentru serviciile web (e-Transport, e-Factura).
 *
 * Fluxul are trei pași:
 *   1. utilizatorul e trimis la ANAF, unde se autentifică cu certificatul;
 *   2. ANAF redirecționează înapoi cu un cod, care se preschimbă în tokene;
 *   3. tokenul de acces se reînnoiește automat cu cel de reîmprospătare.
 *
 * Tokenele se păstrează per CIF și per client (company), în tabela existentă
 * a aplicației.
 */
class OauthAnaf
{
    /** Marja înainte de expirare la care tokenul se reînnoiește. */
    protected const ZILE_MARJA = 2;

    public function urlAutorizare(string $cif, ?string $redirect = null): string
    {
        if (!$this->configurat()) {
            throw new OauthException(
                'Aplicația nu are datele de client OAuth primite de la ANAF. '
                . 'Completați CLIENT_ANAF_ID și CLIENT_ANAF_SECRET în configurare.'
            );
        }

        $parametri = [
            'response_type' => 'code',
            'client_id' => $this->clientId(),
            'redirect_uri' => $redirect ?: $this->redirectUri(),
            'token_content_type' => 'jwt',
            'state' => $this->semneazaStare($cif),
        ];

        return $this->urlAnaf('link_authorization', config('anaf.oauth.url_autorizare'))
            . '?' . http_build_query($parametri);
    }

    /**
     * Preschimbă codul primit de la ANAF în tokene și le salvează.
     */
    public function preiaToken(string $cod, string $stare, ?string $redirect = null): Etransporttokens
    {
        $date = $this->verificaStare($stare);

        $raspuns = Http::asForm()->post($this->urlToken(), [
            'grant_type' => 'authorization_code',
            'code' => $cod,
            'client_id' => $this->clientId(),
            'client_secret' => $this->clientSecret(),
            'redirect_uri' => $redirect ?: $this->redirectUri(),
            'token_content_type' => 'jwt',
        ]);

        if ($raspuns->failed()) {
            throw new OauthException($this->eroareDin($raspuns->body(), $raspuns->status()));
        }

        return $this->salveaza($date['cif'], $date['company_id'], $raspuns->json());
    }

    /**
     * Tokenul valabil pentru CIF-ul dat, reînnoit automat dacă e aproape expirat.
     * Întoarce null când nu există autorizare — utilizatorul trebuie să o facă.
     */
    public function token(string $cif): ?string
    {
        $inregistrare = $this->inregistrare($cif);

        if (!$inregistrare) {
            return null;
        }

        $valabil = $inregistrare->data_expirare
            && Carbon::parse($inregistrare->data_expirare)->greaterThan(now()->addDays(self::ZILE_MARJA));

        if ($valabil && $inregistrare->access_token) {
            return $inregistrare->access_token;
        }

        if (!$inregistrare->refresh_token) {
            return null;
        }

        return $this->reinnoieste($inregistrare);
    }

    /**
     * @return array{autorizat: bool, expira_la: ?string, zile_ramase: ?int}
     */
    public function stare(string $cif): array
    {
        $inregistrare = $this->inregistrare($cif);

        if (!$inregistrare || !$inregistrare->access_token) {
            return ['autorizat' => false, 'expira_la' => null, 'zile_ramase' => null];
        }

        $expira = $inregistrare->data_expirare ? Carbon::parse($inregistrare->data_expirare) : null;

        return [
            'autorizat' => true,
            'expira_la' => $expira ? $expira->format('d.m.Y') : null,
            'zile_ramase' => $expira ? now()->startOfDay()->diffInDays($expira->startOfDay(), false) : null,
        ];
    }

    protected function reinnoieste(Etransporttokens $inregistrare): ?string
    {
        $raspuns = Http::asForm()->post($this->urlToken(), [
            'grant_type' => 'refresh_token',
            'refresh_token' => $inregistrare->refresh_token,
            'client_id' => $this->clientId(),
            'client_secret' => $this->clientSecret(),
            'token_content_type' => 'jwt',
        ]);

        if ($raspuns->failed()) {
            // Reîmprospătarea eșuată nu e o eroare fatală: se cere reautorizarea.
            return null;
        }

        $salvat = $this->salveaza($inregistrare->cui, $inregistrare->company_id, $raspuns->json(), $inregistrare);

        return $salvat->access_token;
    }

    protected function salveaza($cif, $companyId, array $date, ?Etransporttokens $inregistrare = null): Etransporttokens
    {
        $inregistrare = $inregistrare ?: Etransporttokens::firstOrNew([
            'cui' => $cif,
            'company_id' => $companyId,
        ]);

        // ANAF întoarce durata în secunde (de regulă 90 de zile).
        $secunde = (int) ($date['expires_in'] ?? 0);

        $inregistrare->fill([
            'cui' => $cif,
            'company_id' => $companyId,
            'access_token' => $date['access_token'] ?? null,
            'refresh_token' => $date['refresh_token'] ?? $inregistrare->refresh_token,
            'data_obtinerii' => now(),
            'data_expirare' => $secunde > 0 ? now()->addSeconds($secunde) : now()->addDays(90),
        ])->save();

        return $inregistrare;
    }

    protected function inregistrare(string $cif): ?Etransporttokens
    {
        $companie = ContextCompanie::curenta();

        $query = Etransporttokens::where('cui', $cif);

        if ($companie !== null) {
            $query->where('company_id', $companie);
        }

        return $query->first();
    }

    /** Starea leagă răspunsul ANAF de CIF-ul și clientul care au inițiat cererea. */
    protected function semneazaStare(string $cif): string
    {
        $date = ['cif' => $cif, 'company_id' => ContextCompanie::curenta(), 'timp' => now()->timestamp];
        $continut = base64_encode(json_encode($date));

        return $continut . '.' . hash_hmac('sha256', $continut, config('app.key'));
    }

    /**
     * @return array{cif: string, company_id: ?int}
     */
    protected function verificaStare(string $stare): array
    {
        $parti = explode('.', $stare);

        if (count($parti) !== 2 || !hash_equals(hash_hmac('sha256', $parti[0], config('app.key')), $parti[1])) {
            throw new OauthException('Răspunsul de la ANAF nu poate fi asociat cererii (stare invalidă).');
        }

        $date = json_decode(base64_decode($parti[0]), true);

        if (!is_array($date) || empty($date['cif'])) {
            throw new OauthException('Răspunsul de la ANAF nu conține CIF-ul solicitat.');
        }

        return ['cif' => $date['cif'], 'company_id' => $date['company_id'] ?? null];
    }

    protected function urlToken(): string
    {
        return $this->urlAnaf('link_token', config('anaf.oauth.url_token'));
    }

    /** Adresele ANAF sunt configurabile în aplicație; aici doar valorile implicite. */
    protected function urlAnaf(string $camp, string $implicit): string
    {
        $parametri = Efacturaparams::first();

        return $parametri && !empty($parametri->$camp) ? $parametri->$camp : $implicit;
    }

    /**
     * Aplicația poate cere autorizarea doar dacă are datele de client primite de
     * la ANAF. Interfața folosește asta ca să explice ce lipsește.
     */
    public function configurat(): bool
    {
        return $this->clientId() !== '' && $this->clientSecret() !== '';
    }

    protected function clientId(): string
    {
        return (string) config('anaf.oauth.client_id');
    }

    protected function clientSecret(): string
    {
        return (string) config('anaf.oauth.client_secret');
    }

    public function redirectUri(): string
    {
        return config('anaf.oauth.redirect')
            ?: rtrim(config('app.url'), '/') . '/api/anaf-oauth/callback';
    }

    protected function eroareDin(string $corp, int $status): string
    {
        $payload = json_decode($corp, true);

        if (is_array($payload)) {
            return $payload['error_description'] ?? ($payload['error'] ?? 'HTTP ' . $status);
        }

        return 'HTTP ' . $status . ': ' . mb_substr(strip_tags($corp), 0, 200);
    }
}
