<?php

namespace App\Http\Controllers\Api;

use App\Models\Alertetransmise;
// use App\Events\AlertetransmiseUpdated;
use App\Models\Exports\AlertetransmiseExport;
//use App\Models\Imports\AlertetransmiseImport;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class AlertetransmiseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function indexPaginat(Request $request)
    {
          $records= Alertetransmise::select('*')->where("company_id",session("company_id"));
          $records=filterRequest($records,$request->searchModel,$request->cautareDupa,$request->sortModel,$request->filterModel);
        $records=  $records->orderBy('id','desc');
        $records=  $records->paginate($request->pageLength,
                                                                    ["page"=>$request->page]);
        
                              //::where("user_id",auth()->user()->id)
                                
        return json_encode($records);
    }
     public function index()
    {
          $alertetransmise= Alertetransmise::where("company_id",session("company_id"))->get();
          return json_encode($alertetransmise);
    }
    public function export() 
        {
             
            ob_end_clean(); 
            ob_start(); 
             $company_id=session("company_id");
            return Excel::download((new AlertetransmiseExport)->forCompany($company_id),"alertetransmise.xls");


        }

    public function import(Request $request) 
        {
          $fileName = "alertetransmise_".time().".".$request->file->getClientOriginalExtension();
          $request->file->move(public_path("upload"), $fileName);
          
          Excel::import(new AlertetransmiseImport, public_path("upload")."/".$fileName);

          
            $alertetransmise= Alertetransmise::where("company_id",session("company_id"))
                                                     ->paginate($request->pageLength,['page'=>$request->page]);
         
            return json_encode($alertetransmise);
             
        
           
         }
        
    public function storeExcel() 
        {
            ob_end_clean(); 
            ob_start(); 
            $company_id=session("company_id");
            
            Excel::store((new AlertetransmiseExport)->forCompany($company_id), "alertetransmise.xls","public",null,[
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

        // event(new AlertetransmiseUpdated());
         $ora= Alertetransmise::create([
        "company_id"=>session("company_id"),
        
    	  "alerta"=>$request->alerta,
        "data"=>$request->data?dateFormatStocare($request->data):null,
    	  "ora"=>$request->ora,           
        ]);
        return $ora;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Alertetransmise  $alertetransmise
     * @return \Illuminate\Http\Response
     */
    public function show(Alertetransmise $alertetransmise)
    {
        $resp= Alertetransmise::where("id",$alertetransmise->id)
                               ->where("company_id",session("company_id"))
        					   ->get()->first();
                                
        return json_encode($resp);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Alertetransmise  $alertetransmise
     * @return \Illuminate\Http\Response
     */
    public function edit(Alertetransmise $alertetransmise)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Alertetransmise  $alertetransmise
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Alertetransmise $alertetransmise)
    {
        $alertetransmise->update([
    	  "alerta"=>$request->alerta,
        "data"=>$request->data?dateFormatStocare($request->data):null,
    	  "ora"=>$request->ora,
        ]);
       // event(new AlertetransmiseUpdated());
        return $alertetransmise;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Alertetransmise  $alertetransmise
     * @return \Illuminate\Http\Response
     */
    public function destroy(Alertetransmise $alertetransmise)
    {
        $alertetransmise->delete();
      //  event(new AlertetransmiseUpdated());

    }
}