<?php

namespace App\Services\Anaf\Bridge;

use App\Models\AnafCertificat;
use App\Models\BridgeComanda;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

/**
 * Coada de comenzi dintre server și programul local al clientului.
 *
 * Serverul scrie o comandă și așteaptă; agentul de la client o ia, o duce la
 * programul lui local și scrie răspunsul înapoi. Nimic nu pleacă dinspre server
 * spre rețeaua clientului, deci nu e nevoie de niciun port deschis acolo.
 */
class Punte
{
    /** Cât așteaptă aplicația răspunsul programului local. */
    public const ASTEPTARE_SECUNDE = 120;

    /** Cât ține agentul linia deschisă întrebând dacă are ceva de făcut. */
    public const PANDA_SECUNDE = 25;

    /** Cât de des se uită serverul dacă a venit răspunsul, în microsecunde. */
    protected const PAS_ASTEPTARE = 150000;

    protected $licente;

    public function __construct(Licente $licente)
    {
        $this->licente = $licente;
    }

    /**
     * Cât timp după ultima pândă mai socotim că agentul e treaz.
     *
     * El întreabă din 25 în 25 de secunde, iar când serverul nu răspunde așteaptă
     * din ce în ce mai mult, până la un minut. Două minute și jumătate acoperă și
     * cazul acela, fără să declarăm mort un agent care doar a răbdat puțin.
     */
    public const AGENT_TREAZ_SECUNDE = 150;

    /** Certificatele legate prin tunel primesc adresa punții, nu una din rețea. */
    public function esteTunel(?AnafCertificat $certificat): bool
    {
        return $certificat && $certificat->mod_legatura === 'tunel';
    }

    /**
     * A mai întrebat agentul de curând?
     *
     * Dacă nu, nu are rost să punem comanda în coadă și să ținem omul un minut
     * în așteptare: calculatorul e închis, sau agentul nu rulează.
     */
    public function agentulEsteTreaz(AnafCertificat $certificat): bool
    {
        return $certificat->agent_vazut_la
            && $certificat->agent_vazut_la->gt(now()->subSeconds(self::AGENT_TREAZ_SECUNDE));
    }

    public function adresa(AnafCertificat $certificat): string
    {
        return rtrim(config('app.url'), '/') . '/api/punte/' . $certificat->id;
    }

    /**
     * Cererea vine chiar de la serverul nostru?
     *
     * Puntea e o rută publică — trebuie să fie, ca aplicația să o cheme ca pe
     * orice program local. Jetonul semnat e cel care o apără: numai serverul îl
     * poate face, pentru că numai el are cheia privată.
     */
    public function cerereDeLaServer(Request $request): bool
    {
        $jeton = (string) $request->bearerToken();

        if (!$this->licente->areChei()) {
            // Fără chei nu se poate verifica nimic; puntea rămâne închisă.
            return false;
        }

        return $this->licente->jetonValid($jeton);
    }

    /**
     * Certificatele pe care le deservește agentul care bate la ușă.
     *
     * Se legitimează doar cu codul lui de instalare — nu trebuie să știe ce
     * certificate sunt pe calculatorul lui, nici să fie reconfigurat când se
     * schimbă tokenul din USB. Pe același calculator pot fi mai multe.
     *
     * @return \Illuminate\Support\Collection<int, AnafCertificat>
     */
    public function certificateleAgentului(Request $request)
    {
        $cod = (string) $request->bearerToken();

        if ($cod === '') {
            return collect();
        }

        return AnafCertificat::query()->toateCompaniile()
            ->where('mod_legatura', 'tunel')
            ->where('bridge_token', $cod)
            ->get();
    }

    /** Varianta pentru un singur certificat, folosită la verificări punctuale. */
    public function certificatulAgentului(Request $request): ?AnafCertificat
    {
        return $this->certificateleAgentului($request)->first();
    }

