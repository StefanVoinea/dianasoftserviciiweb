<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Exports\JurnalsmsExport;
use App\Models\Jurnalsms;
use App\Models\User;
use App\Notifications\AlerteazaAdministrator;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\CriteriideevaluareExport;
use App\Exports\FisaCreditPFExport;
use App\Exports\SablonExport;
use App\Exports\SablonGraficSolicitareExport;
use App\Exports\SituatieClientiPotentialiExport;
use App\Exports\SolicitareExport;
use App\Exports\TemplateExport;
use App\Models\Capitalsocial;
use App\Models\Company;
use App\Models\Contract;
use App\Models\Gestiune;
use App\Models\Notificationlog;
use App\Models\Notificationtype;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpWord\Element\Table;
use PhpOffice\PhpWord\SimpleType\TblWidth;
use PhpOffice\PhpWord\TemplateProcessor;
use Illuminate\Support\Facades\Mail;
use App\Mail\AlertaEroareEmail;
use Illuminate\Support\Facades\DB;



class JurnalsmsController extends Controller
{

    public function transmiteSMS(Request $request)
{     
    try{
     
       $mesajeDeTransmis=[];
       if($request->hasFile('file')){
         $dateFisier = Excel::toArray(collect([]), $request->file('file'));
         foreach($dateFisier[0] as $rand){
            $mesaj=new \Stdclass();
            $mesaj->telefon=$rand[0];
            $mesaj->mesaj=$rand[1];
            $mesaj->nr_contract=null;
            $mesaj->nume=null;  
            if(count($rand)==4){
              $mesaj->nr_contract=$rand[2];
              $mesaj->nume=$rand[3];  
            }
            if($mesaj->telefon&&$mesaj->telefon!="Telefon"){
                $mesaj=Jurnalsms::create([
                                    "company_id"=>1,
                                    "telefon"=>$mesaj->telefon,
                                    "mesaj"=>$mesaj->mesaj,
                                    "nr_contract"=>$mesaj->nr_contract,
                                    "catre"=>$mesaj->nume,
                                    "status"=>"In curs de transmitere",
                                    "data_operare"=>Carbon::now(),
                                    "utilizator"=>AUth::user()->name
                                    ]);
                $client = new \SoapClient(env("ORANGE_URL"));
                try{
                    $data=Carbon::now();
                    $raspuns=$client->sendSmsAuthKey(env("ORANGE_USER"), env("ORANGE_API_KEY"), env("ORANGE_SENDER"), $mesaj->telefon, $mesaj->mesaj, $data, 0, "");
                    $mesaj->update(["id_site"=>$raspuns,
                                    "status"=>"In curs de transmitere 0",
                                    "data_verificare_status"=>Carbon::now(),
                                    ]);
                    sleep(0.5);
                     }catch(\Exception $e){
                       
                     }
                        }
                    }
        
        $this->checkStatusSMS();
        

        }


        } catch (\Exception $e) {
            Mail::to("stefan.voinea@gmail.com")
            ->send(new AlertaEroareEmail("TRANSMITE SMS",$e->getMessage(),$e,Auth::user()));
            return response()->json(['message' => $e->getMessage()], 500);
        }      
    }
    public function raportsms(Request $request)
    {     
        try{

            $datai=Carbon::parse($request->datai);
            $datasf=Carbon::parse($request->datasf);
           

            $records=Jurnalsms::whereDate("data_operare",">=",$datai)
                    ->whereDate("data_operare","<=",$datasf)
                    ->orderBy("id","asc")
                    ->with("contract")
                    ->get();

          
            $recordsAll=collect($records);


            $antetTabel=[

                       ['col'=>'contract.agentia','denumire'=>'Agentia','type'=>'','align'=>'center','width'=>'5%'],
                        ['col'=>'nr_contract','denumire'=>'Nr contract','type'=>'','align'=>'center','width'=>'5%'],
                        ['col'=>'telefon','denumire'=>'Telefon','type'=>'','align'=>'center','width'=>'10%'],
                        ['col'=>'mesaj','denumire'=>'Mesaj','type'=>'','align'=>'center','width'=>'20%'],
                        ['col'=>'status','denumire'=>'Status','type'=>'','align'=>'center','width'=>'10%'],
                        ['col'=>'utilizator','denumire'=>'Utilizator','type'=>'','align'=>'center','width'=>'10%'],
                        ['col'=>'data_operare','denumire'=>'Data operare','type'=>'','align'=>'center','width'=>'10%'],
                        ['col'=>'data_transmitere','denumire'=>'Data transmitere','type'=>'','align'=>'center','width'=>'10%'],
                        ['col'=>'catre','denumire'=>'Catre','type'=>'','align'=>'center','width'=>'10%'],
                        ['col'=>'id_site','denumire'=>'Id site','type'=>'','align'=>'center','width'=>'10%'],

            ];

            $tabel=collect($recordsAll);
            $groupBy=[                                           
            ];   
            $totalBy=[];
            $titluRaport="Raport SMS transmise in perioada " .dateFormatAfisare($datai)." - ".dateFormatAfisare($datasf);
            if($request->format_fisier=="Excel"){
                $company_id=session("company_id");
                ob_end_clean(); 
                ob_start(); 

                $titluSheet="Raport SMS";
                $fileName="raport_sms.xls";
                $columnFormat=[
                                     // 'D' => NumberFormat::FORMAT_DATE_DDMMYYYY,
                                     // 'I' => NumberFormat::FORMAT_CURRENCY_EUR_SIMPLE
                ];


                return Excel::download((new SablonExport)
                    ->forCompany($company_id,$titluSheet,$tabel,$antetTabel,$groupBy,$totalBy,$columnFormat,$titluRaport)
                    ,$fileName);
            } 
            if($request->format_fisier=="PDF"){  
                $company=Company::where("id",session("company_id"))->get()->first();
                $numefis=storage_path('app/public/'.$company->slug.'/raportr_sms_'.time().".pdf");
                if(File::exists($numefis)){
                    File::delete($numefis);
                };
                ob_end_clean(); 
                ob_start();     
                $numeview="layouts.sablonview";
                $tippag='landscape';
                $pdf = \Barryvdh\Snappy\Facades\SnappyPdf::loadView($numeview, [
                    'antetTabel' => $antetTabel,
                    'tabel' => $tabel,
                    'titluRaport'=>$titluRaport,
                    'groupBy'=>$groupBy,
                    'totalBy'=>$totalBy,
                    'company'=>$company

                ])->setPaper('a4')
                ->setOrientation($tippag)
                ->setOption('margin-top',10) //80)
                ->setOption('margin-bottom',10)
                ->setOption('margin-left',20)
                ->setOption('margin-right',10)
                 // ->setOption("header-html",$header)
                ->setOption('footer-font-size', '8')
                ->setOption("footer-right","Pag. [page] / [topage]") 
                ->setOption("footer-left",Auth::user()->name." ".datasioracurenta()) ;

                          // $pdf->setOption('javascript-delay', 3000);

                $pdf->save($numefis);

                $headers = array(
                    'Content-Type: application/pdf',
                );


                return Response::download($numefis, 'raport_sms.pdf',$headers);
            }                  
        } catch (\Exception $e) {
            
            Mail::to("stefan.voinea@gmail.com")
            ->send(new AlertaEroareEmail("RAPORT SMS",$e->getMessage(),$e,Auth::user()));
            return response()->json(['message' => $e->getMessage()], 500);
        }     

    }
   
