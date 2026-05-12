<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\DianaSoftMenuOption;
use App\Models\DianaSoftMenuOption_User;
use App\Models\Gestiune_User;
use App\Models\Judet;
use App\Models\Localitati;
use App\Models\Nomalerte;
use App\Models\Notificationtype;
use App\Models\Notificationuser;
use App\Models\Optiunidropdown;
use App\Models\Permission;
use App\Models\Permission_User;
use App\Models\Tari;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use App\Exports\SablonCuViewExport;
use App\Exports\SablonExport;
use App\Exports\SablonMultipleSheetsExport;
use App\Models\DianaSoftField;
use App\Models\DianaSoftModel;
use App\Models\Notificationlog;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use Madnest\Madzipper\Facades\Madzipper;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpWord\Element\Table;
use PhpOffice\PhpWord\SimpleType\TblWidth;
use PhpOffice\PhpWord\TemplateProcessor;
use Illuminate\Support\Facades\Mail;
use App\Mail\AlertaEroareEmail;

class UtilizatoriController extends Controller
{
     public function situatieDrepturiUtilizatori(Request $request){
        Log::info("Situatie drepturi utilizator PAS 1");
        $users=User::with(["alldianasoftmenuoptionsCompany","allgestiuniCompany","allpermissionsCompany"])->get();
        Log::info("Situatie drepturi utilizator PAS 2");
        $titluRaport="Situatie drepturi utilizatori".dateFormatAfisare(Carbon::today());
        $toateOptiunile=[];
        $toateOperatiunile=[];
        $toateGestiunile=[];
        $toateGrupurile=[];
        $toateNotificarile=[];
        //Log::info("Situatie_drepturi_utilizatori PAS 1");
        Log::info("Situatie drepturi utilizator PAS 3");
        foreach ($users as $user){
         //Log::info("Situatie_drepturi_utilizatori PAS 2 ".$user->name);
        $optiuni=collect($user->alldianasoftmenuoptionsCompany); 
        //Log::info("Situatie_drepturi_utilizatori PAS 3");
        foreach($optiuni as $optiune){
            $optiune->utilizator=$user->name;
            $optiune->activa=$optiune->pivot->isactive?"Da":"Nu";
        }
        //Log::info("Situatie_drepturi_utilizatori PAS 4");
        $toateOptiunile=array_merge($toateOptiunile,$optiuni->toArray());
        $operatiuni=collect($user->allpermissionsCompany);   
        //Log::info("Situatie_drepturi_utilizatori PAS 5");
        foreach($operatiuni as $operatiune){
            $operatiune->utilizator=$user->name;
            $operatiune->activa=$operatiune->pivot->isactive?"Da":"Nu";
            $optiune=$optiuni->where("id",$operatiune->dianasoftmenuoption_id)->values();
           
            if(count($optiune)>0){
                $operatiune->optiune=$optiune[0]->name;
                $operatiune->optiune_activa=$optiune[0]->pivot->isactive?"Da":"Nu";
            }else{
                $operatiune->optiune="";
                $operatiune->optiune_activa="";
            }    
        }
        //Log::info("Situatie_drepturi_utilizatori PAS 6");
        $toateOperatiunile=array_merge($toateOperatiunile,$operatiuni->toArray());

         $gestiuni=collect($user->allgestiuniCompany);   

        foreach($gestiuni as $gestiune){
            $gestiune->utilizator=$user->name;
            $gestiune->activa=$gestiune->pivot->isactive?"Da":"Nu";
        }

        $toateGestiunile=array_merge($toateGestiunile,$gestiuni->toArray());
        //Log::info("Situatie_drepturi_utilizatori PAS 7");
        $company= Company::where("id",session("company_id"))->get()->first();
        $utilizator= $company->users;
        $grupuri=$utilizator->whereIn("user_type",["Sistem","group"])->sortby("name")->values();
        $notificari=Notificationtype::orderBy("denumire","asc")->get();
        //Log::info("Situatie_drepturi_utilizatori PAS 8");
        foreach($notificari as $notificare){
            $canal= Notificationuser::where("notificationtype_id",$notificare->id)->where("user_id",$user->id)->get()->first();
          if($canal){
            $notificare->channel=$canal->channel;
          }else{
            $notificare->channel="";
          }    
           $notificare->utilizator=$user->name;
        }
       $toateNotificarile=array_merge($toateNotificarile,$notificari->toArray());
       //Log::info("Situatie_drepturi_utilizatori PAS 9");
        foreach($grupuri as $grup){
            $grup->utilizator=$user->name;
            if($grup->grup){
                
                if(in_array($user->id,$grup->grup)){
                    $grup->activa="Da";
                }else{
                    $grup->activa="Nu";      
                }
            }else{
                    $grup->activa="Nu";    
            }
        }
        $toateGrupurile=array_merge($toateGrupurile,$grupuri->toArray());
        //Log::info("Situatie_drepturi_utilizatori PAS 10");
       }
       Log::info("Situatie drepturi utilizator PAS 4");
        $sheeturi=[];
          $antetTabel=[
            ["col"=>"name","denumire"=>"Utilizator","type"=>"","align"=>"left","width"=>"50%"],
            ["col"=>"email","denumire"=>"E-mail","type"=>"","align"=>"left","width"=>"50%"], 
            ["col"=>"functia","denumire"=>"Functia","type"=>"","align"=>"left","width"=>"50%"], 
            ["col"=>"departament","denumire"=>"Departament","type"=>"","align"=>"right","width"=>"10%"],
        ];

        
        $tabel=collect($users)->sortBy(["name"=>"asc"]);
        
        $groupBy=[
        ];   
        $totalBy=[];
        $titluSheet="Lista utilizatori";
        $columnFormat=[
                                 // 'D' => NumberFormat::FORMAT_DATE_DDMMYYYY,
                                 // 'I' => NumberFormat::FORMAT_CURRENCY_EUR_SIMPLE
                               ];
        $sheet= new \StdClass;
        $sheet->titluSheet=$titluSheet;
        $sheet->tabel=$tabel;
        $sheet->antetTabel=$antetTabel;
        $sheet->totalBy=$totalBy;    
        $sheet->groupBy=$groupBy;
       $sheet->columnFormat=$columnFormat;
       array_push($sheeturi,$sheet);

        $antetTabel=[
            ["col"=>"utilizator","denumire"=>"Utilizator","type"=>"","align"=>"left","width"=>"50%"],
            ["col"=>"name","denumire"=>"Denumire","type"=>"","align"=>"left","width"=>"50%"], 
            ["col"=>"activa","denumire"=>"Acces","type"=>"","align"=>"right","width"=>"10%"],
        ];

        //Log::info("Situatie_drepturi_utilizatori PAS 11");
        $tabel=collect($toateOptiunile)->sortBy(["utilizator"=>"asc","name"=>"asc"]);
        
        $groupBy=[
        ];   
        $totalBy=[];
        $titluSheet="Optiuni menu";
        $columnFormat=[
                                 // 'D' => NumberFormat::FORMAT_DATE_DDMMYYYY,
                                 // 'I' => NumberFormat::FORMAT_CURRENCY_EUR_SIMPLE
                               ];
        $sheet= new \StdClass;
        $sheet->titluSheet=$titluSheet;
        $sheet->tabel=$tabel;
        $sheet->antetTabel=$antetTabel;
        $sheet->totalBy=$totalBy;    
        $sheet->groupBy=$groupBy;
       $sheet->columnFormat=$columnFormat;
       array_push($sheeturi,$sheet);
       
       $antetTabel=[
            ["col"=>"utilizator","denumire"=>"Utilizator","type"=>"","align"=>"left","width"=>"50%"],
            ["col"=>"optiune","denumire"=>"Optiune menu","type"=>"","align"=>"left","width"=>"50%"], 
            ["col"=>"optiune_activa","denumire"=>"Acces optiune","type"=>"","align"=>"right","width"=>"10%"],
            ["col"=>"display_name","denumire"=>"Denumire","type"=>"","align"=>"left","width"=>"50%"], 
            ["col"=>"activa","denumire"=>"Acces","type"=>"","align"=>"right","width"=>"10%"],
        ];

        
        $tabel=collect($toateOperatiunile)->sortBy([
                                                    "utilizator"=>"asc",
                                                    "optiune"=>"asc",
                                                     "display_name"=>"asc"
                                                   ]);
        $groupBy=[
        ];   
        $totalBy=[];
        $titluSheet="Operatiuni";
        $columnFormat=[
                                 // 'D' => NumberFormat::FORMAT_DATE_DDMMYYYY,
                                 // 'I' => NumberFormat::FORMAT_CURRENCY_EUR_SIMPLE
                               ];
        
        $sheet= new \StdClass;
        $sheet->titluSheet=$titluSheet;
        $sheet->tabel=$tabel;
        $sheet->antetTabel=$antetTabel;
        $sheet->totalBy=$totalBy;    
        $sheet->groupBy=$groupBy;
       $sheet->columnFormat=$columnFormat;
       array_push($sheeturi,$sheet);
       
        $antetTabel=[
            ["col"=>"utilizator","denumire"=>"Utilizator","type"=>"","align"=>"left","width"=>"50%"],
            ["col"=>"denumire","denumire"=>"Denumire","type"=>"","align"=>"left","width"=>"50%"], 
            ["col"=>"activa","denumire"=>"Acces","type"=>"","align"=>"right","width"=>"10%"],
        ];

        
        $tabel=collect($toateGestiunile)->sortBy([ 
                                                    "utilizator"=>"asc",
                                                    "denumire"=>"asc"
                                                 ]);
        $groupBy=[];   
        $totalBy=[];
        $titluSheet="Agentii";
        $columnFormat=[
                                 // 'D' => NumberFormat::FORMAT_DATE_DDMMYYYY,
                                 // 'I' => NumberFormat::FORMAT_CURRENCY_EUR_SIMPLE
                               ];
        
        $sheet= new \StdClass;
        $sheet->titluSheet=$titluSheet;
        $sheet->tabel=$tabel;
        $sheet->antetTabel=$antetTabel;
        $sheet->totalBy=$totalBy;    
        $sheet->groupBy=$groupBy;
       $sheet->columnFormat=$columnFormat;
       array_push($sheeturi,$sheet);

        $antetTabel=[
            ["col"=>"utilizator","denumire"=>"Utilizator","type"=>"","align"=>"left","width"=>"50%"],
            ["col"=>"name","denumire"=>"Denumire","type"=>"","align"=>"left","width"=>"50%"], 
            ["col"=>"activa","denumire"=>"Acces","type"=>"","align"=>"right","width"=>"10%"],
        ];

        
        $tabel=collect($toateGrupurile)->sortBy( [
                                                    "utilizator"=>"asc",
                                                    "name"=>"asc"
                                                     ]);
        $groupBy=[
        ];   
        $totalBy=[];
        $titluSheet="Grupuri";
        $columnFormat=[
                                 // 'D' => NumberFormat::FORMAT_DATE_DDMMYYYY,
                                 // 'I' => NumberFormat::FORMAT_CURRENCY_EUR_SIMPLE
                               ];
        
        $sheet= new \StdClass;
        $sheet->titluSheet=$titluSheet;
        $sheet->tabel=$tabel;
        $sheet->antetTabel=$antetTabel;
        $sheet->totalBy=$totalBy;    
        $sheet->groupBy=$groupBy;
       $sheet->columnFormat=$columnFormat;
       array_push($sheeturi,$sheet);

       $antetTabel=[
            ["col"=>"utilizator","denumire"=>"Utilizator","type"=>"","align"=>"left","width"=>"50%"],
            ["col"=>"denumire","denumire"=>"Denumire","type"=>"","align"=>"left","width"=>"50%"], 
            ["col"=>"channel","denumire"=>"Acces","type"=>"","align"=>"right","width"=>"10%"],
        ];

        
        $tabel=collect($toateNotificarile)->sortBy(["utilizator"=>"asc", "denumire"=>"asc"]);
        $groupBy=[
        ];   
        $totalBy=[];
        $titluSheet="Notificari";
        $columnFormat=[
                                 // 'D' => NumberFormat::FORMAT_DATE_DDMMYYYY,
                                 // 'I' => NumberFormat::FORMAT_CURRENCY_EUR_SIMPLE
                               ];
        
        $sheet= new \StdClass;
        $sheet->titluSheet=$titluSheet;
        $sheet->tabel=$tabel;
        $sheet->antetTabel=$antetTabel;
        $sheet->totalBy=$totalBy;    
        $sheet->groupBy=$groupBy;
       $sheet->columnFormat=$columnFormat;
       array_push($sheeturi,$sheet);
               $company_id=session("company_id");
                ob_end_clean(); 
                 ob_start(); 
                 Log::info("Situatie drepturi utilizator PAS 5");    
                 $fileName="Situatie_drepturi_utilizatori.xlsx";
                 
                return Excel::download((new SablonMultipleSheetsExport)->forCompany($company_id,$sheeturi,$titluRaport),$fileName);
       
    }  
    public function fisautilizator(Request $request,User $user){
        
        $user=User::where("id",$user->id)->with(["alldianasoftmenuoptionsCompany","allgestiuniCompany","allpermissionsCompany"])->get()->first();
        $optiuni=collect($user->alldianasoftmenuoptionsCompany);   
        foreach($optiuni as $optiune){
            $optiune->activa=$optiune->pivot->isactive?"Da":"Nu";
        }
        $titluRaport="Fisa utilizator " .$user->name;

        $operatiuni=collect($user->allpermissionsCompany);   
        
        foreach($operatiuni as $operatiune){
            $operatiune->activa=$operatiune->pivot->isactive?"Da":"Nu";
            $optiune=$optiuni->where("id",$operatiune->dianasoftmenuoption_id)->values();
            if(count($optiune)>0){
                $operatiune->optiune=$optiune[0]->name;
            }else{
                $operatiune->optiune="";
            }    
        }
         $gestiuni=collect($user->allgestiuniCompany);   

        foreach($gestiuni as $gestiune){
            $gestiune->activa=$gestiune->pivot->isactive?"Da":"Nu";
        }
        $company= Company::where("id",session("company_id"))->get()->first();
        $utilizator= $company->users;
        $grupuri=$utilizator->whereIn("user_type",["Sistem","group"])->sortby("name")->values();
        $notificari=Notificationtype::orderBy("denumire","asc")->get();
        foreach($notificari as $notificare){
            $canal= Notificationuser::where("notificationtype_id",$notificare->id)->where("user_id",$user->id)->get()->first();
          if($canal){
            $notificare->channel=$canal->channel;
          }else{
            $notificare->channel="";
          }    
        }

        foreach($grupuri as $grup){
            if($grup->grup){
                
                if(in_array($user->id,$grup->grup)){
                    $grup->activa="Da";
                }else{
                    $grup->activa="Nu";      
                }
            }else{
                    $grup->activa="Nu";    
            }
        }


        $antetTabel=[
            ["col"=>"name","denumire"=>"Denumire","type"=>"","align"=>"left","width"=>"50%"], 
            ["col"=>"activa","denumire"=>"Acces","type"=>"","align"=>"right","width"=>"10%"],
        ];

        
        $tabel=collect($optiuni)->sortBy("optiune");
        $groupBy=[
        ];   
        $totalBy=[];
        $titluSheet="Optiuni menu";
        $columnFormat=[
                                 // 'D' => NumberFormat::FORMAT_DATE_DDMMYYYY,
                                 // 'I' => NumberFormat::FORMAT_CURRENCY_EUR_SIMPLE
                               ];
        $sheeturi=[];
        $sheet= new \StdClass;
        $sheet->titluSheet=$titluSheet;
        $sheet->tabel=$tabel;
        $sheet->antetTabel=$antetTabel;
        $sheet->totalBy=$totalBy;    
        $sheet->groupBy=$groupBy;
       $sheet->columnFormat=$columnFormat;
       array_push($sheeturi,$sheet);
       
       $antetTabel=[
            ["col"=>"optiune","denumire"=>"Optiune menu","type"=>"","align"=>"left","width"=>"50%"], 
            ["col"=>"display_name","denumire"=>"Denumire","type"=>"","align"=>"left","width"=>"50%"], 
            ["col"=>"activa","denumire"=>"Acces","type"=>"","align"=>"right","width"=>"10%"],
        ];

        
        $tabel=collect($operatiuni)->sortBy("optiune");
        $groupBy=[
        ];   
        $totalBy=[];
        $titluSheet="Operatiuni";
        $columnFormat=[
                                 // 'D' => NumberFormat::FORMAT_DATE_DDMMYYYY,
                                 // 'I' => NumberFormat::FORMAT_CURRENCY_EUR_SIMPLE
                               ];
        
        $sheet= new \StdClass;
        $sheet->titluSheet=$titluSheet;
        $sheet->tabel=$tabel;
        $sheet->antetTabel=$antetTabel;
        $sheet->totalBy=$totalBy;    
        $sheet->groupBy=$groupBy;
       $sheet->columnFormat=$columnFormat;
       array_push($sheeturi,$sheet);
       
        $antetTabel=[
            ["col"=>"denumire","denumire"=>"Denumire","type"=>"","align"=>"left","width"=>"50%"], 
            ["col"=>"activa","denumire"=>"Acces","type"=>"","align"=>"right","width"=>"10%"],
        ];

        
        $tabel=collect($gestiuni)->sortBy("denumire");
        $groupBy=[
        ];   
        $totalBy=[];
        $titluSheet="Agentii";
        $columnFormat=[
                                 // 'D' => NumberFormat::FORMAT_DATE_DDMMYYYY,
                                 // 'I' => NumberFormat::FORMAT_CURRENCY_EUR_SIMPLE
                               ];
        
        $sheet= new \StdClass;
        $sheet->titluSheet=$titluSheet;
        $sheet->tabel=$tabel;
        $sheet->antetTabel=$antetTabel;
        $sheet->totalBy=$totalBy;    
        $sheet->groupBy=$groupBy;
       $sheet->columnFormat=$columnFormat;
       array_push($sheeturi,$sheet);

        $antetTabel=[
            ["col"=>"name","denumire"=>"Denumire","type"=>"","align"=>"left","width"=>"50%"], 
            ["col"=>"activa","denumire"=>"Acces","type"=>"","align"=>"right","width"=>"10%"],
        ];

        
        $tabel=collect($grupuri)->sortBy("name");
        $groupBy=[
        ];   
        $totalBy=[];
        $titluSheet="Grupuri";
        $columnFormat=[
                                 // 'D' => NumberFormat::FORMAT_DATE_DDMMYYYY,
                                 // 'I' => NumberFormat::FORMAT_CURRENCY_EUR_SIMPLE
                               ];
        
        $sheet= new \StdClass;
        $sheet->titluSheet=$titluSheet;
        $sheet->tabel=$tabel;
        $sheet->antetTabel=$antetTabel;
        $sheet->totalBy=$totalBy;    
        $sheet->groupBy=$groupBy;
       $sheet->columnFormat=$columnFormat;
       array_push($sheeturi,$sheet);

       $antetTabel=[
            ["col"=>"denumire","denumire"=>"Denumire","type"=>"","align"=>"left","width"=>"50%"], 
            ["col"=>"channel","denumire"=>"Acces","type"=>"","align"=>"right","width"=>"10%"],
        ];

        
        $tabel=collect($notificari)->sortBy("denumire");
        $groupBy=[
        ];   
        $totalBy=[];
        $titluSheet="Notificari";
        $columnFormat=[
                                 // 'D' => NumberFormat::FORMAT_DATE_DDMMYYYY,
                                 // 'I' => NumberFormat::FORMAT_CURRENCY_EUR_SIMPLE
                               ];
        
        $sheet= new \StdClass;
        $sheet->titluSheet=$titluSheet;
        $sheet->tabel=$tabel;
        $sheet->antetTabel=$antetTabel;
        $sheet->totalBy=$totalBy;    
        $sheet->groupBy=$groupBy;
       $sheet->columnFormat=$columnFormat;
       array_push($sheeturi,$sheet);
               $company_id=session("company_id");
                ob_end_clean(); 
                 ob_start(); 
                     
                 $fileName="Fisa_utilizator.xls";
                 
                return Excel::download((new SablonMultipleSheetsExport)->forCompany($company_id,$sheeturi,$titluRaport),$fileName);
       
    }   
    public function permisiunifiltrate(Request $request){
        // $permissions=Permission_User::where("user_id",$request->id)
        //                               ->whereHas('permission', function ($query) use($request) {
        //                                 return $query->where('dianasoftmenuoption_id', $request->id_optiune);
        //                                 })
        //                                 ->with("permission")  
        //                               ->get();
        DB::beginTransaction();
        try{
        $resp= User::where("id",$request->id)
                    ->with(['companies'])
                    ->get()->first();
         $user=$request;           
        foreach ($user["permissions"] as $permission) {
         
            Permission_User::where("user_id",$permission["pivot"]["user_id"])
                           ->where("company_id",session("company_id")) 
                           ->where("permission_id",$permission["pivot"]["permission_id"]) 
                            ->update([
                                    "isactive"=>$permission["pivot"]["isactive"]
                                    ]);
         }
            
        if($request->id_optiune){

        $permissions=collect($resp->allpermissionsCompany->filter(function ($value) use($request) {
                                                        return $value->dianasoftmenuoption_id ==$request->id_optiune;
                                                    }))->values()->toArray();//->where("dianasoftmenuoption_id",$request->id_optiune);
        }else{
        $permissions=$resp->allpermissionsCompany;
        }

                                                                                          
        
        DB::commit();
        return json_encode($permissions);      
           
        } catch (\Exception $e) {
            DB::rollback();
            $methodName = __FUNCTION__;
            $fileName = basename(__FILE__);
            Mail::to("stefan.voinea@gmail.com")
            ->send(new AlertaEroareEmail($methodName." ".$fileName,$e->getMessage(),$e,Auth::user()));
            return response()->json(['message' => $e->getMessage()], 500);
        }                          
    }

