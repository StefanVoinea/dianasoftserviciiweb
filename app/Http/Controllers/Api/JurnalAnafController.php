<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Exports\JurnalAnafExport;
use App\Models\AnafJurnal;
use App\Services\Anaf\Jurnal;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

/**
 * Jurnalul de activitate al modulului ANAF/SPV — cine ce a făcut și când.
 */
class JurnalAnafController extends Controller
{
    public function index(Request $request)
    {
        $intrari = $this->intrari($request);

        return response()->json([
            'success' => true,
            'data' => $intrari,
            'actiuni' => AnafJurnal::ACTIUNI,
            // Utilizatorii care apar in jurnal, pentru filtrare
            'utilizatori' => AnafJurnal::query()
                ->whereNotNull('user_id')
                ->select('user_id', 'user_nume')
                ->distinct()
                ->get(),
        ]);
    }

    /**
     * Acelasi jurnal, in Excel.
     *
     * Filtrele sunt aceleasi ca la afisare, deci fisierul poarta chiar ce se
     * vede pe ecran — nu tot jurnalul, cand omul a filtrat ceva anume.
     */
    public function export(Request $request)
    {
        $intrari = $this->intrari($request);

        Jurnal::scrie(
            'jurnal_export',
            'A exportat jurnalul de activitate: ' . $intrari->count() . ' înregistrări',
            $request->query()
        );

        $nume = 'jurnal_activitate_' . now()->format('Ymd_His') . '.xlsx';

        return Excel::download(new JurnalAnafExport($intrari), $nume);
    }

    /** Randurile jurnalului, dupa filtrele cerute. */
    protected function intrari(Request $request)
    {
        $query = AnafJurnal::query()->orderByDesc('created_at');

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->query('user_id'));
        }

        if ($request->filled('actiune')) {
            $query->where('actiune', $request->query('actiune'));
        }

        if ($request->filled('cif')) {
            $query->where('cif', 'like', '%' . $request->query('cif') . '%');
        }

        if ($request->filled('cautare')) {
            $query->where('descriere', 'like', '%' . $request->query('cautare') . '%');
        }

        if ($request->filled('de_la')) {
            $query->whereDate('created_at', '>=', $request->query('de_la'));
        }

        if ($request->filled('pana_la')) {
            $query->whereDate('created_at', '<=', $request->query('pana_la'));
        }

        if ($request->boolean('doar_esecuri')) {
            $query->where('reusit', false);
        }

        return $query->limit((int) $request->query('limita', 300))->get()->map(function (AnafJurnal $intrare) {
            return [
                'id' => $intrare->id,
                'cand' => optional($intrare->created_at)->format('d.m.Y H:i:s'),
                'utilizator' => $intrare->user_nume ?: 'necunoscut',
                'email' => $intrare->user_email,
                'user_id' => $intrare->user_id,
                'actiune' => $intrare->actiune,
                'actiune_eticheta' => $intrare->actiune_etichete,
                'descriere' => $intrare->descriere,
                'cif' => $intrare->cif,
                'ip' => $intrare->ip,
                'reusit' => $intrare->reusit,
                'context' => $intrare->context,
            ];
        });
    }
}