    public function sendSMS(){
        $data=Carbon::now(); 
        Log::info("START TRANSMITE SMS");
     

        $client = new \SoapClient(env("ORANGE_URL"));
        $mesajeDeTransmis=Jurnalsms::where("telefon","<>","")->where("status","In curs de transmitere")->get(); 
        foreach($mesajeDeTransmis as $mesaj){
        try{
        $raspuns=$client->sendSmsAuthKey(env("ORANGE_USER"), env("ORANGE_API_KEY"), env("ORANGE_SENDER"), $mesaj->telefon, $mesaj->mesaj, $data, 0, "");
        $mesaj->update(["id_site"=>$raspuns,
                        "status"=>"In curs de transmitere 0",
                        "data_verificare_status"=>Carbon::now(),
                        ]);
        sleep(0.5);
         }catch(\Exception $e){
             $user=User::where("id",1)->get()->first();
            // $user->notify(new AlerteazaAdministrator("EROARE LA TRANSMITE SMS ".$mesaj->telefon,$e->getMessage()));
         }
       }
       Log::info("STOP TRANSMITE SMS");
    }

     public function checkStatusSMS(){
        Log::info("START CHECK STATUS SMS");
        $mesajeDeVerificat=Jurnalsms::where("id_site","<>","")->where("status","like","In curs de transmitere%")->get(); 
        foreach($mesajeDeVerificat as $mesaj){
        sleep(0.5);
        try{
        $client = new \SoapClient(env("ORANGE_URL"));
        $stare=$client->checkStatus("92adafb6aee78d783a33745085a90cc9|1|2");
        $raspuns="";
        switch ($stare) {
        case "0":
            $raspuns = "In curs de transmitere 0";
            break;
        case "1":
            $raspuns =  "In curs de transmitere 1";
            break;
        case "2":
            $raspuns = "Livrat la client";
            break;
        case "3":
            $raspuns = "In curs de transmitere 3 Eroare temporara";
            break;
        case "4":
            $raspuns = "Respins";
            break;
        case "5":
            $raspuns = "5 Eroare permanenta";
            break;
        case "6":
            $raspuns = "6 Eroare permanenta";
        break;
         case "14":
            $raspuns = "14 Verifica portabilitate";
            break;
        case "16":
            $raspuns = "Numar incorect";
            break;
        default:
            break;


                            };
         $mesaj->update([
                        "status"=>$raspuns,
                        "data_verificare_status"=>Carbon::now(),
                        "data_transmitere"=>Carbon::now()
                        ]);
        }catch(\Exception $e){
             $user=User::where("id",1)->get()->first();
          //   $user->notify(new AlerteazaAdministrator("EROARE LA VERIFICARE STATUS SMS ".$mesaj->id_site,$e->getMessage()));
         }
     }
       Log::info("STOP CHECK STATUS SMS");
         
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function indexPaginat(Request $request)
    {
          $records= Jurnalsms::select('*')->where("company_id",session("company_id"))->with("contract");
          $records=filterRequest($records,$request->searchModel,$request->cautareDupa,$request->sortModel,$request->filterModel);
        $records=  $records->orderBy('id','desc');
        $records=  $records->paginate($request->pageLength,
                                                                    ["page"=>$request->page]);
        
                              //::where("user_id",auth()->user()->id)
                                
        return json_encode($records);
    }
     public function index()
    {
          $jurnalsms= Jurnalsms::where("company_id",session("company_id"))->with("contract")->get();
          return json_encode($jurnalsms);
    }
    public function export() 
        {
             
            ob_end_clean(); 
            ob_start(); 
             $company_id=session("company_id");
            return Excel::download((new JurnalsmsExport)->forCompany($company_id),"jurnalsms.xls");


        }

    public function import(Request $request) 
        {
          $fileName = "jurnalsms_".time().".".$request->file->getClientOriginalExtension();
          $request->file->move(public_path("upload"), $fileName);
          
          Excel::import(new JurnalsmsImport, public_path("upload")."/".$fileName);

          
            $jurnalsms= Jurnalsms::where("company_id",session("company_id"))
                                                     ->paginate($request->pageLength,['page'=>$request->page]);
         
            return json_encode($jurnalsms);
             
        
           
         }
        
    public function storeExcel() 
        {
            ob_end_clean(); 
            ob_start(); 
            $company_id=session("company_id");
            
            Excel::store((new JurnalsmsExport)->forCompany($company_id), "jurnalsms.xls","public",null,[
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

        // event(new JurnalsmsUpdated());
         $data_verificare_status= Jurnalsms::create([
        "company_id"=>session("company_id"),
        
    	  "nr_contract"=>$request->nr_contract,
    	  "telefon"=>$request->telefon,
    	  "mesaj"=>$request->mesaj,
    	  "status"=>$request->status,
    	  "utilizator"=>$request->utilizator,
    	  "data_operare"=>$request->data_operare,
    	  "data_transmitere"=>$request->data_transmitere,
    	  "catre"=>$request->catre,
    	  "id_site"=>$request->id_site,
    	  "data_verificare_status"=>$request->data_verificare_status,           
        ]);
          $this->checkStatusSMS();
          $this->sendSMS();
          $this->checkStatusSMS();
        return $data_verificare_status;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Jurnalsms  $jurnalsms
     * @return \Illuminate\Http\Response
     */
    public function show(Jurnalsms $jurnalsms)
    {
        $resp= Jurnalsms::where("id",$jurnalsms->id)
                               ->where("company_id",session("company_id"))
        					   ->get()->first();
                                
        return json_encode($resp);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Jurnalsms  $jurnalsms
     * @return \Illuminate\Http\Response
     */
    public function edit(Jurnalsms $jurnalsms)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Jurnalsms  $jurnalsms
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Jurnalsms $jurnalsms)
    {
        $jurnalsms->update([
    	  "nr_contract"=>$request->nr_contract,
    	  "telefon"=>$request->telefon,
    	  "mesaj"=>$request->mesaj,
    	  "status"=>$request->status,
    	  "utilizator"=>$request->utilizator,
    	  "data_operare"=>$request->data_operare,
    	  "data_transmitere"=>$request->data_transmitere,
    	  "catre"=>$request->catre,
    	  "id_site"=>$request->id_site,
    	  "data_verificare_status"=>$request->data_verificare_status,
        ]);
       // event(new JurnalsmsUpdated());
        return $jurnalsms;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Jurnalsms  $jurnalsms
     * @return \Illuminate\Http\Response
     */
    public function destroy(Jurnalsms $jurnalsms)
    {
        $jurnalsms->delete();
      //  event(new JurnalsmsUpdated());

    }
}