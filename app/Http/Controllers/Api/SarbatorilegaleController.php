<?php

namespace App\Http\Controllers\Api;

use App\Models\Sarbatorilegale;
// use App\Events\SarbatorilegaleUpdated;
use App\Models\Exports\SarbatorilegaleExport;
use App\Models\Imports\SarbatorilegaleImport;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class SarbatorilegaleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function indexPaginat(Request $request)
    {
         $records= Sarbatorilegale::select('*');
          
          $records=filterRequest($records,$request->searchModel,$request->cautareDupa,$request->sortModel,$request->filterModel);
          $records= $records->orderBy('id','desc')
                                          ->paginate($request->pageLength,
                                                      ['page'=>$request->page]);
          
                                //::where("user_id",auth()->user()->id)
                                  
          return json_encode($records);
    }
     public function index()
    {
          $sarbatorilegale= Sarbatorilegale::get();
          return json_encode($sarbatorilegale);
    }
    public function export() 
        {
             
            ob_end_clean(); 
            ob_start(); 
             $company_id=session("company_id");
           // return Excel::download((new SarbatorilegaleExport)->forCompany($company_id),"Sarbatorilegale.xls");


        }

    public function import(Request $request) 
        {
          $fileName = "Sarbatorilegale_".time().".".$request->file->getClientOriginalExtension();
          $request->file->move(public_path("upload"), $fileName);
          
          Excel::import(new SarbatorilegaleImport, public_path("upload")."/".$fileName);

          
            $sarbatorilegale= Sarbatorilegale::where("company_id",session("company_id"))
                                                     ->paginate($request->pageLength,['page'=>$request->page]);
         
            return json_encode($sarbatorilegale);
             
        
           
         }
        
    public function storeExcel() 
        {
            ob_end_clean(); 
            ob_start(); 
            $company_id=session("company_id");
            
            Excel::store((new SarbatorilegaleExport)->forCompany($company_id), "Sarbatorilegale.xls","public",null,[
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
         
    	  // "data"=>["required"],]);

        // event(new SarbatorilegaleUpdated());
         $data= Sarbatorilegale::create([
    	  "data"=>$request->data?dateFormatStocare($request->data):null,           
        ]);
        return $data;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Sarbatorilegale  $sarbatorilegale
     * @return \Illuminate\Http\Response
     */
    public function show(Sarbatorilegale $sarbatorilegale)
    {
        $resp= Sarbatorilegale::where("id",$sarbatorilegale->id)
                            //   ->where("company_id",session("company_id"))
        					   ->get()->first();
                                
        return json_encode($resp);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Sarbatorilegale  $sarbatorilegale
     * @return \Illuminate\Http\Response
     */
    public function edit(Sarbatorilegale $sarbatorilegale)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Sarbatorilegale  $sarbatorilegale
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Sarbatorilegale $sarbatorilegale)
    {
        $sarbatorilegale->update([
    	  "data"=>$request->data?dateFormatStocare($request->data):null,
        ]);
       // event(new SarbatorilegaleUpdated());
        return $sarbatorilegale;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Sarbatorilegale  $sarbatorilegale
     * @return \Illuminate\Http\Response
     */
    public function destroy(Sarbatorilegale $sarbatorilegale)
    {
        $sarbatorilegale->delete();
      //  event(new SarbatorilegaleUpdated());

    }
}