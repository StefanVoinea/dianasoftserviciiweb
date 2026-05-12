<?php

namespace App\Http\Controllers\Api;

use App\Models\Documente;
// use App\Events\DocumenteUpdated;
use App\Models\Exports\DocumenteExport;
//use App\Models\Imports\DocumenteImport;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class DocumenteController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function indexPaginat(Request $request)
    {
          $records= Documente::select('*')->where("company_id",session("company_id"));
          $records=filterRequest($records,$request->searchModel,$request->cautareDupa,$request->sortModel,$request->filterModel);
        $records=  $records->orderBy('id','desc');
        $records=  $records->paginate($request->pageLength,
                                                                    ["page"=>$request->page]);
        
                              //::where("user_id",auth()->user()->id)
                                
        return json_encode($records);
    }
     public function index()
    {
          $documente= Documente::where("company_id",session("company_id"))->get();
          return json_encode($documente);
    }
    public function export() 
        {
             
            ob_end_clean(); 
            ob_start(); 
             $company_id=session("company_id");
            return Excel::download((new DocumenteExport)->forCompany($company_id),"documente.xls");


        }

    public function import(Request $request) 
        {
          $fileName = "documente_".time().".".$request->file->getClientOriginalExtension();
          $request->file->move(public_path("upload"), $fileName);
          
          Excel::import(new DocumenteImport, public_path("upload")."/".$fileName);

          
            $documente= Documente::where("company_id",session("company_id"))
                                                     ->paginate($request->pageLength,['page'=>$request->page]);
         
            return json_encode($documente);
             
        
           
         }
        
    public function storeExcel() 
        {
            ob_end_clean(); 
            ob_start(); 
            $company_id=session("company_id");
            
            Excel::store((new DocumenteExport)->forCompany($company_id), "documente.xls","public",null,[
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

        // event(new DocumenteUpdated());
         $exportabil= Documente::create([
        "company_id"=>session("company_id"),
        
    	  "agentia"=>$request->agentia,
    	  "denumire_doc"=>$request->denumire_doc,
    	  "tip_doc"=>$request->tip_doc,
    	  "aplicatie"=>$request->aplicatie,
    	  "continut"=>$request->continut,
        "data"=>$request->data?dateFormatStocare($request->data):null,
    	  "utilizator"=>$request->utilizator,
        "data_operarii"=>$request->data_operarii?dateFormatStocare($request->data_operarii):null,
    	  "printabil"=>$request->printabil,
    	  "exportabil"=>$request->exportabil,           
        ]);
        return $exportabil;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Documente  $documente
     * @return \Illuminate\Http\Response
     */
    public function show(Documente $documente)
    {
        $resp= Documente::where("id",$documente->id)
                               ->where("company_id",session("company_id"))
        					   ->get()->first();
                                
        return json_encode($resp);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Documente  $documente
     * @return \Illuminate\Http\Response
     */
    public function edit(Documente $documente)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Documente  $documente
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Documente $documente)
    {
        $documente->update([
    	  "agentia"=>$request->agentia,
    	  "denumire_doc"=>$request->denumire_doc,
    	  "tip_doc"=>$request->tip_doc,
    	  "aplicatie"=>$request->aplicatie,
    	  "continut"=>$request->continut,
        "data"=>$request->data?dateFormatStocare($request->data):null,
    	  "utilizator"=>$request->utilizator,
        "data_operarii"=>$request->data_operarii?dateFormatStocare($request->data_operarii):null,
    	  "printabil"=>$request->printabil,
    	  "exportabil"=>$request->exportabil,
        ]);
       // event(new DocumenteUpdated());
        return $documente;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Documente  $documente
     * @return \Illuminate\Http\Response
     */
    public function destroy(Documente $documente)
    {
        $documente->delete();
      //  event(new DocumenteUpdated());

    }
}