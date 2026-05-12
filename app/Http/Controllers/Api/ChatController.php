<?php

namespace App\Http\Controllers\Api;

use App\Events\ChatEvents;
use App\Http\Controllers\Controller;
use App\Models\Chat;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class ChatController extends Controller
{
     public function  chats(Request $request){
       /* $chats=Chat::select("textContent","isSeen","isArchived","isDeleted")
                    ->selectRaw("if((user_id=".session("user_id")."), true,false) as isSent,false as isPinned,'msg' as grupmesaje, if((user_id=".session("user_id")."), catre_id,user_id) as to_id,created_at as time ")
                    ->where("company_id",session("company_id"))
                    ->where(function($q){
                        return $q->where("user_id",session("user_id"))
                                ->orWhere("catre_id",session("user_id"));   
                    })
                    ->get();
                    */
           Chat::where(function($q) use($request){
                        return $q->where("user_id",$request->userId)
                                ->orWhere("catre_id",session("user_id"));   
                    })
                    ->update(['isSeen' => true]);

        $groups=User::select("*","id as userId")
                    ->where("grup","<>","")
                    ->get();
       $grupuri=[];
       foreach($groups as $grup){
        if (in_array(session("user_id"),$grup->grup)) {
        array_push($grupuri,$grup->id);
         }
        }
        $userGrup=User::where("id",$request->userId)
             ->where("grup","<>","")
             ->get()->first();
        if($userGrup){ 
            $chats=Chat::selectRaw("if((user_id=".session("user_id")."), catre_id,user_id) as userId,user_id as senderId,isSeen,textContent as message,created_at as time,false as isPinned,sender_name ")
                    ->where("company_id",session("company_id"))
                    ->where(function($q) use($grupuri){
                        return $q->where("user_id",session("user_id"))
                                ->orWhere("catre_id",session("user_id"))
                                ->orWhereIn("catre_id",$grupuri);      
                                
                    })
                    ->where(function($q) use($request,$grupuri){
                        return $q->where("user_id",$request->userId)
                                ->orWhere("catre_id",$request->userId);
                                
                    
                    })
                    ->orderBy("created_at","ASC")
                    ->get();
       }else{
          $chats=Chat::selectRaw("if((user_id=".session("user_id")."), catre_id,user_id) as userId,user_id as senderId,isSeen,textContent as message,created_at as time,false as isPinned,sender_name ")
                    ->where("company_id",session("company_id"))
                    ->where(function($q) use($grupuri){
                        return $q->where("user_id",session("user_id"))
                                ->orWhere("catre_id",session("user_id"));
                                
                    })
                    ->where(function($q) use($request,$grupuri){
                        return $q->where("user_id",$request->userId)
                                ->orWhere("catre_id",$request->userId);
                                
                    
                    })
                    ->orderBy("created_at","ASC")
                    ->get();
       }
       $resp=new \StdClass();
         $resp->chat=$chats;           
         $resp->contact= User::select("*","id as userId")->where("id",$request->userId)->get()->first();     
         return json_encode($resp);              
        //return json_encode($chats->groupBy(["to_id","grupmesaje"]));   
      }

      public function  markAllSeen(Request $request){
        
        Chat::where("catre_id",session("user_id"))
              ->where("user_id",$request["id"])
              ->update(["isSeen"=>true]);  
        // $chats=Chat::select("textContent","isSeen","isArchived","isDeleted")
        //             ->selectRaw("if((user_id=".session("user_id")."), true,false) as isSent,if((user_id=".session("user_id")."), catre_id,user_id) as to_id,created_at as time ")
        //             ->where("company_id",session("company_id"))
        //             ->where(function($q){
        //                 return $q->where("user_id",session("user_id"))
        //                         ->orWhere("catre_id",session("user_id"));   
        //             })
        //             ->get();
        // return $chats->groupBy("to_id");
      }
      public function  setPinned(Request $request){
      }
    public function  sendChatMessage(Request $request){
        $mesaj=$request->payload;
        $user= User::where("id",session("user_id"))->get()->first();     
        $mesajCreat=Chat::create([
                        "company_id"=>session("company_id"), 
                        "user_id"=>session("user_id"),
                        "textContent"=>$mesaj["message"],
                        "isSeen"=>false,
                        "isDeleted"=>false,
                        "isArchived"=>false,
                        "catre_id"=>$mesaj["contactId"],
                        "sender_name"=>$user->name

              ]);

       
      Log::info("CHAT CREAT");
        
        $chats=Chat::selectRaw("if((user_id=".session("user_id")."), catre_id,user_id) as userId,user_id as senderId,isSeen,textContent as message,created_at as time,false as isPinned,sender_name ")
                    ->where("id",$mesajCreat->id)
                    ->get()->first();

         $resp=new \StdClass();
         $resp->newMessageData=$chats;
         $resp->chat=[];
         array_push($resp->chat,$chats);           
         $resp->contact= User::select("*","id as userId")->where("id",$mesaj["contactId"])->get()->first();     

        $eveniment="Transmite mesaj";
        $company_id=session("company_id");
        $user_id=session("user_id");
        $catre_id=$mesaj["contactId"];

        $status=""; 
        $chat= json_encode($resp); 
        Log::info("CHAT EVENTS");
        event(new ChatEvents($eveniment,$company_id,$user_id,$catre_id,$chat,$status));
        return $chat;
           
    }
    public function modificaStatus(Request $request)
    {
        $user=User::where("id","<>",session("user_id"))
                    ->get()->first();
        $user->update(["status"=>$request->status]);
        $eveniment="Modifica status";
        $company_id=session("company_id");
        $user_id=session("user_id");
        $status=$request->status;
        $catre_id="";  
        $mesaj= new Chat;
        event(new ChatEvents($eveniment,$company_id,$user_id,$catre_id,null,$status));
        
    }
    public function contacte()
    {
       $users=User::where("id","<>",session("user_id"))
                    ->orderBy("name")
                    ->get();
        $resp= new \StdClass();
       $resp->contacts=$users;
       
        $resp->chatsContacts=Chat::select("textContent","isSeen","isArchived","isDeleted","sender_name")
                    ->selectRaw("if((user_id=".session("user_id")."), true,false) as isSent,false as isPinned,'msg' as grupmesaje, if((user_id=".session("user_id")."), catre_id,user_id) as to_id,created_at as time ,sender_name")
                    ->where("company_id",session("company_id"))
                    ->where(function($q){
                        return $q->where("user_id",session("user_id"))
                                ->orWhere("catre_id",session("user_id"));   
                    })
                    ->get();
        $resp->profileUser  =User::where("id",session("user_id"))
                            ->get()->first();
        return json_encode($resp);
    }
    public function contacteChat()
    {
        $users=User::select("*","id as userId")
                    ->where("id","<>",session("user_id"))
                    ->orderBy("name")
                    ->get();

        $resp= new \StdClass();
        $resp->contacts=json_encode($users);
        
        $groups=User::select("*","id as userId")
                    ->where("grup","<>","")
                    ->get();
       $grupuri=[];
       foreach($groups as $grup){
        if (in_array(session("user_id"),$grup->grup)) {
        array_push($grupuri,$grup->id);
        }
       }
       
        $allChats=Chat::selectRaw("if((user_id=".session("user_id")."), catre_id,user_id) as userId,user_id as senderId,isSeen,textContent as message,created_at as time,false as isPinned,sender_name ")
                    ->where("company_id",session("company_id"))
                    ->where(function($q) use($grupuri){
                        return $q->where("user_id",session("user_id"))
                                ->orWhere("catre_id",session("user_id"))
                                ->orWhereIn("catre_id",$grupuri);   
                    })
                    
                    ->orderBy("created_at","ASC")
                    ->get();

        $allChats=$allChats->groupBy("userId");
        $chatsGrouped= [];
        // $i=0;

        foreach($allChats as $name => $value ){

            $chats= User::select("*","id as userId")->where("id",$name)->get()->first();
            // $chats->id=$i;
            $chats->userId=$name;
            $chats->unseenMsgs=$value->where("isSeen",false)->where("senderId","<>",session("user_id"))->count("isSeen");
            $chats->lastMessage=$value[0];
            $chats->chat=$value;

            array_push($chatsGrouped,$chats);
        }
        
        $resp->chatsContacts=json_encode($chatsGrouped);
        
        $resp->profileUser  =User::where("id",session("user_id"))
                            ->get()->first();
        return json_encode($resp);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function indexPaginat(Request $request)
    {
          $records= Chat::select('*')->where("company_id",session("company_id"));
        $records=filterRequest($records,$request->searchModel,$request->cautareDupa,$request->sortModel,$request->filterModel);
          $records= $records->orderBy('id','desc')
                                          ->paginate($request->pageLength,
                                                      ['page'=>$request->page]);
          
                                //::where("user_id",auth()->user()->id)
                                  
          return json_encode($records);
    }
     public function index()
    {
          $chat= Chat::where("company_id",session("company_id"))->get();
          return json_encode($chat);
    }
    public function export() 
        {
             
            ob_end_clean(); 
            ob_start(); 
             $company_id=session("company_id");
            return Excel::download((new ChatExport)->forCompany($company_id),"chat.xls");


        }

    public function import(Request $request) 
        {
          $fileName = "chat_".time().".".$request->file->getClientOriginalExtension();
          $request->file->move(public_path("upload"), $fileName);
          
          Excel::import(new ChatImport, public_path("upload")."/".$fileName);

          
            $chat= Chat::where("company_id",session("company_id"))
                                                     ->paginate($request->pageLength,['page'=>$request->page]);
         
            return json_encode($chat);
             
        
           
         }
        
    public function storeExcel() 
        {
            ob_end_clean(); 
            ob_start(); 
            $company_id=session("company_id");
            
            Excel::store((new ChatExport)->forCompany($company_id), "chat.xls","public",null,[
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

        // event(new ChatUpdated());
         $taguri= Chat::create([
    	  "company_id"=>$request->company_id,
    	  "user_id"=>$request->user_id,
    	  "textContent"=>$request->textContent,
    	  "link_fisier"=>$request->link_fisier,
    	  "isSeen"=>$request->isSeen??false,
    	  "isDeleted"=>$request->isDeleted??false,
    	  "isArchived"=>$request->isArchived??false,
    	  "tip_catre"=>$request->tip_catre??null,
    	  "catre_id"=>$request->catre_id,
    	  "taguri"=>$request->taguri,           
        ]);
        return $taguri;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Chat  $chat
     * @return \Illuminate\Http\Response
     */
    public function show(Chat $chat)
    {
        $resp= Chat::where("id",$chat->id)
                               ->where("company_id",session("company_id"))
        					   ->get()->first();
                                
        return json_encode($resp);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Chat  $chat
     * @return \Illuminate\Http\Response
     */
    public function edit(Chat $chat)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Chat  $chat
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Chat $chat)
    {
        $chat->update([
    	  "company_id"=>$request->company_id,
    	  "user_id"=>$request->user_id,
    	  "textContent"=>$request->textContent,
    	  "link_fisier"=>$request->link_fisier,
    	  "isSeen"=>$request->isSeen,
    	  "isDeleted"=>$request->isDeleted,
    	  "isArchived"=>$request->isArchived,
    	  "tip_catre"=>$request->tip_catre,
    	  "catre_id"=>$request->catre_id,
    	  "taguri"=>$request->taguri,
        ]);
       // event(new ChatUpdated());
        return $chat;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Chat  $chat
     * @return \Illuminate\Http\Response
     */
    public function destroy(Chat $chat)
    {
        $chat->delete();
      //  event(new ChatUpdated());

    }
}