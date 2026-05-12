<?php

namespace App\Http\Controllers\Api;

use App\Models\Societati;
// use App\Events\SocietatiUpdated;
use App\Models\Exports\SocietatiExport;
//use App\Models\Imports\SocietatiImport;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class SocietatiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function indexPaginat(Request $request)
    {
          $records= Societati::select('*')->where("company_id",session("company_id"));
          $records=filterRequest($records,$request->searchModel,$request->cautareDupa,$request->sortModel,$request->filterModel);
        $records=  $records->orderBy('id','desc');
        $records=  $records->paginate($request->pageLength,
                                                                    ["page"=>$request->page]);
        
                              //::where("user_id",auth()->user()->id)
                                
        return json_encode($records);
    }
     public function index()
    {
          $societati= Societati::where("company_id",session("company_id"))->get();
          return json_encode($societati);
    }
    public function export() 
        {
             
            ob_end_clean(); 
            ob_start(); 
             $company_id=session("company_id");
            return Excel::download((new SocietatiExport)->forCompany($company_id),"societati.xls");


        }

    public function import(Request $request) 
        {
          $fileName = "societati_".time().".".$request->file->getClientOriginalExtension();
          $request->file->move(public_path("upload"), $fileName);
          
          Excel::import(new SocietatiImport, public_path("upload")."/".$fileName);

          
            $societati= Societati::where("company_id",session("company_id"))
                                                     ->paginate($request->pageLength,['page'=>$request->page]);
         
            return json_encode($societati);
             
        
           
         }
        
    public function storeExcel() 
        {
            ob_end_clean(); 
            ob_start(); 
            $company_id=session("company_id");
            
            Excel::store((new SocietatiExport)->forCompany($company_id), "societati.xls","public",null,[
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

        // event(new SocietatiUpdated());
         $cod_firma= Societati::create([
        "company_id"=>session("company_id"),
        
    	  "denumire"=>$request->denumire,
    	  "cod_firma"=>$request->cod_firma,           
        ]);
        return $cod_firma;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Societati  $societati
     * @return \Illuminate\Http\Response
     */
    public function show(Societati $societati)
    {
        $resp= Societati::where("id",$societati->id)
                               ->where("company_id",session("company_id"))
        					   ->get()->first();
                                
        return json_encode($resp);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Societati  $societati
     * @return \Illuminate\Http\Response
     */
    public function edit(Societati $societati)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Societati  $societati
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Societati $societati)
    {
        $societati->update([
    	  "denumire"=>$request->denumire,
    	  "cod_firma"=>$request->cod_firma,
        ]);
       // event(new SocietatiUpdated());
        return $societati;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Societati  $societati
     * @return \Illuminate\Http\Response
     */
    public function destroy(Societati $societati)
    {
        $societati->delete();
      //  event(new SocietatiUpdated());

    }
}