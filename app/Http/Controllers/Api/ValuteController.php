<?php

namespace App\Http\Controllers\Api;

use App\Models\Valute;
// use App\Events\ValuteUpdated;
use App\Models\Exports\ValuteExport;
//use App\Models\Imports\ValuteImport;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ValuteController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function indexPaginat(Request $request)
    {
          $records= Valute::select('*')->where("company_id",session("company_id"));
          $records=filterRequest($records,$request->searchModel,$request->cautareDupa,$request->sortModel,$request->filterModel);
        $records=  $records->orderBy('id','desc');
        $records=  $records->paginate($request->pageLength,
                                                                    ["page"=>$request->page]);
        
                              //::where("user_id",auth()->user()->id)
                                
        return json_encode($records);
    }
     public function index()
    {
          $valute= Valute::where("company_id",session("company_id"))->get();
          return json_encode($valute);
    }
    public function export() 
        {
             
            ob_end_clean(); 
            ob_start(); 
             $company_id=session("company_id");
            return Excel::download((new ValuteExport)->forCompany($company_id),"valute.xls");


        }

    public function import(Request $request) 
        {
          $fileName = "valute_".time().".".$request->file->getClientOriginalExtension();
          $request->file->move(public_path("upload"), $fileName);
          
          Excel::import(new ValuteImport, public_path("upload")."/".$fileName);

          
            $valute= Valute::where("company_id",session("company_id"))
                                                     ->paginate($request->pageLength,['page'=>$request->page]);
         
            return json_encode($valute);
             
        
           
         }
        
    public function storeExcel() 
        {
            ob_end_clean(); 
            ob_start(); 
            $company_id=session("company_id");
            
            Excel::store((new ValuteExport)->forCompany($company_id), "valute.xls","public",null,[
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

        // event(new ValuteUpdated());
         $curs= Valute::create([
        "company_id"=>session("company_id"),
        
        "data"=>$request->data?dateFormatStocare($request->data):null,
    	  "simbol"=>$request->simbol,
    	  "denumire"=>$request->denumire,
    	  "paritate"=>$request->paritate,
    	  "curs"=>$request->curs,           
        ]);
        return $curs;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Valute  $valute
     * @return \Illuminate\Http\Response
     */
    public function show(Valute $valute)
    {
        $resp= Valute::where("id",$valute->id)
                               ->where("company_id",session("company_id"))
        					   ->get()->first();
                                
        return json_encode($resp);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Valute  $valute
     * @return \Illuminate\Http\Response
     */
    public function edit(Valute $valute)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Valute  $valute
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Valute $valute)
    {
        $valute->update([
        "data"=>$request->data?dateFormatStocare($request->data):null,
    	  "simbol"=>$request->simbol,
    	  "denumire"=>$request->denumire,
    	  "paritate"=>$request->paritate,
    	  "curs"=>$request->curs,
        ]);
       // event(new ValuteUpdated());
        return $valute;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Valute  $valute
     * @return \Illuminate\Http\Response
     */
    public function destroy(Valute $valute)
    {
        $valute->delete();
      //  event(new ValuteUpdated());

    }
}