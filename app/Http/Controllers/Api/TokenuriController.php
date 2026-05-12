<?php

namespace App\Http\Controllers\Api;

use App\Models\Tokenuri;
// use App\Events\TokenuriUpdated;
use App\Models\Exports\TokenuriExport;
//use App\Models\Imports\TokenuriImport;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class TokenuriController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function indexPaginat(Request $request)
    {
          $records= Tokenuri::select('*')->where("company_id",session("company_id"));
          $records=filterRequest($records,$request->searchModel,$request->cautareDupa,$request->sortModel,$request->filterModel);
        $records=  $records->orderBy('id','desc');
        $records=  $records->paginate($request->pageLength,
                                                                    ["page"=>$request->page]);
        
                              //::where("user_id",auth()->user()->id)
                                
        return json_encode($records);
    }
     public function index()
    {
          $tokenuri= Tokenuri::where("company_id",session("company_id"))->get();
          return json_encode($tokenuri);
    }
    public function export() 
        {
             
            ob_end_clean(); 
            ob_start(); 
             $company_id=session("company_id");
            return Excel::download((new TokenuriExport)->forCompany($company_id),"tokenuri.xls");


        }

    public function import(Request $request) 
        {
          $fileName = "tokenuri_".time().".".$request->file->getClientOriginalExtension();
          $request->file->move(public_path("upload"), $fileName);
          
          Excel::import(new TokenuriImport, public_path("upload")."/".$fileName);

          
            $tokenuri= Tokenuri::where("company_id",session("company_id"))
                                                     ->paginate($request->pageLength,['page'=>$request->page]);
         
            return json_encode($tokenuri);
             
        
           
         }
        
    public function storeExcel() 
        {
            ob_end_clean(); 
            ob_start(); 
            $company_id=session("company_id");
            
            Excel::store((new TokenuriExport)->forCompany($company_id), "tokenuri.xls","public",null,[
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

        // event(new TokenuriUpdated());
         $data_obtinere= Tokenuri::create([
        "company_id"=>session("company_id"),
        
    	  "access_token"=>$request->access_token,
    	  "refresh_token"=>$request->refresh_token,
        "data_expirarii"=>$request->data_expirarii?dateFormatStocare($request->data_expirarii):null,
        "data_obtinere"=>$request->data_obtinere?dateFormatStocare($request->data_obtinere):null,           
        ]);
        return $data_obtinere;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Tokenuri  $tokenuri
     * @return \Illuminate\Http\Response
     */
    public function show(Tokenuri $tokenuri)
    {
        $resp= Tokenuri::where("id",$tokenuri->id)
                               ->where("company_id",session("company_id"))
        					   ->get()->first();
                                
        return json_encode($resp);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Tokenuri  $tokenuri
     * @return \Illuminate\Http\Response
     */
    public function edit(Tokenuri $tokenuri)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Tokenuri  $tokenuri
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Tokenuri $tokenuri)
    {
        $tokenuri->update([
    	  "access_token"=>$request->access_token,
    	  "refresh_token"=>$request->refresh_token,
        "data_expirarii"=>$request->data_expirarii?dateFormatStocare($request->data_expirarii):null,
        "data_obtinere"=>$request->data_obtinere?dateFormatStocare($request->data_obtinere):null,
        ]);
       // event(new TokenuriUpdated());
        return $tokenuri;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Tokenuri  $tokenuri
     * @return \Illuminate\Http\Response
     */
    public function destroy(Tokenuri $tokenuri)
    {
        $tokenuri->delete();
      //  event(new TokenuriUpdated());

    }
}