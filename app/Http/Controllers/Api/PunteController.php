<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AbonamentClient;
use App\Models\AnafCertificat;
use App\Models\BridgeComanda;
use App\Services\Anaf\AlertaEroare;
use App\Services\Anaf\Bridge\ActualizareBridge;
use App\Services\Anaf\Bridge\Licente;
use App\Services\Anaf\Bridge\Punte;
use App\Services\Anaf\Jurnal;
use App\Services\Anaf\Spv\CertificatService;
use App\Support\ContextCompanie;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

/**
 * Puntea către programul local al unui client aflat în spatele unui router.
 *
 * Are două fețe:
 *
 *   - **spre aplicație**: o rută care se poartă exact ca programul local. Tot
 *     ce trimite aplicația azi către bridge (adresă, antete, corp) ajunge aici
 *     neschimbat, se pune în coadă și se așteaptă răspunsul. Nimic din restul
 *     aplicației nu trebuie să știe că legătura merge altfel.
 *
 *   - **spre programul de la client**: trei rute pe care agentul lui le
 *     folosește ca să întrebe ce are de făcut, să ia corpul comenzii și să
 *     trimită răspunsul. Toate pornesc dinspre client, pe 443 — deci nu trebuie
 *     deschis niciun port la el.
 */
class PunteController extends Controller
{
    protected $punte;

    public function __construct(Punte $punte)
    {
        $this->punte = $punte;
    }

    /**
     * Fața dinspre aplicație: primește o cerere ca și cum ar fi programul local.
     */
    public function proxy(Request $request, AnafCertificat $certificat, string $cale = '')
    {
        /*
         * 503, nu 401: aici nu e vorba de drepturile cuiva la ANAF, ci de
         * legătura noastră, neconfigurată. Un 401 ar fi citit mai departe ca
         * refuz al SPV-ului și ar trimite omul să-și verifice certificatul.
         */
        /*
         * Lipsa cheilor si un jeton nepotrivit se spun deosebit.
         *
         * Pana acum, amandoua trimiteau omul la „anaf:chei-bridge” — un sfat
         * primejdios cand cheile exista: rescrise, ele lasa fara valabilitate
         * toate licentele emise, iar fiecare calculator cu token ar avea nevoie
         * de un kit nou. Iar cazul cel mai des intalnit e tocmai celalalt: un
         * jeton care a apucat sa expire in timpul unei lucrari lungi.
         */
        if (!$this->punte->areChei()) {
            return response()->json([
                'eroare' => 'Puntea către programul local nu este configurată.',
                'detalii' => 'Serverul nu are cheile de semnare. Rulați „php artisan anaf:chei-bridge".',
            ], 503);
        }

        if (!$this->punte->cerereDeLaServer($request)) {
            return response()->json([
                'eroare' => 'Cererea către programul local nu a fost primită.',
                'detalii' => 'Jetonul lipsește, a expirat sau nu e semnat de acest server.'
                    . ' Cheile sunt la locul lor — nu le rescrieți.',
            ], 503);
        }

        /*
         * Dacă agentul n-a mai întrebat de mult, se spune din prima. Altfel
         * comanda ar sta în coadă până se plictisește cel care a cerut-o, iar
         * omul ar primi o eroare de rețea în loc de motivul adevărat.
         */
        if (!$this->punte->agentulEsteTreaz($certificat)) {
            return response()->json([
                'eroare' => 'Programul de pe calculatorul cu tokenul nu rulează.',
                'detalii' => $certificat->agent_vazut_la
                    ? 'Ultima dată a dat semne la ' . $certificat->agent_vazut_la->format('d.m.Y H:i')
                        . '. Porniți calculatorul sau reinstalați kitul.'
                    : 'Nu a pornit niciodată de la instalare. Verificați kitul pe acel calculator.',
            ], 504);
        }

        $comanda = $this->punte->pune($certificat, $request, '/' . ltrim($cale, '/'));

        $terminata = $this->punte->asteapta($comanda);

        if ($terminata === null) {
            return response()->json([
                'eroare' => 'Programul local nu a răspuns.',
                'detalii' => 'Calculatorul cu tokenul este închis sau agentul nu rulează.',
            ], 504);
        }

        if ($terminata->stare === 'eroare') {
            return response()->json([
                'eroare' => 'Programul local nu a putut duce comanda la capăt.',
                'detalii' => $terminata->eroare,
            ], 502);
        }

        $antete = $terminata->rezultat_antete ?: [];
        $corp = $terminata->rezultatul();

        $terminata->curata();

        return response($corp, $terminata->status ?: 200, array_filter([
            'Content-Type' => $antete['content-type'] ?? 'application/octet-stream',
        ]));
    }

