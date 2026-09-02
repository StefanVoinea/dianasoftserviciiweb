<?php

namespace App\Services\Anaf\Spv;

use App\Models\AnafCertificat;
use App\Models\AnafSocietate;
use App\Support\ContextCompanie;
use App\Support\PeRand;
use App\Models\SpvSolicitare;

/**
 * Registrul societatilor pentru care certificatul digital are drept de semnatura.
 *
 * Lista vine chiar de la ANAF: raspunsul /listaMesaje contine campul "cui" cu
 * toate CIF-urile accesibile certificatului. Pentru fiecare se solicita apoi din
 * SPV documentele de identificare si vectorul fiscal, din care se preia denumirea
 * folosita mai departe la mesaje si solicitari.
 */
class SocietatiService
{
    protected $client;
    protected $solicitari;
    protected $certificate;

    public function __construct(SpvClient $client, SolicitareService $solicitari, CertificatService $certificate)
    {
        $this->client = $client;
        $this->solicitari = $solicitari;
        $this->certificate = $certificate;
    }

    /**
     * Initializeaza sau actualizeaza lista de societati din certificat.
     *
     * @return array{gasite: int, noi: int, dezactivate: int, cif: array}
     */
    public function sincronizeaza(?AnafCertificat $certificat = null): array
    {
        // Cand certificatul e dat, interogarea SPV merge pe el (si pe bridge-ul lui).
        if ($certificat !== null) {
            $this->certificate->foloseste($certificat);
        }

        $raspuns = $this->client->listaMesajeBrut(1);
        $lista = $this->cifurile($raspuns);

        /*
         * Lista de CIF-uri vine in raspunsul la mesaje, iar cand in ziua cerută
         * nu e niciun mesaj, ANAF raspunde uneori doar cu motivul, fara ea. Se
         * mai intreaba o data, pe fereastra intreaga: acolo se gaseste aproape
         * sigur ceva, iar odata cu mesajele vine si lista.
         */
        if ($lista === '') {
            $raspuns = $this->client->listaMesajeBrut((int) config('anaf.spv.zile_max', 60));
            $lista = $this->cifurile($raspuns);
        }

        if ($lista === '') {
            // Ce a spus ANAF lamureste mai bine decat orice presupunere a noastra.
            $spuseDeAnaf = trim((string) ($raspuns['eroare'] ?? ''));

            throw new SpvException(
                'ANAF nu a returnat lista de CIF-uri pentru acest certificat. '
                . 'Verificați că tokenul este conectat și are drepturi în SPV.'
                . ($spuseDeAnaf !== '' ? ' ANAF a răspuns: „' . $spuseDeAnaf . '”.' : '')
            );
        }

        $cifuri = array_values(array_unique(array_filter(array_map('trim', explode(',', $lista)))));

        /*
         * Certificatul care a returnat lista devine cel asociat entitatilor.
         *
         * De obicei el e chiar cel cu care tocmai s-a vorbit, si atunci se
         * pastreaza asa cum e — se completeaza doar seria si CNP-ul aflate de la
         * ANAF. Numai cand nu se stie cu care s-a lucrat se merge sa fie citit de
         * pe calculatorul din configuratie.
         */
        $certificat = $certificat
            ?: $this->certificate->completeazaDinSpv([
                'serial' => $raspuns['serial'] ?? null,
                'cnp' => $raspuns['cnp'] ?? null,
            ])
            ?: $this->certificate->sincronizeaza([
                'serial' => $raspuns['serial'] ?? null,
                'cnp' => $raspuns['cnp'] ?? null,
            ]);

        $noi = $this->scrieCifurile($cifuri, $certificat, $raspuns);

        // Entitatile la care ACEST certificat nu mai are drepturi raman in evidenta,
        // dar devin inactive. Cele ale altor certificate nu sunt atinse.
        $dezactivate = AnafSocietate::where('certificat_id', $certificat->id)
            ->whereNotIn('cif', $cifuri)
            ->where('activ', true)
            ->update(['activ' => false]);

        return [
            'certificat' => $certificat->cn,
            'certificat_id' => $certificat->id,
            'gasite' => count($cifuri),
            'noi' => $noi,
            'dezactivate' => $dezactivate,
            'cif' => $cifuri,
        ];
    }

