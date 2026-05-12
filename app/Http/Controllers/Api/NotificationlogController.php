<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Exports\NotificationlogExport;
use App\Models\Notificationlog;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class NotificationlogController extends Controller
{

    public function notificariusercurent()
    {
          $notificationlog= Notificationlog::select('*')
                                            ->where("company_id",session("company_id"))
                                            ->where("user_id",session("user_id"))
                                            ->where("read_at",null)
                                            ->where("channel","Application")

                                            ->with(["notificationtype","user","from"])
                                            ->orderBy("id","desc")
                                            ->get();
          $notificationGrup=Notificationlog::selectRaw('count(id) as nrMesaje,notificationtype_id')
                                            ->where("company_id",session("company_id"))
                                            ->where("user_id",session("user_id"))
                                            ->where("read_at",null)
                                            ->where("channel","Application")
                                            ->with("notificationtype")
                                            ->groupBy("notificationtype_id")
                                            ->get();
          $raspuns=new \StdClass();
          $raspuns->new=$notificationlog->where("read_at",null)->count();
          $raspuns->notificari=$notificationlog;
          $raspuns->notificariGrup=$notificationGrup;
                                            
          return json_encode($raspuns);
    }
    public function markAsRead(Request $request)
    {
          
          if($request->id){
             DB::table('notificationlog')->where("company_id",session("company_id"))
                                      ->where("user_id",session("user_id"))
                                      ->where("id",$request->id)
                                      ->where("read_at",null)
                                      ->update(['read_at'=>Carbon::now()]);     
          }else{

          if($request->notificationtype_id){
                DB::table('notificationlog')->where("company_id",session("company_id"))
                                      ->where("user_id",session("user_id"))
                                      ->where("notificationtype_id",$request->notificationtype_id)
                                      ->where("read_at",null)
                                      ->update(['read_at'=>Carbon::now()]);     
          }else{
          DB::table('notificationlog')->where("company_id",session("company_id"))
                                      ->where("user_id",session("user_id"))
                                      ->where("read_at",null)
                                      ->update(['read_at'=>Carbon::now()]); 
        }
    }
         // $notificationlog= Notificationlog::where("company_id",session("company_id"))
         //                                    ->where("user_id",session("user_id"))
         //                                    ->where("read_at",null)
         //                                    ->with(["notificationtype","user","from"])
         //                                    ->orderBy("id","desc")
         //                                    ->get();
         //  $raspuns=new \StdClass();
         //  $raspuns->new=$notificationlog->where("read_at",null)->count();
         //  $raspuns->data=$notificationlog;
         $notificationlog= Notificationlog::select('*')
                                            ->where("company_id",session("company_id"))
                                            ->where("user_id",session("user_id"))
                                            ->where("read_at",null)
                                            ->where("channel","Application")

                                            ->with(["notificationtype","user","from"])
                                            ->orderBy("id","desc")
                                            ->get();
          $notificationGrup=Notificationlog::selectRaw('count(id) as nrMesaje,notificationtype_id')
                                            ->where("company_id",session("company_id"))
                                            ->where("user_id",session("user_id"))
                                            ->where("read_at",null)
                                            ->where("channel","Application")
                                            ->with("notificationtype")
                                            ->groupBy("notificationtype_id")
                                            ->get();
          $raspuns=new \StdClass();
          $raspuns->new=$notificationlog->where("read_at",null)->count();
          $raspuns->notificari=$notificationlog;
          $raspuns->notificariGrup=$notificationGrup;
          return json_encode($raspuns);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function indexPaginat(Request $request)
    {
          $records= Notificationlog::select('*')->where("company_id",session("company_id"))->with(["notificationtype","user","from"]);
          $records=filterRequest($records,$request->searchModel,$request->cautareDupa,$request->sortModel,$request->filterModel);
          $records=  $records->orderBy('id','desc');

          $records=  $records->paginate($request->pageLength,
                                        ["page"=>$request->page]);
        
                              //::where("user_id",auth()->user()->id)
                                
        return json_encode($records);
    }
     public function index()
    {
          $notificationlog= Notificationlog::where("company_id",session("company_id"))->with(["notificationtype","user","from"])->get();
          return json_encode($notificationlog);
    }
    public function export() 
        {
             
            ob_end_clean(); 
            ob_start(); 
             $company_id=session("company_id");
            return Excel::download((new NotificationlogExport)->forCompany($company_id),"notificationlog.xls");


        }

    public function import(Request $request) 
        {
          $fileName = "notificationlog_".time().".".$request->file->getClientOriginalExtension();
          $request->file->move(public_path("upload"), $fileName);
          
          Excel::import(new NotificationlogImport, public_path("upload")."/".$fileName);

          
            $notificationlog= Notificationlog::where("company_id",session("company_id"))
                                                     ->paginate($request->pageLength,['page'=>$request->page]);
         
            return json_encode($notificationlog);
             
        
           
         }
        
    public function storeExcel() 
        {
            ob_end_clean(); 
            ob_start(); 
            $company_id=session("company_id");
            
            Excel::store((new NotificationlogExport)->forCompany($company_id), "notificationlog.xls","public",null,[
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

        // event(new NotificationlogUpdated());
         
         $read_at= Notificationlog::create([
        
          "company_id"=>session("company_id"),
        
    	  "notificationtype_id"=>$request->notificationtype_id,
    	  "from_id"=>$request->from_id,
    	  "user_id"=>$request->user_id,
    	  "channel"=>$request->channel,
    	   "email"=>$request->email,
          "telefon"=>$request->telefon,
          "title"=>$request->title,
          "subtitle"=>$request->subtitle,
          "type"=>$request->type,
          "icon"=>$request->icon,
          "avatar"=>$request->avatar,
          "link"=>$request->link,
          "category"=>$request->category,
    	  
        ]);
        return $read_at;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Notificationlog  $notificationlog
     * @return \Illuminate\Http\Response
     */
    public function show(Notificationlog $notificationlog)
    {
        $resp= Notificationlog::where("id",$notificationlog->id)
                               ->where("company_id",session("company_id"))
        					   ->get()->first();
                                
        return json_encode($resp);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Notificationlog  $notificationlog
     * @return \Illuminate\Http\Response
     */
    public function edit(Notificationlog $notificationlog)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Notificationlog  $notificationlog
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Notificationlog $notificationlog)
    {
        $notificationlog->update([
    	 "notificationtype_id"=>$request->notificationtype_id,
          "from_id"=>$request->from_id,
          "user_id"=>$request->user_id,
          "channel"=>$request->channel,
           "email"=>$request->email,
          "telefon"=>$request->telefon,
          "title"=>$request->title,
          "subtitle"=>$request->subtitle,
          "type"=>$request->type,
          "icon"=>$request->icon,
          "avatar"=>$request->avatar,
          "link"=>$request->link,
          "category"=>$request->category,
    	  "read_at"=>$request->read_at,
        ]);
       // event(new NotificationlogUpdated());
        return $notificationlog;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Notificationlog  $notificationlog
     * @return \Illuminate\Http\Response
     */
    public function destroy(Notificationlog $notificationlog)
    {
        $notificationlog->delete();
      //  event(new NotificationlogUpdated());

    }
}