   public function modificaAvatar(Request $request) 
        {
          $fileName = "avatar_".$request->file->hashName();
          $request->file->move(public_path("images/portrait/small/"), $fileName);
          $user=User::where("id",session("user_id"))
                     ->get()->first();
           $user->update([
                          "link_poza"=>env("APP_URL")."/images/portrait/small/".$fileName
                            ]);          
          return json_encode(env("APP_URL")."/images/portrait/small/".$fileName.$fileName);
           
         }

    public function importAvatar(Request $request) 
        {
          $fileName = "avatar_".$request->file->hashName();
          $request->file->move(public_path("images/portrait/small/"), $fileName);
           $user=User::where("id",session("user_id"))
                     ->get()->first();
           $user->update([
                          "link_poza"=>env("APP_URL")."/images/portrait/small/".$fileName
                            ]);          
          return  json_encode(env("APP_URL")."/images/portrait/small/".$fileName);
           
         }
     public function indexPaginat(Request $request)
    {
        try{         $records= Company::where("id",session("company_id"))

                            ->get()->first();
        
         
        $users=User::select('*')->where("user_type","user")->whereIn("id",$records->users->pluck("id"));
         $users=filterRequest($users,$request->searchModel,$request->cautareDupa,$request->sortModel,$request->filterModel);
          $users= $users->orderBy('id','desc')
                                          ->paginate($request->pageLength,
                                                      ['page'=>$request->page]);
          
                                //::where("user_id",auth()->user()->id)
                                  
          return json_encode($users);                         

        } catch (\Exception $e) {
            $methodName = __FUNCTION__;
            $fileName = basename(__FILE__);
            Mail::to("stefan.voinea@gmail.com")
            ->send(new AlertaEroareEmail($methodName." ".$fileName,$e->getMessage(),$e,Auth::user()));
            return response()->json(['message' => $e->getMessage()], 500);
        }  
        
    }
    public function cookiesLocal(Request $request)
    {
        $company= Company::where("id",session("company_id"))->get()->first();
        
        $cookieLocal=[];   
        switch($request->cookieLocal){
             
            case 'judet':
                 $judet= Judet::select("denumire")
                    ->orderBy("denumire","asc")
                    ->get();
                $cookieLocal+=["judet"=> json_encode($judet)];               //JUDET
                break;   
            case 'tari':
                // $tari= Tari::select("denumire")
                //     ->orderBy("denumire","asc")
                //     ->get();
               $tari= Tari::select("*")
                    ->orderBy("denumire","asc")
                    ->get();
                $cookieLocal+=["tari"=>json_encode($tari)];              //TARI
                break;   
          

            case 'nomalerte':
                $nomalerte= Nomalerte::where("company_id",session("company_id"))
                                ->orderBy("denumire","asc")
                                ->get();
                $cookieLocal+=["nomalerte"=>json_encode($nomalerte)];  //NOMENCLATOR ALERTE
            break;   
            
         
           
            break; 
            
            case 'optiunidropdown':
                $optiunidropdown= Optiunidropdown::where("company_id",session("company_id"))->orderBy("field_option")
                                ->get();
                $cookieLocal+=["optiunidropdown"=>json_encode($optiunidropdown)];  //OPTIUNI DROPDOWN
            break; 
           
             case 'user':
                
                $user=User::where("id",session("user_id"))->with(['companies','dianasoftmenuoptions','permissions',
            'gestiuni'])
                                ->get()->first();
                $cookieLocal+=["user"=>json_encode($user)];  //User
            break; 
            // case 'contacts':
            //     $company= Company::where("id",session("company_id"))
            //                     ->with(["users"])    
            //                     ->get()->first();
            //     $cookieLocal+=["contacts"=>json_encode($company->users)];  //Utilizatori chat
          //  break; 
            default:   
                $user=User::where("id",session("user_id"))->get()->first();
               
                $judet= Judet::select("denumire")
                            ->orderBy("denumire","asc")
                            ->get();
                $cookieLocal+=["judet"=> json_encode($judet)];               //JUDET

                $tari= Tari::select("*")
                            ->orderBy("denumire","asc")
                            ->get();
                $cookieLocal+=["tari"=>json_encode($tari)];              //TARI

                $user=User::where("id",session("user_id"))->get()->first();
                $gestiunipermise=$user->gestiuniPermiseCompany();
               
               
                $nomalerte= Nomalerte::where("company_id",session("company_id"))
                                        ->orderBy("denumire","asc")
                                        ->get();
                $cookieLocal+=["nomalerte"=>json_encode($nomalerte)];  //NOMENCLATOR ALERTE
                
                $optiunidropdown= Optiunidropdown::where("company_id",session("company_id"))->orderBy("field_option")
                                ->get();
                $cookieLocal+=["optiunidropdown"=>json_encode($optiunidropdown)];  //OPTIUNI DROPDOWN
              
        }
       
        return json_encode($cookieLocal);
    }
    public function utilizatori(){
       $company= Company::where("id",session("company_id"))->get()->first();
        
        $utilizatori= $company->users;
       return json_encode($utilizatori->map->only("name")) ;                       
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        
        $company= Company::where("id",session("company_id"))->get()->first();
        
        $utilizator= $company->users;
        
        
        return json_encode($utilizator);
    }
    public function indexOrdonat()
    {
        
        //$company= Company::where("id",session("company_id"))->get()->first();
        
        //$utilizator= json_decode($company->users->sortBy("name"));
        $utilizator=DB::select(DB::raw("SELECT users.*
                                            FROM company_user INNER JOIN users ON company_user.user_id = users.id
                                            WHERE (((company_user.company_id)=".session("company_id")."))
                                            ORDER BY users.name;
                                            "));
        
        return json_encode($utilizator);
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

    public function modificaParola(Request $request)
    {
         $request->validate( [
          "password"=>["required"],]);

         $utilizator=Auth::user();
         
         
         if(!Hash::check($request->password,$utilizator->password)){

         $utilizator->update([
          "password"=>Hash::make($request->password),
          "data_expirare_parola"=>dateFormatStocare(Carbon::today()->addMonths(3))
                    
        ]);
        

         return $utilizator;
         }else{
            return json_encode("PAROLA NESCHIMBATA");
         }
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
          "email"=>["required"],]);

        DB::beginTransaction();
        try{ 
         $utilizator= User::create([
          "name"=>$request->name,
          "user_type"=>"user",
          "email"=>$request->email,
          "password"=>Hash::make($request->password),
          "telefon"=>$request->telefon??null,
          "functia"=>$request->functia??null,
          "blocat"=>"Nu",
          "status"=>"offline",
          "link_poza"=>$request->link_poza?$request->link_poza:($request->sex=="Feminin"?public_path("images/portrait/small/avatarDefaultWomen.png"):public_path("images/portrait/small/avatarDefaultMan.png")),
          "program_de_lucru"=>$request->program_de_lucru??null,
          "data_expirare_parola"=>Carbon::today()->addMonths(3),
          "departament"=>$request->departament??null,
          "sex"=>$request->sex??null,
                    
        ]);
         $utilizator->companies()->attach(session("company_id"));
         
         $gestiuni=Gestiune::get();
         foreach ($gestiuni as $gestiune) {
           
           

             Gestiune_User::create([
                                     "user_id"=>$utilizator->id,
                                     "company_id"=>session("company_id"),
                                     "gestiune_id"=>$gestiune->id,
                                     "isactive"=>0
                                     ]);
          }
         $allPermission=Permission::get();
         foreach ($allPermission as $permission) {
            
                 $utilizator->permissions()->attach($permission->id,['isactive'=>false,
                                                        'company_id'=>session("company_id")]);    
            
         }

         $allDianaSoftMenuOption=DianaSoftMenuOption::get();
         foreach ($allDianaSoftMenuOption as $DianaSoftMenuOption) {
            
                 $utilizator->dianasoftmenuoptions()->attach($DianaSoftMenuOption->id,['isactive'=>false,
                                                        'company_id'=>session("company_id")]);    
            
         }

         DB::commit();
         return $utilizator;
           
        } catch (\Exception $e) {
            DB::rollback();
            $methodName = __FUNCTION__;
            $fileName = basename(__FILE__);
            Mail::to("stefan.voinea@gmail.com")
            ->send(new AlertaEroareEmail($methodName." ".$fileName,$e->getMessage(),$e,Auth::user()));
            return response()->json(['message' => $e->getMessage()], 500);
        }  
    }
    public function storegroup(Request $request)
    {
         // $request->validate( [
         //  "email"=>["required"],]);
        DB::beginTransaction();
        try{
         $grup=collect($request["payload"]);
         $grupUseri=collect($grup["usersList"])->pluck("id")->toArray();
         array_push($grupUseri,session("user_id")); 
         $utilizator= User::create([
          "name"=>$grup["numeGrup"],
          "user_type"=>"group",
          "email"=>$grup["numeGrup"],
          "password"=>Hash::make("sdfhgkjgls36h%##$%#dh"),
          "functia"=>$grup["descriereGrup"]??null,
          "blocat"=>"Nu",
          "status"=>"online",
          "link_poza"=>public_path("images/portrait/small/avatarGroup.png"),
          "program_de_lucru"=>$grup["program_de_lucru"]??null,
          "data_expirare_parola"=>Carbon::today()->addMonths(3),
          "departament"=>$grup["numeGrup"]??null,
          "selectat"=>false,
          "grup"=>$grupUseri,                    
        ]);
         $utilizator->companies()->attach(session("company_id"));
         $allPermission=Permission::get();
         foreach ($allPermission as $permission) {
            
                 $utilizator->permissions()->attach($permission->id,['isactive'=>true,
                                                        'company_id'=>session("company_id")]);    
            
         }

         $allDianaSoftMenuOption=DianaSoftMenuOption::get();
         foreach ($allDianaSoftMenuOption as $DianaSoftMenuOption) {
            
                 $utilizator->dianasoftmenuoptions()->attach($DianaSoftMenuOption->id,['isactive'=>true,
                                                        'company_id'=>session("company_id")]);    
            
         }

         DB::commit();
         return $utilizator;
           
        } catch (\Exception $e) {
            DB::rollback();
            $methodName = __FUNCTION__;
            $fileName = basename(__FILE__);
            Mail::to("stefan.voinea@gmail.com")
            ->send(new AlertaEroareEmail($methodName." ".$fileName,$e->getMessage(),$e,Auth::user()));
            return response()->json(['message' => $e->getMessage()], 500);
        }  
    }
    /**
     * Display the specified resource.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        $resp= User::where("id",$user->id)
                    ->with(['companies'])
                   ->get()->first();
        $resp->dianasoftmenuoptions=collect($resp->alldianasoftmenuoptionsCompany->sortby("name"))->values()->toArray();
        $resp->permissions=$resp->allpermissionsCompany;
        
        
        $resp->gestiuni=$resp->allgestiuniCompany;
         $company= Company::where("id",session("company_id"))->get()->first();
        $utilizator= $company->users;
        $resp->grupuri=$utilizator->whereIn("user_type",["Sistem","group"])->sortby("name")->values();
        $resp->notificari=Notificationtype::orderBy("denumire","asc")->get();
        foreach($resp->notificari as $notificare){
            $canal= Notificationuser::where("notificationtype_id",$notificare->id)->where("user_id",$resp->id)->get()->first();
          if($canal){
            $notificare->channel=$canal->channel;
          }else{
            $notificare->channel="";
          }    
        }

        foreach($resp->grupuri as $grup){
            if($grup->grup){
                
            if(in_array($user->id,$grup->grup)){
                $grup->isactive=1;
            }else{
                $grup->isactive=0;    
            }
        }else{
                $grup->isactive=0;    
        }
        }

        return json_encode($resp);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\User  $utilizator
     * @return \Illuminate\Http\Response
     */
    public function edit(User $utilizator)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\User  $utilizator
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        DB::beginTransaction();
        try{
        $user->update([
          "name"=>$request->name,
          "user_type"=>"user",
          "email"=>$request->email,
          "password"=>Hash::make($request->password),
          "telefon"=>$request->telefon??null,
          "functia"=>$request->functia??null,
          "blocat"=>"Nu",
          "status"=>"offline",
          "link_poza"=>$request->link_poza?$request->link_poza:($request->sex=="Feminin"?public_path("images/portrait/small/avatarDefaultWomen.png"):public_path("images/portrait/small/avatarDefaultMan.png")),
          "program_de_lucru"=>$request->program_de_lucru??null,
          "data_expirare_parola"=>Carbon::today()->addMonths(3),
          "departament"=>$request->departament??null,
          "sex"=>$request->sex??null,
        ]);
        DB::commit();
        return $user;
           
        } catch (\Exception $e) {
            DB::rollback();
            $methodName = __FUNCTION__;
            $fileName = basename(__FILE__);
            Mail::to("stefan.voinea@gmail.com")
            ->send(new AlertaEroareEmail($methodName." ".$fileName,$e->getMessage(),$e,Auth::user()));
            return response()->json(['message' => $e->getMessage()], 500);
        }  
    }

