<?php

namespace App\Http\Controllers\Api;

use App\Models\Localitati;
// use App\Events\LocalitatiUpdated;
use App\Models\Exports\LocalitatiExport;
use App\Models\Imports\LocalitatiImport;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class LocalitatiController extends Controller
{

    public function indexFiltrat(Request $request)
    {     

         if($request->judet!=""){
          $localitati= Localitati::where("judet","like","%".$request->judet."%")
                                ->orderBy("denumire",'asc')
                               ->get();  
         }else{
          if($request->filtru!=""){  
          $localitati= Localitati::where("denumire","like",$request->filtru."%")
                                ->orderBy("denumire",'asc')
                               ->get();
           }else{
            $localitati=[];
           }
         }
          
                               
          return json_encode($localitati);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
      public function searchLocalitate(Request $request)
    {     

       
          $localitati= Localitati::where("denumire","like","%".$request->valCaut."%")
                                ->orderBy($request->colCaut,'asc')
                               ->get();
           
          
                               
          return json_encode($localitati);
    }
   public function indexPaginat(Request $request)
    {
         $records= Localitati::select('*');
          
          $records=filterRequest($records,$request->searchModel,$request->cautareDupa,$request->sortModel,$request->filterModel);
          $records= $records->orderBy('id','desc')
                                          ->paginate($request->pageLength,
                                                      ['page'=>$request->page]);
          
                                //::where("user_id",auth()->user()->id)
                                  
          return json_encode($records);
    }
    public function index()
    {
          $localitati= Localitati::get();
          return json_encode($localitati);
    }
    public function export() 
        {
             
            ob_end_clean(); 
            ob_start(); 
             $company_id=session("company_id");
            return Excel::download((new LocalitatiExport)->forCompany($company_id),"localitati.xls");


        }

    public function import(Request $request) 
        {
          $fileName = "localitati_".time().".".$request->file->getClientOriginalExtension();
          $request->file->move(public_path("upload"), $fileName);
          
          Excel::import(new LocalitatiImport, public_path("upload")."/".$fileName);

          
            $localitati= Localitati::paginate($request->pageLength,['page'=>$request->page]);
         
            return json_encode($localitati);
             
        
           
         }
        
    public function storeExcel() 
        {
            ob_end_clean(); 
            ob_start(); 
            $company_id=session("company_id");
            
            Excel::store((new LocalitatiExport)->forCompany($company_id), "localitati.xls","public",null,[
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
    	  "denumire"=>["required"],]);

        // event(new LocalitatiUpdated());
         return Localitati::create([
    	  "denumire"=>$request->denumire,
    	  "judet"=>$request->judet,           
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Localitati  $localitati
     * @return \Illuminate\Http\Response
     */
    public function show(Localitati $localitati)
    {
        $resp= Localitati::where("id",$localitati->id)
        											->get()->first();
                                
        return json_encode($resp);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Localitati  $localitati
     * @return \Illuminate\Http\Response
     */
    public function edit(Localitati $localitati)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Localitati  $localitati
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Localitati $localitati)
    {
        $localitati->update([
    	  "denumire"=>$request->denumire,
    	  "judet"=>$request->judet,
        ]);
       // event(new LocalitatiUpdated());
        return $localitati;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Localitati  $localitati
     * @return \Illuminate\Http\Response
     */
    public function destroy(Localitati $localitati)
    {
        $localitati->delete();
      //  event(new LocalitatiUpdated());

    }
}