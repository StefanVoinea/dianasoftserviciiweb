<?php

namespace App\Http\Controllers\Api;

use App\Models\Chatgrup;
// use App\Events\ChatgrupUpdated;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ChatgrupController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function indexPaginat(Request $request)
    {
          $records= Chatgrup::select('*')->where("company_id",session("company_id"));
        $records=filterRequest($records,$request->searchModel,$request->cautareDupa,$request->sortModel,$request->filterModel);
          $records= $records->orderBy('id','desc')
                                          ->paginate($request->pageLength,
                                                      ['page'=>$request->page]);
          
                                //::where("user_id",auth()->user()->id)
                                  
          return json_encode($records);
    }
     public function index()
    {
          $chatgrup= Chatgrup::where("company_id",session("company_id"))->get();
          return json_encode($chatgrup);
    }
    public function export() 
        {
             
            ob_end_clean(); 
            ob_start(); 
             $company_id=session("company_id");
            return Excel::download((new ChatgrupExport)->forCompany($company_id),"chatgrup.xls");


        }

    public function import(Request $request) 
        {
          $fileName = "chatgrup_".time().".".$request->file->getClientOriginalExtension();
          $request->file->move(public_path("upload"), $fileName);
          
          Excel::import(new ChatgrupImport, public_path("upload")."/".$fileName);

          
            $chatgrup= Chatgrup::where("company_id",session("company_id"))
                                                     ->paginate($request->pageLength,['page'=>$request->page]);
         
            return json_encode($chatgrup);
             
        
           
         }
        
    public function storeExcel() 
        {
            ob_end_clean(); 
            ob_start(); 
            $company_id=session("company_id");
            
            Excel::store((new ChatgrupExport)->forCompany($company_id), "chatgrup.xls","public",null,[
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

        // event(new ChatgrupUpdated());
         $tip_contact= Chatgrup::create([
    	  "company_id"=>$session("company_id"),
    	  "nume"=>$request->nume,
    	      
        ]);
        return $tip_contact;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Chatgrup  $chatgrup
     * @return \Illuminate\Http\Response
     */
    public function show(Chatgrup $chatgrup)
    {
        $resp= Chatgrup::where("id",$chatgrup->id)
                               ->where("company_id",session("company_id"))
        					   ->get()->first();
                                
        return json_encode($resp);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Chatgrup  $chatgrup
     * @return \Illuminate\Http\Response
     */
    public function edit(Chatgrup $chatgrup)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Chatgrup  $chatgrup
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Chatgrup $chatgrup)
    {
        $chatgrup->update([
    	
    	  "nume"=>$request->nume,
    	 
        ]);
       // event(new ChatgrupUpdated());
        return $chatgrup;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Chatgrup  $chatgrup
     * @return \Illuminate\Http\Response
     */
    public function destroy(Chatgrup $chatgrup)
    {
        $chatgrup->delete();
      //  event(new ChatgrupUpdated());

    }
}