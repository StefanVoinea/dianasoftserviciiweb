<?php

namespace App\Http\Controllers\Api;

use App\Models\Datefinanciarepj;
// use App\Events\DatefinanciarepjUpdated;
use App\Models\Exports\DatefinanciarepjExport;
//use App\Models\Imports\DatefinanciarepjImport;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class DatefinanciarepjController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function indexPaginat(Request $request)
    {
          $records= Datefinanciarepj::select('*')->where("company_id",session("company_id"));
          $records=filterRequest($records,$request->searchModel,$request->cautareDupa,$request->sortModel,$request->filterModel);
        $records=  $records->orderBy('id','desc');
        $records=  $records->paginate($request->pageLength,
                                                                    ["page"=>$request->page]);
        
                              //::where("user_id",auth()->user()->id)
                                
        return json_encode($records);
    }
     public function index()
    {
          $datefinanciarepj= Datefinanciarepj::where("company_id",session("company_id"))->get();
          return json_encode($datefinanciarepj);
    }
    public function export() 
        {
             
            ob_end_clean(); 
            ob_start(); 
             $company_id=session("company_id");
            return Excel::download((new DatefinanciarepjExport)->forCompany($company_id),"datefinanciarepj.xls");


        }

    public function import(Request $request) 
        {
          $fileName = "datefinanciarepj_".time().".".$request->file->getClientOriginalExtension();
          $request->file->move(public_path("upload"), $fileName);
          
          Excel::import(new DatefinanciarepjImport, public_path("upload")."/".$fileName);

          
            $datefinanciarepj= Datefinanciarepj::where("company_id",session("company_id"))
                                                     ->paginate($request->pageLength,['page'=>$request->page]);
         
            return json_encode($datefinanciarepj);
             
        
           
         }
        
    public function storeExcel() 
        {
            ob_end_clean(); 
            ob_start(); 
            $company_id=session("company_id");
            
            Excel::store((new DatefinanciarepjExport)->forCompany($company_id), "datefinanciarepj.xls","public",null,[
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

        // event(new DatefinanciarepjUpdated());
         $val_den_indicator= Datefinanciarepj::create([
        "company_id"=>session("company_id"),
        
    	  "cui"=>$request->cui,
    	  "an"=>$request->an,
    	  "indicator"=>$request->indicator,
    	  "val_indicator"=>$request->val_indicator,
    	  "val_den_indicator"=>$request->val_den_indicator,           
        ]);
        return $val_den_indicator;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Datefinanciarepj  $datefinanciarepj
     * @return \Illuminate\Http\Response
     */
    public function show(Datefinanciarepj $datefinanciarepj)
    {
        $resp= Datefinanciarepj::where("id",$datefinanciarepj->id)
                               ->where("company_id",session("company_id"))
        					   ->get()->first();
                                
        return json_encode($resp);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Datefinanciarepj  $datefinanciarepj
     * @return \Illuminate\Http\Response
     */
    public function edit(Datefinanciarepj $datefinanciarepj)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Datefinanciarepj  $datefinanciarepj
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Datefinanciarepj $datefinanciarepj)
    {
        $datefinanciarepj->update([
    	  "cui"=>$request->cui,
    	  "an"=>$request->an,
    	  "indicator"=>$request->indicator,
    	  "val_indicator"=>$request->val_indicator,
    	  "val_den_indicator"=>$request->val_den_indicator,
        ]);
       // event(new DatefinanciarepjUpdated());
        return $datefinanciarepj;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Datefinanciarepj  $datefinanciarepj
     * @return \Illuminate\Http\Response
     */
    public function destroy(Datefinanciarepj $datefinanciarepj)
    {
        $datefinanciarepj->delete();
      //  event(new DatefinanciarepjUpdated());

    }
}