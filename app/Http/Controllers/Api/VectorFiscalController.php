<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AnafDeclaratie;
use App\Models\VectorFiscal;
use App\Models\VectorSpv;
use App\Services\Anaf\Format;
use App\Services\Anaf\Jurnal;
use Illuminate\Http\Request;

class VectorFiscalController extends Controller
{
    public function index(Request $request)
    {
        $query = VectorFiscal::query()->orderBy('denumire');

        if ($request->filled('cui')) {
            $query->where('cui', 'like', '%' . $request->query('cui') . '%');
        }

        return response()->json([
            'success' => true,
            'data' => $query->get(),
            'declaratii' => VectorFiscal::DECLARATII,
            'periodicitati' => VectorFiscal::PERIODICITATI,
        ]);
    }

    public function store(Request $request)
    {
        $date = $this->valideaza($request);

        $vector = VectorFiscal::updateOrCreate(['cui' => $date['cui']], $date);

        Jurnal::scrie(
            'vector_modificare',
            'A adăugat în vectorul fiscal ' . $vector->cui . ' (' . ($vector->denumire ?: 'fără denumire') . ')',
            $date,
            $vector->cui
        );

        return response()->json(['success' => true, 'data' => $vector]);
    }

    public function update(Request $request, VectorFiscal $vector)
    {
        $vector->update($this->valideaza($request, $vector->id));

        Jurnal::scrie(
            'vector_modificare',
            'A modificat vectorul fiscal pentru ' . $vector->cui,
            [],
            $vector->cui
        );

        return response()->json(['success' => true, 'data' => $vector->fresh()]);
    }

    public function destroy(VectorFiscal $vector)
    {
        Jurnal::scrie('vector_stergere', 'A șters din vectorul fiscal ' . $vector->cui, [], $vector->cui);

        $vector->delete();

        return response()->json(['success' => true]);
    }

    /** Vectorul fiscal citit din SPV pentru un CUI (obligatiile reale de la ANAF). */
    public function spv(Request $request)
    {
        $query = VectorSpv::query()->orderBy('cui')->orderBy('cod_imp');

        if ($request->filled('cui')) {
            $query->where('cui', $request->query('cui'));
        }

        $randuri = $query->get()->map(function (VectorSpv $rand) {
            return array_merge($rand->toArray(), [
                'data_vector' => Format::data($rand->data_vector),
                'data_inceput' => Format::data($rand->data_inceput),
                'data_sfarsit' => Format::data($rand->data_sfarsit),
            ]);
        });

        return response()->json(['success' => true, 'data' => $randuri]);
    }

    /**
     * Situatia vector vs. declaratii depuse pentru o perioada: ce ar fi trebuit
     * depus conform vectorului si ce s-a depus efectiv.
     */
    public function situatie(Request $request)
    {
        $date = $request->validate([
            'luna' => 'required|integer|min:1|max:12',
            'anul' => 'required|integer|min:2000|max:2100',
        ]);

        $depuse = AnafDeclaratie::where('luna', $date['luna'])
            ->where('anul', $date['anul'])
            ->get()
            ->groupBy('cui');

        $situatie = VectorFiscal::orderBy('denumire')->get()->map(function ($vector) use ($depuse, $date) {
            $declaratiiCui = $depuse->get($vector->cui, collect());
            $randuri = [];

            foreach (VectorFiscal::DECLARATII as $tip) {
                $periodicitate = $vector->$tip;

                if (!$periodicitate || !$this->seDepuneInLuna($periodicitate, (int) $date['luna'])) {
                    continue;
                }

                $declaratie = $declaratiiCui->firstWhere('tip', $tip);

                $randuri[] = [
                    'tip' => $tip,
                    'periodicitate' => $periodicitate,
                    'depusa' => $declaratie !== null,
                    'stare' => $declaratie ? $declaratie->stare_declaratie : null,
                    'pas' => $declaratie ? $declaratie->pas : null,
                ];
            }

            return [
                'cui' => $vector->cui,
                'denumire' => $vector->denumire,
                'obligatii' => $randuri,
                'lipsa' => collect($randuri)->where('depusa', false)->count(),
            ];
        })->filter(function ($rand) {
            return $rand['obligatii'] !== [];
        })->values();

        return response()->json(['success' => true, 'data' => $situatie]);
    }

    /** O declaratie trimestriala se depune in lunile 1,4,7,10 etc. */
    protected function seDepuneInLuna(string $periodicitate, int $luna): bool
    {
        switch (mb_strtolower($periodicitate)) {
            case 'lunar':
                return true;
            case 'trimestrial':
                return in_array($luna, [1, 4, 7, 10], true);
            case 'semestrial':
                return in_array($luna, [1, 7], true);
            case 'anual':
                return $luna === 1;
            default:
                return false;
        }
    }

    protected function valideaza(Request $request, ?int $id = null): array
    {
        $reguli = [
            'cui' => 'required|string|max:20|unique:vector_fiscal,cui' . ($id ? ',' . $id : ''),
            'denumire' => 'nullable|string|max:255',
        ];

        foreach (VectorFiscal::DECLARATII as $tip) {
            $reguli[$tip] = 'nullable|string|in:' . implode(',', VectorFiscal::PERIODICITATI);
        }

        return $request->validate($reguli);
    }
}
