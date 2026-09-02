<?php

namespace App\Services\Anaf\Spv\Transport;

use App\Services\Anaf\Bridge\LicentiereBridge;
use App\Services\Anaf\Bridge\Punte;
use App\Services\Anaf\Spv\CertificatService;
use App\Services\Anaf\Spv\Contracts\SpvTransport;
use App\Services\Anaf\Spv\ProgramLocalVechiException;
use App\Services\Anaf\Spv\SpvException;
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

    /**
     * Cat se asteapta ca omul sa scrie PIN-ul, cand fereastra e deschisa.
     *
     * Atat cat ii trebuie cuiva sa se intoarca la calculator si sa-l scrie —
     * nu cat sa ramana fereastra deschisa peste noapte, tinand cererea pe loc.
     * Se poate schimba din configuratie, si pentru probe, si pentru clientii
     * la care omul e mai departe de calculator.
     */
    protected function pinSecunde(): int
    {
        return (int) ($this->config['pin_asteptare_secunde'] ?? 90);
    }

    /** Cat de des se intreaba daca fereastra s-a inchis. */
    protected function pinPasSecunde(): int
    {
        return max(1, (int) ($this->config['pin_pas_secunde'] ?? 2));
    }

    protected $config;
    protected $certificate;

    public function __construct(array $config, CertificatService $certificate)
    {
        $this->config = $config;
        $this->certificate = $certificate;
    }

    /**
     * Aduce documentul din SPV de-a dreptul in arhiva clientului.
     *
     * Serverul nu mai vede documentul deloc: programul local il ia de la ANAF si
     * il scrie in dosarul firmei, iar inapoi vine doar calea sub care l-a pus si,
     * daca s-a cerut, textul din el — atat cat sa se stie ce scrie ANAF acolo.
     */
    public function descarcaInArhiva(string $id, array $destinatie): array
    {
        $intrebare = array_filter([
            'id' => $id,
            'firma' => $destinatie['firma'] ?? '',
            'dosar' => $destinatie['dosar'] ?? '',
            'nume' => $destinatie['nume'] ?? '',
            'inlocuieste' => $destinatie['inlocuieste'] ?? null,
            'text' => !empty($destinatie['text']) ? 1 : null,
        ], function ($valoare) {
            return $valoare !== null && $valoare !== '';
        });

        $raspuns = $this->cereDinArhiva($intrebare);

        if ($this->faraLicenta($raspuns)) {
            $certificat = $this->certificate->activ();

            if ($certificat && app(LicentiereBridge::class)->reinnoieste($certificat, true)['emisa']) {
                $raspuns = $this->cereDinArhiva($intrebare);
            }
        }

        /*
         * Kiturile mai vechi nu stiu de operatia asta. Se spune limpede, ca
         * lucrarea sa se faca pe drumul dinainte in loc sa esueze.
         */
        if ($raspuns->status() === 404) {
            throw new ProgramLocalVechiException(
                'Programul local de pe calculatorul clientului nu cunoaște descărcarea direct în arhivă.'
            );
        }

        if ($raspuns->failed()) {
            $primit = json_decode($raspuns->body(), true);

            throw new SpvException(trim(
                ($primit['eroare'] ?? 'Descărcarea în arhiva locală a eșuat (HTTP ' . $raspuns->status() . ').')
                . ' ' . ($primit['detalii'] ?? '')
            ));
        }

        $cale = $raspuns->json('cale');

        if (!is_string($cale) || $cale === '') {
            throw new SpvException('Programul local nu a întors calea documentului arhivat.');
        }

        return [
            'cale' => $cale,
            'extensie' => (string) $raspuns->json('extensie'),
            'marime' => (int) $raspuns->json('marime'),
            'hash' => (string) $raspuns->json('hash'),
            'text' => $raspuns->json('text'),
        ];
    }

    /**
     * Aceleasi documente, cerute programului local toate deodata.
     *
     * Raspunsul curge — cate un obiect JSON pe rand —, deci cel dintai document
     * scris se afla fara sa se astepte si ultimul. Aici se strang toate si se
     * dau inapoi pe numarul mesajului.
     */
    public function descarcaLotInArhiva(array $documente, int $pauzaMs): array
    {
        $bridge = $this->certificate->bridge();
        $antete = ['Content-Type' => 'application/json'];

        if ($bridge['thumbprint']) {
            $antete['X-Thumbprint'] = $bridge['thumbprint'];
        }

        if ($bridge['arhiva']) {
            $antete['X-Arhiva-Cale'] = $bridge['arhiva'];
        }

        /*
         * Rabdarea creste cu lotul: fiecare document isi are pauza lui si
         * drumul lui pana la ANAF, deci un lot de zece tine de zece ori cat
         * unul singur.
         */
        $raspuns = Http::withToken($bridge['token'])
            ->withHeaders($antete)
            ->timeout($this->rabdarea() + count($documente) * (int) ceil($pauzaMs / 1000 + 5))
            ->withOptions(['connect_timeout' => self::CONECTARE_SECUNDE])
            ->post(rtrim($bridge['url'], '/') . '/spv-arhiva-lot', [
                'documente' => array_values($documente),
                'pauza_ms' => $pauzaMs,
            ]);

        /*
         * Kiturile mai vechi nu stiu de transe. Se spune limpede, ca lucrarea sa
         * se faca pe drumul dinainte — document cu document — in loc sa esueze.
         */
        if ($raspuns->status() === 404) {
            throw new ProgramLocalVechiException(
                'Programul local de pe calculatorul clientului nu cunoaște aducerea în transe.'
            );
        }

        if ($raspuns->failed()) {
            $primit = json_decode($raspuns->body(), true);

            throw new SpvException(trim(
                ($primit['eroare'] ?? 'Aducerea transei a eșuat (HTTP ' . $raspuns->status() . ').')
                . ' ' . ($primit['detalii'] ?? '')
            ));
        }

        $iesite = [];

        foreach (explode("\n", $raspuns->body()) as $rand) {
            $rand = trim($rand);

            if ($rand === '') {
                continue;
            }

            $pas = json_decode($rand, true);

            if (is_array($pas) && isset($pas['id'])) {
                $iesite[(string) $pas['id']] = $pas;
            }
        }

        return $iesite;
    }

    /** Cererea catre capatul care descarca si arhiveaza intr-un singur pas. */
    protected function cereDinArhiva(array $intrebare): Response
    {
        $bridge = $this->certificate->bridge();

        $antete = [];

        if ($bridge['thumbprint']) {
            $antete['X-Thumbprint'] = $bridge['thumbprint'];
        }

        // Unde tine clientul arhiva; gol inseamna ce scrie in configurare.env.
        if ($bridge['arhiva']) {
            $antete['X-Arhiva-Cale'] = $bridge['arhiva'];
        }

        return Http::withToken($bridge['token'])
            ->withHeaders($antete)
            ->timeout($this->rabdarea())
            ->withOptions(['connect_timeout' => self::CONECTARE_SECUNDE])
            ->get(rtrim($bridge['url'], '/') . '/spv-arhiva', $intrebare);
    }

    public function get($path, array $query = array()): Response
    {
        $raspuns = $this->cere($path, $query);

        /*
         * „Programul nu are licenta valida pe acest calculator."
         *
         * Se intampla dupa o reinstalare, dupa schimbarea calculatorului sau
         * cand licenta a expirat cat timp statia a stat inchisa. Serverul stie
         * s-o dea — o da acum si reia comanda, in loc sa trimita omul in fila
         * de certificate ca sa apese un buton pe care tot noi il apasam.
         */
        if ($this->faraLicenta($raspuns)) {
            $certificat = $this->certificate->activ();

            if ($certificat && app(LicentiereBridge::class)->reinnoieste($certificat, true)['emisa']) {
                return $this->cere($path, $query);
            }
        }

        /*
         * Tokenul isi asteapta PIN-ul: se asteapta sa fie scris si se reia
         * apelul, o singura data. Fara asta, omul scria PIN-ul si tot trebuia
         * sa apese din nou butonul — iar de multe ori nici nu stia de ce.
         */
        if ($this->asteaptaPinul($raspuns) && $this->asteaptaSaFieScris()) {
            return $this->cere($path, $query);
        }

        return $raspuns;
    }

    /** Programul local a spus ca tokenul isi asteapta PIN-ul? */
    public function asteaptaPinul(Response $raspuns): bool
    {
        return $raspuns->failed() && $raspuns->json('pin_asteapta') === true;
    }

    /**
     * Asteapta ca fereastra de PIN sa se inchida — adica omul sa fi scris codul.
     *
     * PIN-ul nu trece pe aici si nici prin server: el se scrie in fereastra lui,
     * pe calculatorul unde e tokenul. Aici se intreaba doar din cand in cand
     * daca fereastra mai e pe ecran.
     *
     * @return bool  s-a inchis inauntrul rabdarii
     */
    protected function asteaptaSaFieScris(): bool
    {
        $pana = microtime(true) + $this->pinSecunde();

        while (microtime(true) < $pana) {
            sleep($this->pinPasSecunde());

            $fereastra = $this->fereastraDePin();

            if ($fereastra === null) {
                // Programul local nu stie de proba: nu se poate astepta nimic.
                return false;
            }

            if (!$fereastra['deschisa']) {
                return true;
            }
        }

        return false;
    }

    /**
     * Duce codul pana la programul local, care il scrie in fereastra deschisa.
     *
     * Codul trece o data si nu se opreste nicaieri: nu se tine minte, nu se
     * scrie in niciun jurnal si nu se pune in niciun mesaj. Aici se afla doar
     * daca a fost primit sau nu.
     *
     * @return array{scris: bool, motiv: string}
     */
    public function scriePinul(string $pin): array
    {
        $bridge = $this->certificate->bridge();

        try {
            $raspuns = Http::withToken($bridge['token'])
                ->withHeaders($bridge['thumbprint'] ? ['X-Thumbprint' => $bridge['thumbprint']] : [])
                ->timeout(60)
                ->withOptions(['connect_timeout' => self::CONECTARE_SECUNDE])
                ->post(rtrim($bridge['url'], '/') . '/pin/scrie', ['pin' => $pin]);
        } catch (\Exception $e) {
            return ['scris' => false, 'motiv' => 'Nu se poate ajunge la programul local.'];
        }

        if ($raspuns->status() === 404 || $raspuns->status() === 501) {
            return [
                'scris' => false,
                'motiv' => 'Programul local de pe calculatorul clientului nu cunoaște încă scrierea PIN-ului.',
            ];
        }

        return [
            'scris' => $raspuns->json('scris') === true,
            'motiv' => (string) $raspuns->json('motiv'),
        ];
    }

    /**
     * Ce spune programul local despre fereastra de PIN.
     *
     * „null" inseamna ca nu s-a putut afla — cel mai des un program local mai
     * vechi, care nu cunoaste inca proba.
     *
     * @return array{deschisa: bool, titlu: string, proces: string}|null
     */
    public function fereastraDePin(): ?array
    {
        $bridge = $this->certificate->bridge();

        try {
            $raspuns = Http::withToken($bridge['token'])
                ->withHeaders($bridge['thumbprint'] ? ['X-Thumbprint' => $bridge['thumbprint']] : [])
                ->timeout(30)
                ->withOptions(['connect_timeout' => self::CONECTARE_SECUNDE])
                ->get(rtrim($bridge['url'], '/') . '/pin/fereastra');
        } catch (\Exception $e) {
            return null;
        }

        if ($raspuns->failed() || $raspuns->json('deschisa') === null) {
            return null;
        }

        return [
            'deschisa' => (bool) $raspuns->json('deschisa'),
            'titlu' => (string) $raspuns->json('titlu'),
            'proces' => (string) $raspuns->json('proces'),
        ];
    }

    /** Cererea propriu-zisa catre programul local. */
    protected function cere(string $path, array $query): Response
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

    /** Programul local a raspuns ca nu are licenta? */
    protected function faraLicenta(Response $raspuns): bool
    {
        if ($raspuns->status() !== 403) {
            return false;
        }

        return stripos((string) $raspuns->json('eroare'), 'licen') !== false;
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
