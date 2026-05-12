<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Exports\TaskExport;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class TaskController extends Controller
{

    public function toggleCompleted(Request $request, Task $task)
    {
        $task->update([
          "completed_at"=>$task->iscompleted?Carbon::now():null,
          "iscompleted"=>$task->iscompleted,
          "completedby_id"=>$task->iscompleted?session("user_id"):null,           
        ]);
       // event(new TaskUpdated());
        $task= Task::where("company_id",session("company_id"))
                      ->where(function($q){

                              return $q->where("assignedby_id",session("user_id"))
                                      ->orWhere("assignedto_id",session("user_id"))  ;
                              })
                      ->with(["assignedby","assignedto","completedby"])
                      ->orderBy("id","desc")
                      ->get();
        return json_encode($task);
    }
    public function taskuriusercurent()
    {
          $task= Task::where("company_id",session("company_id"))
                      ->where(function($q){

                              return $q->where("assignedby_id",session("user_id"))
                                      ->orWhere("assignedto_id",session("user_id"))  ;
                              })
                      ->with(["assignedby","assignedto","completedby"])
                      ->orderBy("id","desc")
                      ->get();
         
                                            
          return json_encode($task);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function indexPaginat(Request $request)
    {
          $records= Task::select('*')->where("company_id",session("company_id"))->with(["assignedby","assignedto","completedby"]);
          $records=filterRequest($records,$request->searchModel,$request->cautareDupa,$request->sortModel,$request->filterModel);
        $records=  $records->orderBy('id','desc');
        $records=  $records->paginate($request->pageLength,
                                                                    ["page"=>$request->page]);
        
                              //::where("user_id",auth()->user()->id)
                                
        return json_encode($records);
    }
     public function index()
    {
          $task= Task::where("company_id",session("company_id"))->with(["assignedby","assignedto","completedby"])->get();
          return json_encode($task);
    }
    public function export() 
        {
             
            ob_end_clean(); 
            ob_start(); 
             $company_id=session("company_id");
            return Excel::download((new TaskExport)->forCompany($company_id),"task.xls");


        }

    public function import(Request $request) 
        {
          $fileName = "task_".time().".".$request->file->getClientOriginalExtension();
          $request->file->move(public_path("upload"), $fileName);
          
          Excel::import(new TaskImport, public_path("upload")."/".$fileName);

          
            $task= Task::where("company_id",session("company_id"))
                                                     ->paginate($request->pageLength,['page'=>$request->page]);
         
            return json_encode($task);
             
        
           
         }
        
    public function storeExcel() 
        {
            ob_end_clean(); 
            ob_start(); 
            $company_id=session("company_id");
            
            Excel::store((new TaskExport)->forCompany($company_id), "task.xls","public",null,[
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

        // event(new TaskUpdated());
        
           $completedby_id= Task::create([
          "company_id"=>session("company_id"),
        
    	  "assignedby_id"=>session("user_id"),
    	  "assignedto_id"=>valoareCamp("id",$request->assignedto),
    	  "title"=>$request->title,
    	  "description"=>$request->description,
    	  "duedate"=>datasioraFormatStocare($request->duedate),
    	  "tags"=>$request->tags,
    	  "completed_at"=>null,
    	  "iscompleted"=>false,
          "isdeleted"=>false,
    	  "isimportant"=>$request->isimportant??false,
    	  "completedby_id"=>null,           
        ]);
         $task= Task::where("company_id",session("company_id"))
                      ->where(function($q){

                              return $q->where("assignedby_id",session("user_id"))
                                      ->orWhere("assignedto_id",session("user_id"))  ;
                              })
                      ->with(["assignedby","assignedto","completedby"])
                      ->orderBy("id","desc")
                      ->get();
        return json_encode($task);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function show(Task $task)
    {
        $resp= Task::where("id",$task->id)
                               ->where("company_id",session("company_id"))
        					   ->get()->first();
                                
        return json_encode($resp);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function edit(Task $task)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Task $task)
    {

       Log::info($request);
       
        $task->update([
    	  "assignedto_id"=>valoareCamp("id",$request->assignedto),
          "title"=>$request->title,
          "description"=>$request->description,
          "duedate"=>datasioraFormatStocare($request->duedate),
          "tags"=>$request->tags,
          "isdeleted"=>$request->isdeleted??false,
          "isimportant"=>$request->isimportant??false,
          "completed_at"=>$request->iscompleted?Carbon::now():null,
          "iscompleted"=>$request->iscompleted,
          "completedby_id"=>$request->iscompleted?session("user_id"):null,                  
        ]);
       // event(new TaskUpdated());
 $task= Task::where("company_id",session("company_id"))
                      ->where(function($q){

                              return $q->where("assignedby_id",session("user_id"))
                                      ->orWhere("assignedto_id",session("user_id"))  ;
                              })
                      ->with(["assignedby","assignedto","completedby"])
                      ->orderBy("id","desc")
                      ->get();
        return json_encode($task);
        
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function destroy(Task $task)
    {
        $task->delete();
      //  event(new TaskUpdated());

    }
}