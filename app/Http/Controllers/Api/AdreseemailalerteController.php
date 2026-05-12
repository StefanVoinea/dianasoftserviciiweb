<?php

namespace App\Http\Controllers\Api;

use App\Models\Adreseemailalerte;
// use App\Events\AdreseemailalerteUpdated;
use App\Models\Exports\AdreseemailalerteExport;
//use App\Models\Imports\AdreseemailalerteImport;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class AdreseemailalerteController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function indexPaginat(Request $request)
    {
          $records= Adreseemailalerte::select('*')->where("company_id",session("company_id"));
          $records=filterRequest($records,$request->searchModel,$request->cautareDupa,$request->sortModel,$request->filterModel);
        $records=  $records->orderBy('id','desc');
        $records=  $records->paginate($request->pageLength,
                                                                    ["page"=>$request->page]);
        
                              //::where("user_id",auth()->user()->id)
                                
        return json_encode($records);
    }
     public function index()
    {
          $adreseemailalerte= Adreseemailalerte::where("company_id",session("company_id"))->get();
          return json_encode($adreseemailalerte);
    }
    public function export() 
        {
             
            ob_end_clean(); 
            ob_start(); 
             $company_id=session("company_id");
            return Excel::download((new AdreseemailalerteExport)->forCompany($company_id),"adreseemailalerte.xls");


        }

    public function import(Request $request) 
        {
          $fileName = "adreseemailalerte_".time().".".$request->file->getClientOriginalExtension();
          $request->file->move(public_path("upload"), $fileName);
          
          Excel::import(new AdreseemailalerteImport, public_path("upload")."/".$fileName);

          
            $adreseemailalerte= Adreseemailalerte::where("company_id",session("company_id"))
                                                     ->paginate($request->pageLength,['page'=>$request->page]);
         
            return json_encode($adreseemailalerte);
             
        
           
         }
        
    public function storeExcel() 
        {
            ob_end_clean(); 
            ob_start(); 
            $company_id=session("company_id");
            
            Excel::store((new AdreseemailalerteExport)->forCompany($company_id), "adreseemailalerte.xls","public",null,[
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

        // event(new AdreseemailalerteUpdated());
         $adresa_email= Adreseemailalerte::create([
        "company_id"=>session("company_id"),
        
    	  "alerta"=>$request->alerta,
    	  "adresa_email"=>$request->adresa_email,           
        ]);
        return $adresa_email;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Adreseemailalerte  $adreseemailalerte
     * @return \Illuminate\Http\Response
     */
    public function show(Adreseemailalerte $adreseemailalerte)
    {
        $resp= Adreseemailalerte::where("id",$adreseemailalerte->id)
                               ->where("company_id",session("company_id"))
        					   ->get()->first();
                                
        return json_encode($resp);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Adreseemailalerte  $adreseemailalerte
     * @return \Illuminate\Http\Response
     */
    public function edit(Adreseemailalerte $adreseemailalerte)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Adreseemailalerte  $adreseemailalerte
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Adreseemailalerte $adreseemailalerte)
    {
        $adreseemailalerte->update([
    	  "alerta"=>$request->alerta,
    	  "adresa_email"=>$request->adresa_email,
        ]);
       // event(new AdreseemailalerteUpdated());
        return $adreseemailalerte;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Adreseemailalerte  $adreseemailalerte
     * @return \Illuminate\Http\Response
     */
    public function destroy(Adreseemailalerte $adreseemailalerte)
    {
        $adreseemailalerte->delete();
      //  event(new AdreseemailalerteUpdated());

    }
}