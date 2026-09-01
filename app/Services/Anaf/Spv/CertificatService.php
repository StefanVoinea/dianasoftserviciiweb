<?php

namespace App\Services\Anaf\Spv;

use App\Models\AnafCertificat;
use App\Models\CertificatUtilizator;
use App\Services\Anaf\Bridge\Licente;
use App\Services\Anaf\Bridge\Punte;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

/**
 * Evidenta certificatelor digitale si rutarea catre bridge-ul corect.
 *
 * Un client poate avea mai multe certificate, fiecare pe alt calculator din
 * retea, cu propriul bridge. Certificatul folosit pentru o operatie se alege in
 * ordinea: cel cerut explicit, cel atribuit utilizatorului conectat, cel marcat
 * implicit, iar in lipsa lor configuratia din .env.
 */
class CertificatService
{
    protected $config;

    /** Certificatul rezolvat pentru cererea curenta. */
    protected $activ;

    /** Fortat programatic (ex. dintr-o comanda sau la semnare). */
    protected $fortat;

    /** Jetonul semnat al cererii curente. */
    protected $jeton;

    /** Pana cand mai e bun de folosit jetonul de mai sus (timp unix). */
    protected $jetonPanaLa = 0;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /** Fixeaza certificatul folosit pentru operatiile urmatoare. */
    public function foloseste(?AnafCertificat $certificat): void
    {
        $this->fortat = $certificat;
        $this->activ = $certificat;
    }

    /**
     * Fixeaza certificatul dupa numarul lui, daca mai e in uz.
     *
     * Asa se cere un document de pe calculatorul unde chiar se afla: fiecare
     * document stie cu ce certificat a fost adus, iar bridge-ul acelui
     * certificat e singurul care il are. Un numar gol sau un certificat scos
     * din uz nu schimba nimic — se lucreaza mai departe cu cel hotarat de
     * aplicatie, ca sa nu ramana lucrarea fara niciun bridge.
     */
    public function folosesteDupaId(?int $id): void
    {
        if (!$id) {
            return;
        }

        $certificat = AnafCertificat::where('activ', true)->find($id);

        if ($certificat) {
            $this->foloseste($certificat);
        }
    }

    /**
     * Certificatul care trebuie folosit acum. Poate fi null doar daca nu exista
     * niciun certificat inregistrat si nici configuratie in .env.
     */
    public function activ(): ?AnafCertificat
    {
        if ($this->activ !== null) {
            return $this->activ;
        }

        return $this->activ = $this->rezolva();
    }

    public function idCurent(): ?int
    {
        return optional($this->activ())->id;
    }

    /** Alias pastrat pentru compatibilitate cu apelurile existente. */
    public function curent(): ?AnafCertificat
    {
        return $this->activ();
    }

    /**
     * Coordonatele bridge-ului pe care trebuie trimisa cererea.
     *
     * @return array{url: string, token: ?string, cod_instalare: ?string, thumbprint: ?string, arhiva: ?string}
     */
    public function bridge(): array
    {
        $certificat = $this->activ();

        $codInstalare = $certificat && $certificat->bridge_token
            ? $certificat->bridge_token
            : ($this->config['bridge']['token'] ?? null);

        return [
            /*
             * Certificatele legate prin tunel nu au adresa in reteaua clientului:
             * comenzile lor trec prin puntea de pe serverul nostru, iar programul
             * local si le ia singur de acolo.
             */
            'url' => $this->adresa($certificat),
            /*
             * Comenzile merg cu un jeton semnat, valabil cateva minute: nici cel
             * care stie codul de instalare nu poate porni programul local din
             * alta aplicatie, pentru ca n-are cheia cu care se semneaza.
             *
             * Jetonul pleaca doar catre programele care au primit deja licenta —
             * unul vechi nu l-ar recunoaste si ar raspunde „cod de acces
             * invalid" la tot. Asa, instalarile existente merg mai departe pana
             * sunt licentiate.
             */
            'token' => $this->legitimarea($certificat, $codInstalare),
            // Codul din configurare.env, bun doar la instalare si la licentiere
            'cod_instalare' => $codInstalare,
            // Bridge-ul poate deservi mai multe certificate de pe acelasi
            // calculator; amprenta ii spune cu care sa lucreze.
            'thumbprint' => $certificat ? $certificat->thumbprint : ($this->config['thumbprint'] ?? null),
            // Unde tine acel calculator arhiva de documente. Gol inseamna ce
            // scrie in bridge.env pe statia respectiva.
            'arhiva' => $certificat ? $certificat->arhiva_cale : null,
        ];
    }