    /**
     * De ce nu i se potrivește codul agentului care bate la ușă.
     *
     * Sunt două pricini care arată la fel din afară — codul nu e legat de
     * niciun certificat, sau certificatul lui nu e pe legătura „prin tunel" —
     * dar se îndreaptă din locuri diferite. Motivul se spune pe șleau, ca omul
     * să nu ghicească.
     */
    public function deCeNuAgentul(Request $request): string
    {
        $cod = (string) $request->bearerToken();

        if ($cod === '') {
            return 'Cererea nu poartă niciun cod de acces.';
        }

        $aleCodului = AnafCertificat::query()->toateCompaniile()
            ->where('bridge_token', $cod)
            ->get();

        if ($aleCodului->isEmpty()) {
            return 'Codul de acces nu este legat de niciun certificat din aplicație.'
                . ' Porniți agentul o dată cu tokenul conectat, ca să se înroleze singur.';
        }

        return 'Certificatul „' . $aleCodului->first()->cn . '” nu este pe legătura „prin tunel”.'
            . ' Comutați-l din aplicație: SPV → Certificate digitale → Calculator → Prin tunel.';
    }

    /** Pune în coadă cererea venită de la aplicație. */
    public function pune(AnafCertificat $certificat, Request $request, string $cale): BridgeComanda
    {
        $intrebare = $request->getQueryString();
        $antete = $this->anteteleDeDus($request);

        $corp = $request->getContent();

        /*
         * Corpul multipart nu se poate lua cu getContent(): PHP il consuma in
         * $_POST/$_FILES si lasa fluxul gol. Fara pasul acesta, documentul de
         * arhivat pleca prin tunel fara continut, iar programul local raspundea
         * „Cererea nu contine documentul de arhivat". Se reface din campuri si
         * fisiere, cu granita proprie.
         */
        if ($corp === '' && stripos((string) $request->header('content-type'), 'multipart/form-data') !== false) {
            $corp = $this->multipartRefacut($request, $antete);
        }

        $fisier = null;

        if ($corp !== '') {
            $fisier = BridgeComanda::DOSAR . '/cmd_' . uniqid('', true) . '.bin';
            Storage::put($fisier, $corp);
        }

        /*
         * Legitimarea se schimbă aici, pentru că cele două uși cer lucruri
         * diferite: puntea cere jeton semnat de server — el a și deschis-o —
         * iar programul local cere ce știe el. Un program licențiat recunoaște
         * jetonul; unul care abia s-a instalat, doar codul lui de instalare.
         */
        $antete['authorization'] = 'Bearer ' . ($certificat->licenta_pana_la
            ? $this->licente->jeton()
            : $certificat->bridge_token);

        return BridgeComanda::create([
            'company_id' => $certificat->company_id,
            'certificat_id' => $certificat->id,
            'metoda' => $request->method(),
            'cale' => $cale . ($intrebare ? '?' . $intrebare : ''),
            'antete' => $antete,
            'corp_fisier' => $fisier,
            'stare' => 'asteapta',
        ]);
    }

    /**
     * Reface corpul multipart al cererii, ca sa poata fi dus prin tunel.
     *
     * Se scriu intai campurile de formular, apoi fisierele, sub o granita nou
     * aleasa; antetul content-type primeste granita aceasta, altfel programul
     * local ar cauta-o pe cea veche si n-ar gasi nimic.
     */
    protected function multipartRefacut(Request $request, array &$antete): string
    {
        $granita = 'punte' . bin2hex(random_bytes(16));
        $corp = '';

        foreach ($this->campuriPlate($request->request->all()) as $nume => $valoare) {
            $corp .= '--' . $granita . "\r\n"
                . 'Content-Disposition: form-data; name="' . $nume . '"' . "\r\n\r\n"
                . $valoare . "\r\n";
        }

        foreach ($this->campuriPlate($request->files->all()) as $nume => $fisier) {
            $numeFisier = str_replace(['"', "\r", "\n"], '', $fisier->getClientOriginalName() ?: 'document');

            $corp .= '--' . $granita . "\r\n"
                . 'Content-Disposition: form-data; name="' . $nume . '"; filename="' . $numeFisier . '"' . "\r\n"
                . 'Content-Type: application/octet-stream' . "\r\n\r\n"
                . file_get_contents($fisier->getRealPath()) . "\r\n";
        }

        $antete['content-type'] = 'multipart/form-data; boundary=' . $granita;

        return $corp . '--' . $granita . '--' . "\r\n";
    }

