<?php

namespace App\Services\Anaf\Declaratii;

use App\Services\Anaf\Spv\CertificatService;
use Illuminate\Support\Facades\Cache;
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

    /**
     * Cat se socoteste buna o sesiune deschisa la ANAF.
     *
     * Sub cat tine ea cu adevarat, ca sa nu se lucreze pe una tocmai stinsa; si
     * oricum, o sesiune care s-a stins mai devreme se vede din raspuns si se
     * deschide alta.
     */
    protected const SESIUNEA_TINE_MINUTE = 8;

    /**
     * Deschide sesiunea la ANAF, daca nu e deja una buna.
     *
     * Autentificarea nu e un apel, ci trei — pagina, poarta F5 si provocarea ei
     * —, si incepe prin a arunca cosul de cookies. Facuta inaintea fiecarei
     * declaratii, ea costa de trei ori mai mult decat depunerea insasi si
     * arunca de fiecare data sesiunea abia deschisa.
     */
    public function autentificare(): void
    {
        $cheia = $this->cheiaSesiunii();

        if ($cheia !== null && Cache::get($cheia)) {
            return;
        }

        $this->deschideSesiunea($cheia);
    }

    /**
     * Deschide o sesiune noua, chiar daca cea de acum parea buna.
     *
     * Semnatura lui `autentificare()` a ramas neatinsa: peste ea se aseaza
     * dubluri in probe, iar un argument nou le-ar fi facut nepotrivite.
     */
    public function reautentifica(): void
    {
        $this->deschideSesiunea($this->cheiaSesiunii());
    }

    protected function deschideSesiunea(?string $cheia): void
    {

        $bridge = $this->certificate->bridge();

        $raspuns = Http::withToken($bridge['token'])
            ->withHeaders($this->anteteCertificat($bridge))
            ->timeout($this->config['timeout'])
            ->post(rtrim($bridge['url'], '/') . '/decl/login');

        if ($raspuns->failed()) {
            if ($cheia !== null) {
                Cache::forget($cheia);
            }

            $payload = json_decode($raspuns->body(), true);

            throw new DeclaratieException(
                'Autentificarea la ANAF a eșuat: ' . ($payload['detalii'] ?? $payload['eroare'] ?? 'HTTP ' . $raspuns->status())
            );
        }

        if ($cheia !== null) {
            Cache::put($cheia, true, now()->addMinutes(self::SESIUNEA_TINE_MINUTE));
        }
    }

    /**
     * Unde se tine minte ca sesiunea e deschisa.
     *
     * Pe certificat, fiindca sesiunea e a lui: cosul de cookies sta pe
     * calculatorul acelui token. Fara certificat cunoscut nu se tine minte
     * nimic si se lucreaza ca pana acum.
     */
    protected function cheiaSesiunii(): ?string
    {
        $id = $this->certificate->idCurent();

        return $id === null ? null : 'anaf:decl:sesiune:' . $id;
    }

    /** Sesiunea se socoteste stinsa: urmatoarea depunere o deschide din nou. */
    public function uitaSesiunea(): void
    {
        $cheia = $this->cheiaSesiunii();

        if ($cheia !== null) {
            Cache::forget($cheia);
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

        $continut = file_get_contents($calePdfSemnat);
        $nume = basename($calePdfSemnat);

        $raspuns = $this->trimite($continut, $nume);

        /*
         * Sesiunea se poate stinge inainte de vreme, si atunci ANAF intoarce
         * pagina de intrare in loc de raspuns. Se intra din nou si se incearca
         * o singura data: daca nici asa, pricina e alta.
         */
        if ($this->cereIarasiIntrarea($raspuns)) {
            $this->reautentifica();

            $raspuns = $this->trimite($continut, $nume);
        }

        if ($raspuns->failed()) {
            $payload = json_decode($raspuns->body(), true);

            throw new DeclaratieException(
                'Depunerea a eșuat: ' . ($payload['detalii'] ?? $payload['eroare'] ?? 'HTTP ' . $raspuns->status())
            );
        }

        return $this->extrageIndice($raspuns->body());
    }

    /** Trimite documentul programului local, care il duce mai departe la ANAF. */
    protected function trimite(string $continut, string $nume)
    {
        $bridge = $this->certificate->bridge();

        return Http::withToken($bridge['token'])
            ->withHeaders(array_merge($this->anteteCertificat($bridge), [
                'Content-Type' => 'application/pdf',
                'X-Filename' => $nume,
            ]))
            ->timeout($this->config['timeout'])
            ->withBody($continut, 'application/pdf')
            ->post(rtrim($bridge['url'], '/') . '/decl/upload');
    }

    /**
     * Raspunsul e pagina de intrare, nu unul al depunerii?
     *
     * Cand sesiunea s-a stins, poarta ANAF nu spune asta: intoarce chiar
     * formularul de intrare, cu stare 200. Se cunoaste dupa ce nu are —
     * nici indice de incarcare, nici eroare — si dupa poarta F5 din el.
     */
    protected function cereIarasiIntrarea($raspuns): bool
    {
        if ($raspuns->failed()) {
            return false;
        }

        $corp = $raspuns->body();

        if (strpos($corp, 'my.policy') !== false || strpos($corp, 'name="dummy"') !== false) {
            $this->uitaSesiunea();

            return true;
        }

        return false;
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
