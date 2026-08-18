<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\EtransportNotificare;
use App\Services\Anaf\Etransport\EtransportClient;
use App\Services\Anaf\Etransport\EtransportException;
use App\Services\Anaf\Etransport\EtransportSincronizare;
use App\Services\Anaf\Format;
use App\Services\Anaf\Jurnal;
use App\Services\Anaf\Oauth\OauthAnaf;
use App\Services\Anaf\Oauth\OauthException;
use Illuminate\Http\Request;

/**
 * e-Transport: declararea și urmărirea transporturilor de bunuri.
 * (Distinct de EtransportController, care folosește autentificarea OAuth2.)
 */
class EtransportAnafController extends Controller
{
    public function index(Request $request)
    {
        $query = EtransportNotificare::with('certificat')
            ->orderByDesc('data_creare')
            ->orderByDesc('id');

        if ($request->filled('cif')) {
            $query->where('cod_decl', 'like', '%' . $request->query('cif') . '%');
        }

        if ($request->filled('uit')) {
            $query->where('uit', 'like', '%' . $request->query('uit') . '%');
        }

        if ($request->boolean('doar_erori')) {
            $query->cuErori();
        }

        $notificari = $query->limit((int) $request->query('limita', 300))->get()
            ->map(function (EtransportNotificare $n) {
                return [
                    'id' => $n->id,
                    'uit' => $n->uit,
                    'tip' => $n->tip,
                    'tip_eticheta' => EtransportNotificare::TIPURI[$n->tip] ?? $n->tip,
                    'stare' => $n->stare,
                    'are_erori' => $n->are_erori,
                    'cod_decl' => $n->cod_decl,
                    'ref_decl' => $n->ref_decl,
                    'operatiune' => $n->operatiune,
                    'data_transp' => Format::data($n->data_transp),
                    'data_creare' => Format::dataOra($n->data_creare),
                    'partener' => trim(($n->pc_den ?: '') . ' ' . ($n->pc_cod ? '(' . $n->pc_cod . ')' : '')),
                    'transportator' => trim(($n->tr_den ?: '') . ' ' . ($n->tr_cod ? '(' . $n->tr_cod . ')' : '')),
                    'vehicul' => trim(implode(' + ', array_filter([$n->nr_veh, $n->nr_rem1, $n->nr_rem2]))),
                    'nr_linii' => $n->nr_linii,
                    'val_tot' => $n->val_tot,
                    'id_incarcare' => $n->id_incarcare,
                    'mesaje' => $n->mesaje,
                    'certificat' => optional($n->certificat)->cn,
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $notificari,
            'operatiuni' => EtransportNotificare::OPERATIUNI,
            'tipuri' => EtransportNotificare::TIPURI,
        ]);
    }

    /**
     * Adresa la care utilizatorul se autorizează la ANAF (OAuth2). După
     * autentificarea cu certificatul, ANAF redirecționează înapoi în aplicație.
     */
    public function oauthUrl(Request $request, OauthAnaf $oauth)
    {
        $date = $request->validate(['cif' => 'required|string|max:20']);

        try {
            $url = $oauth->urlAutorizare($date['cif']);
        } catch (OauthException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }

        Jurnal::scrie(
            'etransport_autorizare',
            'A pornit autorizarea OAuth2 la ANAF pentru ' . $date['cif'],
            [],
            $date['cif']
        );

        return response()->json(['success' => true, 'url' => $url]);
    }

    /** Starea autorizării OAuth2 pentru un CIF. */
    public function oauthStare(Request $request, OauthAnaf $oauth, EtransportClient $client)
    {
        $date = $request->validate(['cif' => 'nullable|string|max:20']);

        $companie = \App\Support\ContextCompanie::curenta();
        $cuiClient = $companie ? optional(\App\Models\Company::find($companie))->cui : null;

        return response()->json([
            'success' => true,
            // CIF-ul autorizat merge prin OAuth de la sine, chiar fara modul global.
            'mod' => $client->foloseseOauth($date['cif'] ?? null) ? 'oauth' : 'certificat',
            'configurat' => $oauth->configurat(),
            'redirect_uri' => $oauth->redirectUri(),
            // CIF-ul clientului, ca filele sa nu-l mai ceara scris de mana.
            'cif_implicit' => $cuiClient ? preg_replace('/\D/', '', $cuiClient) : null,
            // Fără CIF se întoarce doar modul de lucru, ca interfața să se poată configura.
            'data' => !empty($date['cif'])
                ? $oauth->stare($date['cif'])
                : ['autorizat' => false, 'expira_la' => null, 'zile_ramase' => null],
        ]);
    }

    /** Preia notificările din ultimele N zile pentru un CIF. */
    public function sincronizeaza(Request $request, EtransportSincronizare $serviciu)
    {
        $date = $request->validate([
            'cif' => 'required|string|max:20',
            'zile' => 'nullable|integer|min:1|max:60',
        ]);

        try {
            $rezultat = $serviciu->preia(
                (int) ($date['zile'] ?? 30),
                $date['cif'],
                optional($request->user())->id
            );
        } catch (EtransportException $e) {
            Jurnal::esec('etransport_sincronizare', 'Preluarea notificărilor e-Transport a eșuat: ' . $e->getMessage(), [], $date['cif']);

            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }

        Jurnal::scrie(
            'etransport_sincronizare',
            sprintf(
                'A preluat notificările e-Transport pentru %s: %d primite, %d noi, %d cu erori',
                $date['cif'],
                $rezultat['preluate'],
                $rezultat['noi'],
                $rezultat['cu_erori']
            ),
            $rezultat,
            $date['cif']
        );

        return response()->json(['success' => true, 'data' => $rezultat]);
    }

    /** Depune o declarație de transport (fișier XML). */
    public function depune(Request $request, EtransportClient $client)
    {
        $date = $request->validate([
            'cif' => 'required|string|max:20',
            'fisier' => 'required|file|max:10240',
            'versiune' => 'nullable|integer|in:1,2',
        ]);

        $xml = file_get_contents($date['fisier']->getRealPath());

        try {
            $raspuns = $client->upload($xml, $date['cif'], $date['versiune'] ?? null);
        } catch (EtransportException $e) {
            Jurnal::esec('etransport_depunere', 'Depunerea declarației e-Transport a eșuat: ' . $e->getMessage(), [], $date['cif']);

            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }

        $index = $raspuns['index_incarcare'] ?? null;
        $erori = $raspuns['Errors'] ?? ($raspuns['errors'] ?? []);

        Jurnal::scrie(
            'etransport_depunere',
            $index
                ? 'A depus o declarație e-Transport pentru ' . $date['cif'] . ', index de încărcare ' . $index
                : 'Declarația e-Transport pentru ' . $date['cif'] . ' a fost respinsă de ANAF',
            $raspuns,
            $date['cif'],
            (bool) $index
        );

        return response()->json([
            'success' => (bool) $index,
            'data' => $raspuns,
            'index_incarcare' => $index,
            'erori' => $erori,
        ], $index ? 200 : 422);
    }

    /** Starea unei declarații depuse, după indexul de încărcare. */
    public function stare(Request $request, EtransportClient $client)
    {
        $date = $request->validate([
            'id_incarcare' => 'required|string|max:40',
            'cif' => 'nullable|string|max:20',
        ]);

        try {
            $raspuns = $client->stareMesaj($date['id_incarcare'], $date['cif'] ?? null);
        } catch (EtransportException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }

        return response()->json(['success' => true, 'data' => $raspuns]);
    }

    /** Trimite pe email codul UIT al unei notificări preluate de la ANAF. */
    public function trimiteEmail(Request $request, EtransportNotificare $notificare)
    {
        if (!$notificare->uit) {
            return response()->json([
                'success' => false,
                'message' => 'Notificarea nu are un cod UIT de trimis.',
            ], 422);
        }

        $date = $request->validate(['adrese' => 'required|string|max:500']);

        $adrese = array_filter(array_map('trim', preg_split('/[,;\s]+/', $date['adrese'])));

        $gresite = array_filter($adrese, function ($adresa) {
            return !filter_var($adresa, FILTER_VALIDATE_EMAIL);
        });

        if ($adrese === [] || $gresite !== []) {
            return response()->json([
                'success' => false,
                'message' => $gresite !== []
                    ? 'Adresa „' . reset($gresite) . '" nu arată a email.'
                    : 'Scrieți cel puțin o adresă de email.',
            ], 422);
        }

        foreach ($adrese as $adresa) {
            \Illuminate\Support\Facades\Mail::to($adresa)->send(\App\Mail\EtransportUitEmail::dinNotificare($notificare));
        }

        Jurnal::scrie(
            'etransport_declaratie',
            'A trimis codul UIT ' . $notificare->uit . ' către ' . implode(', ', $adrese),
            ['adrese' => $adrese],
            $notificare->cod_decl
        );

        return response()->json([
            'success' => true,
            'message' => 'Codul UIT a plecat către ' . implode(', ', $adrese) . '.',
        ]);
    }

    /** Notificările în care CIF-ul figurează ca organizator de transport. */
    public function transportator(Request $request, EtransportClient $client)
    {
        $date = $request->validate([
            'cui_op' => 'required|string|max:20',
            'cui_decl' => 'nullable|string|max:20',
            'uit' => 'nullable|string|max:40',
            'ref_decl' => 'nullable|string|max:100',
        ]);

        try {
            $raspuns = $client->info($date['cui_op'], $date);
        } catch (EtransportException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }

        return response()->json(['success' => true, 'data' => $raspuns]);
    }
}
