<?php

namespace App\Http\Controllers\Api;

use App\Models\Efacturaparams;
// use App\Events/EfacturaparamsUpdated;
use App\Exports\EfacturaparamsExport;
use App\Imports\EfacturaparamsImport;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class EfacturaparamsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function indexPaginat(Request $request)
    {
          $efacturaparams= Efacturaparams::select('*')->where("company_id",session("company_id"));
        $efacturaparams=filterRequest($efacturaparams,$request->searchModel,$request->cautareDupa,$request->sortModel,$request->filterModel);
        $efacturaparams=  $efacturaparams->paginate(50);
        
                              //::where("user_id",auth()->user()->id)
                                
        return json_encode($efacturaparams);
    }
     public function index()
    {
          $efacturaparams= Efacturaparams::get()->first();
          ob_end_clean(); 
          ob_start(); 
          return json_encode($efacturaparams);
    }
    public function export() 
        {
             
            ob_end_clean(); 
            ob_start(); 
             $company_id=session("company_id");
            return Excel::download((new EfacturaparamsExport)->forCompany($company_id),"efacturaparams.xls");


        }

    public function import(Request $request) 
        {
          $fileName = "efacturaparams_".time().".".$request->file->getClientOriginalExtension();
          $request->file->move(public_path("upload"), $fileName);
          
          Excel::import(new EfacturaparamsImport, public_path("upload")."/".$fileName);

          
            $efacturaparams= Efacturaparams::where("company_id",session("company_id"))
                                                     ->paginate(50);
         
            return json_encode($efacturaparams);
             
        
           
         }
        
    public function storeExcel() 
        {
            ob_end_clean(); 
            ob_start(); 
            $company_id=session("company_id");
            
            Excel::store((new EfacturaparamsExport)->forCompany($company_id), "efacturaparams.xls","public",null,[
        "visibility" => "private",
    ]);
        }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
         $request->validate( [
         
    	  "link_authorization"=>["required"],]);

        // event(new EfacturaparamsUpdated());
         $linie_versiune_efactura= Efacturaparams::create([
    	  "link_authorization"=>$request->link_authorization,
    	  "link_token"=>$request->link_token,
    	  "link_revoke_token"=>$request->link_revoke_token,
    	  "link_upload"=>$request->link_upload,
    	  "link_test_upload"=>$request->link_test_upload,
    	  "link_stare_mesaj"=>$request->link_stare_mesaj,
    	  "link_test_stare_mesaj"=>$request->link_test_stare_mesaj,
    	  "link_lista_mesaje"=>$request->link_lista_mesaje,
    	  "link_test_lista_mesaje"=>$request->link_test_lista_mesaje,
    	  "link_lista_mesaje_cu_paginatie"=>$request->link_lista_mesaje_cu_paginatie,
    	  "link_test_lista_mesaje_cu_paginatie"=>$request->link_test_lista_mesaje_cu_paginatie,
    	  "link_descarcare_raspuns"=>$request->link_descarcare_raspuns,
    	  "link_test_descarcare_raspuns"=>$request->link_test_descarcare_raspuns,
    	  "link_validare_xml"=>$request->link_validare_xml,
    	  "link_transform_xml_to_pdf"=>$request->link_transform_xml_to_pdf,
    	  "linie_versiune_efactura"=>$request->linie_versiune_efactura,           
        ]);
        return $linie_versiune_efactura->paginate(50);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App/Efacturaparams  $efacturaparams
     * @return \Illuminate\Http\Response
     */
    public function show(Efacturaparams $efacturaparams)
    {
        $resp= Efacturaparams::where("id",$efacturaparams->id)
                               ->where("company_id",session("company_id"))
        					   ->get()->first();
                                
        return json_encode($resp);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App/Efacturaparams  $efacturaparams
     * @return \Illuminate\Http\Response
     */
    public function edit(Efacturaparams $efacturaparams)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App/Efacturaparams  $efacturaparams
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Efacturaparams $efacturaparams)
    {
        $efacturaparams->update([
    	  "link_authorization"=>$request->link_authorization,
    	  "link_token"=>$request->link_token,
    	  "link_revoke_token"=>$request->link_revoke_token,
    	  "link_upload"=>$request->link_upload,
    	  "link_test_upload"=>$request->link_test_upload,
    	  "link_stare_mesaj"=>$request->link_stare_mesaj,
    	  "link_test_stare_mesaj"=>$request->link_test_stare_mesaj,
    	  "link_lista_mesaje"=>$request->link_lista_mesaje,
    	  "link_test_lista_mesaje"=>$request->link_test_lista_mesaje,
    	  "link_lista_mesaje_cu_paginatie"=>$request->link_lista_mesaje_cu_paginatie,
    	  "link_test_lista_mesaje_cu_paginatie"=>$request->link_test_lista_mesaje_cu_paginatie,
    	  "link_descarcare_raspuns"=>$request->link_descarcare_raspuns,
    	  "link_test_descarcare_raspuns"=>$request->link_test_descarcare_raspuns,
    	  "link_validare_xml"=>$request->link_validare_xml,
    	  "link_transform_xml_to_pdf"=>$request->link_transform_xml_to_pdf,
    	  "linie_versiune_efactura"=>$request->linie_versiune_efactura,
        ]);
       // event(new EfacturaparamsUpdated());
        return $efacturaparams;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App/Efacturaparams  $efacturaparams
     * @return \Illuminate\Http\Response
     */
    public function destroy(Efacturaparams $efacturaparams)
    {
        $efacturaparams->delete();
      //  event(new EfacturaparamsUpdated());

    }
}