    /**
     * Campurile cererii, aduse la nume plate: PHP desface „fisiere[0]" intr-o
     * matrice imbricata, iar la refacere numele trebuie sa arate ca la plecare.
     *
     * @return array<string, mixed>
     */
    protected function campuriPlate(array $valori, string $prefix = ''): array
    {
        $plate = [];

        foreach ($valori as $cheie => $valoare) {
            $nume = $prefix === '' ? (string) $cheie : $prefix . '[' . $cheie . ']';

            if (is_array($valoare)) {
                $plate += $this->campuriPlate($valoare, $nume);
            } else {
                $plate[$nume] = $valoare;
            }
        }

        return $plate;
    }

    /**
     * Antetele care au înțeles la celălalt capăt.
     *
     * Se lasă deoparte cele ale legăturii noastre — gazdă, lungime, cookie —
     * dar autorizarea merge mai departe: programul local o cere, iar prin tunel
     * el trebuie să primească exact ce ar fi primit dacă serverul l-ar fi sunat
     * de-a dreptul (jetonul semnat sau, cât timp nu e licențiat, codul lui).
     */
    protected function anteteleDeDus(Request $request): array
    {
        $lasate = ['host', 'content-length', 'connection', 'cookie', 'accept-encoding'];
        $antete = [];

        foreach ($request->headers->all() as $nume => $valori) {
            if (!in_array($nume, $lasate, true) && isset($valori[0])) {
                $antete[$nume] = $valori[0];
            }
        }

        return $antete;
    }

    /** Așteaptă răspunsul agentului. Null înseamnă că n-a venit la timp. */
    public function asteapta(BridgeComanda $comanda, ?int $secunde = null): ?BridgeComanda
    {
        $pana = microtime(true) + ($secunde ?: self::ASTEPTARE_SECUNDE);

        while (microtime(true) < $pana) {
            $proaspata = $comanda->fresh();

            if ($proaspata === null) {
                return null;
            }

            if (in_array($proaspata->stare, ['gata', 'eroare'], true)) {
                return $proaspata;
            }

            usleep(self::PAS_ASTEPTARE);
        }

        $comanda->curata();

        return null;
    }

    /**
     * Prima comandă care așteaptă, pentru agentul acestui certificat.
     *
     * Ține linia deschisă până apare ceva sau până se împlinește pânda: așa
     * comanda pleacă îndată ce e scrisă, fără întrebări repetate.
     */
    public function urmatoarea($certificate, ?int $secunde = null): ?BridgeComanda
    {
        $iduri = $certificate instanceof AnafCertificat
            ? [$certificate->id]
            : collect($certificate)->pluck('id')->all();

        if ($iduri === []) {
            return null;
        }

        // Se ține minte că agentul e treaz: altfel n-am ști cui are rost să-i trimitem.
        AnafCertificat::query()->toateCompaniile()
            ->whereIn('id', $iduri)
            ->update(['agent_vazut_la' => now()]);

        $pana = microtime(true) + ($secunde ?: self::PANDA_SECUNDE);

        do {
            $comanda = BridgeComanda::query()
                ->whereIn('certificat_id', $iduri)
                ->where('stare', 'asteapta')
                ->orderBy('id')
                ->first();

            if ($comanda) {
                $comanda->update(['stare' => 'luata', 'luata_la' => now()]);

                return $comanda;
            }

            usleep(self::PAS_ASTEPTARE);
        } while (microtime(true) < $pana);

        return null;
    }
}