    public function copyDrepturi(Request $request)
    {
       DB::beginTransaction();
       try{ 
        $fromUser=User::where("id",$request->from_id)->get()->first();
        

        // Gestiune_User::where("user_id",$request->to_id)
        //                    ->where("company_id",session("company_id")) 
        //                    ->delete();
         
        // foreach ($fromUser->allgestiuniCompany as $gestiune) {
           
           

        //     Gestiune_User::create([
        //                             "user_id"=>$request->to_id,
        //                             "company_id"=>session("company_id"),
        //                             "gestiune_id"=>$gestiune->id,
        //                             "isactive"=>$gestiune->pivot->isactive
        //                             ]);
        //  }
        foreach ($request->to as $catre) {
           
       
         Permission_User::where("user_id",$catre["id"])
                           ->where("company_id",session("company_id")) 
                           ->delete();
        
        foreach ($fromUser->allpermissionsCompany as $permission) {
           
            
            Permission_User::create([
                                    "user_id"=>$catre["id"],
                                    "company_id"=>session("company_id"),
                                    "permission_id"=>$permission->id,
                                    "isactive"=>$permission->pivot->isactive
                                    ]);
        }   
         
        DianaSoftMenuOption_User::where("user_id",$catre["id"])
                           ->where("company_id",session("company_id")) 
                           ->delete();

        foreach ($fromUser->alldianasoftmenuoptionsCompany as $dianasoftmenuoption) {
            DianaSoftMenuOption_User::create([
                                    "user_id"=>$catre["id"],
                                    "company_id"=>session("company_id"),
                                    "dianasoftmenuoption_id"=>$dianasoftmenuoption->id,
                                    "isactive"=>$dianasoftmenuoption->pivot->isactive
                                    ]);
        }  
       }
       DB::commit();
           
        } catch (\Exception $e) {
            DB::rollback();
            $methodName = __FUNCTION__;
            $fileName = basename(__FILE__);
            Mail::to("stefan.voinea@gmail.com")
            ->send(new AlertaEroareEmail($methodName." ".$fileName,$e->getMessage(),$e,Auth::user()));
            return response()->json(['message' => $e->getMessage()], 500);
        }    

    }

