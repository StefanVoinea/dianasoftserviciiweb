<?php

namespace App\Http\Controllers\Api;

use App\Models\Datefirmeanaf;
use App\Models\Datefirmeregcom;
use App\Models\Exports\DatefirmeregcomExport;
use App\Http\Controllers\Controller;
use App\Models\Imports\DatefirmeregcomImport;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Maatwebsite\Excel\Facades\Excel;

class DatefirmeregcomController extends Controller
{
   public function indexPaginat(Request $request)
    {
         $records= Datefirmeregcom::select('*');
          
          $records=filterRequest($records,$request->searchModel,$request->cautareDupa,$request->sortModel,$request->filterModel);
          $records= $records->orderBy('id','desc')
                                          ->paginate($request->pageLength,
                                                      ['page'=>$request->page]);
          
                                //::where("user_id",auth()->user()->id)
                                  
          return json_encode($records);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $datefirmeregcom= Datefirmeregcom::select('*');
        $datefirmeregcom=filterRequest($datefirmeregcom,$request->searchModel,$request->cautareDupa,$request->sortModel,$request->filterModel);
        $datefirmeregcom=  $datefirmeregcom->paginate($request->pageLength,['page'=>$request->page]);
        
                              //::where("user_id",auth()->user()->id)
        
        return json_encode($datefirmeregcom);
    }

    public function export() 
        {
             
            ob_end_clean(); 
            ob_start(); 
            
            return Excel::download((new DatefirmeregcomExport),"datefirmeregcom.xls");


        }
   public function importdepeserver() 
        {
          
           // $toateFisierele=File::files(public_path("upload"));
           // foreach ($toateFisierele as $fileName){
           //  Log::info($fileName);
           //  Excel::import(new DatefirmeregcomImport, $fileName);
           // }
           $var=0;
           Datefirmeregcom::where("persoana_de_contact",null)->chunk(500, function($firme) use(&$var){
           // Datefirmeregcom::chunk(500, function($firme) use(&$var){
           $cuiArray=$firme->pluck("cui");
           $data=Carbon::today();
           $raspuns=verificaCuiAnafBulk($cuiArray,$data);
           if (isset(json_decode($raspuns)->found)){

           $resp=json_decode($raspuns)->found;
           Log::info("ANAF ".$var);
           foreach ($resp as $soc){
               $firma=Datefirmeanaf::create([
                              "cui"=>($soc->cui??null),  
                              "telefon"=>($soc->telefon??null),
                              "iban"=>($soc->iban??null),
                              "adresa"=>($soc->adresa??null),
                              "cod_postal"=>($soc->codPostal??null),

                            ]);
            
           }  

           }else{
            Log::info($raspuns);
           }

           ++$var;   

          });

           
           
           
           
         
            // $datefirmeregcom= Datefirmeregcom::paginate($request->pageLength,['page'=>$request->page]);
         
            // return json_encode($datefirmeregcom);
             
        
           
         }
    public function import(Request $request) 
        {
          $fileName = "datefirmeregcom_".time().".".$request->file->getClientOriginalExtension();
          $request->file->move(public_path("upload"), $fileName);
          Log::info("INCEPE IMPORT");
          Excel::import(new DatefirmeregcomImport, public_path("upload")."/".$fileName);
         Log::info("TERMINA IMPORT");
          
            $datefirmeregcom= Datefirmeregcom::paginate($request->pageLength,['page'=>$request->page]);
         
            return json_encode($datefirmeregcom);
             
        
           
         }
        
    public function storeExcel() 
        {
            ob_end_clean(); 
            ob_start(); 
          
            Excel::store((new DatefirmeregcomExport), "datefirmeregcom.xls","public",null,[
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

        // event(new DatefirmeregcomUpdated());
         return Datefirmeregcom::create([
    	  "denumire"=>$request->denumire,
    	  "cui"=>$request->cui,
    	  "regcom"=>$request->regcom,
    	  "adresa"=>$request->adresa,
    	  "localitate"=>$request->localitate,
    	  "judet"=>$request->judet,
    	  "telefon"=>$request->telefon,
    	  "email"=>$request->email,           
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Datefirmeregcom  $datefirmeregcom
     * @return \Illuminate\Http\Response
     */
    public function show(Datefirmeregcom $datefirmeregcom)
    {
        $resp= Datefirmeregcom::where("id",$datefirmeregcom->id)
        											->get()->first();
                                
        return json_encode($resp);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Datefirmeregcom  $datefirmeregcom
     * @return \Illuminate\Http\Response
     */
    public function edit(Datefirmeregcom $datefirmeregcom)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Datefirmeregcom  $datefirmeregcom
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Datefirmeregcom $datefirmeregcom)
    {
        $datefirmeregcom->update([
    	  "denumire"=>$request->denumire,
    	  "cui"=>$request->cui,
    	  "regcom"=>$request->regcom,
    	  "adresa"=>$request->adresa,
    	  "localitate"=>$request->localitate,
    	  "judet"=>$request->judet,
    	  "telefon"=>$request->telefon,
    	  "email"=>$request->email,
        ]);
       // event(new DatefirmeregcomUpdated());
        return $datefirmeregcom;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Datefirmeregcom  $datefirmeregcom
     * @return \Illuminate\Http\Response
     */
    public function destroy(Datefirmeregcom $datefirmeregcom)
    {
        $datefirmeregcom->delete();
      //  event(new DatefirmeregcomUpdated());

    }
}