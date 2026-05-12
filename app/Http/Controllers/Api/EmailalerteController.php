<?php

namespace App\Http\Controllers\Api;

use App\Models\Emailalerte;
// use App\Events\EmailalerteUpdated;
use App\Models\Exports\EmailalerteExport;
use App\Models\Imports\EmailalerteImport;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class EmailalerteController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function indexPaginat(Request $request)
    {
          $records= Emailalerte::select('*')->where("company_id",session("company_id"));
          $records=filterRequest($records,$request->searchModel,$request->cautareDupa,$request->sortModel,$request->filterModel);
          $records= $records->orderBy('id','desc')
                                          ->paginate($request->pageLength,
                                                      ['page'=>$request->page]);
          
                                //::where("user_id",auth()->user()->id)
                                  
          return json_encode($records);
    }
     public function index()
    {
          $emailalerte= Emailalerte::where("company_id",session("company_id"))->get();
          return json_encode($emailalerte);
    }
    public function export() 
        {
             
            ob_end_clean(); 
            ob_start(); 
             $company_id=session("company_id");
            return Excel::download((new EmailalerteExport)->forCompany($company_id),"emailalerte.xls");


        }

    public function import(Request $request) 
        {
          $fileName = "emailalerte_".time().".".$request->file->getClientOriginalExtension();
          $request->file->move(public_path("upload"), $fileName);
          
          Excel::import(new EmailalerteImport, public_path("upload")."/".$fileName);

          
            $emailalerte= Emailalerte::where("company_id",session("company_id"))
                                                     ->paginate($request->pageLength,['page'=>$request->page]);
         
            return json_encode($emailalerte);
             
        
           
         }
        
    public function storeExcel() 
        {
            ob_end_clean(); 
            ob_start(); 
            $company_id=session("company_id");
            
            Excel::store((new EmailalerteExport)->forCompany($company_id), "emailalerte.xls","public",null,[
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
         
    	  "company_id"=>["required"],]);

        // event(new EmailalerteUpdated());
         $cc= Emailalerte::create([
    	  "company_id"=>$request->company_id,
    	  "tip_alerta"=>$request->tip_alerta,
    	  "email"=>$request->email,
    	  "cc"=>$request->cc,           
        ]);
        return $cc->paginate($request->pageLength,['page'=>$request->page]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Emailalerte  $emailalerte
     * @return \Illuminate\Http\Response
     */
    public function show(Emailalerte $emailalerte)
    {
        $resp= Emailalerte::where("id",$emailalerte->id)
                               ->where("company_id",session("company_id"))
        					   ->get()->first();
                                
        return json_encode($resp);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Emailalerte  $emailalerte
     * @return \Illuminate\Http\Response
     */
    public function edit(Emailalerte $emailalerte)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Emailalerte  $emailalerte
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Emailalerte $emailalerte)
    {
        $emailalerte->update([
    	  "company_id"=>$request->company_id,
    	  "tip_alerta"=>$request->tip_alerta,
    	  "email"=>$request->email,
    	  "cc"=>$request->cc,
        ]);
       // event(new EmailalerteUpdated());
        return $emailalerte;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Emailalerte  $emailalerte
     * @return \Illuminate\Http\Response
     */
    public function destroy(Emailalerte $emailalerte)
    {
        $emailalerte->delete();
      //  event(new EmailalerteUpdated());

    }
}