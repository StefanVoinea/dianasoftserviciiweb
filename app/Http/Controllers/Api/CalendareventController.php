<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Calendarevent;
use App\Models\Exports\CalendareventExport;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class CalendareventController extends Controller
{

    public function calendareventsusercurent(Request $request){
        
        $calendarevent= Calendarevent::where("company_id",session("company_id"))
                                       ->where("participating_users",'like','%"'.session("user_id").'"%')
                                       ->whereIn("calendar",explode(',',$request->calendars))
                                       ->with(["createdby"])
                                       ->get();
          return json_encode($calendarevent);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function indexPaginat(Request $request)
    {
        $records= Calendarevent::select('*')->where("company_id",session("company_id"))->with(["createdby"]);
        $records=filterRequest($records,$request->searchModel,$request->cautareDupa,$request->sortModel,$request->filterModel);
        $records=  $records->orderBy('id','desc');
        $records=  $records->paginate($request->pageLength,
                                                                    ["page"=>$request->page]);
        
                              //::where("user_id",auth()->user()->id)
                                
        return json_encode($records);
    }
     public function index()
    {
          $calendarevent= Calendarevent::where("company_id",session("company_id"))->with(["createdby"])->get();
          return json_encode($calendarevent);
    }
    public function export() 
        {
             
            ob_end_clean(); 
            ob_start(); 
             $company_id=session("company_id");
            return Excel::download((new CalendareventExport)->forCompany($company_id),"calendarevent.xls");


        }

    public function import(Request $request) 
        {
          $fileName = "calendarevent_".time().".".$request->file->getClientOriginalExtension();
          $request->file->move(public_path("upload"), $fileName);
          
          Excel::import(new CalendareventImport, public_path("upload")."/".$fileName);

          
            $calendarevent= Calendarevent::where("company_id",session("company_id"))
                                                     ->paginate($request->pageLength,['page'=>$request->page]);
         
            return json_encode($calendarevent);
             
        
           
         }
        
    public function storeExcel() 
        {
            ob_end_clean(); 
            ob_start(); 
            $company_id=session("company_id");
            
            Excel::store((new CalendareventExport)->forCompany($company_id), "calendarevent.xls","public",null,[
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

        // event(new CalendareventUpdated());
        $guests=[strval(session("user_id"))];
       
        foreach($request->extendedProps["guests"] as $guest){
            $userGuest=User::where("id",$guest)->get()->first();
           
            if($userGuest->grup){
                $guests=array_merge($guests,$userGuest->grup);
            }else{
                array_push($guests,strval($guest["id"]));
            }

        }
         $description= Calendarevent::create([
          "company_id"=>session("company_id"),
          "createdby_id"=>session("user_id"),
    	  "title"=>$request->title??null,
    	  "url"=>"",
    	  "start"=>datasioraFormatStocare($request->start),
    	  "end"=>datasioraFormatStocare($request->end),
    	  "allday"=>$request->allday??false,
    	  "calendar"=>valoareCamp("calendar",$request->extendedProps),
    	  "guests"=>valoareCamp("guests",$request->extendedProps),
    	  "location"=>valoareCamp("location",$request->extendedProps),
    	  "description"=>valoareCamp("description",$request->extendedProps),      
          "participating_users"=>$guests??null,     
        ]);
        return $description;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Calendarevent  $calendarevent
     * @return \Illuminate\Http\Response
     */
    public function show(Calendarevent $calendarevent)
    {
        $resp= Calendarevent::where("id",$calendarevent->id)
                               ->where("company_id",session("company_id"))
        					   ->get()->first();
                                
        return json_encode($resp);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Calendarevent  $calendarevent
     * @return \Illuminate\Http\Response
     */
    public function edit(Calendarevent $calendarevent)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Calendarevent  $calendarevent
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Calendarevent $calendarevent)
    {
       

        $guests=[strval(session("user_id"))];
       
        foreach($request->extendedProps["guests"] as $guest){
            $userGuest=User::where("id",$guest)->get()->first();
           
            if($userGuest->grup){
                $guests=array_merge($guests,$userGuest->grup);
            }else{
                array_push($guests,strval($guest["id"]));
            }

        }
        Log::info($request);
        $calendarevent->update([
    	   "title"=>$request->title??null,
          "url"=>"",
          "start"=>datasioraFormatStocare($request->start),
          "end"=>datasioraFormatStocare($request->end),
          "allday"=>$request->allday??false,
          "calendar"=>valoareCamp("calendar",$request->extendedProps),
          "guests"=>valoareCamp("guests",$request->extendedProps),
          "location"=>valoareCamp("location",$request->extendedProps),
          "description"=>valoareCamp("description",$request->extendedProps),      
          "participating_users"=>$guests??null,     
        ]);
       // event(new CalendareventUpdated());
        // $calendarRaspuns = new \StdClass();
        // $calendarRaspuns->id=$calendarevent->id;
        // $calendarRaspuns->title=$calendarevent->title;
        // $calendarRaspuns->url=$calendarevent->url;
        // $calendarRaspuns->start=$calendarevent->start;
        // $calendarRaspuns->end=$calendarevent->end;
        // $calendarRaspuns->allday=$calendarevent->allday;
        // $calendarRaspuns->calendar=$calendarevent->calendar;
        // $calendarRaspuns->guests=$calendarevent->guests;
        // $calendarRaspuns->location=$calendarevent->location;
        // $calendarRaspuns->description=$calendarevent->description;
        // $calendarRaspuns->participating_users=$calendarevent->participating_users;
        $calendarevent->extendedProps=['calendar'=>$calendarevent->calendar,
                                         'guests'=>$calendarevent->guests,
                                         'location'=>$calendarevent->location,
                                         'participating_users'=>$calendarevent->description];
        return json_encode($calendarevent);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Calendarevent  $calendarevent
     * @return \Illuminate\Http\Response
     */
    public function destroy(Calendarevent $calendarevent)
    {
        $calendarevent->delete();
      //  event(new CalendareventUpdated());

    }
}