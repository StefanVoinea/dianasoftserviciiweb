<?php

namespace App\Http\Controllers\Api;

use App\Models\Etransporttokens;
use App\Exports\EtransporttokensExport;
use App\Imports\EtransporttokensImport;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class EtransporttokensController extends Controller
{
    public function indexPaginat(Request $request)
    {
        $etransporttokens = Etransporttokens::select('*')->where("company_id", session("company_id"));
        $etransporttokens = filterRequest($etransporttokens, $request->searchModel, $request->cautareDupa, $request->sortModel, $request->filterModel);
        $etransporttokens = $etransporttokens->paginate(50);

        return json_encode($etransporttokens);
    }

    public function index()
    {
        $etransporttokens = Etransporttokens::where("company_id", session("company_id"))->get();
        return json_encode($etransporttokens);
    }

    public function export()
    {
        ob_end_clean();
        ob_start();
        $company_id = session("company_id");
        return Excel::download((new EtransporttokensExport)->forCompany($company_id), "etransporttokens.xls");
    }

    public function import(Request $request)
    {
        $fileName = "etransporttokens_" . time() . "." . $request->file->getClientOriginalExtension();
        $request->file->move(public_path("upload"), $fileName);

        Excel::import(new EtransporttokensImport, public_path("upload") . "/" . $fileName);

        $etransporttokens = Etransporttokens::where("company_id", session("company_id"))
            ->paginate(50);

        return json_encode($etransporttokens);
    }

    public function store(Request $request)
    {
        $request->validate([
            'cui' => 'required|string|max:13',
        ]);

        $etransporttoken = Etransporttokens::create([
            'cui' => $request->cui,
            'access_token' => $request->access_token ?? null,
            'refresh_token' => $request->refresh_token ?? null,
            'data_obtinerii' => $request->data_obtinerii ?? null,
            'data_expirare' => $request->data_expirare ?? null,
            'company_id' => session('company_id'),
        ]);

        return json_encode($etransporttoken);
    }

    public function show(Etransporttokens $etransporttoken)
    {
        if ($etransporttoken->company_id !== session('company_id')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        return json_encode($etransporttoken);
    }

    public function update(Request $request, Etransporttokens $etransporttoken)
    {
        if ($etransporttoken->company_id !== session('company_id')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $etransporttoken->update([
            'cui' => $request->cui ?? $etransporttoken->cui,
            'access_token' => $request->access_token ?? $etransporttoken->access_token,
            'refresh_token' => $request->refresh_token ?? $etransporttoken->refresh_token,
            'data_obtinerii' => $request->data_obtinerii ?? $etransporttoken->data_obtinerii,
            'data_expirare' => $request->data_expirare ?? $etransporttoken->data_expirare,
        ]);

        return json_encode($etransporttoken);
    }

    public function destroy(Etransporttokens $etransporttoken)
    {
        if ($etransporttoken->company_id !== session('company_id')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $etransporttoken->delete();
        return json_encode(['message' => 'Token deleted successfully']);
    }
}