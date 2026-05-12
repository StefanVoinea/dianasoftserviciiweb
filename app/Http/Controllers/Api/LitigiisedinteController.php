<?php

namespace App\Http\Controllers\Api;

use App\Models\Litigiisedinte;
// use App\Events\LitigiisedinteUpdated;
use App\Models\Exports\LitigiisedinteExport;
//use App\Models\Imports\LitigiisedinteImport;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class LitigiisedinteController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function indexPaginat(Request $request)
    {
          $records= Litigiisedinte::select('*')->where("company_id",session("company_id"));
          $records=filterRequest($records,$request->searchModel,$request->cautareDupa,$request->sortModel,$request->filterModel);
        $records=  $records->orderBy('id','desc');
        $records=  $records->paginate($request->pageLength,
                                                                    ["page"=>$request->page]);
        
                              //::where("user_id",auth()->user()->id)
                                
        return json_encode($records);
    }
     public function index()
    {
          $litigiisedinte= Litigiisedinte::where("company_id",session("company_id"))->get();
          return json_encode($litigiisedinte);
    }
    public function export() 
        {
             
            ob_end_clean(); 
            ob_start(); 
             $company_id=session("company_id");
            return Excel::download((new LitigiisedinteExport)->forCompany($company_id),"litigiisedinte.xls");


        }

    public function import(Request $request) 
        {
          $fileName = "litigiisedinte_".time().".".$request->file->getClientOriginalExtension();
          $request->file->move(public_path("upload"), $fileName);
          
          Excel::import(new LitigiisedinteImport, public_path("upload")."/".$fileName);

          
            $litigiisedinte= Litigiisedinte::where("company_id",session("company_id"))
                                                     ->paginate($request->pageLength,['page'=>$request->page]);
         
            return json_encode($litigiisedinte);
             
        
           
         }
        
    public function storeExcel() 
        {
            ob_end_clean(); 
            ob_start(); 
            $company_id=session("company_id");
            
            Excel::store((new LitigiisedinteExport)->forCompany($company_id), "litigiisedinte.xls","public",null,[
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
      //   $request->validate( [
       //]);

        // event(new LitigiisedinteUpdated());
         $data_document= Litigiisedinte::create([
        "company_id"=>session("company_id"),
        
    	  "litigiu_id"=>$request->litigiu_id,
    	  "complet"=>$request->complet,
        "data_sedinta"=>$request->data_sedinta?dateFormatStocare($request->data_sedinta):null,
    	  "ora_sedinta"=>$request->ora_sedinta,
    	  "solutie"=>$request->solutie,
    	  "solutie_sumar"=>$request->solutie_sumar,
    	  "data_pronuntare"=>$request->data_pronuntare,
    	  "document_sedinta"=>$request->document_sedinta,
    	  "numar_document"=>$request->numar_document,
    	  "data_document"=>$request->data_document,           
        ]);
        return $data_document;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Litigiisedinte  $litigiisedinte
     * @return \Illuminate\Http\Response
     */
    public function show(Litigiisedinte $litigiisedinte)
    {
        $resp= Litigiisedinte::where("id",$litigiisedinte->id)
                               ->where("company_id",session("company_id"))
        					   ->get()->first();
                                
        return json_encode($resp);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Litigiisedinte  $litigiisedinte
     * @return \Illuminate\Http\Response
     */
    public function edit(Litigiisedinte $litigiisedinte)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Litigiisedinte  $litigiisedinte
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Litigiisedinte $litigiisedinte)
    {
        $litigiisedinte->update([
    	  "litigiu_id"=>$request->litigiu_id,
    	  "complet"=>$request->complet,
        "data_sedinta"=>$request->data_sedinta?dateFormatStocare($request->data_sedinta):null,
    	  "ora_sedinta"=>$request->ora_sedinta,
    	  "solutie"=>$request->solutie,
    	  "solutie_sumar"=>$request->solutie_sumar,
    	  "data_pronuntare"=>$request->data_pronuntare,
    	  "document_sedinta"=>$request->document_sedinta,
    	  "numar_document"=>$request->numar_document,
    	  "data_document"=>$request->data_document,
        ]);
       // event(new LitigiisedinteUpdated());
        return $litigiisedinte;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Litigiisedinte  $litigiisedinte
     * @return \Illuminate\Http\Response
     */
    public function destroy(Litigiisedinte $litigiisedinte)
    {
        $litigiisedinte->delete();
      //  event(new LitigiisedinteUpdated());

    }
}