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
use App\Services\Anaf\Spv\KitBridge;
use App\Services\Anaf\Spv\SocietatiService;
use App\Services\Anaf\Spv\SpvException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class CertificateController extends Controller
{
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
                    'mod_legatura' => $certificat->mod_legatura ?: 'direct',
                    'agent_vazut_la' => Format::dataOra($certificat->agent_vazut_la),
                    'agent_treaz' => app(Punte::class)->agentulEsteTreaz($certificat),
                    'versiune_bridge' => $certificat->versiune_bridge,
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
         */
        $licenta = app(LicentiereBridge::class)->reinnoieste($certificat->fresh());

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
