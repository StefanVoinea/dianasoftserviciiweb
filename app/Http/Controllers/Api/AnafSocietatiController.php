<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AnafSocietate;
use App\Services\Anaf\Format;
use App\Services\Anaf\Jurnal;
use App\Services\Anaf\Spv\SocietatiService;
use App\Services\Anaf\Spv\SpvException;
use Illuminate\Http\Request;

/**
 * Societatile pentru care certificatul digital are drept de semnatura.
 * (Distinct de SocietatiController, care gestioneaza societatile aplicatiei.)
 */
class AnafSocietatiController extends Controller
{
    public function index(Request $request)
    {
        $query = AnafSocietate::with('certificat')->orderByDesc('activ')->orderBy('denumire')->orderBy('cif');

        if ($request->filled('cif')) {
            $query->where('cif', 'like', '%' . $request->query('cif') . '%');
        }

        if ($request->boolean('doar_active')) {
            $query->active();
        }

        return response()->json([
            'success' => true,
            'data' => $query->get()->map(function (AnafSocietate $societate) {
                return [
                    'id' => $societate->id,
                    'cif' => $societate->cif,
                    'denumire' => $societate->denumire,
                    'denumire_sursa' => $societate->denumire_sursa,
                    'tip' => $societate->tip,
                    'activ' => $societate->activ,
                    'vector_la' => Format::dataOra($societate->vector_la),
                    'date_identificare_la' => Format::dataOra($societate->date_identificare_la),
                    'sincronizat_la' => Format::dataOra($societate->sincronizat_la),
                    'certificat' => optional($societate->certificat)->cn,
                    'certificat_expira' => Format::data(optional($societate->certificat)->valabil_pana_la),
                    'complet' => $societate->denumire !== null && $societate->vector_la !== null,
                ];
            }),
        ]);
    }

    /** Initializeaza sau actualizeaza lista din drepturile certificatului. */
    public function sincronizeaza(SocietatiService $serviciu)
    {
        try {
            $rezultat = $serviciu->sincronizeaza();
        } catch (SpvException $e) {
            Jurnal::esec('entitati_sincronizare', 'Actualizarea listei de entități a eșuat: ' . $e->getMessage());

            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }

        Jurnal::scrie(
            'entitati_sincronizare',
            sprintf(
                'A actualizat lista entităților din certificat: %d CIF-uri (%d noi, %d fără drepturi)',
                $rezultat['gasite'],
                $rezultat['noi'],
                $rezultat['dezactivate']
            ),
            $rezultat
        );

        return response()->json(['success' => true, 'data' => $rezultat]);
    }

    /** Trimite catre SPV cererile de date identificare si vector fiscal. */
    public function solicita(Request $request, SocietatiService $serviciu)
    {
        $date = $request->validate([
            'tipuri' => 'nullable|array',
            'tipuri.*' => 'string|in:DATE IDENTIFICARE,VECTOR FISCAL',
        ]);

        $tipuri = $date['tipuri'] ?? ['DATE IDENTIFICARE', 'VECTOR FISCAL'];

        $rezultat = $serviciu->solicitaDocumente($tipuri, optional($request->user())->id);

        Jurnal::scrie(
            'entitati_solicitare',
            sprintf(
                'A cerut din SPV %s pentru entități: %d solicitări trimise, %d sărite, %d documente reinterpretate',
                implode(' și ', $tipuri),
                $rezultat['trimise'],
                $rezultat['sarite'],
                $rezultat['reinterpretate']
            ),
            $rezultat,
            null,
            $rezultat['erori'] === []
        );

        return response()->json(['success' => true, 'data' => $rezultat]);
    }

    public function update(Request $request, AnafSocietate $societate)
    {
        $date = $request->validate([
            'denumire' => 'nullable|string|max:255',
            'activ' => 'nullable|boolean',
        ]);

        if (array_key_exists('denumire', $date)) {
            $societate->seteazaDenumire($date['denumire'], 'manual');
        }

        if (array_key_exists('activ', $date)) {
            $societate->update(['activ' => $date['activ']]);
        }

        Jurnal::scrie(
            'entitate_redenumire',
            'A modificat entitatea ' . $societate->cif . ': ' . ($societate->denumire ?: 'fără denumire'),
            $date,
            $societate->cif
        );

        return response()->json(['success' => true, 'data' => $societate->fresh()]);
    }
}
