<?php

namespace App\Http\Controllers\Api;

use App\Models\Portcomsms;
// use App\Events\PortcomsmsUpdated;
use App\Models\Exports\PortcomsmsExport;
//use App\Models\Imports\PortcomsmsImport;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class PortcomsmsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function indexPaginat(Request $request)
    {
          $records= Portcomsms::select('*')->where("company_id",session("company_id"));
          $records=filterRequest($records,$request->searchModel,$request->cautareDupa,$request->sortModel,$request->filterModel);
        $records=  $records->orderBy('id','desc');
        $records=  $records->paginate($request->pageLength,
                                                                    ["page"=>$request->page]);
        
                              //::where("user_id",auth()->user()->id)
                                
        return json_encode($records);
    }
     public function index()
    {
          $portcomsms= Portcomsms::where("company_id",session("company_id"))->get();
          return json_encode($portcomsms);
    }
    public function export() 
        {
             
            ob_end_clean(); 
            ob_start(); 
             $company_id=session("company_id");
            return Excel::download((new PortcomsmsExport)->forCompany($company_id),"portcomsms.xls");


        }

    public function import(Request $request) 
        {
          $fileName = "portcomsms_".time().".".$request->file->getClientOriginalExtension();
          $request->file->move(public_path("upload"), $fileName);
          
          Excel::import(new PortcomsmsImport, public_path("upload")."/".$fileName);

          
            $portcomsms= Portcomsms::where("company_id",session("company_id"))
                                                     ->paginate($request->pageLength,['page'=>$request->page]);
         
            return json_encode($portcomsms);
             
        
           
         }
        
    public function storeExcel() 
        {
            ob_end_clean(); 
            ob_start(); 
            $company_id=session("company_id");
            
            Excel::store((new PortcomsmsExport)->forCompany($company_id), "portcomsms.xls","public",null,[
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

        // event(new PortcomsmsUpdated());
         $portcom= Portcomsms::create([
        "company_id"=>session("company_id"),
        
    	  "portcom"=>$request->portcom,           
        ]);
        return $portcom;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Portcomsms  $portcomsms
     * @return \Illuminate\Http\Response
     */
    public function show(Portcomsms $portcomsms)
    {
        $resp= Portcomsms::where("id",$portcomsms->id)
                               ->where("company_id",session("company_id"))
        					   ->get()->first();
                                
        return json_encode($resp);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Portcomsms  $portcomsms
     * @return \Illuminate\Http\Response
     */
    public function edit(Portcomsms $portcomsms)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Portcomsms  $portcomsms
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Portcomsms $portcomsms)
    {
        $portcomsms->update([
    	  "portcom"=>$request->portcom,
        ]);
       // event(new PortcomsmsUpdated());
        return $portcomsms;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Portcomsms  $portcomsms
     * @return \Illuminate\Http\Response
     */
    public function destroy(Portcomsms $portcomsms)
    {
        $portcomsms->delete();
      //  event(new PortcomsmsUpdated());

    }
}