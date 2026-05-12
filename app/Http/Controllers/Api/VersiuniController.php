<?php

namespace App\Http\Controllers\Api;

use App\Models\Versiuni;
// use App\Events\VersiuniUpdated;
use App\Models\Exports\VersiuniExport;
//use App\Models\Imports\VersiuniImport;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class VersiuniController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function indexPaginat(Request $request)
    {
          $records= Versiuni::select('*')->where("company_id",session("company_id"));
          $records=filterRequest($records,$request->searchModel,$request->cautareDupa,$request->sortModel,$request->filterModel);
        $records=  $records->orderBy('id','desc');
        $records=  $records->paginate($request->pageLength,
                                                                    ["page"=>$request->page]);
        
                              //::where("user_id",auth()->user()->id)
                                
        return json_encode($records);
    }
     public function index()
    {
          $versiuni= Versiuni::where("company_id",session("company_id"))->get();
          return json_encode($versiuni);
    }
    public function export() 
        {
             
            ob_end_clean(); 
            ob_start(); 
             $company_id=session("company_id");
            return Excel::download((new VersiuniExport)->forCompany($company_id),"versiuni.xls");


        }

    public function import(Request $request) 
        {
          $fileName = "versiuni_".time().".".$request->file->getClientOriginalExtension();
          $request->file->move(public_path("upload"), $fileName);
          
          Excel::import(new VersiuniImport, public_path("upload")."/".$fileName);

          
            $versiuni= Versiuni::where("company_id",session("company_id"))
                                                     ->paginate($request->pageLength,['page'=>$request->page]);
         
            return json_encode($versiuni);
             
        
           
         }
        
    public function storeExcel() 
        {
            ob_end_clean(); 
            ob_start(); 
            $company_id=session("company_id");
            
            Excel::store((new VersiuniExport)->forCompany($company_id), "versiuni.xls","public",null,[
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

        // event(new VersiuniUpdated());
         $data= Versiuni::create([
        "company_id"=>session("company_id"),
        
    	  "versiunea"=>$request->versiunea,
    	  "agentia"=>$request->agentia,
        "data"=>$request->data?dateFormatStocare($request->data):null,           
        ]);
        return $data;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Versiuni  $versiuni
     * @return \Illuminate\Http\Response
     */
    public function show(Versiuni $versiuni)
    {
        $resp= Versiuni::where("id",$versiuni->id)
                               ->where("company_id",session("company_id"))
        					   ->get()->first();
                                
        return json_encode($resp);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Versiuni  $versiuni
     * @return \Illuminate\Http\Response
     */
    public function edit(Versiuni $versiuni)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Versiuni  $versiuni
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Versiuni $versiuni)
    {
        $versiuni->update([
    	  "versiunea"=>$request->versiunea,
    	  "agentia"=>$request->agentia,
        "data"=>$request->data?dateFormatStocare($request->data):null,
        ]);
       // event(new VersiuniUpdated());
        return $versiuni;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Versiuni  $versiuni
     * @return \Illuminate\Http\Response
     */
    public function destroy(Versiuni $versiuni)
    {
        $versiuni->delete();
      //  event(new VersiuniUpdated());

    }
}