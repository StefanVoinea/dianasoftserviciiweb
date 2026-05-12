<?php

namespace App\Http\Controllers\Api;

use App\Models\Datefirmeanaf;
// use App\Events\DatefirmeanafUpdated;
use App\Models\Exports\DatefirmeanafExport;
use App\Models\Imports\DatefirmeanafImport;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class DatefirmeanafController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
  public function indexPaginat(Request $request)
    {
         $records= Datefirmeanaf::select('*');
          
          $records=filterRequest($records,$request->searchModel,$request->cautareDupa,$request->sortModel,$request->filterModel);
          $records= $records->orderBy('id','desc')
                                          ->paginate($request->pageLength,
                                                      ['page'=>$request->page]);
          
                                //::where("user_id",auth()->user()->id)
                                  
          return json_encode($records);
    }
     public function index()
    {
          $datefirmeanaf= Datefirmeanaf::where("company_id",session("company_id"))->get();
          return json_encode($datefirmeanaf);
    }
    public function export() 
        {
             
            ob_end_clean(); 
            ob_start(); 
             $company_id=session("company_id");
            return Excel::download((new DatefirmeanafExport)->forCompany($company_id),"datefirmeanaf.xls");


        }

    public function import(Request $request) 
        {
          $fileName = "datefirmeanaf_".time().".".$request->file->getClientOriginalExtension();
          $request->file->move(public_path("upload"), $fileName);
          
          Excel::import(new DatefirmeanafImport, public_path("upload")."/".$fileName);

          
            $datefirmeanaf= Datefirmeanaf::where("company_id",session("company_id"))
                                                     ->paginate($request->pageLength,['page'=>$request->page]);
         
            return json_encode($datefirmeanaf);
             
        
           
         }
        
    public function storeExcel() 
        {
            ob_end_clean(); 
            ob_start(); 
            $company_id=session("company_id");
            
            Excel::store((new DatefirmeanafExport)->forCompany($company_id), "datefirmeanaf.xls","public",null,[
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
         
    	  "cui"=>["required"],]);

        // event(new DatefirmeanafUpdated());
         $cod_postal= Datefirmeanaf::create([
    	  "cui"=>$request->cui,
    	  "adresa"=>$request->adresa,
    	  "telefon"=>$request->telefon,
    	  "iban"=>$request->iban,
    	  "cod_postal"=>$request->cod_postal,           
        ]);
        return $cod_postal;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Datefirmeanaf  $datefirmeanaf
     * @return \Illuminate\Http\Response
     */
    public function show(Datefirmeanaf $datefirmeanaf)
    {
        $resp= Datefirmeanaf::where("id",$datefirmeanaf->id)
                               ->where("company_id",session("company_id"))
        					   ->get()->first();
                                
        return json_encode($resp);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Datefirmeanaf  $datefirmeanaf
     * @return \Illuminate\Http\Response
     */
    public function edit(Datefirmeanaf $datefirmeanaf)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Datefirmeanaf  $datefirmeanaf
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Datefirmeanaf $datefirmeanaf)
    {
        $datefirmeanaf->update([
    	  "cui"=>$request->cui,
    	  "adresa"=>$request->adresa,
    	  "telefon"=>$request->telefon,
    	  "iban"=>$request->iban,
    	  "cod_postal"=>$request->cod_postal,
        ]);
       // event(new DatefirmeanafUpdated());
        return $datefirmeanaf;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Datefirmeanaf  $datefirmeanaf
     * @return \Illuminate\Http\Response
     */
    public function destroy(Datefirmeanaf $datefirmeanaf)
    {
        $datefirmeanaf->delete();
      //  event(new DatefirmeanafUpdated());

    }
}