<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Datefirmeregcom;
use Illuminate\Http\Request;

/**
 * API-ul de consultare a datelor firmelor din Registrul Comertului pentru celelalte aplicatii DianaSoft (cheie de
 * aplicatie, middleware regcom.cheie; config/regcom.php). Tabela datefirmeregcom sta doar in app.dianasoft.ro
 * (10.11.2026). Numai citire: datele se administreaza aici, din Nomenclatoare > Date firme Reg. Com.
 *
 *  GET  /api/regcom/cui/{cui}                 firma dupa CUI (doar cifrele conteaza), 404 daca nu e
 *  GET  /api/regcom/cauta?text=&camp=&ordine= firmele cu denumirea / CUI-ul care contin textul (camp=telefon: egal),
 *                                             cel mult regcom.limita_cautare randuri
 *  POST /api/regcom/lista                     lista paginata a nomenclatorului (searchModel, cautareDupa, sortModel,
 *                                             filterModel, pageLength, page), exact ca indexPaginat-ul din aplicatii
 *  GET  /api/regcom/{id}                      o firma dupa id
 */
class RegcomApiController extends Controller
{
    public function dupaCui(string $cui)
    {
        $cifre = preg_replace('/[^0-9]/', '', $cui);
        if ($cifre === '') {
            return response()->json(['message' => 'CUI gol.'], 422);
        }
        $firma = Datefirmeregcom::where('cui', $cifre)->first();
        if (! $firma) {
            return response()->json(['message' => "Nu exista firma cu CUI $cifre."], 404);
        }

        return response()->json($firma);
    }

    public function cauta(Request $request)
    {
        $text = trim((string) $request->query('text', ''));
        if ($text === '') {
            return response()->json([]);
        }
        $camp = (string) $request->query('camp', 'denumire_cui');
        $ordine = (string) $request->query('ordine', 'denumire');
        if (! in_array($ordine, ['denumire', 'cui', 'regcom', 'localitate', 'judet', 'telefon', 'id'], true)) {
            $ordine = 'denumire';
        }
        $limita = min((int) config('regcom.limita_cautare', 200), max(1, (int) $request->query('limita', 200)));

        $q = Datefirmeregcom::query();
        if ($camp === 'telefon') {
            $q->where('telefon', $text);
        } elseif ($camp === 'cui') {
            $q->where('cui', 'like', '%'.str_replace(' ', '%', $text).'%');
        } elseif ($camp === 'denumire') {
            $q->where('denumire', 'like', '%'.str_replace(' ', '%', $text).'%');
        } else {
            // ca la cautarea partenerului din aplicatii: denumirea sau CUI-ul contin textul (spatiile = orice)
            $sablon = '%'.str_replace(' ', '%', $text).'%';
            $q->where(function ($w) use ($sablon) {
                $w->where('denumire', 'like', $sablon)->orWhere('cui', 'like', $sablon);
            });
        }

        return response()->json($q->orderBy($ordine)->limit($limita)->get());
    }

    public function lista(Request $request)
    {
        $records = Datefirmeregcom::select('*');
        // tabelele din aplicatii trimit sortModel ca lista cu cel putin un element ({colId, sort}) si filterModel ca lista
        // (si goala); filterRequest le asteapta asa, deci fara ele punem echivalentul "nesortat, nefiltrat"
        $sortModel = (array) ($request->sortModel ?? []);
        if (! $sortModel) {
            $sortModel = [['colId' => 'id', 'sort' => null]];
        }
        $records = filterRequest($records, $request->searchModel, $request->cautareDupa, $sortModel, (array) ($request->filterModel ?? []));
        $records = $records->orderBy('id', 'desc')->paginate((int) ($request->pageLength ?: 25), ['*'], 'page', (int) ($request->page ?: 1));

        return response()->json($records);
    }

    public function arata(int $id)
    {
        $firma = Datefirmeregcom::find($id);
        if (! $firma) {
            return response()->json(['message' => 'Firma nu exista.'], 404);
        }

        return response()->json($firma);
    }
}
