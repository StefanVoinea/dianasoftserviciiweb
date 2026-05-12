<?php

namespace App\Http\Controllers\Api;

use App\Models\Notificationuser;
// use App\Events\NotificationuserUpdated;
use App\Models\Exports\NotificationuserExport;
//use App\Models\Imports\NotificationuserImport;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class NotificationuserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function indexPaginat(Request $request)
    {
          $records= Notificationuser::select('*')->where("company_id",session("company_id"));
          $records=filterRequest($records,$request->searchModel,$request->cautareDupa,$request->sortModel,$request->filterModel);
          $records=  $records->orderBy('id','desc');
          $records=  $records->paginate($request->pageLength,
                                                                    ["page"=>$request->page]);
        
                              //::where("user_id",auth()->user()->id)
                                
        return json_encode($records);
    }
     public function index()
    {
          $notificationuser= Notificationuser::where("company_id",session("company_id"))->get();
          return json_encode($notificationuser);
    }
    public function export() 
        {
             
            ob_end_clean(); 
            ob_start(); 
             $company_id=session("company_id");
            return Excel::download((new NotificationuserExport)->forCompany($company_id),"notificationuser.xls");


        }

    public function import(Request $request) 
        {
          $fileName = "notificationuser_".time().".".$request->file->getClientOriginalExtension();
          $request->file->move(public_path("upload"), $fileName);
          
          Excel::import(new NotificationuserImport, public_path("upload")."/".$fileName);

          
            $notificationuser= Notificationuser::where("company_id",session("company_id"))
                                                     ->paginate($request->pageLength,['page'=>$request->page]);
         
            return json_encode($notificationuser);
             
        
           
         }
        
    public function storeExcel() 
        {
            ob_end_clean(); 
            ob_start(); 
            $company_id=session("company_id");
            
            Excel::store((new NotificationuserExport)->forCompany($company_id), "notificationuser.xls","public",null,[
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

        // event(new NotificationuserUpdated());
         $channel= Notificationuser::create([
        "company_id"=>session("company_id"),
        
    	  "notificationtype_id"=>$request->notificationtype_id,
    	  "user_id"=>$request->user_id,
    	  "channel"=>$request->channel,           
        ]);
        return $channel;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Notificationuser  $notificationuser
     * @return \Illuminate\Http\Response
     */
    public function show(Notificationuser $notificationuser)
    {
        $resp= Notificationuser::where("id",$notificationuser->id)
                               ->where("company_id",session("company_id"))
        					   ->get()->first();
                                
        return json_encode($resp);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Notificationuser  $notificationuser
     * @return \Illuminate\Http\Response
     */
    public function edit(Notificationuser $notificationuser)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Notificationuser  $notificationuser
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Notificationuser $notificationuser)
    {
        $notificationuser->update([
    	  "notificationtype_id"=>$request->notificationtype_id,
    	  "user_id"=>$request->user_id,
    	  "channel"=>$request->channel,
        ]);
       // event(new NotificationuserUpdated());
        return $notificationuser;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Notificationuser  $notificationuser
     * @return \Illuminate\Http\Response
     */
    public function destroy(Notificationuser $notificationuser)
    {
        $notificationuser->delete();
      //  event(new NotificationuserUpdated());

    }
}