    /**
     * Cu ce se legitimeaza comanda de acum.
     *
     * Prin tunel, cererea se opreste intai la puntea de pe serverul nostru, iar
     * ea cere jeton semnat — numai serverul il poate face. Mai departe, catre
     * programul local, puntea pune singura ce trebuie: jetonul, daca programul
     * e licentiat, sau codul lui de instalare, daca nu e inca.
     *
     * La legatura directa nu e nimeni la mijloc: programul primeste jetonul doar
     * daca stie sa-l verifice, altfel codul, ca pana acum.
     */
    protected function legitimarea(?AnafCertificat $certificat, ?string $codInstalare): ?string
    {
        if (app(Punte::class)->esteTunel($certificat)) {
            return $this->jetonul() ?: $codInstalare;
        }

        return $certificat && $certificat->licenta_pana_la
            ? ($this->jetonul() ?: $codInstalare)
            : $codInstalare;
    }

    /**
     * Unde se trimit comenzile pentru acest certificat.
     *
     * La legatura directa, adresa calculatorului din reteaua clientului. La
     * tunel, puntea de pe serverul nostru: acolo asteapta comanda pana o ia
     * programul local, care intreaba singur, pe 443.
     */
    protected function adresa(?AnafCertificat $certificat): string
    {
        if (app(Punte::class)->esteTunel($certificat)) {
            return app(Punte::class)->adresa($certificat);
        }

        return $certificat && $certificat->bridge_url
            ? $certificat->bridge_url
            : $this->config['bridge']['url'];
    }

    /**
     * Jetonul semnat pentru comanda de acum.
     *
     * Se tine minte intre apeluri: o operatie trimite mai multe cereri catre
     * acelasi program local, iar semnarea e ieftina, dar nu degeaba.
     *
     * Se reinnoieste insa cand imbatraneste. Jetonul e bun cateva minute, ceea ce
     * ajunge pentru orice cerere obisnuita — dar aducerea documentelor in flux e
     * o singura cerere care tine o jumatate de ora si face sute de apeluri. La un
     * client cu doua sute cincizeci de entitati s-au adus 390 de documente din
     * 568, si de acolo incolo au cazut toate: jetonul facut la pornire expirase,
     * iar puntea le-a refuzat pe toate cu „cererea nu poarta jeton semnat”.
     */
    protected function jetonul(): ?string
    {
        if ($this->jeton !== null && time() < $this->jetonPanaLa) {
            return $this->jeton;
        }

        $licente = app(Licente::class);

        if (!$licente->areChei()) {
            return null;
        }

        /*
         * Se reface din vreme, nu chiar la expirare: intre semnare si sosirea
         * cererii la punte trece drumul pana la calculatorul clientului, iar un
         * jeton bun la plecare poate fi expirat la sosire.
         */
        $this->jetonPanaLa = time() + (int) (Licente::JETON_SECUNDE * 0.6);

        return $this->jeton = $licente->jeton();
    }

    protected function rezolva(): ?AnafCertificat
    {
        return $this->cerutExplicit()
            ?? $this->alUtilizatorului()
            ?? AnafCertificat::where('activ', true)->where('implicit', true)->first()
            ?? $this->dinConfiguratie();
    }

    /** Selectia din interfata, trimisa ca antet sau parametru. */
    protected function cerutExplicit(): ?AnafCertificat
    {
        if (!app()->bound('request')) {
            return null;
        }

        $id = request()->header('X-Certificat-Id') ?: request()->input('certificat_id');

        return $id ? AnafCertificat::where('activ', true)->find($id) : null;
    }

    /**
     * Certificatul atribuit utilizatorului conectat. Cand are mai multe, se ia
     * cel marcat implicit, altfel primul atribuit.
     */
    protected function alUtilizatorului(): ?AnafCertificat
    {
        /*
         * Nu orice cerere poarta un token al aplicatiei: agentul de la client
         * vine cu codul lui de instalare, iar Passport, incercand sa-l citeasca
         * drept JWT, se opreste cu „The JWT string must have two dots" si
         * darama toata cererea. Orice poticnire a autentificarii inseamna aici
         * doar „nimeni conectat" — nu e treaba rezolvarii certificatului sa
         * pice lucrarea.
         */
        try {
            $user = Auth::guard('api')->user();
        } catch (\Throwable $e) {
            $user = null;
        }

        $user = $user ?: Auth::user();

        if (!$user) {
            return null;
        }

        $certificate = AnafCertificat::where('activ', true)
            ->whereIn('id', CertificatUtilizator::where('activ', true)
                ->where(function ($query) use ($user) {
                    $query->where('user_id', $user->id);

                    if (!empty($user->email)) {
                        $query->orWhere('email', $user->email);
                    }
                })
                ->pluck('certificat_id'))
            ->orderByDesc('implicit')
            ->get();

        return $certificate->first();
    }

