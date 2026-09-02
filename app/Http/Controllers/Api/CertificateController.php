<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AnafCertificat;
use App\Models\CertificatAbonat;
use App\Models\CertificatUtilizator;
use App\Services\Anaf\Bridge\LicentiereBridge;
use App\Services\Anaf\Bridge\Punte;
use App\Services\Anaf\CaleWindows;
use App\Services\Anaf\Format;
use App\Services\Anaf\Jurnal;
use App\Services\Anaf\Spv\CertificatService;
use App\Services\Anaf\Spv\Contracts\SpvTransport;
use App\Services\Anaf\Spv\KitBridge;
use App\Services\Anaf\Spv\SocietatiService;
use App\Services\Anaf\Spv\SpvException;
use App\Support\Aplicatia;
use App\Support\ContextUtilizator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class CertificateController extends Controller
{
    /**
     * Cat tine vestea ca un token isi asteapta codul.
     *
     * Programul local o spune din doua in doua minute cat sta fereastra
     * deschisa, deci cinci e destul cat sa nu se piarda o veste intarziata si
     * putin cat sa nu se ceara codul pentru o fereastra inchisa de mult.
     */
    protected const PIN_VESTE_MINUTE = 5;

    public function index()
    {
        $certificate = AnafCertificat::withCount('societati')
            ->with(['utilizatori' => function ($query) {
                $query->orderBy('email');
            }])
            ->orderByDesc('activ')
            ->orderBy('valabil_pana_la')
            ->get()
            ->map(function (AnafCertificat $certificat) {
                return [
                    'id' => $certificat->id,
                    'cn' => $certificat->cn,
                    'subiect' => $certificat->subiect,
                    'emitent' => $certificat->emitent,
                    'serie' => $certificat->serie,
                    'serie_anaf' => $certificat->serie_anaf,
                    'email' => $certificat->email,
                    'cnp' => $certificat->cnp,
                    'thumbprint' => $certificat->thumbprint,
                    'bridge_url' => $certificat->bridge_url ?: config('anaf.spv.bridge.url'),
                    'bridge_implicit' => $certificat->bridge_url === null,
                    'arhiva_cale' => $certificat->arhiva_cale,
                    'monitorizare_cale' => $certificat->monitorizare_cale,
                    'monitorizare_activa' => (bool) $certificat->monitorizare_activa,
                    'monitorizare_cadenta' => (int) ($certificat->monitorizare_cadenta ?: 5),
                    // Lipsa coloanei (instalare veche) inseamna purtarea veche: semneaza, nu depune.
                    'monitorizare_semneaza' => $certificat->monitorizare_semneaza === null ? true : (bool) $certificat->monitorizare_semneaza,
                    'monitorizare_depune' => (bool) $certificat->monitorizare_depune,
                    'monitorizare_la' => Format::dataOra($certificat->monitorizare_la),
                    'licenta_pana_la' => Format::dataOra($certificat->licenta_pana_la),
                    // Starea PIN-ului la ultima proba: 'gata', 'refuzat', 'lipsa' sau gol
                    'pin_stare' => $certificat->pin_stare,
                    'pin_motiv' => $certificat->pin_motiv,
                    'pin_verificat_la' => Format::dataOra($certificat->pin_verificat_la),
                    'pin_de_la_distanta' => (bool) $certificat->pin_de_la_distanta,
                    'mod_legatura' => $certificat->mod_legatura ?: 'direct',
                    'agent_vazut_la' => Format::dataOra($certificat->agent_vazut_la),
                    'agent_treaz' => app(Punte::class)->agentulEsteTreaz($certificat),
                    'versiune_bridge' => $certificat->versiune_bridge,
                    // De cand are versiunea pe care o are — nu de cand a fost vazut
                    'versiune_la' => Format::dataOra($certificat->versiune_la),
                    'implicit' => $certificat->implicit,
                    'valabil_de_la' => Format::dataOra($certificat->valabil_de_la),
                    'valabil_pana_la' => Format::dataOra($certificat->valabil_pana_la),
                    'expira_la' => Format::data($certificat->valabil_pana_la),
                    'zile_ramase' => $certificat->zile_ramase,
                    'expirat' => $certificat->expirat,
                    'activ' => $certificat->activ,
                    'ultima_utilizare' => Format::dataOra($certificat->ultima_utilizare),
                    'avertizat_la' => Format::dataOra($certificat->avertizat_la),
                    'entitati' => $certificat->societati_count,
                    'utilizatori' => $certificat->utilizatori->map(function (CertificatUtilizator $utilizator) {
                        return [
                            'id' => $utilizator->id,
                            'email' => $utilizator->email,
                            'nume' => $utilizator->nume,
                            'are_cont' => $utilizator->user_id !== null,
                        ];
                    }),
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $certificate,
            'abonati' => CertificatAbonat::orderBy('email')->get(),
            'zile_avertizare' => config('anaf.certificate.zile_avertizare'),
            // Versiunea de acum a programului local: dupa ea se vede cine a ramas in urma
            'versiune_program' => app(\App\Services\Anaf\Bridge\ActualizareBridge::class)->versiunea(),
        ]);
    }

    /**
     * Inregistreaza toate tokenele conectate acum la un bridge. Pe acelasi
     * calculator se pot conecta succesiv mai multe tokene, iar fiecare client
     * poate avea bridge-uri pe mai multe calculatoare din retea.
     */
    public function descopera(Request $request, CertificatService $serviciu, SocietatiService $societati)
    {
        $date = $request->validate([
            'bridge_url' => 'nullable|url|max:255',
            'bridge_token' => 'nullable|string|max:255',
            'fara_entitati' => 'nullable|boolean',
        ]);

        $url = $date['bridge_url'] ?? config('anaf.spv.bridge.url');
        $token = $date['bridge_token'] ?? config('anaf.spv.bridge.token');

        try {
            $gasite = $serviciu->descoperaPeBridge($url, $token);
        } catch (SpvException $e) {
            Jurnal::esec('certificat_sincronizare', 'Citirea certificatelor de la ' . $url . ' a eșuat: ' . $e->getMessage());

            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }

        // Fiecare certificat isi aduce si entitatile pe care le poate reprezenta.
        $entitati = empty($date['fara_entitati'])
            ? $societati->sincronizeazaPentruCertificate($gasite)
            : ['rezultate' => [], 'erori' => []];

        $totalEntitati = collect($entitati['rezultate'])->sum('gasite');

        Jurnal::scrie(
            'certificat_sincronizare',
            'A citit ' . count($gasite) . ' certificat(e) de la ' . $url
                . ' și ' . $totalEntitati . ' entități înrolate pe ele',
            [
                'calculator' => $url,
                'certificate' => collect($gasite)->pluck('cn')->all(),
                'entitati' => $entitati['rezultate'],
                'erori' => $entitati['erori'],
            ],
            null,
            $entitati['erori'] === []
        );

        return response()->json([
            'success' => true,
            'data' => $gasite,
            'entitati' => [
                'total' => $totalEntitati,
                'pe_certificat' => $entitati['rezultate'],
                'erori' => $entitati['erori'],
            ],
        ]);
    }

    /** Citeste certificatul curent de pe bridge-ul configurat. */
    public function sincronizeaza(CertificatService $serviciu)
    {
        try {
            $certificat = $serviciu->sincronizeaza();
        } catch (SpvException $e) {
            Jurnal::esec('certificat_sincronizare', 'Citirea certificatului de pe token a eșuat: ' . $e->getMessage());

            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }

        Jurnal::scrie(
            'certificat_sincronizare',
            'A citit certificatul „' . $certificat->cn . '” (expiră la '
                . optional($certificat->valabil_pana_la)->format('d.m.Y') . ')',
            ['certificat' => $certificat->cn, 'serie' => $certificat->serie]
        );

        return response()->json(['success' => true, 'data' => $certificat]);
    }

    /**
     * Reînnoiește acum licența programului local al acestui certificat.
     *
     * De obicei se face singură, în fiecare dimineață, cu zece zile înainte de
     * expirare. Butonul e pentru cazurile în care nu se poate aștepta: un
     * calculator nou, unul care a stat închis, sau un abonament tocmai plătit.
     */
    public function reinnoiesteLicenta(AnafCertificat $certificat, LicentiereBridge $licentiere)
    {
        $rezultat = $licentiere->reinnoieste($certificat, true);

        if (!$rezultat['emisa']) {
            return response()->json([
                'success' => false,
                'message' => 'Licența nu a putut fi trimisă: ' . ($rezultat['motiv'] ?: 'motiv necunoscut'),
            ], 422);
        }

        return response()->json([
            'success' => true,
            'message' => 'Licență trimisă, valabilă până la ' . Format::dataOra($rezultat['expira']) . '.',
            'data' => $certificat->fresh(),
        ]);
    }

    /**
     * Kitul de instalare a bridge-ului, cu token propriu pentru calculatorul
     * pe care va fi instalat.
     */
    public function kit(Request $request, KitBridge $kit)
    {
        $date = $request->validate([
            'port' => 'nullable|integer|min:1|max:65535',
        ]);

        try {
            $arhiva = $kit->construieste(null, (int) ($date['port'] ?? 8099));
        } catch (SpvException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }

        Jurnal::scrie(
            'kit_descarcare',
            'A descărcat kitul de instalare a bridge-ului (token nou generat)',
            ['nume' => $arhiva['nume']]
        );

        return response()->download($arhiva['cale'], $arhiva['nume'], [
            'Content-Type' => 'application/zip',
            // Tokenul e si in arhiva, dar il expunem si aici pentru interfata.
            'X-Bridge-Token' => $arhiva['token'],
        ])->deleteFileAfterSend(true);
    }

    /**
     * Duce PIN-ul scris de om pana la fereastra care il asteapta.
     *
     * Codul trece o singura data, prin cererea aceasta, si nu se opreste
     * nicaieri: nu se scrie in baza de date, nu intra in jurnal si nu se
     * intoarce inapoi. In jurnal ramane doar ca s-a trimis un cod pentru
     * tokenul cutare — atat cat sa se stie cine a facut-o si cand.
     *
     * Merge numai la tokenele pentru care omul a pornit anume facilitatea: e
     * cheia lui, si el hotaraste daca vrea s-o poata trimite de la distanta.
     */
    public function trimitePin(Request $request, AnafCertificat $certificat, CertificatService $certificate)
    {
        $date = $request->validate([
            'pin' => 'required|string|max:64',
        ]);

        if (!$certificat->pin_de_la_distanta) {
            return response()->json([
                'success' => false,
                'message' => 'Pentru tokenul „' . $certificat->cn . '" nu e pornită trimiterea PIN-ului de la distanță.',
            ], 422);
        }

        $certificate->foloseste($certificat);

        $transport = app(SpvTransport::class);

        if (!method_exists($transport, 'scriePinul')) {
            return response()->json([
                'success' => false,
                'message' => 'Legătura cu programul local nu poate duce PIN-ul.',
            ], 422);
        }

        $iesit = $transport->scriePinul($date['pin']);

        // Codul nu mai are ce cauta in memorie de aici incolo.
        unset($date);

        Jurnal::scrie(
            'pin_trimis',
            ($iesit['scris'] ? 'A trimis PIN-ul pentru tokenul „' : 'A încercat să trimită PIN-ul pentru tokenul „')
                . $certificat->cn . '”'
                . ($iesit['scris'] ? '' : ': ' . $iesit['motiv']),
            // Aici nu intra codul, si nici vreo bucata din el.
            ['certificat_id' => $certificat->id],
            null,
            $iesit['scris']
        );

        $certificat->update([
            'pin_stare' => $iesit['scris'] ? 'gata' : 'refuzat',
            'pin_motiv' => $iesit['scris'] ? null : $iesit['motiv'],
            'pin_verificat_la' => now(),
            // Codul a fost scris: nu mai are cine sa fie intrebat de el.
            'pin_cerut_de' => $iesit['scris'] ? null : $certificat->pin_cerut_de,
            'pin_cerut_din' => $iesit['scris'] ? null : $certificat->pin_cerut_din,
        ]);

        return response()->json([
            'success' => $iesit['scris'],
            'message' => $iesit['scris']
                ? 'PIN-ul a fost scris în fereastra tokenului.'
                : $iesit['motiv'],
        ], $iesit['scris'] ? 200 : 422);
    }

    /**
     * Tokenurile care isi asteapta acum PIN-ul si pentru care omul a pornit
     * trimiterea de la distanta.
     *
     * De aici afla fila si telefonul ca au ce cere: fara asta, fiecare loc din
     * aplicatie ar fi trebuit sa duca vestea mai departe, iar o lucrare pornita
     * din alta parte — dosarul urmarit, sarcina de noapte — n-ar fi spus-o
     * nimanui.
     */
    public function pinInAsteptare()
    {
        $omul = optional(ContextUtilizator::curent())->id;
        $deUnde = Aplicatia::curenta();

        $tokene = AnafCertificat::where('activ', true)
            ->where('pin_stare', 'asteapta')
            ->where('pin_de_la_distanta', true)
            /*
             * Numai vestea proaspata. Fereastra se poate inchide si fara stirea
             * noastra — omul se duce pana la calculator si scrie codul acolo, ori
             * o inchide de tot — si atunci nimeni nu ne mai spune nimic.
             *
             * Cat sta deschisa, programul local o spune din doua in doua minute,
             * deci o veste mai veche de atat inseamna ca fereastra nu mai e. Fara
             * termenul acesta, o veste de dimineata cerea codul si dupa-amiaza.
             */
            ->where('pin_verificat_la', '>=', now()->subMinutes(self::PIN_VESTE_MINUTE))
            ->where(function ($intrebare) use ($omul, $deUnde) {
                /*
                 * Fiecare e intrebat unde a apasat: cel care a pornit lucrarea
                 * de pe telefon nu are de ce sa fie intrebat intr-o fila din
                 * browser, si nici invers.
                 */
                $intrebare->where(function ($alLui) use ($omul, $deUnde) {
                    $alLui->where('pin_cerut_de', $omul)
                        ->where('pin_cerut_din', $deUnde);
                })
                /*
                 * …afara de lucrarile pornite de la sine, care n-au pe nimeni in
                 * spate: acelea se arata oriunde, fiindca oricine e prin preajma
                 * le poate dezlega.
                 */
                ->orWhere('pin_cerut_din', Aplicatia::FUNDAL)
                ->orWhereNull('pin_cerut_din');
            })
            ->orderByDesc('pin_verificat_la')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $tokene->map(function (AnafCertificat $certificat) {
                return [
                    'id' => $certificat->id,
                    'cn' => $certificat->cn,
                    // Ce scrie pe fereastra deschisa, asa cum a citit-o programul local
                    'fereastra' => $certificat->pin_motiv,
                    'de_cand' => Format::dataOra($certificat->pin_verificat_la),
                    // Lucrarile pornite de la sine se spun asa: omul sa stie ca
                    // nu el a cerut-o, si totusi el o poate dezlega.
                    'din_fundal' => $certificat->pin_cerut_din === Aplicatia::FUNDAL
                        || $certificat->pin_cerut_din === null,
                ];
            })->all(),
        ]);
    }

    /** Sta deschisa acum o fereastra de PIN pe calculatorul acestui token? */
    public function fereastraPin(AnafCertificat $certificat, CertificatService $certificate)
    {
        $certificate->foloseste($certificat);

        $transport = app(SpvTransport::class);

        $fereastra = method_exists($transport, 'fereastraDePin')
            ? $transport->fereastraDePin()
            : null;

        return response()->json([
            'success' => true,
            'data' => [
                'stiuta' => $fereastra !== null,
                'deschisa' => $fereastra['deschisa'] ?? false,
                'titlu' => $fereastra['titlu'] ?? '',
                'proces' => $fereastra['proces'] ?? '',
                'pin_de_la_distanta' => (bool) $certificat->pin_de_la_distanta,
            ],
        ]);
    }

    /**
     * Cere de la token o semnatura mica, ca sa se afle daca PIN-ul e deja dat.
     *
     * Citirea certificatului nu atinge cheia privata, deci nu cere niciodata
     * PIN. El se cere abia cand cheia e chiar folosita — la semnare, sau la
     * intrarea in SPV. Pana acum, primul lucru care avea nevoie de el se
     * impiedica de fereastra deschisa pe calculatorul clientului, adesea in
     * mijlocul unei descarcari de zeci de documente si adesea pe alt ecran
     * decat al omului care apasase.
     *
     * Aici se cere semnatura dinadins, la intrarea in aplicatie: daca driverul
     * are PIN-ul in minte, proba se face pe loc si nu se vede nimic; daca nu-l
     * are, fereastra se deschide atunci, cand nu asteapta nimeni nimic dupa ea.
     * Proba e deci si declansatorul — nu se poate afla fara sa se forteze.
     *
     * PIN-ul nu trece prin aplicatie si nu se pastreaza nicaieri: el ramane
     * intre om si driverul tokenului. Se tine minte doar ce s-a vazut.
     */
    public function verificaPin(Request $request, CertificatService $certificate)
    {
        $cerut = $request->input('certificat');

        $deVerificat = AnafCertificat::where('activ', true)
            ->when($cerut, function ($intrebare) use ($cerut) {
                return $intrebare->where('id', $cerut);
            })
            ->get()
            ->filter(function (AnafCertificat $certificat) {
                $punte = app(Punte::class);

                /*
                 * Un calculator inchis nu se probeaza: cererea ar astepta pana
                 * i-ar expira rabdarea, iar intrarea in aplicatie ar parea
                 * incetinita fara pricina.
                 */
                return $punte->esteTunel($certificat)
                    ? $punte->agentulEsteTreaz($certificat)
                    : (bool) $certificat->bridge_url;
            });

        $stari = [];

        foreach ($deVerificat as $certificat) {
            $stari[] = ['certificat' => $certificat->id, 'cn' => $certificat->cn]
                + $this->probaPin($certificat, $certificate);
        }

        return response()->json(['success' => true, 'data' => $stari]);
    }

    /**
     * Proba propriu-zisa a unui certificat.
     *
     * Rabdarea e larga dinadins: intre cerere si raspuns sta un om care scrie
     * PIN-ul, nu doar o masina. Un esec nu opreste nimic — se scrie starea si se
     * merge mai departe, ca intrarea in aplicatie sa nu atarne de un token.
     *
     * @return array{stare: string, motiv: string, verificat_la: string|null}
     */
    protected function probaPin(AnafCertificat $certificat, CertificatService $certificate): array
    {
        $certificate->foloseste($certificat);
        $bridge = $certificate->bridge();

        if (empty($bridge['url'])) {
            return $this->tinePin($certificat, 'lipsa', 'certificatul nu are calculator configurat');
        }

        try {
            $raspuns = Http::withToken($bridge['token'])
                ->withHeaders(['X-Thumbprint' => (string) $certificat->thumbprint])
                ->timeout(150)
                ->get(rtrim($bridge['url'], '/') . '/pin');
        } catch (\Exception $e) {
            return $this->tinePin($certificat, 'lipsa', 'calculatorul cu tokenul nu răspunde');
        }

        // Kiturile mai vechi nu cunosc proba; nu e o defectiune, doar nu se stie.
        if ($raspuns->status() === 404) {
            return $this->tinePin($certificat, '', 'programul de la client nu cunoaște încă proba PIN-ului');
        }

        if ($raspuns->failed()) {
            $primit = json_decode($raspuns->body(), true);

            return $this->tinePin($certificat, 'lipsa', $primit['detalii'] ?? $primit['eroare'] ?? 'proba a eșuat');
        }

        $date = $raspuns->json();

        if (!empty($date['gata'])) {
            /*
             * „cerut" spune daca fereastra s-a deschis chiar acum. Se scrie in
             * jurnal numai atunci: o proba tacuta, care se face de fiecare data
             * la intrare, n-are ce cauta acolo.
             */
            if (!empty($date['cerut'])) {
                Jurnal::scrie(
                    'certificat_pin',
                    'S-a introdus PIN-ul pentru certificatul ' . $certificat->cn,
                    ['secunde' => $date['secunde'] ?? null]
                );
            }

            return $this->tinePin($certificat, 'gata', '');
        }

        return $this->tinePin($certificat, 'refuzat', (string) ($date['motiv'] ?? 'PIN-ul nu a fost introdus'));
    }

    /** Scrie starea la certificat si o intoarce, in forma in care o asteapta fila. */
    protected function tinePin(AnafCertificat $certificat, string $stare, string $motiv): array
    {
        $certificat->update([
            'pin_stare' => $stare ?: null,
            'pin_verificat_la' => now(),
            'pin_motiv' => $motiv !== '' ? mb_substr($motiv, 0, 250) : null,
        ]);

        return [
            'stare' => $stare,
            'motiv' => $motiv,
            'verificat_la' => now()->format('d.m.Y H:i'),
        ];
    }

    /**
     * Dosarele de pe calculatorul acestui certificat, pentru alegerea arhivei.
     *
     * Alegerea nu se poate face cu un selector din browser: dosarul e pe
     * calculatorul cu tokenul, care de obicei nu e cel din fata omului. Lista
     * vine deci de la programul local de acolo.
     */
    public function foldere(Request $request, AnafCertificat $certificat, CertificatService $certificate)
    {
        $certificate->foloseste($certificat);
        $bridge = $certificate->bridge();

        try {
            $raspuns = Http::withToken($bridge['token'])
                ->timeout(30)
                ->get(rtrim($bridge['url'], '/') . '/arhiva/foldere', [
                    'cale' => $request->query('cale', ''),
                ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Calculatorul ' . $bridge['url'] . ' nu răspunde.',
            ], 502);
        }

        if ($raspuns->failed()) {
            $payload = json_decode($raspuns->body(), true);

            return response()->json([
                'success' => false,
                'message' => $payload['eroare'] ?? 'Dosarele nu au putut fi citite (HTTP ' . $raspuns->status() . ').',
            ], 502);
        }

        return response()->json(['success' => true, 'data' => $this->inOrdine($raspuns->json() ?: [])]);
    }

    /**
     * Dosarele, asezate alfabetic pentru ochiul omului.
     *
     * Programul local le da in ordinea in care le tine Windows-ul, adica pe
     * coduri de caractere: acolo „Zeta" vine inaintea lui „arhiva", iar
     * „Ședințe" ajunge tocmai la urma. Se aseaza aici, nu la client: asa se
     * indreapta si listele venite de la programele deja instalate.
     */
    protected function inOrdine(array $payload): array
    {
        if (empty($payload['foldere']) || !is_array($payload['foldere'])) {
            return $payload;
        }

        usort($payload['foldere'], function ($unul, $altul) {
            return strnatcmp(
                self::pentruAsezare($unul['nume'] ?? ''),
                self::pentruAsezare($altul['nume'] ?? '')
            );
        });

        return $payload;
    }

    /**
     * Denumirea adusa la o forma buna de asezat: fara diacritice si fara
     * deosebire intre litere mari si mici, ca „Ședințe" sa stea langa „Sedinte",
     * nu dupa toate celelalte.
     */
    protected static function pentruAsezare(string $nume): string
    {
        $fara = strtr(mb_strtolower($nume, 'UTF-8'), [
            'ă' => 'a', 'â' => 'a', 'î' => 'i', 'ș' => 's', 'ş' => 's', 'ț' => 't', 'ţ' => 't',
        ]);

        return $fara;
    }

    /**
     * Imprimantele de pe calculatorul acestui certificat.
     *
     * Tiparirea se face acolo, langa om, nu pe serverul din cloud — deci lista
     * vine de la programul local de pe acea statie.
     */
    public function imprimante(AnafCertificat $certificat, CertificatService $certificate)
    {
        $certificate->foloseste($certificat);
        $bridge = $certificate->bridge();

        try {
            $raspuns = Http::withToken($bridge['token'])
                ->timeout(30)
                ->get(rtrim($bridge['url'], '/') . '/imprimante');
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Calculatorul ' . $bridge['url'] . ' nu răspunde.',
            ], 502);
        }

        if ($raspuns->failed()) {
            $payload = json_decode($raspuns->body(), true);

            return response()->json([
                'success' => false,
                'message' => $payload['eroare'] ?? 'Imprimantele nu au putut fi citite.',
            ], 502);
        }

        return response()->json([
            'success' => true,
            'data' => $raspuns->json('imprimante') ?: [],
        ]);
    }

    /** Ruta bridge-ului, certificatul implicit si starea. */
    /**
     * Regula pentru o cale de pe calculatorul clientului.
     *
     * @param  string  $cePrezinta  cum se numeste calea in mesajul de refuz
     */
    protected function caleDeCalculator(string $cePrezinta): \Closure
    {
        return function ($atribut, $valoare, $opreste) use ($cePrezinta) {
            $motiv = CaleWindows::motivRefuz($valoare, $cePrezinta);

            if ($motiv !== null) {
                $opreste($motiv);
            }
        };
    }

    public function update(Request $request, AnafCertificat $certificat)
    {
        $date = $request->validate([
            'bridge_url' => 'nullable|url|max:255',
            'bridge_token' => 'nullable|string|max:255',
            /*
             * Cale intreaga pe calculatorul clientului: „D:\Documente fiscale"
             * sau un dosar din retea. Fara „..", ca sa nu se plimbe nimeni prin
             * discul lui pornind de la o setare gresita.
             *
             * Verificarea se face pe text, nu cu tipare: intr-un tipar scris in
             * PHP, bara oblica inversa trebuie indoita de patru ori, iar o
             * singura scapare il face de necitit — si atunci salvarea se opreste
             * cu eroare de server in loc de un raspuns despre cale.
             */
            'arhiva_cale' => ['nullable', 'string', 'max:300', $this->caleDeCalculator('Calea arhivei')],
            'implicit' => 'nullable|boolean',
            'activ' => 'nullable|boolean',
            /*
             * Trimiterea PIN-ului de la distanta, pornita anume pentru tokenul
             * acesta. Nu e pornita din start si nu se porneste singura: e
             * alegerea celui care tine tokenul.
             */
            'pin_de_la_distanta' => 'nullable|boolean',
            // Dosarul urmarit: aceleasi reguli ca la arhiva
            'monitorizare_cale' => ['nullable', 'string', 'max:300', $this->caleDeCalculator('Dosarul urmărit')],
            'monitorizare_activa' => 'nullable|boolean',
            // Din cat in cat sa fie verificat dosarul, in minute
            'monitorizare_cadenta' => 'nullable|integer|in:' . implode(',', AnafCertificat::CADENTE_MONITORIZARE),
            // Ce se face cu declaratiile valide: se si semneaza? se si depun?
            'monitorizare_semneaza' => 'nullable|boolean',
            'monitorizare_depune' => 'nullable|boolean',
            /*
             * direct — serverul cheamă calculatorul clientului la adresa lui
             * tunel  — programul de acolo întreabă singur serverul ce are de
             *          făcut, pe 443, fără niciun port deschis pe routerul lui
             */
            'mod_legatura' => 'nullable|in:direct,tunel',
        ]);

        $certificat->fill(array_intersect_key(
            $date,
            array_flip([
                'bridge_url', 'bridge_token', 'arhiva_cale', 'monitorizare_cale', 'monitorizare_activa',
                'monitorizare_cadenta', 'monitorizare_semneaza', 'monitorizare_depune', 'activ', 'mod_legatura',
                'pin_de_la_distanta',
            ])
        ));

        if (!empty($date['implicit'])) {
            // Un singur certificat poate fi implicit.
            AnafCertificat::where('id', '!=', $certificat->id)->update(['implicit' => false]);
            $certificat->implicit = true;
        } elseif (array_key_exists('implicit', $date)) {
            $certificat->implicit = false;
        }

        $certificat->save();

        Jurnal::scrie(
            'certificat_configurare',
            'A actualizat certificatul „' . $certificat->cn . '”'
                . ($certificat->bridge_url ? ' (bridge: ' . $certificat->bridge_url . ')' : '')
                . ($certificat->implicit ? ' — marcat implicit' : ''),
            $date
        );

        /*
         * Calculatorul tocmai configurat primește licența acum, nu la noapte:
         * altfel omul salvează, încearcă o operație și primește „fără licență".
         * Eșecul nu oprește salvarea — poate fi închis în clipa asta.
         *
         * Se cere insa numai cand s-a schimbat ceva din legatura cu el.
         * Reinnoirea vorbeste cu programul local, iar el serveste o cerere pe
         * rand: prins intr-o fereastra de PIN, tace minute intregi. Pana acum,
         * orice bifa — pana si cea prin care omul deschide tocmai trimiterea
         * PIN-ului — astepta dupa el, si salvarea parea ca nu se mai face.
         */
        $licenta = $certificat->wasChanged(['bridge_url', 'bridge_token', 'mod_legatura', 'activ'])
            ? app(LicentiereBridge::class)->reinnoieste($certificat->fresh())
            : ['emisa' => false, 'expira' => null, 'motiv' => 'legătura nu s-a schimbat'];

        return response()->json([
            'success' => true,
            'data' => $certificat->fresh(),
            'licenta' => $licenta,
        ]);
    }

    /**
     * Scoate certificatul din uz, sau il repune.
     *
     * Pe tokenele clientului stau si certificate cu care el nu lucreaza in
     * relatia cu SPV: ale altei firme, ori ramase de la cineva plecat. Scos din
     * uz, certificatul ramane in lista cu tot ce s-a strans pe el — entitati,
     * utilizatori, declaratii — dar aplicatia nu-l mai foloseste: nu se mai
     * alege pentru operatii (nici cerut anume, nici prin utilizator, nici ca
     * implicit), nu i se mai umbla in dosarul urmarit, nu i se mai reinnoieste
     * licenta calculatorului si nu se mai avertizeaza expirarea lui.
     */
    public function comutaActiv(AnafCertificat $certificat)
    {
        $certificat->activ = !$certificat->activ;

        /*
         * Cel scos din uz nu mai poate fi si cel implicit: implicit inseamna
         * „cu asta se lucreaza cand omul n-are altul atribuit", iar cautarea
         * celui implicit cere oricum sa fie in uz — ar ramane doar o bifa care
         * nu se vede nicaieri, dar impiedica alt certificat sa-i ia locul.
         */
        if (!$certificat->activ) {
            $certificat->implicit = false;
        }

        $certificat->save();

        Jurnal::scrie(
            'certificat_activare',
            ($certificat->activ ? 'A repus în uz certificatul „' : 'A scos din uz certificatul „')
                . $certificat->cn . '”',
            ['certificat_id' => $certificat->id]
        );

        return response()->json([
            'success' => true,
            'data' => $certificat->fresh(),
            'message' => $certificat->activ
                ? 'Certificatul „' . $certificat->cn . '” este din nou în uz.'
                : 'Certificatul „' . $certificat->cn . '” a fost scos din uz; aplicația îl va ignora.',
        ]);
    }

    public function abonare(Request $request)
    {
        $date = $request->validate([
            'email' => 'required|email|max:255',
            'certificat_id' => 'nullable|exists:anaf_certificate,id',
        ]);

        $abonat = CertificatAbonat::updateOrCreate(
            ['email' => $date['email'], 'certificat_id' => $date['certificat_id'] ?? null],
            ['activ' => true]
        );

        Jurnal::scrie(
            'certificat_abonare',
            'A înscris adresa ' . $abonat->email . ' la avertizările de expirare'
                . ($abonat->certificat_id ? ' pentru un certificat anume' : ' pentru toate certificatele'),
            ['email' => $abonat->email]
        );

        return response()->json(['success' => true, 'data' => $abonat]);
    }

    /**
     * Ataseaza un utilizator (dupa email) la certificatul folosit in comun.
     * Optional, adresa este inscrisa si la avertizarile de expirare.
     */
    public function atasareUtilizator(Request $request, AnafCertificat $certificat)
    {
        $date = $request->validate([
            'email' => 'required|email|max:255',
            'nume' => 'nullable|string|max:255',
            'avertizare' => 'nullable|boolean',
        ]);

        $cont = CertificatUtilizator::contDupaEmail($date['email']);

        $utilizator = CertificatUtilizator::updateOrCreate(
            ['certificat_id' => $certificat->id, 'email' => $date['email']],
            [
                'user_id' => optional($cont)->id,
                'nume' => $date['nume'] ?? optional($cont)->name,
                'activ' => true,
            ]
        );

        if (!empty($date['avertizare'])) {
            CertificatAbonat::updateOrCreate(
                ['email' => $date['email'], 'certificat_id' => $certificat->id],
                ['activ' => true]
            );
        }

        Jurnal::scrie(
            'certificat_utilizator_atasare',
            'A atașat utilizatorul ' . $utilizator->email . ' la certificatul „' . $certificat->cn . '”'
                . ($cont ? '' : ' (adresă fără cont în aplicație)'),
            ['email' => $utilizator->email, 'certificat' => $certificat->cn]
        );

        return response()->json(['success' => true, 'data' => $utilizator]);
    }

    public function detasareUtilizator(CertificatUtilizator $utilizator)
    {
        $email = $utilizator->email;
        $certificat = optional($utilizator->certificat)->cn;

        $utilizator->delete();

        Jurnal::scrie(
            'certificat_utilizator_detasare',
            'A eliminat utilizatorul ' . $email . ' de la certificatul „' . $certificat . '”',
            ['email' => $email]
        );

        return response()->json(['success' => true]);
    }

    public function dezabonare(CertificatAbonat $abonat)
    {
        $email = $abonat->email;
        $abonat->delete();

        Jurnal::scrie('certificat_dezabonare', 'A retras adresa ' . $email . ' de la avertizările de expirare');

        return response()->json(['success' => true]);
    }
}
