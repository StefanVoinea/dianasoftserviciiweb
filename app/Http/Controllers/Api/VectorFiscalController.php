<?php

namespace App\Http\Controllers\Api;

use App\Exports\VectorLunarExport;
use App\Http\Controllers\Controller;
use App\Models\AnafDeclaratie;
use App\Models\VectorDeclaratie;
use App\Models\VectorFiscal;
use App\Models\VectorSpv;
use App\Services\Anaf\Format;
use App\Services\Anaf\Jurnal;
use App\Services\Anaf\Spv\RaportVectorLunar;
use Barryvdh\Snappy\Facades\SnappyPdf;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

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

    /**
     * Vectorul fiscal al unei luni, pe hartie sau in Excel.
     *
     * Randurile sunt entitatile inrolate, coloanele — declaratiile deduse din
     * vectorul fiscal citit din SPV. Pentru fiecare declaratie: recipisa
     * (index, data si ora), daca e depusa; altfel periodicitatea obligatiei,
     * cu atentionare cand luna ceruta chiar era a ei.
     */
    public function lunar(Request $request, RaportVectorLunar $serviciu)
    {
        $date = $request->validate([
            'luna' => 'required|integer|min:1|max:12',
            'anul' => 'required|integer|min:2000|max:2100',
            'format' => 'required|in:pdf,excel',
        ]);

        $raport = $serviciu->pentruLuna((int) $date['luna'], (int) $date['anul']);
        $perioada = sprintf('%02d_%d', $raport['luna'], $raport['anul']);

        Jurnal::scrie(
            'vector_raport_lunar',
            sprintf(
                'A extras vectorul fiscal al lunii %02d/%d (%s): %d entități',
                $raport['luna'],
                $raport['anul'],
                $date['format'],
                count($raport['randuri'])
            ),
            ['luna' => $raport['luna'], 'anul' => $raport['anul'], 'format' => $date['format']]
        );

        if ($date['format'] === 'excel') {
            return Excel::download(new VectorLunarExport($raport), 'vector_fiscal_' . $perioada . '.xlsx');
        }

        return SnappyPdf::loadView('spv.vector-lunar', [
            'raport' => $raport,
            'sigla' => $this->sigla(),
        ])
            ->setPaper('a4')
            ->setOrientation('landscape')
            ->setOption('margin-top', 8)
            ->setOption('margin-bottom', 10)
            ->setOption('margin-left', 8)
            ->setOption('margin-right', 8)
            ->setOption('footer-font-size', '7')
            ->setOption('footer-right', 'Pag. [page] / [topage]')
            ->download('vector_fiscal_' . $perioada . '.pdf');
    }

    /**
     * Declaratiile asteptate pe CUI-uri: cele deduse de aplicatie si cele
     * scrise de om, cu periodicitatea si valabilitatea fiecareia.
     *
     * Cu „deduce", deductia se face pe loc, pentru luna curenta: fereastra de
     * actualizare are astfel ce arata si inainte de primul raport descarcat —
     * altfel tabelul ar fi gol pana intocmeste cineva un raport.
     */
    public function declaratii(Request $request, RaportVectorLunar $serviciu)
    {
        if ($request->boolean('deduce')) {
            try {
                $serviciu->pentruLuna((int) now()->format('n'), (int) now()->format('Y'));
            } catch (\Exception $e) {
                // Deductia e un ajutor, nu o conditie: lista se arata si fara ea.
            }
        }

        $query = VectorDeclaratie::query()->orderBy('cui')->orderBy('tip')->orderBy('data_inceput');

        if ($request->filled('cui')) {
            $query->where('cui', $request->query('cui'));
        }

        return response()->json([
            'success' => true,
            'data' => $query->get()->map(function (VectorDeclaratie $rand) {
                return [
                    'id' => $rand->id,
                    'cui' => $rand->cui,
                    'tip' => $rand->tip,
                    'perfisc' => $rand->perfisc,
                    'data_inceput' => $rand->data_inceput ? $rand->data_inceput->format('Y-m-d') : null,
                    'data_sfarsit' => $rand->data_sfarsit ? $rand->data_sfarsit->format('Y-m-d') : null,
                    'sursa' => $rand->sursa,
                    'obligatii' => $rand->obligatii,
                ];
            }),
            'periodicitati' => VectorDeclaratie::PERIODICITATI,
        ]);
    }

    /** Omul adauga o declaratie pe un CUI: Bilant semestrial, de pilda. */
    public function adaugaDeclaratie(Request $request)
    {
        $date = $request->validate([
            'cui' => 'required|string|max:20',
            'tip' => 'required|string|max:20',
            'perfisc' => 'required|in:' . implode(',', VectorDeclaratie::PERIODICITATI),
            'data_inceput' => 'nullable|date',
            'data_sfarsit' => 'nullable|date|after_or_equal:data_inceput',
        ]);

        $declaratie = VectorDeclaratie::create(array_merge($date, [
            'tip' => strtoupper(trim($date['tip'])),
            'sursa' => 'manuala',
        ]));

        Jurnal::scrie(
            'vector_modificare',
            sprintf('A adăugat în vector %s %s pentru %s', $declaratie->tip, $declaratie->perfisc, $declaratie->cui),
            $date,
            $declaratie->cui
        );

        return response()->json(['success' => true, 'data' => $declaratie]);
    }

    /** Se pot indrepta periodicitatea si valabilitatea, nu firma sau tipul. */
    public function modificaDeclaratie(Request $request, VectorDeclaratie $declaratie)
    {
        $date = $request->validate([
            'perfisc' => 'required|in:' . implode(',', VectorDeclaratie::PERIODICITATI),
            'data_inceput' => 'nullable|date',
            'data_sfarsit' => 'nullable|date|after_or_equal:data_inceput',
        ]);

        /*
         * Un rand dedus indreptat de om devine manual: altfel urmatoarea
         * intocmire a raportului i-ar scrie deductia la loc peste indreptare.
         */
        $declaratie->update(array_merge($date, ['sursa' => 'manuala']));

        Jurnal::scrie(
            'vector_modificare',
            sprintf('A modificat în vector %s pentru %s', $declaratie->tip, $declaratie->cui),
            $date,
            $declaratie->cui
        );

        return response()->json(['success' => true, 'data' => $declaratie->fresh()]);
    }

    public function stergeDeclaratie(VectorDeclaratie $declaratie)
    {
        Jurnal::scrie(
            'vector_stergere',
            sprintf('A șters din vector %s pentru %s', $declaratie->tip, $declaratie->cui),
            [],
            $declaratie->cui
        );

        $declaratie->delete();

        return response()->json(['success' => true]);
    }

    /**
     * Sigla SPV Curier, pusa in pagina ca SVG, nu ca legatura: wkhtmltopdf
     * citeste pagina in afara aplicatiei si nu ar putea cere fisierul.
     */
    protected function sigla(): string
    {
        $cale = public_path('images/sigle/spv-curier-orizontal.svg');

        return is_file($cale) ? (string) file_get_contents($cale) : '';
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