    public function updatedrepturi(Request $request)
    {
        
        DB::beginTransaction();
      try {
        $user=$request;
        
        foreach ($user["gestiuni"] as $gestiune) {
            //// Log::info($gestiune);
            Gestiune_User::where("user_id",$gestiune["pivot"]["user_id"])
                           ->where("company_id",session("company_id")) 
                           ->where("gestiune_id",$gestiune["pivot"]["gestiune_id"]) 
                            ->update([
                                    "isactive"=>$gestiune["pivot"]["isactive"]
                                    ]);
         }

         foreach ($user["permissions"] as $permission) {
         
            Permission_User::where("user_id",$permission["pivot"]["user_id"])
                           ->where("company_id",session("company_id")) 
                           ->where("permission_id",$permission["pivot"]["permission_id"]) 
                            ->update([
                                    "isactive"=>$permission["pivot"]["isactive"]
                                    ]);
         }

         foreach ($user["dianasoftmenuoptions"] as $dianasoftmenuoption) {
             
            DianaSoftMenuOption_User::where("user_id",$dianasoftmenuoption["pivot"]["user_id"])
                           ->where("company_id",session("company_id")) 
                           ->where("dianasoftmenuoption_id",$dianasoftmenuoption["pivot"]["dianasoftmenuoption_id"]) 
                            ->update([
                                    "isactive"=>$dianasoftmenuoption["pivot"]["isactive"]
                                    ]);
         }
         Notificationuser::where("user_id",$user["id"])->delete();
        foreach ($user["notificari"] as $notificare) { 
            if($notificare["channel"]){ 
             $canal= Notificationuser::where("notificationtype_id",$notificare["id"])
                                       ->where("user_id",$user["id"])
                                       ->where("channel",$notificare["channel"])
                                        ->get()->first();
            if(!$canal){
                Notificationuser::create([
                                        "notificationtype_id"=>$notificare["id"],
                                        "user_id"=>$user["id"],
                                        "channel"=>$notificare["channel"],
                                        "company_id"=>session("company_id")
                                        ]);
            }
        }
        }
        foreach ($user["grupuri"] as $grup) {

            $user=User::where("id",$grup["id"])->get()->first();
            if($grup["grup"]==null){
                    $grup["grup"]=[];
                }
                      
            if($grup["isactive"]){

                if(!in_array($request->id,$grup["grup"])){
                    $grupnou=$grup["grup"];
                    array_push($grupnou,$request->id);
                    array_merge($grupnou,$grup["grup"]);
                    $user->update(["grup"=>$grupnou]);
                }
        }else{
             if(in_array($request->id,$grup["grup"])){
                    
                    if (($key = array_search($request->id, $grup["grup"])) !== false) {
                        unset($grup["grup"][$key]);
                    }
                    
                    $user->update(["grup"=>$grup["grup"]]);
                }
        }  
    }
     DB::commit();
   
      return response()->json(['message' => "Success",
                                'data'=>$user], 200);
        } catch (\Exception $e) {
            DB::rollback();
            $methodName = __FUNCTION__;
            $fileName = basename(__FILE__);
            Mail::to("stefan.voinea@gmail.com")
            ->send(new AlertaEroareEmail($methodName." ".$fileName,$e->getMessage(),$e,Auth::user()));
            return response()->json(['message' => $e->getMessage()], 500);
        }  
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\User  $utilizator
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        $user->companies()->detach(session("company_id"));
        $user->delete();

    }
}