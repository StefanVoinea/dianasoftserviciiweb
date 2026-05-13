<?php

namespace App\Http\Controllers\Api;

use App\Models\Tipvaluta;
// use App\Events\TipvalutaUpdated;
use App\Models\Exports\TipvalutaExport;
use App\Models\Imports\TipvalutaImport;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class TipvalutaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
   public function indexPaginat(Request $request)
    {
         $records= Tipvaluta::select('*');
          
          $records=filterRequest($records,$request->searchModel,$request->cautareDupa,$request->sortModel,$request->filterModel);
          $records= $records->orderBy('id','desc')
                                          ->paginate($request->pageLength,
                                                      ['page'=>$request->page]);
          
                                //::where("user_id",auth()->user()->id)
                                  
          return json_encode($records);
    }
     public function index()
    {
          $tipvaluta= Tipvaluta::get();
          return json_encode($tipvaluta);
    }
    public function export() 
        {
             
            ob_end_clean(); 
            ob_start(); 
             $company_id=session("company_id");
            return Excel::download((new TipvalutaExport)->forCompany($company_id),"tipvaluta.xls");


        }

    public function import(Request $request) 
        {
          $fileName = "tipvaluta_".time().".".$request->file->getClientOriginalExtension();
          $request->file->move(public_path("upload"), $fileName);
          
          Excel::import(new TipvalutaImport, public_path("upload")."/".$fileName);

          
            $tipvaluta= Tipvaluta::where("company_id",session("company_id"))
                                                     ->paginate($request->pageLength,['page'=>$request->page]);
         
            return json_encode($tipvaluta);
             
        
           
         }
        
    public function storeExcel() 
        {
            ob_end_clean(); 
            ob_start(); 
            $company_id=session("company_id");
            
            Excel::store((new TipvalutaExport)->forCompany($company_id), "tipvaluta.xls","public",null,[
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
         
    	  "simbol"=>["required"],]);

        // event(new TipvalutaUpdated());
         $denumire= Tipvaluta::create([
    	  "simbol"=>$request->simbol,
    	  "denumire"=>$request->denumire,           
        ]);
        return $denumire->paginate($request->pageLength,['page'=>$request->page]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Tipvaluta  $tipvaluta
     * @return \Illuminate\Http\Response
     */
    public function show(Tipvaluta $tipvaluta)
    {
        $resp= Tipvaluta::where("id",$tipvaluta->id)
                                ->get()->first();
                                
        return json_encode($resp);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Tipvaluta  $tipvaluta
     * @return \Illuminate\Http\Response
     */
    public function edit(Tipvaluta $tipvaluta)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Tipvaluta  $tipvaluta
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Tipvaluta $tipvaluta)
    {
        $tipvaluta->update([
    	  "simbol"=>$request->simbol,
    	  "denumire"=>$request->denumire,
        ]);
       // event(new TipvalutaUpdated());
        return $tipvaluta;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Tipvaluta  $tipvaluta
     * @return \Illuminate\Http\Response
     */
    public function destroy(Tipvaluta $tipvaluta)
    {
        $tipvaluta->delete();
      //  event(new TipvalutaUpdated());

    }
}