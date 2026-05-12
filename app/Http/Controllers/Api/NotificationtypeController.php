<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Exports\NotificationtypeExport;
use App\Models\Notificationtype;
use App\Models\Notificationuser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Mail;
use App\Mail\AlertaEroareEmail;
use App\Models\User;
use Illuminate\Support\Facades\DB;


class NotificationtypeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function indexPaginat(Request $request)
    {
        try{
        $records= Notificationtype::select('*')->where("company_id",session("company_id"))->with(["notificationuser"]);
        $records=filterRequest($records,$request->searchModel,$request->cautareDupa,$request->sortModel,$request->filterModel);
        $records=  $records->orderBy('id','desc');

        $records=  $records->paginate($request->pageLength,
                                               ["page"=>$request->page]);
        
                              //::where("user_id",auth()->user()->id)
                                
        return json_encode($records);

        } catch (\Exception $e) {
            Mail::to("stefan.voinea@gmail.com")
            ->send(new AlertaEroareEmail("INDEX PAGINAT NOTIFICATION TYPE",$e->getMessage(),$e,Auth::user()));
            return response()->json(['message' => $e->getMessage()], 500);
        }  
    }

     public function index()
    {
        try{
          $notificationtype= Notificationtype::where("company_id",session("company_id"))->get();
          return json_encode($notificationtype);
        } catch (\Exception $e) {
            Mail::to("stefan.voinea@gmail.com")
            ->send(new AlertaEroareEmail("INDEX NOTIFICATION TYPE",$e->getMessage(),$e,Auth::user()));
            return response()->json(['message' => $e->getMessage()], 500);
        }    
    }
    public function export() 
        {
             
            ob_end_clean(); 
            ob_start(); 
             $company_id=session("company_id");
            return Excel::download((new NotificationtypeExport)->forCompany($company_id),"notificationtype.xls");


        }

    public function import(Request $request) 
        {
          $fileName = "notificationtype_".time().".".$request->file->getClientOriginalExtension();
          $request->file->move(public_path("upload"), $fileName);
          
          Excel::import(new NotificationtypeImport, public_path("upload")."/".$fileName);

          
            $notificationtype= Notificationtype::where("company_id",session("company_id"))
                                                     ->paginate($request->pageLength,['page'=>$request->page]);
         
            return json_encode($notificationtype);
             
        
           
         }
        
    public function storeExcel() 
        {
            ob_end_clean(); 
            ob_start(); 
            $company_id=session("company_id");
            
            Excel::store((new NotificationtypeExport)->forCompany($company_id), "notificationtype.xls","public",null,[
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
       
        // event(new NotificationtypeUpdated());
        DB::beginTransaction();
        try{
         $denumire= Notificationtype::create([
                                               "company_id"=>session("company_id"),
                                               "categoria"=>$request->categoria,
                                        	   "denumire"=>$request->denumire,           
                                            ]);
         foreach ($request->notificationuser as $notificationuser){
            
            if(valoareCamp("user",$notificationuser)&&valoareCamp("channel",$notificationuser)){
             Notificationuser::create([
                                        "company_id"=>session("company_id"),
                                        "notificationtype_id"=>$denumire->id,
                                        "user_id"=>$notificationuser["user"]["id"],
                                        "channel"=>$notificationuser["channel"],
                                    ]);
            }
         }
           DB::commit();
        return $denumire;
           
        } catch (\Exception $e) {
            DB::rollback();
            Mail::to("stefan.voinea@gmail.com")
            ->send(new AlertaEroareEmail("STORE NOTIFICATION TYPE",$e->getMessage(),$e,Auth::user()));
            return response()->json(['message' => $e->getMessage()], 500);
        }  
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Notificationtype  $notificationtype
     * @return \Illuminate\Http\Response
     */
    public function show(Notificationtype $notificationtype)
    {
        try{
        $resp= Notificationtype::where("id",$notificationtype->id)
                               ->where("company_id",session("company_id"))
        					   ->get()->first();
                                
        return json_encode($resp);

        } catch (\Exception $e) {
            Mail::to("stefan.voinea@gmail.com")
            ->send(new AlertaEroareEmail("SHOW NOTIFICATION TYPE",$e->getMessage(),$e,Auth::user()));
            return response()->json(['message' => $e->getMessage()], 500);
        }  
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Notificationtype  $notificationtype
     * @return \Illuminate\Http\Response
     */
    public function edit(Notificationtype $notificationtype)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Notificationtype  $notificationtype
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Notificationtype $notificationtype)
    {
        DB::beginTransaction();
        try{
        $notificationtype->update([
    	  "categoria"=>$request->categoria,
    	  "denumire"=>$request->denumire,
        ]);
        Notificationuser::where("notificationtype_id",$notificationtype->id)->delete();

        foreach ($request->notificationuser as $notificationuser){
           if(valoareCamp("user",$notificationuser)&&valoareCamp("channel",$notificationuser)){
             Notificationuser::create([
                                        "company_id"=>session("company_id"),
                                        "notificationtype_id"=>$notificationtype->id,
                                        "user_id"=>$notificationuser["user"]["id"],
                                        "channel"=>$notificationuser["channel"],
                                    ]);
            }
         }
       // event(new NotificationtypeUpdated());
           DB::commit();
        return $notificationtype;
           
        } catch (\Exception $e) {
            DB::rollback();
            Mail::to("stefan.voinea@gmail.com")
            ->send(new AlertaEroareEmail("UPDATE NOTIFICATION TYPE",$e->getMessage(),$e,Auth::user()));
            return response()->json(['message' => $e->getMessage()], 500);
        }  
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Notificationtype  $notificationtype
     * @return \Illuminate\Http\Response
     */
    public function destroy(Notificationtype $notificationtype)
    {
         DB::beginTransaction();
         try{
        Notificationuser::where("notificationtype_id",$notificationtype->id)->delete();
        $notificationtype->delete();
           DB::commit();
           
        } catch (\Exception $e) {
            DB::rollback();
            Mail::to("stefan.voinea@gmail.com")
            ->send(new AlertaEroareEmail("STERGE NOTIFICATION TYPE",$e->getMessage(),$e,Auth::user()));
            return response()->json(['message' => $e->getMessage()], 500);
        }  
      //  event(new NotificationtypeUpdated());

    }
}