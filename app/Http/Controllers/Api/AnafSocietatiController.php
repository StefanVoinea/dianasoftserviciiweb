<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AnafSocietate;
use App\Services\Anaf\Declaratii\D300\AntetD300;
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
        $query = AnafSocietate::with('certificat')
            ->orderByDesc('activ')
            ->orderBy('scos_din_uz')
            ->orderBy('denumire')
            ->orderBy('cif');

        if ($request->filled('cif')) {
            $query->where('cif', 'like', '%' . $request->query('cif') . '%');
        }

        /*
         * „doar_active" inseamna acum „cu care se si lucreaza": si drepturile de
         * la ANAF, si voia omului. Filele care cer firme de ales n-au ce face cu
         * o entitate scoasa din uz — ar incurca lista fara sa aduca nimic.
         *
         * Fila entitatilor cere altfel: ea le arata pe toate cand se apasa
         * „Arată și cele scoase din uz".
         */
        if ($request->boolean('doar_active')) {
            $query->inLucru();
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
                    'scos_din_uz' => $societate->scos_din_uz,
                    'in_lucru' => $societate->esteInLucru(),
                    'vector_la' => Format::dataOra($societate->vector_la),
                    'date_identificare_la' => Format::dataOra($societate->date_identificare_la),
                    'sincronizat_la' => Format::dataOra($societate->sincronizat_la),
                    'certificat' => optional($societate->certificat)->cn,
                    // Filele care filtreaza firmele pe token au nevoie si de id.
                    'certificat_id' => $societate->certificat_id,
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
            // Transa de firme, cand interfata le trimite pe rand
            'cif' => 'nullable|array',
            'cif.*' => 'string|max:20',
            // Recitirea documentelor deja descarcate, ceruta la primul lot
            'reinterpreteaza' => 'nullable|boolean',
        ]);

        $tipuri = $date['tipuri'] ?? ['DATE IDENTIFICARE', 'VECTOR FISCAL'];

        $rezultat = $serviciu->solicitaDocumente(
            $tipuri,
            optional($request->user())->id,
            $date['cif'] ?? [],
            $request->boolean('reinterpreteaza', ($date['cif'] ?? []) === [])
        );

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
            'scos_din_uz' => 'nullable|boolean',
        ]);

        if (array_key_exists('denumire', $date)) {
            $societate->seteazaDenumire($date['denumire'], 'manual');
        }

        if (array_key_exists('activ', $date)) {
            $societate->update(['activ' => $date['activ']]);
        }

        /*
         * Scoaterea din uz e hotararea omului si sta deoparte de „activ", care e
         * cuvantul ANAF-ului: altfel prima sincronizare i-ar sterge alegerea.
         */
        if (array_key_exists('scos_din_uz', $date)) {
            $societate->update(['scos_din_uz' => (bool) $date['scos_din_uz']]);

            Jurnal::scrie(
                'entitate_stare',
                ($date['scos_din_uz'] ? 'A scos din uz entitatea ' : 'A pus iar în lucru entitatea ')
                    . $societate->cif . ($societate->denumire ? ' (' . $societate->denumire . ')' : ''),
                ['scos_din_uz' => (bool) $date['scos_din_uz']],
                $societate->cif
            );
        }

        if (array_key_exists('denumire', $date)) {
            Jurnal::scrie(
                'entitate_redenumire',
                'A modificat entitatea ' . $societate->cif . ': ' . ($societate->denumire ?: 'fără denumire'),
                $date,
                $societate->cif
            );
        }

        return response()->json(['success' => true, 'data' => $societate->fresh()]);
    }

    /**
     * Datele firmei care intra in antetul declaratiilor.
     *
     * Ele nu se schimba de la o luna la alta — adresa, banca, contul, cine
     * semneaza —, asa ca se scriu o data aici si se iau de aici la fiecare
     * declaratie. Vezi AntetD300.
     */
    public function dateDeclaratii(Request $request, AnafSocietate $societate)
    {
        $date = $request->validate([
            'adresa' => 'nullable|string|max:1000',
            'telefon' => 'nullable|string|max:15',
            'fax' => 'nullable|string|max:15',
            'email' => 'nullable|email|max:200',
            'banca' => 'nullable|string|max:50',
            'cont' => 'nullable|string|max:50',
            'caen' => 'nullable|string|max:10',

            'nume_declarant' => 'nullable|string|max:75',
            'prenume_declarant' => 'nullable|string|max:75',
            'functie_declarant' => 'nullable|string|max:50',
            'prin_reprezentant' => 'nullable|boolean',

            // Felurile de decont sunt cele din schema ANAF: L, T, S, A.
            'd300_tip_decont' => 'nullable|in:' . implode(',', array_keys(AntetD300::FELURI_DECONT)),
            'd300_pro_rata' => 'nullable|numeric|min:0|max:100',
            'd300_bifa_interne' => 'nullable|boolean',
            'd300_bifa_cereale' => 'nullable|boolean',
            'd300_bifa_mob' => 'nullable|boolean',
            'd300_bifa_disp' => 'nullable|boolean',
            'd300_bifa_cons' => 'nullable|boolean',
            'd300_solicit_ramb' => 'nullable|boolean',
        ]);

        $societate->update($date);
        $societate = $societate->fresh();

        Jurnal::scrie(
            'entitate_date_declaratii',
            'A completat datele de declarație ale entității ' . $societate->cif
                . ($societate->denumire ? ' (' . $societate->denumire . ')' : ''),
            ['campuri' => array_keys($date)],
            $societate->cif
        );

        return response()->json([
            'success' => true,
            'data' => $this->dateleDeclaratiilor($societate),
        ]);
    }

    /** Datele de declaratie ale unei entitati, cu ce mai lipseste din ele. */
    public function citesteDateDeclaratii(AnafSocietate $societate)
    {
        return response()->json([
            'success' => true,
            'data' => $this->dateleDeclaratiilor($societate),
        ]);
    }

    /** @return array<string, mixed> */
    protected function dateleDeclaratiilor(AnafSocietate $societate): array
    {
        $antet = AntetD300::pentru($societate);

        return [
            'cif' => $societate->cif,
            'denumire' => $societate->denumire,
            'date' => $societate->only([
                'adresa', 'telefon', 'fax', 'email', 'banca', 'cont', 'caen',
                'nume_declarant', 'prenume_declarant', 'functie_declarant', 'prin_reprezentant',
                'd300_tip_decont', 'd300_pro_rata', 'd300_bifa_interne', 'd300_bifa_cereale',
                'd300_bifa_mob', 'd300_bifa_disp', 'd300_bifa_cons', 'd300_solicit_ramb',
            ]),
            // Ce mai trebuie completat ca decontul sa poata fi scris
            'lipsesc' => $antet['lipsesc'],
            'gata' => $antet['gata'],
            'feluri_decont' => AntetD300::FELURI_DECONT,
        ];
    }
}
