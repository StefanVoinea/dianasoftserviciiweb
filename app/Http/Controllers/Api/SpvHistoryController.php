<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SpvMesaj;
use Illuminate\Http\Request;

class SpvHistoryController extends Controller
{
    public function index(Request $request)
    {
        $query = SpvMesaj::query()->orderByDesc('created_at');

        if ($request->filled('cif')) {
            $query->where('cif', 'like', '%' . $request->query('cif') . '%');
        }

        $items = $query->get()->map(function ($item) {
            return [
                'id' => $item->mesaj_id,
                'tip' => $item->tip,
                'cif' => $item->cif,
                'data_creare' => $item->data_creare,
                'id_solicitare' => $item->id_solicitare,
                'detalii' => $item->detalii,
                'cale_fisier' => $item->cale_fisier,
                'hash_fisier' => $item->hash_fisier,
                'descarcat_la' => $item->descarcat_la,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $items,
        ]);
    }
}
