<?php

namespace App\Http\Controllers\Api;

use App\Models\Ipautorizat;
// use App\Events\IpautorizatUpdated;
use App\Models\Exports\IpautorizatExport;
//use App\Models\Imports\IpautorizatImport;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Mail;
use App\Mail\AlertaEroareEmail;
use App\Models\User;
use Illuminate\Support\Facades\DB;
    

class IpautorizatController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function indexPaginat(Request $request)
    {
        try{
          $records= Ipautorizat::select('*')->where("company_id",session("company_id"));
          $records=filterRequest($records,$request->searchModel,$request->cautareDupa,$request->sortModel,$request->filterModel);
        $records=  $records->orderBy('id','desc');
        $records=  $records->paginate($request->pageLength,
                                                                    ["page"=>$request->page]);
        
                              //::where("user_id",auth()->user()->id)
                                
        return json_encode($records);
    } catch (\Exception $e) {
            Mail::to("stefan.voinea@gmail.com")
            ->send(new AlertaEroareEmail("INDEX PAGINAT IP AUTORIZAT",$e->getMessage(),$e,Auth::user()));
            return response()->json(['message' => $e->getMessage()], 500);
        }  
    }
     public function index()
    {
          $ipautorizat= Ipautorizat::where("company_id",session("company_id"))->get();
          return json_encode($ipautorizat);
    }
    public function export() 
        {
             
            ob_end_clean(); 
            ob_start(); 
             $company_id=session("company_id");
            return Excel::download((new IpautorizatExport)->forCompany($company_id),"ipautorizat.xls");


        }

    public function import(Request $request) 
        {
            try{
          $fileName = "ipautorizat_".time().".".$request->file->getClientOriginalExtension();
          $request->file->move(public_path("upload"), $fileName);
          
          Excel::import(new IpautorizatImport, public_path("upload")."/".$fileName);

          
            $ipautorizat= Ipautorizat::where("company_id",session("company_id"))
                                                     ->paginate($request->pageLength,['page'=>$request->page]);
         
            return json_encode($ipautorizat);
             
         } catch (\Exception $e) {
            Mail::to("stefan.voinea@gmail.com")
            ->send(new AlertaEroareEmail("INDEX IP AUTORIZAT",$e->getMessage(),$e,Auth::user()));
            return response()->json(['message' => $e->getMessage()], 500);
        }  
           
         }
        
    public function storeExcel() 
        {
            ob_end_clean(); 
            ob_start(); 
            $company_id=session("company_id");
            
            Excel::store((new IpautorizatExport)->forCompany($company_id), "ipautorizat.xls","public",null,[
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

        // event(new IpautorizatUpdated());
        DB::beginTransaction();
        try{
                 $utilizator= Ipautorizat::create([
        
        "company_id"=>session("company_id"),
        
    	  "ip"=>$request->ip,
    	  "utilizator"=>$request->utilizator,           
        ]);
        DB::commit();
        return $utilizator;
        
        } catch (\Exception $e) {
            DB::rollback();
            Mail::to("stefan.voinea@gmail.com")
            ->send(new AlertaEroareEmail("STORE IP AUTORIZAT",$e->getMessage(),$e,Auth::user()));
            return response()->json(['message' => $e->getMessage()], 500);
        }  
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Ipautorizat  $ipautorizat
     * @return \Illuminate\Http\Response
     */
    public function show(Ipautorizat $ipautorizat)
    {
        try{
        $resp= Ipautorizat::where("id",$ipautorizat->id)
                               ->where("company_id",session("company_id"))
        					   ->get()->first();
                                
        return json_encode($resp);

        } catch (\Exception $e) {
            Mail::to("stefan.voinea@gmail.com")
            ->send(new AlertaEroareEmail("SHOW IP AUTORIZAT",$e->getMessage(),$e,Auth::user()));
            return response()->json(['message' => $e->getMessage()], 500);
        }  
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Ipautorizat  $ipautorizat
     * @return \Illuminate\Http\Response
     */
    public function edit(Ipautorizat $ipautorizat)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Ipautorizat  $ipautorizat
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Ipautorizat $ipautorizat)
    {
        DB::beginTransaction();
        try{
        $ipautorizat->update([
    	  "ip"=>$request->ip,
    	  "utilizator"=>$request->utilizator,
        ]);
       // event(new IpautorizatUpdated());
        DB::commit();
        return $ipautorizat;
           
        } catch (\Exception $e) {
            DB::rollback();
            Mail::to("stefan.voinea@gmail.com")
            ->send(new AlertaEroareEmail("UPDATE IP AUTORIZAT",$e->getMessage(),$e,Auth::user()));
            return response()->json(['message' => $e->getMessage()], 500);
        }  
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Ipautorizat  $ipautorizat
     * @return \Illuminate\Http\Response
     */
    public function destroy(Ipautorizat $ipautorizat)
    {
        try{
        $ipautorizat->delete();
      //  event(new IpautorizatUpdated());
        DB::commit();
           
        } catch (\Exception $e) {
            Mail::to("stefan.voinea@gmail.com")
            ->send(new AlertaEroareEmail("STERGE IP AUTORIZAT",$e->getMessage(),$e,Auth::user()));
            return response()->json(['message' => $e->getMessage()], 500);
        }  
    }
}