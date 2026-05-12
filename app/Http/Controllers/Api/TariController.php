<?php

namespace App\Http\Controllers\Api;

use App\Models\Tari;
// use App\Events\TariUpdated;
use App\Models\Exports\TariExport;
use App\Models\Imports\TariImport;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class TariController extends Controller
{
    public function indexPaginat(Request $request)
    {
          $tari= Tari::select('*') ;
          
          $tari=filterRequest($tari,$request->searchModel,$request->cautareDupa,$request->sortModel,$request->filterModel);
          $tari= $tari->orderBy('id','desc')
                                          ->paginate($request->pageLength,
                                                      ['page'=>$request->page]);
          
                                //::where("user_id",auth()->user()->id)
                                  
          return json_encode($tari);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
          $tari= Tari::orderBy("denumire","asc")->get();
        // $tari=filterRequest($tari,$request->searchModel,$request->cautareDupa,$request->sortModel,$request->filterModel);
        // $tari=  $tari->paginate($request->pageLength,['page'=>$request->page]);
        
                              //::where("user_id",auth()->user()->id)
                                
        return json_encode($tari);
    }
   
    public function export() 
        {
             
            ob_end_clean(); 
            ob_start(); 
             $company_id=session("company_id");
            return Excel::download((new TariExport)->forCompany($company_id),"tari.xls");


        }

    public function import(Request $request) 
        {
          $fileName = "tari_".time().".".$request->file->getClientOriginalExtension();
          $request->file->move(public_path("upload"), $fileName);
          
          Excel::import(new TariImport, public_path("upload")."/".$fileName);

          
            $tari= Tari::paginate($request->pageLength,['page'=>$request->page]);
         
            return json_encode($tari);
             
        
           
         }
        
    public function storeExcel() 
        {
            ob_end_clean(); 
            ob_start(); 
            $company_id=session("company_id");
            
            Excel::store((new TariExport)->forCompany($company_id), "tari.xls","public",null,[
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
    	  "cod"=>["required"],]);

        // event(new TariUpdated());
         return Tari::create([
    	  "cod"=>$request->cod,
    	  "denumire"=>$request->denumire,
    	  "capitala"=>$request->capitala,
    	  "forma_guvernare"=>$request->forma_guvernare,
    	  "cod_tara_fiscal"=>$request->cod_tara_fiscal,
    	  "cod_bnr"=>$request->cod_bnr,
    	  "valuta"=>$request->valuta,
    	  "cod_sm"=>$request->cod_sm,
    	  "ue"=>$request->ue,           
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Tari  $tari
     * @return \Illuminate\Http\Response
     */
    public function show(Tari $tari)
    {
        $resp= Tari::where("id",$tari->id)
        											->get()->first();
                                
        return json_encode($resp);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Tari  $tari
     * @return \Illuminate\Http\Response
     */
    public function edit(Tari $tari)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Tari  $tari
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Tari $tari)
    {
        $tari->update([
    	  "cod"=>$request->cod,
    	  "denumire"=>$request->denumire,
    	  "capitala"=>$request->capitala,
    	  "forma_guvernare"=>$request->forma_guvernare,
    	  "cod_tara_fiscal"=>$request->cod_tara_fiscal,
    	  "cod_bnr"=>$request->cod_bnr,
    	  "valuta"=>$request->valuta,
    	  "cod_sm"=>$request->cod_sm,
    	  "ue"=>$request->ue,
        ]);
       // event(new TariUpdated());
        return $tari;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Tari  $tari
     * @return \Illuminate\Http\Response
     */
    public function destroy(Tari $tari)
    {
        $tari->delete();
      //  event(new TariUpdated());

    }
}