    /**
     * Fața dinspre client: „ai ceva pentru mine?".
     *
     * Ține linia deschisă câteva zeci de secunde. Așa comanda pleacă în clipa în
     * care apare, fără să întrebe agentul din secundă în secundă.
     */
    public function asteapta(Request $request)
    {
        $certificate = $this->punte->certificateleAgentului($request);

        if ($certificate->isEmpty()) {
            // Motivul exact se spune agentului: el il scrie in jurnalul lui,
            // iar omul care se uita acolo afla ce are de indreptat.
            return response()->json([
                'eroare' => 'Cod de acces invalid.',
                'detalii' => $this->punte->deCeNuAgentul($request),
            ], 401);
        }

        /*
         * Agentul isi spune versiunea la fiecare panda. Se tine minte pe
         * certificat — asa se vede in aplicatie cine a ramas in urma — si se
         * raspunde cu cea de acum, ca el sa stie daca are ce innoi.
         */
        $alLui = trim((string) $request->header('X-Versiune'));
        $acum = app(ActualizareBridge::class)->versiunea();

        if ($alLui !== '') {
            $spusa = mb_substr($alLui, 0, 32);

            /*
             * Ziua innoirii se scrie numai cand versiunea chiar s-a schimbat.
             * Pusa la fiecare panda, ea ar fi aratat mereu „acum un minut" si
             * n-ar fi raspuns la intrebarea pentru care e pusa acolo: a apucat
             * calculatorul acesta sa ia indreptarea de ieri, sau a ramas in urma?
             */
            AnafCertificat::query()->toateCompaniile()
                ->whereIn('id', $certificate->pluck('id'))
                ->where(function ($intrebare) use ($spusa) {
                    $intrebare->where('versiune_bridge', '!=', $spusa)
                        ->orWhereNull('versiune_bridge')
                        ->orWhereNull('versiune_la');
                })
                ->update(['versiune_bridge' => $spusa, 'versiune_la' => now()]);
        }

        $comanda = $this->punte->urmatoarea($certificate);

        $innoire = ($alLui !== '' && $alLui !== $acum) ? ['versiune' => $acum] : null;

        if (!$comanda) {
            return response()->json(['comanda' => null, 'actualizare' => $innoire], 200);
        }

        return response()->json([
            'comanda' => [
                'id' => $comanda->id,
                'metoda' => $comanda->metoda,
                'cale' => $comanda->cale,
                'antete' => $comanda->antete,
                'are_corp' => $comanda->corp_fisier !== null,
            ],
            'actualizare' => $innoire,
        ]);
    }