    /**
     * Scrie lista de CIF-uri primita de la ANAF si spune cate erau noi.
     *
     * Rand cu rand, asta insemna doua interogari de fiecare firma — la un
     * client cu doua sute cincizeci de entitati, cinci sute de drumuri la baza
     * de date pentru o lista care se scrie la fel peste tot. Aici se afla intai
     * ce exista, apoi cele vechi se schimba din doua interogari (una de fiecare
     * fel de firma) si cele noi se scriu dintr-una singura.
     *
     * „activ" e cuvantul ANAF-ului, si numai al lui. Scoaterea din uz, hotarata
     * de om, sta in alta coloana si nu se atinge aici — altfel prima
     * sincronizare i-ar sterge alegerea, iar entitatea ar invia singura, ca
     * certificatele dezactivate odinioara.
     *
     * @param  array<int, string>  $cifuri
     */
    protected function scrieCifurile(array $cifuri, AnafCertificat $certificat, array $raspuns): int
    {
        if ($cifuri === []) {
            return 0;
        }

        $deAcum = [
            'activ' => true,
            'cnp_reprezentant' => $raspuns['cnp'] ?? null,
            'serial_certificat' => $raspuns['serial'] ?? null,
            'certificat_id' => $certificat->id,
            'sincronizat_la' => now(),
        ];

        $stiute = AnafSocietate::whereIn('cif', $cifuri)->pluck('cif')->all();
        $peFeluri = [];

        foreach ($stiute as $cif) {
            $peFeluri[AnafSocietate::tipDupaCif($cif)][] = $cif;
        }

        // Firmele stiute: cate o interogare de fiecare fel, nu de fiecare firma.
        foreach ($peFeluri as $fel => $aleLui) {
            AnafSocietate::whereIn('cif', $aleLui)->update($deAcum + ['tip' => $fel]);
        }

        $noi = array_values(array_diff($cifuri, $stiute));

        if ($noi === []) {
            return 0;
        }

        /*
         * Cele noi se scriu dintr-o data. Se trece si company_id, fiindca
         * scrierea in bloc nu mai chema modelul care il pune singur — iar fara
         * el entitatile ar ramane ale nimanui.
         */
        $acum = now();
        $companie = ContextCompanie::curenta();

        AnafSocietate::insert(array_map(function (string $cif) use ($deAcum, $acum, $companie) {
            return $deAcum + [
                'cif' => $cif,
                'tip' => AnafSocietate::tipDupaCif($cif),
                'company_id' => $companie,
                'created_at' => $acum,
                'updated_at' => $acum,
            ];
        }, $noi));

        return count($noi);
    }

    /**
     * Lista de CIF-uri din raspunsul ANAF, ca text.
     *
     * De obicei vine un sir despartit prin virgule, dar raspunsul poate purta
     * si o lista adevarata; amandoua se citesc la fel de bine.
     */
    protected function cifurile(array $raspuns): string
    {
        $cui = $raspuns['cui'] ?? '';

        if (is_array($cui)) {
            $cui = implode(',', $cui);
        }

        return trim((string) $cui);
    }

    /**
     * Preia entitatile inrolate pe fiecare certificat dat. Un certificat fara
     * drepturi in SPV (sau al carui token nu e conectat) nu opreste restul.
     *
     * @param  AnafCertificat[]  $certificate
     * @return array{rezultate: array, erori: array}
     */
    public function sincronizeazaPentruCertificate(array $certificate): array
    {
        $rezultate = [];
        $erori = [];

        foreach ($certificate as $certificat) {
            try {
                $rezultate[] = $this->sincronizeaza($certificat);
            } catch (SpvException $e) {
                $erori[] = $certificat->cn . ': ' . $e->getMessage();
            }
        }

        return ['rezultate' => $rezultate, 'erori' => $erori];
    }

