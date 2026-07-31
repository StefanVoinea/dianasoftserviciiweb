<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AnafCertificat;
use App\Models\BridgeComanda;
use App\Services\Anaf\Bridge\Punte;
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
        if (!$this->punte->cerereDeLaServer($request)) {
            return response()->json(['eroare' => 'Cerere nesemnată de server.'], 401);
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
            return response()->json(['eroare' => 'Cod de acces invalid.'], 401);
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