    /**
     * Licenta ceruta de programul de la client, pentru calculatorul lui.
     *
     * Pana acum licenta pleca numai dinspre server: la salvarea certificatului,
     * sau noaptea, cu comanda planificata. Un calculator instalat azi ramanea
     * deci nelicentiat pana a doua zi dimineata — pornit, legat prin tunel, si
     * totusi nefolositor, fiindca programul refuza orice comanda fara licenta.
     *
     * Acum o cere el, indata dupa inrolare. Nu se poate face invers — serverul
     * sa i-o duca in clipa inrolarii — fiindca agentul asteapta chiar raspunsul
     * la inrolare: comanda ar sta in coada pana i-ar expira rabdarea.
     */
    public function licenta(Request $request, Licente $licente)
    {
        $certificate = $this->punte->certificateleAgentului($request);

        if ($certificate->isEmpty()) {
            return response()->json([
                'eroare' => 'Cod de acces invalid.',
                'detalii' => $this->punte->deCeNuAgentul($request),
            ], 401);
        }

        $masina = trim((string) $request->input('masina'));

        if ($masina === '') {
            return response()->json(['eroare' => 'Lipsește amprenta calculatorului.'], 422);
        }

        /*
         * Licenta e a calculatorului, nu a certificatului: pe acelasi calculator
         * pot sta mai multe tokene, iar programul e unul singur. Se emite pentru
         * cel dintai certificat in lucru de acolo si se tine minte la toate.
         *
         * Certificatele scoase din uz nu dau licenta: un calculator pe care au
         * ramas numai ele n-are ce lucra, si e mai bine sa se vada asta decat sa
         * para pregatit.
         */
        $inLucru = $certificate->where('activ', true);

        if ($inLucru->isEmpty()) {
            return response()->json([
                'eroare' => 'Nu se poate emite licența.',
                'detalii' => 'Toate certificatele acestui calculator sunt scoase din uz în aplicație.',
            ], 409);
        }

        $certificat = $inLucru->first();

        /*
         * Licenta urmeaza abonamentul, ca si cea data de comanda planificata:
         * altfel un calculator si-ar innoi-o singur la nesfarsit, iar oprirea de
         * la sine a programului n-ar mai insemna nimic.
         */
        $abonament = AbonamentClient::alClientului($certificat->company_id);

        if ($abonament && !$abonament->activ()) {
            return response()->json([
                'eroare' => 'Nu se poate emite licența.',
                'detalii' => $abonament->motiv(),
            ], 402);
        }

        $licenta = $licente->emite($certificat, $masina);

        AnafCertificat::query()->toateCompaniile()
            ->whereIn('id', $inLucru->pluck('id'))
            ->update(['licenta_pana_la' => Carbon::parse($licenta['date']['expira'])]);

        ContextCompanie::pentru($certificat->company_id, function () use ($certificat, $licenta, $masina) {
            Jurnal::scrie(
                'licenta_bridge',
                'Programul de la client si-a cerut licența pentru certificatul ' . $certificat->cn
                    . ', până la ' . $licenta['date']['expira'],
                ['masina' => $masina]
            );
        });

        return response()->json($licenta);
    }

    /**
     * Pachetul de innoire a programului de la client.
     *
     * Se da numai agentilor care se legitimeaza cu codul lor, si merge semnat:
     * clientul verifica semnatura cu cheia publica din kit inainte de a inlocui
     * ceva. Fara verificarea aceea, oricine i-ar putea trimite alt program.
     */
    public function actualizare(Request $request, ActualizareBridge $actualizare)
    {
        if ($this->punte->certificateleAgentului($request)->isEmpty()) {
            return response()->json(['eroare' => 'Cod de acces invalid.'], 401);
        }

        $pachet = $actualizare->pachetul();

        return response()->download($pachet['arhiva'], 'actualizare.json', [
            'X-Versiune' => $pachet['versiune'],
            'X-Amprenta' => $pachet['amprenta'],
            'X-Semnatura' => $pachet['semnatura'],
            'Content-Type' => 'application/json',
        ])->deleteFileAfterSend(true);
    }