    /**
     * Trimite solicitarile SPV lipsa (date identificare si vector fiscal) pentru
     * societatile active. Cererile deja trimise si neprimite nu se repeta.
     *
     * Cu lista de CIF-uri data, se lucreaza doar pe ele: asa interfata poate
     * trimite firmele in transe si arata la cate s-a ajuns, in loc sa tina o
     * singura cerere web minute intregi.
     *
     * @param  array<int, string>  $cifuri          gol inseamna toate firmele active
     * @param  bool                $reinterpreteaza  recitirea documentelor deja descarcate
     * @return array{trimise: int, sarite: int, erori: array}
     */
    public function solicitaDocumente(
        array $tipuri = ['DATE IDENTIFICARE', 'VECTOR FISCAL'],
        ?int $userId = null,
        array $cifuri = [],
        bool $reinterpreteaza = true
    ): array {
        /*
         * Intai se citeste ce e deja adus, si abia pe urma se cere de la ANAF.
         *
         * Pentru fiecare firma se ia ULTIMUL document „DATE IDENTIFICARE" care
         * are fisier, se scoate din el denumirea si se string dosarele. Asa se
         * indreapta si numele citite gresit inainte, fara sa se ceara nimic.
         *
         * Nu se mai recitesc toate solicitarile, de orice tip: tinea minute
         * intregi si nu aducea nimic in plus pentru denumire.
         *
         * Se face doar la primul lot: pe urmatoarele n-ar mai avea ce afla.
         */
        $recitite = $reinterpreteaza
            ? $this->solicitari->citesteDenumirileDinIdentificare($cifuri)
            : ['citite' => 0, 'denumiri' => 0, 'cu_document' => []];

        $reinterpretate = $recitite['citite'];

        // Firmele care au deja documentul: de la ele nu se mai cere nimic.
        $auDocument = array_flip($recitite['cu_document']);

        $trimise = 0;
        $sarite = 0;
        $erori = [];

        // Numai cele in lucru: pe cele scoase din uz nu se cheltuie apeluri la ANAF.
        $firme = AnafSocietate::inLucru()
            ->when($cifuri !== [], function ($intrebare) use ($cifuri) {
                return $intrebare->whereIn('cif', $cifuri);
            })
            ->orderBy('cif')
            ->get();

        /*
         * Firmele se iau pe rand de la fiecare token, nu toate ale unuia si
         * apoi toate ale celuilalt: pauza ceruta de ANAF se tine pe fiecare
         * certificat, deci cat asteapta unul, celalalt poate lucra.
         */
        $firme = PeRand::intercalat($firme, function (AnafSocietate $societate) {
            return $societate->certificat_id ?: 0;
        });

        // Cererile in curs, aflate dintr-o singura interogare: pe rand, ele
        // insemnau o intrebare de fiecare firma si de fiecare fel de document.
        $inCurs = $this->cererileInCurs($firme, $tipuri);

        foreach ($firme as $societate) {
            // ANAF accepta aceste rapoarte doar pentru persoane juridice.
            if ($societate->tip === 'pf') {
                $sarite += count($tipuri);
                continue;
            }

            foreach ($tipuri as $tip) {
                /*
                 * Documentul de identificare adus deja se citeste, nu se cere
                 * inca o data: butonul cere datele lipsa, nu pe cele avute.
                 */
                if (strcasecmp($tip, 'DATE IDENTIFICARE') === 0 && isset($auDocument[$societate->cif])) {
                    $sarite++;
                    continue;
                }

                // Ce s-a aflat deja nu se mai cere: butonul cere datele lipsă.
                if ($this->areDeja($societate, $tip)) {
                    $sarite++;
                    continue;
                }

                if (isset($inCurs[$societate->cif . '|' . $tip])) {
                    $sarite++;
                    continue;
                }

                /*
                 * Cererea pleaca cu certificatul pe care e inrolata firma. Cu
                 * altul, SPV o refuza — si refuzul costa tot un apel din cele
                 * numarate de ANAF.
                 */
                $this->certificate->folosesteDupaId(
                    $societate->certificat_id ? (int) $societate->certificat_id : null
                );

                try {
                    $this->solicitari->solicita($societate->cif, $tip, [], $userId);
                    $trimise++;
                } catch (SpvException $e) {
                    $erori[] = $societate->cif . ' / ' . $tip . ': ' . $e->getMessage();
                }
            }
        }

        return [
            'trimise' => $trimise,
            'sarite' => $sarite,
            'reinterpretate' => $reinterpretate,
            'denumiri' => $recitite['denumiri'],
            'erori' => $erori,
        ];
    }

    /**
     * S-a aflat deja documentul acesta pentru firma?
     *
     * Butonul cere „datele lipsa": ce s-a citit odata dintr-un document ajuns
     * in SPV nu se mai cere a doua oara. Cine vrea sa le improspateze cere
     * documentul anume, din fila de solicitari.
     */
    protected function areDeja(AnafSocietate $societate, string $tip): bool
    {
        $cand = strcasecmp($tip, 'VECTOR FISCAL') === 0
            ? $societate->vector_la
            : $societate->date_identificare_la;

        return $cand !== null;
    }

    /**
     * Cererile trimise si inca fara raspuns, pentru firmele si felurile acestea.
     *
     * O cerere trimisa azi, sau una care isi asteapta inca raspunsul, nu se
     * repeta. Intrebarea se punea pe rand pentru fiecare firma si fiecare fel
     * de document — la doua sute cincizeci de firme, cinci sute de drumuri la
     * baza de date inainte de orice apel catre ANAF.
     *
     * @param  iterable<AnafSocietate>  $firme
     * @param  array<int, string>  $tipuri
     * @return array<string, true>  cheia e „cif|tip"
     */
    protected function cererileInCurs(iterable $firme, array $tipuri): array
    {
        $cifuri = [];

        foreach ($firme as $societate) {
            $cifuri[] = $societate->cif;
        }

        if ($cifuri === [] || $tipuri === []) {
            return [];
        }

        $randuri = SpvSolicitare::whereIn('cif', $cifuri)
            ->whereIn('tip_document', $tipuri)
            ->where(function ($query) {
                $query->whereNull('data_afisare')
                    ->orWhere('data_solicitarii', '>=', now()->startOfDay());
            })
            ->get(['cif', 'tip_document']);

        $inCurs = [];

        foreach ($randuri as $rand) {
            $inCurs[$rand->cif . '|' . $rand->tip_document] = true;
        }

        return $inCurs;
    }

    /** Denumirile cunoscute, pentru afisarea mesajelor si solicitarilor SPV. */
    public static function denumiri(): array
    {
        return AnafSocietate::whereNotNull('denumire')->pluck('denumire', 'cif')->all();
    }
}