    /** Certificatul din .env, inregistrat la prima folosire. */
    protected function dinConfiguratie(): ?AnafCertificat
    {
        $thumbprint = $this->config['thumbprint'] ?? null;

        if ($thumbprint) {
            $cunoscut = AnafCertificat::where('thumbprint', $thumbprint)->first();

            if ($cunoscut) {
                return $cunoscut;
            }
        }

        try {
            return $this->sincronizeaza();
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Inregistreaza toate tokenele conectate acum la un bridge.
     * Certificatele deja cunoscute isi actualizeaza ruta si valabilitatea.
     *
     * @return AnafCertificat[]
     */
    public function descoperaPeBridge(string $bridgeUrl, ?string $bridgeToken = null): array
    {
        $raspuns = Http::withToken($bridgeToken)
            ->timeout($this->config['timeout'])
            ->get(rtrim($bridgeUrl, '/') . '/certificate');

        if ($raspuns->failed()) {
            $payload = json_decode($raspuns->body(), true);

            throw new SpvException(
                'Calculatorul ' . $bridgeUrl . ' nu a răspuns: '
                . ($payload['detalii'] ?? $payload['eroare'] ?? 'HTTP ' . $raspuns->status())
            );
        }

        $lista = $raspuns->json();

        // Un singur certificat vine ca obiect, mai multe ca listă.
        if (isset($lista['thumbprint'])) {
            $lista = [$lista];
        }

        if (!is_array($lista) || $lista === []) {
            throw new SpvException('Niciun token conectat la calculatorul ' . $bridgeUrl . '.');
        }

        $rezultat = [];

        foreach ($lista as $date) {
            if (empty($date['thumbprint']) || !$this->esteCalificat($date)) {
                continue;
            }

            $rezultat[] = $this->inregistreaza($date, $bridgeUrl, $bridgeToken);
        }

        if ($rezultat === []) {
            throw new SpvException(
                'La ' . $bridgeUrl . ' nu s-a găsit niciun certificat calificat. '
                . 'Verificați că tokenul este conectat.'
            );
        }

        return $rezultat;
    }

    /**
     * Inregistreaza certificatele pe care le anunta singur agentul unui client.
     *
     * Prin tunel nu avem cum sa sunam noi la calculatorul lui — de aceea vine el
     * cu lista, la pornire. Certificatele intra pe „tunel", legate de codul de
     * instalare cu care s-a legitimat agentul: dupa asta, comenzile lor stiu
     * singure pe unde sa plece.
     *
     * @param array<int, array> $lista certificatele citite de pe tokenul de acolo
     *
     * @return AnafCertificat[]
     */
    public function inroleazaDinAgent(array $lista, string $codInstalare): array
    {
        // Un singur certificat poate veni ca obiect, mai multe ca lista.
        if (isset($lista['thumbprint'])) {
            $lista = [$lista];
        }

        $rezultat = [];

        foreach ($lista as $date) {
            if (empty($date['thumbprint']) || !$this->esteCalificat($date)) {
                continue;
            }

            $certificat = $this->inregistreaza($date, null, null);

            $certificat->forceFill([
                'mod_legatura' => 'tunel',
                'bridge_token' => $codInstalare,
                // Adresa din retea nu mai inseamna nimic: comenzile trec prin punte.
                'bridge_url' => null,
            ])->save();

            $rezultat[] = $certificat;
        }

        return $rezultat;
    }

    /**
     * Magazinul Windows contine si certificate auto-semnate ale unor aplicatii.
     * Cele calificate sunt emise de o autoritate, deci au emitent diferit de subiect.
     */
    protected function esteCalificat(array $date): bool
    {
        $subiect = trim($date['subiect'] ?? '');
        $emitent = trim($date['emitent'] ?? '');

        return $subiect !== '' && $emitent !== '' && strcasecmp($subiect, $emitent) !== 0;
    }

    protected function inregistreaza(array $date, ?string $bridgeUrl, ?string $bridgeToken): AnafCertificat
    {
        $certificat = AnafCertificat::firstOrNew(['thumbprint' => $date['thumbprint']]);

        $certificat->fill([
            'serie' => $date['serie'] ?? null,
            'cn' => $date['cn'] ?? null,
            'subiect' => $date['subiect'] ?? null,
            'emitent' => $date['emitent'] ?? null,
            'email' => $date['email'] ?? null,
            'valabil_de_la' => $date['valabil_de_la'] ?? null,
            'valabil_pana_la' => $date['valabil_pana_la'] ?? null,
            /*
             * Certificatul cunoscut isi pastreaza starea: unul scos anume din
             * uz — pentru ca e al altui serviciu, SEAP de pilda — n-are voie sa
             * fie inviat de o simpla recitire a tokenelor. Numai cele noi intra
             * in uz din start.
             */
            'activ' => $certificat->exists ? (bool) $certificat->activ : true,
        ]);

        if ($bridgeUrl) {
            $certificat->bridge_url = $bridgeUrl;
            $certificat->bridge_token = $bridgeToken;
        }

        /*
         * Primul certificat inregistrat devine cel implicit — dar numai daca e
         * in uz. Altfel, un certificat scos anume din uz (al altui serviciu) ar
         * ajunge sa fie cel pe care cade toata lumea fara certificat atribuit.
         */
        if ($certificat->activ && !AnafCertificat::where('implicit', true)->exists()) {
            $certificat->implicit = true;
        }

        $certificat->save();

        return $certificat;
    }

    /**
     * Completeaza certificatul cu care se lucreaza acum cu ce a spus ANAF.
     *
     * Cand cererea a mers deja pe un certificat anume, nu are rost sa se mearga
     * si sa fie citit de pe calculatorul din configuratie: el e chiar acesta.
     * Se scriu doar seria si CNP-ul aflate de la ANAF.
     *
     * Intoarce null cand nu se stie cu care certificat s-a lucrat.
     */
    public function completeazaDinSpv(array $dateSpv): ?AnafCertificat
    {
        $certificat = $this->activ();

        if (!$certificat || !$certificat->exists) {
            return null;
        }

        if (!empty($dateSpv['serial'])) {
            $certificat->serie_anaf = $dateSpv['serial'];
        }

        if (!empty($dateSpv['cnp'])) {
            $certificat->cnp = $dateSpv['cnp'];
        }

        $certificat->ultima_utilizare = now();
        $certificat->save();

        return $certificat;
    }

    /**
     * Citeste certificatul de pe un bridge si il inregistreaza in evidenta.
     * Fara parametri se foloseste bridge-ul din configuratie.
     */
    public function sincronizeaza(array $dateSpv = [], ?string $bridgeUrl = null, ?string $bridgeToken = null): AnafCertificat
    {
        $url = $bridgeUrl ?: $this->config['bridge']['url'];
        $token = $bridgeToken ?: ($this->config['bridge']['token'] ?? null);

        /*
         * Fara adresa nu e nimic de intrebat. Spus asa, se intelege ce lipseste;
         * altfel cererea pleaca spre o adresa fara gazda si se intoarce cu
         * „Could not resolve host: certificat", care nu lamureste pe nimeni.
         */
        if (trim((string) $url) === '') {
            throw new SpvException(
                'Nu se știe pe ce calculator se află certificatul. '
                . 'Alegeți-l în fila „Certificate digitale" sau descoperiți-l pe calculatorul cu tokenul.'
            );
        }

        $raspuns = Http::withToken($token)
            ->timeout($this->config['timeout'])
            ->get(rtrim($url, '/') . '/certificat');

        if ($raspuns->failed()) {
            $payload = json_decode($raspuns->body(), true);

            throw new SpvException(
                'Certificatul nu a putut fi citit de la ' . $url . ': '
                . ($payload['detalii'] ?? $payload['eroare'] ?? 'HTTP ' . $raspuns->status())
            );
        }

        $date = $raspuns->json();

        $certificat = AnafCertificat::firstOrNew(['thumbprint' => $date['thumbprint']]);

        $certificat->fill([
            'serie' => $date['serie'] ?? null,
            'cn' => $date['cn'] ?? null,
            'subiect' => $date['subiect'] ?? null,
            'emitent' => $date['emitent'] ?? null,
            'email' => $date['email'] ?? null,
            'valabil_de_la' => $date['valabil_de_la'] ?? null,
            'valabil_pana_la' => $date['valabil_pana_la'] ?? null,
            'ultima_utilizare' => now(),
            // Vezi mai sus: starea unui certificat cunoscut nu se schimba de la sine.
            'activ' => $certificat->exists ? (bool) $certificat->activ : true,
        ]);

        // Bridge-ul de pe care a fost citit devine ruta lui de acces.
        if ($bridgeUrl) {
            $certificat->bridge_url = $bridgeUrl;
            $certificat->bridge_token = $bridgeToken;
        }

        if (isset($dateSpv['serial'])) {
            $certificat->serie_anaf = $dateSpv['serial'];
        }

        if (isset($dateSpv['cnp'])) {
            $certificat->cnp = $dateSpv['cnp'];
        }

        // Primul certificat inregistrat devine cel implicit.
        /*
         * Primul certificat inregistrat devine cel implicit — dar numai daca e
         * in uz. Altfel, un certificat scos anume din uz (al altui serviciu) ar
         * ajunge sa fie cel pe care cade toata lumea fara certificat atribuit.
         */
        if ($certificat->activ && !AnafCertificat::where('implicit', true)->exists()) {
            $certificat->implicit = true;
        }

        $certificat->save();

        return $this->activ = $certificat;
    }
}
