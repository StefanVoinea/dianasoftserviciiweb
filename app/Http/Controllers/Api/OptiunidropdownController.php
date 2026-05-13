<?php

namespace App\Http\Controllers\Api;

use App\Models\Optiunidropdown;
// use App\Events\OptiunidropdownUpdated;
use App\Models\Exports\OptiunidropdownExport;
use App\Models\Imports\OptiunidropdownImport;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class OptiunidropdownController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
   public function indexPaginat(Request $request)
    {
         $records= Optiunidropdown::select('*');
          
          $records=filterRequest($records,$request->searchModel,$request->cautareDupa,$request->sortModel,$request->filterModel);
          $records= $records->orderBy('id','desc')
                                          ->paginate($request->pageLength,
                                                      ['page'=>$request->page]);
          
                                //::where("user_id",auth()->user()->id)
                                  
          return json_encode($records);
    }
     public function index()
    {
          $optiunidropdown= Optiunidropdown::where("company_id",session("company_id"))->get();
          return json_encode($optiunidropdown);
    }
    public function export() 
        {
             
            ob_end_clean(); 
            ob_start(); 
             $company_id=session("company_id");
            return Excel::download((new OptiunidropdownExport)->forCompany($company_id),"optiunidropdown.xls");


        }

    public function import(Request $request) 
        {
          $fileName = "optiunidropdown_".time().".".$request->file->getClientOriginalExtension();
          $request->file->move(public_path("upload"), $fileName);
          
          Excel::import(new OptiunidropdownImport, public_path("upload")."/".$fileName);

          
            $optiunidropdown= Optiunidropdown::where("company_id",session("company_id"))
                                                     ->paginate($request->pageLength,['page'=>$request->page]);
         
            return json_encode($optiunidropdown);
             
        
           
         }
        
    public function storeExcel() 
        {
            ob_end_clean(); 
            ob_start(); 
            $company_id=session("company_id");
            
            Excel::store((new OptiunidropdownExport)->forCompany($company_id), "optiunidropdown.xls","public",null,[
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
        
        // event(new OptiunidropdownUpdated());
         $field_option= Optiunidropdown::create([
    	  "company_id"=>session("company_id"),
    	  "field_name"=>$request->field_name,
    	  "field_option"=>$request->field_option,           
        ]);
        return $field_option;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Optiunidropdown  $optiunidropdown
     * @return \Illuminate\Http\Response
     */
    public function show(Optiunidropdown $optiunidropdown)
    {
        $resp= Optiunidropdown::where("id",$optiunidropdown->id)
                               ->where("company_id",session("company_id"))
        					   ->get()->first();
                                
        return json_encode($resp);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Optiunidropdown  $optiunidropdown
     * @return \Illuminate\Http\Response
     */
    public function edit(Optiunidropdown $optiunidropdown)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Optiunidropdown  $optiunidropdown
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Optiunidropdown $optiunidropdown)
    {
        $optiunidropdown->update([
    	  "field_name"=>$request->field_name,
    	  "field_option"=>$request->field_option,
        ]);
       // event(new OptiunidropdownUpdated());
        return $optiunidropdown;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Optiunidropdown  $optiunidropdown
     * @return \Illuminate\Http\Response
     */
    public function destroy(Optiunidropdown $optiunidropdown)
    {
        $optiunidropdown->delete();
      //  event(new OptiunidropdownUpdated());

    }
}