    /**
     * Înrolarea unui calculator nou, pornită de la el.
     *
     * Prin tunel, serverul n-are cum să sune primul — de aceea vine agentul cu
     * lista certificatelor de pe tokenul de lângă el. Spune al cui client este
     * prin jetonul de înrolare din kit, semnat de noi, deci nu poate pretinde
     * altceva. Contabilul nu are nimic de tastat: instalează kitul, iar
     * certificatele apar în listă.
     */
    public function inrolare(Request $request, Licente $licente, CertificatService $certificate)
    {
        $client = $licente->clientulDinJeton((string) $request->header('X-Inrolare'));
        $cod = (string) $request->bearerToken();

        if (!$client || $cod === '') {
            return response()->json([
                'eroare' => 'Înrolare respinsă.',
                'detalii' => 'Jetonul de înrolare lipsește sau nu mai e valabil. Descărcați un kit nou.',
            ], 401);
        }

        $lista = $request->input('certificate', []);

        if (!is_array($lista) || $lista === []) {
            return response()->json(['eroare' => 'Nu s-a trimis niciun certificat.'], 422);
        }

        $inrolate = ContextCompanie::pentru($client, function () use ($certificate, $lista, $cod) {
            return $certificate->inroleazaDinAgent($lista, $cod);
        });

        /*
         * Lista poate fi nevida si totusi fara niciun certificat calificat:
         * magazinul Windows are mereu certificate auto-semnate ale unor
         * programe, dar semnatura calificata sta pe token — iar tokenul nu era
         * conectat. Un 200 aici ar lasa agentul sa creada ca s-a inrolat si sa
         * se mire pe urma ca serverul nu-i recunoaste codul; se spune limpede,
         * si lui, si in jurnalul aplicatiei.
         */
        if ($inrolate === []) {
            ContextCompanie::pentru($client, function () use ($lista) {
                Jurnal::esec(
                    'certificat_inrolare',
                    'Un calculator a încercat să se înroleze prin tunel, dar nu s-a găsit'
                        . ' niciun certificat calificat pe el — cel mai probabil tokenul nu era conectat',
                    ['certificate_trimise' => count($lista)]
                );
            });

            return response()->json([
                'eroare' => 'Niciun certificat calificat.',
                'detalii' => 'Pe calculator nu s-a găsit niciun certificat de semnătură'
                    . ' — cel mai probabil tokenul nu este conectat. Conectați tokenul;'
                    . ' agentul încearcă singur din nou.',
            ], 422);
        }

        // Jurnalul se scrie in dreptul clientului, ca el sa-l vada in aplicatie.
        ContextCompanie::pentru($client, function () use ($inrolate) {
            Jurnal::scrie(
                'certificat_inrolare',
                'Un calculator s-a înrolat singur, prin tunel, cu ' . count($inrolate) . ' certificat(e)',
                ['certificate' => collect($inrolate)->pluck('cn')->all()]
            );
        });

        return response()->json([
            'inrolate' => collect($inrolate)->map(function ($certificat) {
                return ['id' => $certificat->id, 'cn' => $certificat->cn];
            }),
        ]);
    }

    /** Corpul comenzii, luat separat: poate fi un XML de zeci de megaocteți. */
    public function corp(Request $request, BridgeComanda $comanda)
    {
        $certificate = $this->punte->certificateleAgentului($request);

        if (!$certificate->contains('id', $comanda->certificat_id)) {
            return response()->json(['eroare' => 'Cod de acces invalid.'], 401);
        }

        $continut = $comanda->corpul();

        if ($continut === null) {
            return response('', 204);
        }

        return response($continut, 200, [
            'Content-Type' => $comanda->antete['content-type'] ?? 'application/octet-stream',
        ]);
    }

    /** Răspunsul programului local, întors aplicației care așteaptă. */
    public function rezultat(Request $request, BridgeComanda $comanda)
    {
        $certificate = $this->punte->certificateleAgentului($request);

        if (!$certificate->contains('id', $comanda->certificat_id)) {
            return response()->json(['eroare' => 'Cod de acces invalid.'], 401);
        }

        $eroare = $request->header('X-Eroare');

        if ($eroare) {
            $comanda->update(['stare' => 'eroare', 'eroare' => mb_substr($eroare, 0, 1000), 'terminata_la' => now()]);

            /*
             * Programul de la client a raspuns ca n-a putut duce comanda la
             * capat. Aplicatia merge mai departe — cine astepta primeste eroarea
             * si isi vede de treaba — dar noi trebuie sa aflam, cu tot cu cine
             * si cu ce s-a lucrat: altfel se afla abia cand suna clientul.
             */
            AlertaEroare::trimite('programul de la client', $eroare, [
                'company_id' => $comanda->company_id,
                'certificat_id' => $comanda->certificat_id,
                'comanda' => $comanda->metoda . ' ' . $comanda->cale,
            ]);

            return response()->json(['primit' => true]);
        }

        $fisier = BridgeComanda::DOSAR . '/rez_' . $comanda->id . '_' . uniqid() . '.bin';
        Storage::put($fisier, $request->getContent());

        $comanda->update([
            'stare' => 'gata',
            'status' => (int) ($request->header('X-Status') ?: 200),
            'rezultat_antete' => ['content-type' => $request->header('X-Tip-Continut') ?: 'application/octet-stream'],
            'rezultat_fisier' => $fisier,
            'terminata_la' => now(),
        ]);

        return response()->json(['primit' => true]);
    }
}
