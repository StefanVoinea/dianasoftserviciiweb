<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AnafCertificat;
use App\Models\AnafSocietate;
use App\Models\SpvSolicitare;
use App\Services\Anaf\Declaratii\ConcatenareService;
use App\Services\Anaf\Declaratii\DeclaratieException;
use App\Services\Anaf\Format;
use App\Services\Anaf\Jurnal;
use App\Services\Anaf\Spv\CertificatService;
use App\Services\Anaf\Spv\SolicitareService;
use App\Services\Anaf\Spv\SpvException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SpvSolicitariController extends Controller
{
    public function index(Request $request)
    {
        $query = SpvSolicitare::with('certificat')->orderByDesc('created_at');

        if ($request->filled('cif')) {
            $query->where('cif', 'like', '%' . $request->query('cif') . '%');
        }

        if ($request->filled('stare')) {
            $query->where('stare', $request->query('stare'));
        }

        /*
         * Denumirile se citesc acum, nu se iau pe cele scrise la creare:
         * atunci firma putea fi înrolată fără denumire, iar aceasta să apară
         * mai târziu, după sincronizarea datelor de identificare.
         */
        $denumiri = AnafSocietate::whereNotNull('denumire')->pluck('denumire', 'cif');

        return response()->json([
            'success' => true,
            'data' => $query->get()->map(function (SpvSolicitare $solicitare) use ($denumiri) {
                return array_merge($solicitare->toArray(), [
                    'den_firma' => $denumiri->get($solicitare->cif) ?: $solicitare->den_firma,
                    'inrolata' => $denumiri->has($solicitare->cif),
                    'certificat_nume' => optional($solicitare->certificat)->cn,
                    'data_solicitarii' => Format::dataOra($solicitare->data_solicitarii),
                    'data_afisare' => Format::dataOra($solicitare->data_afisare),
                    'created_at' => Format::dataOra($solicitare->created_at),
                ]);
            }),
            // tip => parametrii suplimentari ceruti de ANAF, pentru formularul din interfata
            'tipuri' => config('anaf.spv.tipuri_documente'),
        ]);
    }

    /** Trimite una sau mai multe cereri de documente catre SPV. */
    public function store(Request $request, SolicitareService $serviciu)
    {
        $tipuri = config('anaf.spv.tipuri_documente');

        $date = $request->validate([
            'cui' => 'required|array|min:1',
            'cui.*' => 'required|string|max:20',
            'tip_document' => 'required|string|in:' . implode(',', array_keys($tipuri)),
            'an' => 'nullable|integer|min:2000|max:2100',
            'luna' => 'nullable|integer|min:1|max:12',
            'motiv' => 'nullable|string|max:255',
            'numar_inregistrare' => 'nullable|string|max:100',
            'cui_pui' => 'nullable|string|max:20',
        ]);

        // Parametrii marcati de ANAF ca obligatorii pentru tipul ales
        $obligatorii = $tipuri[$date['tip_document']];
        $lipsa = [];

        foreach ($obligatorii as $parametru) {
            if ($parametru !== 'cui_pui' && empty($date[$parametru])) {
                $lipsa[] = $parametru;
            }
        }

        if ($lipsa !== []) {
            return response()->json([
                'success' => false,
                'message' => 'Pentru „' . $date['tip_document'] . '” ANAF cere și: ' . implode(', ', $lipsa),
            ], 422);
        }

        $optiuni = array_intersect_key($date, array_flip(['an', 'luna', 'motiv', 'numar_inregistrare', 'cui_pui']));

        $trimise = [];
        $erori = [];

        foreach ($date['cui'] as $cui) {
            try {
                $trimise[] = $serviciu->solicita(
                    $cui,
                    $date['tip_document'],
                    $optiuni,
                    optional($request->user())->id
                );
            } catch (SpvException $e) {
                $erori[] = $cui . ': ' . $e->getMessage();
            }
        }

        Jurnal::scrie(
            'solicitare_trimitere',
            sprintf(
                'A solicitat „%s” pentru %s: %d trimise, %d eșuate',
                $date['tip_document'],
                implode(', ', $date['cui']),
                count($trimise),
                count($erori)
            ),
            ['tip' => $date['tip_document'], 'cui' => $date['cui'], 'erori' => $erori],
            count($date['cui']) === 1 ? $date['cui'][0] : null,
            $erori === []
        );

        return response()->json([
            'success' => $trimise !== [],
            'data' => $trimise,
            'erori' => $erori,
        ], $trimise === [] ? 500 : 200);
    }

    /** Cauta in SPV raspunsurile la solicitarile in asteptare si le descarca. */
    public function preia(Request $request, SolicitareService $serviciu)
    {
        $rezultat = $serviciu->preiaRaspunsuri((int) $request->query('zile', 60));

        Jurnal::scrie(
            'solicitare_preluare',
            sprintf(
                'A preluat răspunsurile din SPV: %d solicitări verificate, %d răspunsuri noi',
                $rezultat['verificate'],
                $rezultat['preluate']
            ),
            $rezultat,
            null,
            $rezultat['erori'] === []
        );

        return response()->json(['success' => true, 'data' => $rezultat]);
    }

    /**
     * Trimite la imprimanta utilizatorului raspunsurile cerute, unite intr-unul
     * singur. Fara imprimanta aleasa, intoarce documentul de descarcat.
     */
    public function tipareste(Request $request, ConcatenareService $concatenare)
    {
        $date = $request->validate([
            'id' => 'required|array|min:1',
            'id.*' => 'integer',
            'filigran' => 'nullable|boolean',
        ]);

        $solicitari = SpvSolicitare::whereIn('id', $date['id'])
            ->whereNotNull('cale_fisier')
            ->orderBy('cif')
            ->orderBy('tip_document')
            ->get()
            ->filter(function (SpvSolicitare $solicitare) {
                return Storage::exists($solicitare->cale_fisier);
            });

        if ($solicitari->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Niciunul dintre răspunsurile cerute nu are document descărcat.',
            ], 422);
        }

        $cai = $solicitari->map(function (SpvSolicitare $solicitare) {
            return Storage::path($solicitare->cale_fisier);
        })->values()->all();

        // Filigranul poarta denumirea firmei fiecarui document: intr-un teanc
        // tiparit pot intra raspunsuri ale mai multor societati. Denumirea vine
        // tot din Entitati inrolate, ca sa fie cea de la ANAF, nu una veche.
        $denumiri = AnafSocietate::whereNotNull('denumire')->pluck('denumire', 'cif');

        $filigrane = empty($date['filigran']) ? [] : $solicitari->map(function (SpvSolicitare $solicitare) use ($denumiri) {
            return $denumiri->get($solicitare->cif) ?: ($solicitare->den_firma ?: $solicitare->cif);
        })->values()->all();

        $utilizator = $request->user();

        if (!$utilizator || !$utilizator->imprimanta) {
            return response()->json([
                'success' => false,
                'message' => 'Nu aveți o imprimantă aleasă. Administratorul firmei o poate seta din fila Utilizatori.',
            ], 422);
        }

        // Bridge-ul imprimantei, nu cel folosit pentru descarcare.
        if ($utilizator->imprimanta_certificat_id) {
            $certificat = AnafCertificat::find($utilizator->imprimanta_certificat_id);

            if ($certificat) {
                app(CertificatService::class)->foloseste($certificat);
            }
        }

        try {
            $rezultat = $concatenare->tipareste($cai, $filigrane, $utilizator->imprimanta);
        } catch (DeclaratieException $e) {
            Jurnal::esec('solicitare_tiparire', 'Tipărirea răspunsurilor a eșuat: ' . $e->getMessage());

            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }

        Jurnal::scrie(
            'solicitare_tiparire',
            'A trimis la imprimanta „' . $rezultat['imprimanta'] . '” ' . $solicitari->count() . ' răspunsuri SPV',
            ['solicitari' => $solicitari->pluck('id')->all()]
        );

        return response()->json([
            'success' => true,
            'data' => [
                'tiparit' => true,
                'imprimanta' => $rezultat['imprimanta'],
                'documente' => $solicitari->count(),
            ],
        ]);
    }

    /**
     * Documentele SPV stau pe discul privat (storage/app), nu sub public/,
     * asa ca sunt servite prin aplicatie, nu printr-un link direct.
     */
    public function fisier(SpvSolicitare $solicitare)
    {
        if (!$solicitare->cale_fisier || !Storage::exists($solicitare->cale_fisier)) {
            return response()->json(['success' => false, 'message' => 'Documentul nu a fost găsit.'], 404);
        }

        Jurnal::scrie(
            'solicitare_deschidere',
            'A deschis documentul „' . $solicitare->tip_document . '” pentru ' . $solicitare->cif,
            ['solicitare_id' => $solicitare->id],
            $solicitare->cif
        );

        $nume = str_replace(' ', '_', $solicitare->tip_document) . '_' . $solicitare->cif . '.pdf';

        return response()->file(Storage::path($solicitare->cale_fisier), [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $nume . '"',
        ]);
    }

    public function destroy(SpvSolicitare $solicitare)
    {
        Jurnal::scrie(
            'solicitare_stergere',
            'A șters solicitarea „' . $solicitare->tip_document . '” pentru ' . $solicitare->cif,
            ['solicitare_id' => $solicitare->id],
            $solicitare->cif
        );

        $solicitare->delete();

        return response()->json(['success' => true]);
    }
}
