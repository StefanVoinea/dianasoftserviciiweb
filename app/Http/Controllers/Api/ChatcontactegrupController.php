<?php

namespace App\Http\Controllers\Api;

use App\Models\Chatcontactegrup;
// use App\Events\ChatcontactegrupUpdated;
use App\Models\Exports\ChatcontactegrupExport;
use App\Models\Imports\ChatcontactegrupImport;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ChatcontactegrupController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function indexPaginat(Request $request)
    {
          $records= Chatcontactegrup::select('*')->where("company_id",session("company_id"));
        $records=filterRequest($records,$request->searchModel,$request->cautareDupa,$request->sortModel,$request->filterModel);
          $records= $records->orderBy('id','desc')
                                          ->paginate($request->pageLength,
                                                      ['page'=>$request->page]);
          
                                //::where("user_id",auth()->user()->id)
                                  
          return json_encode($records);
    }
     public function index()
    {
          $chatcontactegrup= Chatcontactegrup::where("company_id",session("company_id"))->get();
          return json_encode($chatcontactegrup);
    }
    public function export() 
        {
             
            ob_end_clean(); 
            ob_start(); 
             $company_id=session("company_id");
            return Excel::download((new ChatcontactegrupExport)->forCompany($company_id),"chatcontactegrup.xls");


        }

    public function import(Request $request) 
        {
          $fileName = "chatcontactegrup_".time().".".$request->file->getClientOriginalExtension();
          $request->file->move(public_path("upload"), $fileName);
          
          Excel::import(new ChatcontactegrupImport, public_path("upload")."/".$fileName);

          
            $chatcontactegrup= Chatcontactegrup::where("company_id",session("company_id"))
                                                     ->paginate($request->pageLength,['page'=>$request->page]);
         
            return json_encode($chatcontactegrup);
             
        
           
         }
        
    public function storeExcel() 
        {
            ob_end_clean(); 
            ob_start(); 
            $company_id=session("company_id");
            
            Excel::store((new ChatcontactegrupExport)->forCompany($company_id), "chatcontactegrup.xls","public",null,[
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
         
    	  "grup_id"=>["required"],]);

        // event(new ChatcontactegrupUpdated());
         $chatcontact_id= Chatcontactegrup::create([
    	  "grup_id"=>$request->grup_id,
    	  "user_id"=>$request->user_id,           
        ]);
        return $chatcontact_id;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Chatcontactegrup  $chatcontactegrup
     * @return \Illuminate\Http\Response
     */
    public function show(Chatcontactegrup $chatcontactegrup)
    {
        $resp= Chatcontactegrup::where("id",$chatcontactegrup->id)
                               ->where("company_id",session("company_id"))
        					   ->get()->first();
                                
        return json_encode($resp);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Chatcontactegrup  $chatcontactegrup
     * @return \Illuminate\Http\Response
     */
    public function edit(Chatcontactegrup $chatcontactegrup)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Chatcontactegrup  $chatcontactegrup
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Chatcontactegrup $chatcontactegrup)
    {
        $chatcontactegrup->update([
    	  "grup_id"=>$request->grup_id,
    	  "user_id"=>$request->user_id,
        ]);
       // event(new ChatcontactegrupUpdated());
        return $chatcontactegrup;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Chatcontactegrup  $chatcontactegrup
     * @return \Illuminate\Http\Response
     */
    public function destroy(Chatcontactegrup $chatcontactegrup)
    {
        $chatcontactegrup->delete();
      //  event(new ChatcontactegrupUpdated());

    }
}