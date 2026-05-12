<?php

namespace App\Http\Controllers\Api;

use App\Models\Litigiicaleatac;
// use App\Events\LitigiicaleatacUpdated;
use App\Models\Exports\LitigiicaleatacExport;
//use App\Models\Imports\LitigiicaleatacImport;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class LitigiicaleatacController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function indexPaginat(Request $request)
    {
          $records= Litigiicaleatac::select('*')->where("company_id",session("company_id"));
          $records=filterRequest($records,$request->searchModel,$request->cautareDupa,$request->sortModel,$request->filterModel);
        $records=  $records->orderBy('id','desc');
        $records=  $records->paginate($request->pageLength,
                                                                    ["page"=>$request->page]);
        
                              //::where("user_id",auth()->user()->id)
                                
        return json_encode($records);
    }
     public function index()
    {
          $litigiicaleatac= Litigiicaleatac::where("company_id",session("company_id"))->get();
          return json_encode($litigiicaleatac);
    }
    public function export() 
        {
             
            ob_end_clean(); 
            ob_start(); 
             $company_id=session("company_id");
            return Excel::download((new LitigiicaleatacExport)->forCompany($company_id),"litigiicaleatac.xls");


        }

    public function import(Request $request) 
        {
          $fileName = "litigiicaleatac_".time().".".$request->file->getClientOriginalExtension();
          $request->file->move(public_path("upload"), $fileName);
          
          Excel::import(new LitigiicaleatacImport, public_path("upload")."/".$fileName);

          
            $litigiicaleatac= Litigiicaleatac::where("company_id",session("company_id"))
                                                     ->paginate($request->pageLength,['page'=>$request->page]);
         
            return json_encode($litigiicaleatac);
             
        
           
         }
        
    public function storeExcel() 
        {
            ob_end_clean(); 
            ob_start(); 
            $company_id=session("company_id");
            
            Excel::store((new LitigiicaleatacExport)->forCompany($company_id), "litigiicaleatac.xls","public",null,[
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

        // event(new LitigiicaleatacUpdated());
         $tip_cale_atac= Litigiicaleatac::create([
        "company_id"=>session("company_id"),
        
    	  "litigiu_id"=>$request->litigiu_id,
        "data_declarare"=>$request->data_declarare?dateFormatStocare($request->data_declarare):null,
    	  "parte_declaratoare"=>$request->parte_declaratoare,
    	  "tip_cale_atac"=>$request->tip_cale_atac,           
        ]);
        return $tip_cale_atac;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Litigiicaleatac  $litigiicaleatac
     * @return \Illuminate\Http\Response
     */
    public function show(Litigiicaleatac $litigiicaleatac)
    {
        $resp= Litigiicaleatac::where("id",$litigiicaleatac->id)
                               ->where("company_id",session("company_id"))
        					   ->get()->first();
                                
        return json_encode($resp);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Litigiicaleatac  $litigiicaleatac
     * @return \Illuminate\Http\Response
     */
    public function edit(Litigiicaleatac $litigiicaleatac)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Litigiicaleatac  $litigiicaleatac
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Litigiicaleatac $litigiicaleatac)
    {
        $litigiicaleatac->update([
    	  "litigiu_id"=>$request->litigiu_id,
        "data_declarare"=>$request->data_declarare?dateFormatStocare($request->data_declarare):null,
    	  "parte_declaratoare"=>$request->parte_declaratoare,
    	  "tip_cale_atac"=>$request->tip_cale_atac,
        ]);
       // event(new LitigiicaleatacUpdated());
        return $litigiicaleatac;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Litigiicaleatac  $litigiicaleatac
     * @return \Illuminate\Http\Response
     */
    public function destroy(Litigiicaleatac $litigiicaleatac)
    {
        $litigiicaleatac->delete();
      //  event(new LitigiicaleatacUpdated());

    }
}