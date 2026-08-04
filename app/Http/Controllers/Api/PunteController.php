<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AnafCertificat;
use App\Models\BridgeComanda;
use App\Services\Anaf\Bridge\Licente;
use App\Services\Anaf\Bridge\Punte;
use App\Services\Anaf\Jurnal;
use App\Services\Anaf\Spv\CertificatService;
use App\Support\ContextCompanie;
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
        if (!$this->punte->cerereDeLaServer($request)) {
            return response()->json([
                'eroare' => 'Puntea către programul local nu este configurată.',
                'detalii' => 'Cererea nu poartă jeton semnat de server. Rulați „php artisan anaf:chei-bridge".',
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

        $comanda = $this->punte->urmatoarea($certificate);

        if (!$comanda) {
            return response()->json(['comanda' => null], 200);
        }

        return response()->json(['comanda' => [
            'id' => $comanda->id,
            'metoda' => $comanda->metoda,
            'cale' => $comanda->cale,
            'antete' => $comanda->antete,
            'are_corp' => $comanda->corp_fisier !== null,
        ]]);
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

        Jurnal::scrie(
            'certificat_inrolare',
            'Un calculator s-a înrolat singur, prin tunel, cu ' . count($inrolate) . ' certificat(e)',
            ['certificate' => collect($inrolate)->pluck('cn')->all()]
        );

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
