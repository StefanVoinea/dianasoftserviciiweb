<?php

namespace App\Http\Controllers\Api;

use App\Models\Judet;
// use App\Events\JudetUpdated;
use App\Models\Exports\JudetExport;
use App\Models\Imports\JudetImport;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class JudetController extends Controller
{
   public function indexPaginat(Request $request)
    {
          $judete= Judet::select('*') ;
          
          $judete=filterRequest($judete,$request->searchModel,$request->cautareDupa,$request->sortModel,$request->filterModel);
          $judete= $judete->orderBy('id','desc')
                                          ->paginate($request->pageLength,
                                                      ['page'=>$request->page]);
          
                                //::where("user_id",auth()->user()->id)
                                  
          return json_encode($judete);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
          $judet= Judet::get();
                                
        return json_encode($judet);
    }
   
    public function export() 
        {
             
            ob_end_clean(); 
            ob_start(); 
             $company_id=session("company_id");
            return Excel::download((new JudetExport)->forCompany($company_id),"judet.xls");


        }

    public function import(Request $request) 
        {
          $fileName = "judet_".time().".".$request->file->getClientOriginalExtension();
          $request->file->move(public_path("upload"), $fileName);
          
          Excel::import(new JudetImport, public_path("upload")."/".$fileName);

          
            $judet= Judet::where("company_id",session("company_id"))
                                        ::paginate($request->pageLength,['page'=>$request->page]);
         
            return json_encode($judet);
             
        
           
         }
        
    public function storeExcel() 
        {
            ob_end_clean(); 
            ob_start(); 
            $company_id=session("company_id");
            
            Excel::store((new JudetExport)->forCompany($company_id), "judet.xls","public",null,[
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

        // event(new JudetUpdated());
         return Judet::create([
    	  "cod"=>$request->cod,
    	  "denumire"=>$request->denumire,
    	  "auto"=>$request->auto,           
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Judet  $judet
     * @return \Illuminate\Http\Response
     */
    public function show(Judet $judet)
    {
        $resp= Judet::where("id",$judet->id)
        											->get()->first();
                                
        return json_encode($resp);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Judet  $judet
     * @return \Illuminate\Http\Response
     */
    public function edit(Judet $judet)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Judet  $judet
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Judet $judet)
    {
        $judet->update([
    	  "cod"=>$request->cod,
    	  "denumire"=>$request->denumire,
    	  "auto"=>$request->auto,
        ]);
       // event(new JudetUpdated());
        return $judet;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Judet  $judet
     * @return \Illuminate\Http\Response
     */
    public function destroy(Judet $judet)
    {
        $judet->delete();
      //  event(new JudetUpdated());

    }
}