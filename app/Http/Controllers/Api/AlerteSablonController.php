<?php
namespace App\Http\Controllers\Api;



use App\Exports\CentralizatorClientiAcceptati;
use App\Exports\SablonExport;
use App\Exports\SablonMultipleSheetsExport;
use App\Http\Controllers\Controller;
use App\Mail\AlertaEroareEmail;
use App\Mail\AlertaSablonEmail;
use App\Mail\AlertaSablonCuAtasamentEmail;
use App\Mail\AlertaEmail;
use Illuminate\Support\Facades\Mail;
use App\Mail\VerificareDosarInstantaEmail;
use App\Models\Arhivaclientiacceptati;
use App\Models\Cererideexecutare;
use App\Models\Company;
use App\Models\Emailalerte;
use App\Models\Gestiune;
use App\Models\Perioadablocata;
use App\Models\Licitatii;
use App\Models\Sarbatorilegale;
use App\Models\Litigiicaleatac;
use App\Models\Litigiiparti;
use App\Models\Litigiisedinte;
use App\Models\Litigiu;
use App\Models\Antetincasaricontracte;
use App\Models\Incasaricontracte;
use App\Models\Ordinedeblocareanafhtml;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Models\Partisolicitare;
use App\Models\Registrusesizari;
use App\Models\Nrtelefonsms;
use App\Models\Jurnalsms;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Mail\AlertaIncasariEmail;
use Illuminate\Support\Facades\File;
use Maatwebsite\Excel\Facades\Excel;
use Sunra\PhpSimple\HtmlDomParser;


class AlerteSablonController extends Controller
{
     public function smsClientiRestantieri()
    {   
         DB::beginTransaction();
        try{
            $data=Carbon::today()->format("Y-m-d");

            if(!esteZiLucratoare(Carbon::today())){
                return false;
            }
            //$dataScadenta=adaugaZileLucratoare($data, 2);
            $contracte=collect(DB::select(DB::raw("SELECT scadente_neincasate.*
                                FROM 
                                (
                                 SELECT solduriclienti.nr_contract, solduriclienti.partener as nume, solduriclienti.data_scadenta, Round(Sum(coalesce(rata,0)+coalesce(dob_rem,0)+coalesce(dob_rem_reesalonata,0)),2) AS total_rata, contracte.cod_plata, contracte.disc_dob_rem
                                    FROM solduriclienti INNER JOIN contracte ON solduriclienti.nr_contract = contracte.nr_contract
                                    WHERE (((contracte.status) Not Like 'Finalizat')) and (((contracte.status) Not Like 'Executare'))
                                    GROUP BY solduriclienti.nr_contract, solduriclienti.partener, solduriclienti.data_scadenta, contracte.cod_plata, contracte.disc_dob_rem
                                    HAVING (((Round(Sum(coalesce(rata,0)+coalesce(dob_rem,0)+coalesce(dob_rem_reesalonata,0)),2))>0.01))
   
                                ) as scadente_neincasate INNER JOIN 
                                (
                                    SELECT scadente_neincasate.nr_contract, Min(scadente_neincasate.data_scadenta) AS MinOfdata_scadenta
                                    FROM 
                                     (
                                         SELECT solduriclienti.nr_contract, solduriclienti.partener, solduriclienti.data_scadenta, Round(Sum(coalesce(rata,0)+coalesce(dob_rem,0)+coalesce(dob_rem_reesalonata,0)),2) AS total_rata, contracte.cod_plata, contracte.disc_dob_rem
                                        FROM solduriclienti INNER JOIN contracte ON solduriclienti.nr_contract = contracte.nr_contract
                                        WHERE (((contracte.status) Not Like 'Finalizat')) and (((contracte.status) Not Like 'Executare'))
                                        GROUP BY solduriclienti.nr_contract, solduriclienti.partener, solduriclienti.data_scadenta, contracte.cod_plata, contracte.disc_dob_rem
                                        HAVING (((Round(Sum(coalesce(rata,0)+coalesce(dob_rem,0)+coalesce(dob_rem_reesalonata,0)),2))>0.01))
   
                                    ) as scadente_neincasate
                                    GROUP BY scadente_neincasate.nr_contract
                                    HAVING ((Not (Min(scadente_neincasate.data_scadenta)) Is Null))

                                ) 
                                AS prima_scadenta_restanta ON (scadente_neincasate.data_scadenta = prima_scadenta_restanta.MinOfdata_scadenta) AND (scadente_neincasate.nr_contract = prima_scadenta_restanta.nr_contract)
                                WHERE (((prima_scadenta_restanta.MinOfdata_scadenta)<'".$data."'));")));
                $records=[];
                $dataOperarii=Carbon::now();
                foreach($contracte as $contract){
                    $nrtelefoane=Nrtelefonsms::where("nr_contract",$contract->nr_contract)
                                              ->whereNotNull("nr_telefon")->get();
                    foreach($nrtelefoane as $telefon){
                        $linie= new \StdClass();
                        $linie->company_id=1;
                        $linie->nr_contract=$contract->nr_contract;
                        $linie->catre=$contract->nume;
                        $linie->telefon=$telefon->nr_telefon;
                        $linie->status="In curs de transmitere";
                        $linie->utilizator="ROBOT";
                        $linie->data_operare=$dataOperarii;
                        $linie->mesaj="Va informam ca figurati in evidentele noastre cu debite restante.Va rugam sa aveti amabilitatea sa achitati in cel mai scurt timp.Va multumim!";
                        
                        array_push($records,$linie);
                      //  Jurnalsms::create((array) $linie);       DE ACTIVAT CAND SE VOR TRANSMITE SMS -urile
                    }                          
                }
            $adreseEmail=collect(DB::select(DB::raw("SELECT users.email
                                        FROM (notificationuser INNER JOIN users ON notificationuser.user_id = users.id) INNER JOIN notificationtype ON notificationuser.notificationtype_id = notificationtype.id
                                        WHERE (((notificationtype.denumire)='SMS Clienti restantieri'));")))->pluck("email");
       
        
            
        
        
             $antetTabel=[
                            
                            ['col'=>'nr_contract','denumire'=>'Nr contract','type'=>'','align'=>'center','width'=>'10%'],
                            ['col'=>'catre','denumire'=>'Destinatar','type'=>'','align'=>'center','width'=>'10%'],
                            ['col'=>'telefon','denumire'=>'Telefon','type'=>'','align'=>'center','width'=>'10%'],
                            ['col'=>'mesaj','denumire'=>'Mesaj','type'=>'','align'=>'center','width'=>'10%'],
                            
                            ];
                
            $tabel=collect($records);
            $groupBy=[
                                                           
                           ];   
            $totalBy=[
            
                          ];

            $titluRaport="DianaIFNWeb SMS Clienti restantieri ".dateFormatAfisare($data)." (".count($records)." sms-uri)";
            $company=Company::get()->first();
             
            $i=1;
            
          
               $company_id=$company->id;
             
                 $titluSheet="SMS Clienti restantieri";
                
                $columnFormat=[
                                 // 'D' => NumberFormat::FORMAT_DATE_DDMMYYYY,
                                 // 'I' => NumberFormat::FORMAT_CURRENCY_EUR_SIMPLE
                               ];
               
               
           $numefis='public/'.$company->slug.'/sms_clienti_restantieri_'.time().".xls";      
          Excel::store( 
             (new SablonExport)->forCompany($company_id,$titluSheet,$tabel,$antetTabel,$groupBy,$totalBy,$columnFormat,$titluRaport),$numefis);
              
           Mail::to($adreseEmail)
                 ->send(
                    new AlertaSablonEmail(
                            [],
                            $titluRaport,
                            [],
                            $groupBy,
                            $totalBy,
                            $i,
                            $company,
                            $numefis
                        )
                 );        
           DB::commit();
        } catch (\Exception $e) {
            DB::rollback(); 
            $user=User::where("email","stefan.voinea@gmail.com")->first();
            Mail::to("stefan.voinea@gmail.com")
            ->send(new AlertaEroareEmail("SMS CLIENTI RESTANTIERI  ",$e->getMessage(),$e,$user));
            return response()->json(['message' => $e->getMessage()], 500);
    }   
          
    }
    public function smsInformareScadenta()
    {   
         DB::beginTransaction();
        try{
            $data=Carbon::today()->format("Y-m-d");

            if(!esteZiLucratoare(Carbon::today())){
                return false;
            }
            $dataScadenta=adaugaZileLucratoare($data, 2);
            $contracte=collect(DB::select(DB::raw("SELECT scadente_neincasate.*
                                FROM 
                                (
                                 SELECT solduriclienti.nr_contract, solduriclienti.partener as nume, solduriclienti.data_scadenta, Round(Sum(coalesce(rata,0)+coalesce(dob_rem,0)+coalesce(dob_rem_reesalonata,0)),2) AS total_rata, contracte.cod_plata, contracte.disc_dob_rem
                                    FROM solduriclienti INNER JOIN contracte ON solduriclienti.nr_contract = contracte.nr_contract
                                    WHERE (((contracte.status) Not Like 'Finalizat'))
                                    GROUP BY solduriclienti.nr_contract, solduriclienti.partener, solduriclienti.data_scadenta, contracte.cod_plata, contracte.disc_dob_rem
                                    HAVING (((Round(Sum(coalesce(rata,0)+coalesce(dob_rem,0)+coalesce(dob_rem_reesalonata,0)),2))>0.01))
   
                                ) as scadente_neincasate INNER JOIN 
                                (
                                    SELECT scadente_neincasate.nr_contract, Min(scadente_neincasate.data_scadenta) AS MinOfdata_scadenta
                                    FROM 
                                     (
                                         SELECT solduriclienti.nr_contract, solduriclienti.partener, solduriclienti.data_scadenta, Round(Sum(coalesce(rata,0)+coalesce(dob_rem,0)+coalesce(dob_rem_reesalonata,0)),2) AS total_rata, contracte.cod_plata, contracte.disc_dob_rem
                                        FROM solduriclienti INNER JOIN contracte ON solduriclienti.nr_contract = contracte.nr_contract
                                        WHERE (((contracte.status) Not Like 'Finalizat'))
                                        GROUP BY solduriclienti.nr_contract, solduriclienti.partener, solduriclienti.data_scadenta, contracte.cod_plata, contracte.disc_dob_rem
                                        HAVING (((Round(Sum(coalesce(rata,0)+coalesce(dob_rem,0)+coalesce(dob_rem_reesalonata,0)),2))>0.01))
   
                                    ) as scadente_neincasate
                                    GROUP BY scadente_neincasate.nr_contract
                                    HAVING ((Not (Min(scadente_neincasate.data_scadenta)) Is Null))

                                ) 
                                AS prima_scadenta_restanta ON (scadente_neincasate.data_scadenta = prima_scadenta_restanta.MinOfdata_scadenta) AND (scadente_neincasate.nr_contract = prima_scadenta_restanta.nr_contract)
                                WHERE (((prima_scadenta_restanta.MinOfdata_scadenta)='".$dataScadenta."'));")));
                $records=[];
                $dataOperarii=Carbon::now();
                foreach($contracte as $contract){
                    $nrtelefoane=Nrtelefonsms::where("nr_contract",$contract->nr_contract)
                                              ->whereNotNull("nr_telefon")->get();
                    foreach($nrtelefoane as $telefon){
                        $linie= new \StdClass();
                        $linie->company_id=1;
                        $linie->nr_contract=$contract->nr_contract;
                        $linie->catre=$contract->nume;
                        $linie->telefon=$telefon->nr_telefon;
                        $linie->status="In curs de transmitere";
                        $linie->utilizator="ROBOT";
                        $linie->data_operare=$dataOperarii;
                        if(nz($contract->disc_dob_rem,0)!=0){
                          $linie->mesaj="Rata aferenta lunii " . lunainlitere(Carbon::parse($contract->data_scadenta)->month) . " este scadenta in " . dateFormatAfisare($contract->data_scadenta). ". Pentru a evita penalitatile, va rugam sa achitati in termen.Pentru toate platile efectuate prin transfer bancar, va rugam sa mentionati obligatoriu codul de plata " .$contract->cod_plata . ". Lipsa acestui element poate conduce la intarzierea sau imposibilitatea procesarii corecte a platii. Va multumim!";  
                        }else{
                            $linie->mesaj="Rata aferenta lunii " . lunainlitere(Carbon::parse($contract->data_scadenta)->month) . " este scadenta in " . dateFormatAfisare($contract->data_scadenta). ". Va rugam sa achitati in termen.Pentru toate platile efectuate prin transfer bancar, va rugam sa mentionati obligatoriu codul de plata " .$contract->cod_plata . ". Lipsa acestui element poate conduce la intarzierea sau imposibilitatea procesarii corecte a platii. Va multumim!";  
                        }
                        array_push($records,$linie);
                      //  Jurnalsms::create((array) $linie);       DE ACTIVAT CAND SE VOR TRANSMITE SMS -urile
                    }                          
                }
            $adreseEmail=collect(DB::select(DB::raw("SELECT users.email
                                        FROM (notificationuser INNER JOIN users ON notificationuser.user_id = users.id) INNER JOIN notificationtype ON notificationuser.notificationtype_id = notificationtype.id
                                        WHERE (((notificationtype.denumire)='SMS Informare scadenta'));")))->pluck("email");
       
        
            
        
        
             $antetTabel=[
                            
                            ['col'=>'nr_contract','denumire'=>'Nr contract','type'=>'','align'=>'center','width'=>'10%'],
                            ['col'=>'catre','denumire'=>'Destinatar','type'=>'','align'=>'center','width'=>'10%'],
                            ['col'=>'telefon','denumire'=>'Telefon','type'=>'','align'=>'center','width'=>'10%'],
                            ['col'=>'mesaj','denumire'=>'Mesaj','type'=>'','align'=>'center','width'=>'10%'],
                            
                            ];
                
            $tabel=collect($records);
            $groupBy=[
                                                           
                           ];   
            $totalBy=[
            
                          ];

            $titluRaport="DianaIFNWeb SMS informare scadenta ".dateFormatAfisare($dataScadenta)." (".count($records)." sms-uri)";
            $company=Company::get()->first();
             
            $i=1;
            
          
               $company_id=$company->id;
             
                 $titluSheet="SMS informare scadenta";
                
                $columnFormat=[
                                 // 'D' => NumberFormat::FORMAT_DATE_DDMMYYYY,
                                 // 'I' => NumberFormat::FORMAT_CURRENCY_EUR_SIMPLE
                               ];
               
               
           $numefis='public/'.$company->slug.'/sms_informare_scadenta_'.time().".xls";      
          Excel::store( 
             (new SablonExport)->forCompany($company_id,$titluSheet,$tabel,$antetTabel,$groupBy,$totalBy,$columnFormat,$titluRaport),$numefis);
              
           Mail::to($adreseEmail)
                 ->send(
                    new AlertaSablonEmail(
                            [],
                            $titluRaport,
                            [],
                            $groupBy,
                            $totalBy,
                            $i,
                            $company,
                            $numefis
                        )
                 );        
           DB::commit();
        } catch (\Exception $e) {
            DB::rollback(); 
            $user=User::where("email","stefan.voinea@gmail.com")->first();
            Mail::to("stefan.voinea@gmail.com")
            ->send(new AlertaEroareEmail("SMS INFORMARE SCADENTA  ",$e->getMessage(),$e,$user));
            return response()->json(['message' => $e->getMessage()], 500);
    }   
          
    }
    public function alerteperioadedeprobadevalidat()
    {
            
     try{
        
        
            $date=collect(DB::select(DB::raw("select perioadedeproba.luna, perioadedeproba.anul, perioadedeproba.nume, perioadedeproba.data_start, perioadedeproba.data_final, perioadedeproba.status_validare
                                                from perioadedeproba
                                                where (((perioadedeproba.status_validare)='In asteptare'));")));

         
         
        $adreseEmail=collect(DB::select(DB::raw("SELECT users.email
                                        FROM (notificationuser INNER JOIN users ON notificationuser.user_id = users.id) INNER JOIN notificationtype ON notificationuser.notificationtype_id = notificationtype.id
                                        WHERE (((notificationtype.denumire)='Perioade de proba de validat'));")))->pluck("email");
       
        
            
        
        
             $antetTabel=[
                            
                            ['col'=>'luna','denumire'=>'Luna','type'=>'','align'=>'center','width'=>'10%'],
                            ['col'=>'anul','denumire'=>'Anul','type'=>'','align'=>'center','width'=>'10%'],
                            ['col'=>'nume','denumire'=>'Nume','type'=>'','align'=>'center','width'=>'10%'],
                            ['col'=>'data_start','denumire'=>'Data start','type'=>'','align'=>'center','width'=>'10%'],
                            ['col'=>'data_final','denumire'=>'Data final','type'=>'','align'=>'center','width'=>'10%'],
                            ['col'=>'status_validare','denumire'=>'Status validare','type'=>'','align'=>'center','width'=>'10%']
                            ];
                
            $tabel=collect($date);
            $groupBy=[
                                                           
                           ];   
            $totalBy=[
            
                          ];

            $titluRaport="DianaIFNWeb Alerta Perioade de proba de validat  (".count($date)." cazuri)";
            $company=Company::get()->first();
             
            $i=1;
            
          
               $company_id=$company->id;
             
                 $titluSheet="Perioade de proba de validat";
                
                $columnFormat=[
                                 // 'D' => NumberFormat::FORMAT_DATE_DDMMYYYY,
                                 // 'I' => NumberFormat::FORMAT_CURRENCY_EUR_SIMPLE
                               ];
               
               
           $numefis='public/'.$company->slug.'/perioade_de_proba_de_validat_'.time().".xls";      
          Excel::store( 
             (new SablonExport)->forCompany($company_id,$titluSheet,$tabel,$antetTabel,$groupBy,$totalBy,$columnFormat,$titluRaport),$numefis);
              
           Mail::to($adreseEmail)
                 ->send(
                    new AlertaSablonEmail(
                            $antetTabel,
                            $titluRaport,
                            $tabel,
                            $groupBy,
                            $totalBy,
                            $i,
                            $company,
                            $numefis
                        )
                 );
            
        
        
      } catch (\Exception $e) {
        
        $user=User::where("email","stefan.voinea@gmail.com")->first();
        Mail::to("stefan.voinea@gmail.com")
        ->send(new AlertaEroareEmail("ALERTA PERIOADE DE PROBA DE VALIDAT  ",$e->getMessage(),$e,$user));
        return response()->json(['message' => $e->getMessage()], 500);
      }   
          
    }
     public function alerteevenimentedevalidat()
    {
          
     try{
        
        
            $date=collect(DB::select(DB::raw("SELECT nomevenimente.email, evenimente.nume, evenimente.eveniment, evenimente.data_intrarii, evenimente.data_iesirii 
                FROM evenimente, nomevenimente  WHERE evenimente.status = 'In asteptare' And nomevenimente.denumire = eveniment 
                ORDER BY nomevenimente.email ;")));

         
         
        $adreseEmail=collect(DB::select(DB::raw("SELECT users.email
                                        FROM (notificationuser INNER JOIN users ON notificationuser.user_id = users.id) INNER JOIN notificationtype ON notificationuser.notificationtype_id = notificationtype.id
                                        WHERE (((notificationtype.denumire)='Evenimente de validat'));")))->pluck("email");
       
        
            foreach($date->groupBy("email") as $eveniment){
        
        
             $antetTabel=[
                            
                            ['col'=>'email','denumire'=>'Email','type'=>'','align'=>'center','width'=>'10%'],
                            ['col'=>'nume','denumire'=>'Nume','type'=>'','align'=>'center','width'=>'10%'],
                            ['col'=>'eveniment','denumire'=>'Eveniment','type'=>'','align'=>'center','width'=>'10%'],
                            ['col'=>'data_intrarii','denumire'=>'Data intrarii','type'=>'','align'=>'center','width'=>'10%'],
                            ['col'=>'data_iesirii','denumire'=>'Data iesirii','type'=>'','align'=>'center','width'=>'10%']
                            ];
                
            $tabel=collect($date);
            $groupBy=[
                                                           
                           ];   
            $totalBy=[
            
                          ];

            $titluRaport="DianaIFNWeb Alerta Evenimente de validat  (".count($eveniment)." cazuri)";
            $company=Company::get()->first();
             
            $i=1;
            
          
               $company_id=$company->id;
             
                 $titluSheet="Evenimente de validat";
                
                $columnFormat=[
                                 // 'D' => NumberFormat::FORMAT_DATE_DDMMYYYY,
                                 // 'I' => NumberFormat::FORMAT_CURRENCY_EUR_SIMPLE
                               ];
               
               
           $numefis='public/'.$company->slug.'/evenimente_de_validat_'.time().".xls";      
          Excel::store( 
             (new SablonExport)->forCompany($company_id,$titluSheet,$tabel,$antetTabel,$groupBy,$totalBy,$columnFormat,$titluRaport),$numefis);
              
           Mail::to($tabel[0]->email)   
                 ->cc($adreseEmail)
                 ->send(
                    new AlertaSablonEmail(
                            $antetTabel,
                            $titluRaport,
                            $tabel,
                            $groupBy,
                            $totalBy,
                            $i,
                            $company,
                            $numefis
                        )
                 );
            }
        
        
      } catch (\Exception $e) {
        
        $user=User::where("email","stefan.voinea@gmail.com")->first();
        Mail::to("stefan.voinea@gmail.com")
        ->send(new AlertaEroareEmail("ALERTA EVENIMENTE DE VALIDAT  ",$e->getMessage(),$e,$user));
        return response()->json(['message' => $e->getMessage()], 500);
      }   
          
    }
     public function alertescadentepoliteasigurare()
    {
          
     try{
        $data=Carbon::today()->addDays(1)->format("Y-m-d");
        $dataInceputLunaUrmatoare=Carbon::today()->endOfMonth()->addDays(1)->format("Y-m-d");
        $dataSfarsitLunaUrmatoare=Carbon::parse($dataInceputLunaUrmatoare)->endOfMonth()->format("Y-m-d");
        $dataZiLucratoare=ultimazilucratoare($data,'2000-01-01');
        
            $stoparicreante=collect(DB::select(DB::raw("select politeasig.nr_polita, politeasig.data_polita, politeasig.datascadenta as data_expirarii, politeasig.agentia, politeasig.nr_contract, politeasig.data_contract, politeasig.nume, politeasig.asigurator, politeasig.suma_asigurata, politeasig.sumaanuala as valoare_polita_in_lei, politeasig.curs, politeasig.suma_valuta as valoare_polita_in_eur 
                from politeasig inner join contracte on politeasig.nr_contract = contracte.nr_contract
                where politeasig.datascadenta>= '".$dataInceputLunaUrmatoare."' and politeasig.datascadenta<= '".$dataSfarsitLunaUrmatoare."'  and politeasig.data_reinnoirii is null and contracte.status not like 'Finalizat';")));

         
         
        $adreseEmail=collect(DB::select(DB::raw("SELECT users.email
                                        FROM (notificationuser INNER JOIN users ON notificationuser.user_id = users.id) INNER JOIN notificationtype ON notificationuser.notificationtype_id = notificationtype.id
                                        WHERE (((notificationtype.denumire)='Scadente polite asigurare'));")))->pluck("email");
       
        

        
        
             $antetTabel=[
                            
                            ['col'=>'nr_polita','denumire'=>'Nr polita','type'=>'','align'=>'center','width'=>'10%'],
                            ['col'=>'data_polita','denumire'=>'Data polita','type'=>'Date','align'=>'center','width'=>'10%'],
                            ['col'=>'data_expirarii','denumire'=>'Data expirarii','type'=>'Date','align'=>'center','width'=>'10%'],
                            ['col'=>'agentia','denumire'=>'Agentia','type'=>'','align'=>'left','width'=>'10%'],
                            ['col'=>'nr_contract','denumire'=>'Nr contract','type'=>'','align'=>'center','width'=>'10%'],
                            ['col'=>'data_contract','denumire'=>'Data contract','type'=>'Date','align'=>'center','width'=>'10%'],
                            ['col'=>'nume','denumire'=>'Nume','type'=>'','align'=>'left','width'=>'10%'],
                            ['col'=>'asigurator','denumire'=>'Asigurator','type'=>'','align'=>'left','width'=>'10%'],
                            ['col'=>'suma_asigurata','denumire'=>'Suma asigurata','type'=>'','align'=>'center','width'=>'10%'],
                            ['col'=>'valoare_polita_in_lei','denumire'=>'Valoare polita in lei','type'=>'','align'=>'center','width'=>'10%'],
                            ['col'=>'curs','denumire'=>'Curs','type'=>'','align'=>'center','width'=>'10%'],
                            ['col'=>'valoare_polita_in_eur','denumire'=>'Valoare polita in eur','type'=>'','align'=>'center','width'=>'10%']
                            ];
             foreach($stoparicreante->groupBy("agentia") as $filtrat){
            $agentia=Gestiune::where("denumire",$filtrat[0]->agentia)->first();   
            $tabel=collect($filtrat);
            $groupBy=[
                                                           
                           ];   
            $totalBy=[
            
                          ];

            $titluRaport="DianaIFNWeb Alerta Scadente polite asigurare  (".count($filtrat)." cazuri) in data de ".dateFormatAfisare($data);
            $company=Company::get()->first();
             
            $i=1;
            
          
               $company_id=$company->id;
             
                 $titluSheet="Scadente polite asigurare";
                
                $columnFormat=[
                                 // 'D' => NumberFormat::FORMAT_DATE_DDMMYYYY,
                                 // 'I' => NumberFormat::FORMAT_CURRENCY_EUR_SIMPLE
                               ];
               
               
           $numefis='public/'.$company->slug.'/scadente_polite asigurare_'.time().".xls";      
          Excel::store( 
             (new SablonExport)->forCompany($company_id,$titluSheet,$tabel,$antetTabel,$groupBy,$totalBy,$columnFormat,$titluRaport),$numefis);
              
           Mail::to($adreseEmail)
                ->cc($agentia->email)
                 ->send(
                    new AlertaSablonEmail(
                            $antetTabel,
                            $titluRaport,
                            $tabel,
                            $groupBy,
                            $totalBy,
                            $i,
                            $company,
                            $numefis
                        )
                 );
         }
        
        
      } catch (\Exception $e) {
        
        $user=User::where("email","stefan.voinea@gmail.com")->first();
        Mail::to("stefan.voinea@gmail.com")
        ->send(new AlertaEroareEmail("ALERTA SCADENTE POLITE ASIGURARE  ",$e->getMessage(),$e,$user));
        return response()->json(['message' => $e->getMessage()], 500);
      }   
          
    }
     public function alertescadentefinalelunare()
    {
        
     try{
        $data=Carbon::today()->addDays(1)->format("Y-m-d");
        $dataInceputLunaUrmatoare=Carbon::today()->endOfMonth()->addDays(1)->format("Y-m-d");
        $dataSfarsitPesteTreiLuni=Carbon::today()->endOfMonth()->addMonths(3)->endOfMonth()->format("Y-m-d");
        $dataZiLucratoare=ultimazilucratoare($data,'2000-01-01');
        
            $stoparicreante=collect(DB::select(DB::raw("SELECT contracte.agentia, contracte.nr_contract, contracte.data_contract, contracte.data_acordarii, contracte.nume, ultimele_scadente.MaxOfScadenta_reala AS ultima_scadenta, contracte.scadenta_finala_sg AS scadenta_finala_sg, contracte.tip_valuta, Round(coalesce(sumofrata,0),2) AS sold_credit, contracte.status
                FROM ((

                    (
                        select max(graficrambursare.scadenta_reala) as maxofscadenta_reala, graficrambursare.agentia, graficrambursare.nr_contract, graficrambursare.data_contract, contracte.nume
                        from graficrambursare inner join contracte on graficrambursare.nr_contract=contracte.nr_contract
                        where (((contracte.status) not like 'Finalizat%'))
                        group by graficrambursare.agentia, graficrambursare.nr_contract, graficrambursare.data_contract, contracte.nume
                    )
                    AS ultimele_scadente RIGHT JOIN contracte ON ultimele_scadente.nr_contract = contracte.nr_contract) LEFT JOIN 
                (
                    select ultima_stopare_scadente_finale.nr_contract, ultima_stopare_scadente_finale.maxofdata
                    from 
                    (
                        select antetacteaditionale.nr_contract, detaliuacteaditionale.tip_modificare, max(detaliuacteaditionale.data) as maxofdata
                        from antetacteaditionale inner join detaliuacteaditionale on antetacteaditionale.id=detaliuacteaditionale.antetacteaditionale_id
                        group by antetacteaditionale.nr_contract, detaliuacteaditionale.tip_modificare
                        having (((detaliuacteaditionale.tip_modificare) like '%stopare creanta%'))
                    )
                    AS ultima_stopare_scadente_finale left join 
                    (
                        select antetacteaditionale.nr_contract, detaliuacteaditionale.tip_modificare, max(detaliuacteaditionale.data) as maxofdata
                        from antetacteaditionale inner join detaliuacteaditionale on antetacteaditionale.id=detaliuacteaditionale.antetacteaditionale_id
                        group by antetacteaditionale.nr_contract, detaliuacteaditionale.tip_modificare
                        having (((detaliuacteaditionale.tip_modificare) like '%repunere credit pe curent%'))
                    )
                    AS ultima_repunere_pe_curent_scadente_finale on ultima_stopare_scadente_finale.nr_contract=ultima_repunere_pe_curent_scadente_finale.nr_contract
                    where (((ultima_repunere_pe_curent_scadente_finale.maxofdata) is null)) or (((ultima_repunere_pe_curent_scadente_finale.maxofdata)<ultima_stopare_scadente_finale.maxofdata))
                    group by ultima_stopare_scadente_finale.nr_contract, ultima_stopare_scadente_finale.maxofdata
                ) 
                AS stopari_creante_scadente_finale ON ultimele_scadente.nr_contract = stopari_creante_scadente_finale.nr_contract) LEFT JOIN 
                (
                    select solduriclienti.nr_contract, sum(round(coalesce(rata,0),2)) as sumofrata
                    from solduriclienti
                    group by solduriclienti.nr_contract
                )
                AS total_solduri_scadente_finale ON ultimele_scadente.nr_contract = total_solduri_scadente_finale.nr_contract
                WHERE (ultimele_scadente.MaxOfScadenta_reala>='".$dataInceputLunaUrmatoare."' And ultimele_scadente.MaxOfScadenta_reala<='".$dataSfarsitPesteTreiLuni."' AND contracte.status Not Like 'Finalizat' AND stopari_creante_scadente_finale.nr_contract Is Null) OR 
                    (contracte.scadenta_finala_sg>='".$dataInceputLunaUrmatoare."' And contracte.scadenta_finala_sg<'".$dataSfarsitPesteTreiLuni."' AND contracte.status Not Like 'Finalizat' AND stopari_creante_scadente_finale.nr_contract Is Null)
                ORDER BY contracte.agentia, ultimele_scadente.MaxOfScadenta_reala;")));

         
         
        $adreseEmail=collect(DB::select(DB::raw("SELECT users.email
                                        FROM (notificationuser INNER JOIN users ON notificationuser.user_id = users.id) INNER JOIN notificationtype ON notificationuser.notificationtype_id = notificationtype.id
                                        WHERE (((notificationtype.denumire)='Scadente finale lunare'));")))->pluck("email");
       
        

        
        
             $antetTabel=[
                            
                         ['col'=>'agentia','denumire'=>'Agentia','type'=>'','align'=>'center','width'=>'10%'],
                        ['col'=>'nr_contract','denumire'=>'Nr contract','type'=>'','align'=>'center','width'=>'10%'],
                        ['col'=>'data_contract','denumire'=>'Data contract','type'=>'Date','align'=>'center','width'=>'10%'],
                        ['col'=>'data_acordarii','denumire'=>'Data acordarii','type'=>'Date','align'=>'center','width'=>'10%'],
                        ['col'=>'nume','denumire'=>'Nume','type'=>'','align'=>'center','width'=>'10%'],
                        ['col'=>'ultima_scadenta','denumire'=>'Ultima scadenta','type'=>'Date','align'=>'center','width'=>'10%'],
                        ['col'=>'scadenta_finala_sg','denumire'=>'Scadenta finala sg','type'=>'Date','align'=>'center','width'=>'10%'],
                        ['col'=>'tip_valuta','denumire'=>'Tip valuta','type'=>'','align'=>'center','width'=>'10%'],
                        ['col'=>'sold_credit','denumire'=>'Sold credit','type'=>'','align'=>'center','width'=>'10%'],
                        ['col'=>'status','denumire'=>'Status','type'=>'','align'=>'center','width'=>'10%']
                            ];
                
            $tabel=collect($stoparicreante);
            $groupBy=[
                                                           
                           ];   
            $totalBy=[
            
                          ];

            $titluRaport="DianaIFNWeb Alerta Scadente finale lunare  (".count($stoparicreante)." cazuri) in data de ".dateFormatAfisare($data);
            $company=Company::get()->first();
             
            $i=1;
            
          
               $company_id=$company->id;
             
                 $titluSheet="Scadente finale lunare";
                
                $columnFormat=[
                                 // 'D' => NumberFormat::FORMAT_DATE_DDMMYYYY,
                                 // 'I' => NumberFormat::FORMAT_CURRENCY_EUR_SIMPLE
                               ];
               
               
           $numefis='public/'.$company->slug.'/scadente_finale_lunare_'.time().".xls";      
          Excel::store( 
             (new SablonExport)->forCompany($company_id,$titluSheet,$tabel,$antetTabel,$groupBy,$totalBy,$columnFormat,$titluRaport),$numefis);
              
           Mail::to($adreseEmail)
                 ->send(
                    new AlertaSablonEmail(
                            $antetTabel,
                            $titluRaport,
                            $tabel,
                            $groupBy,
                            $totalBy,
                            $i,
                            $company,
                            $numefis
                        )
                 );
         
        
        
      } catch (\Exception $e) {
        
        $user=User::where("email","stefan.voinea@gmail.com")->first();
        Mail::to("stefan.voinea@gmail.com")
        ->send(new AlertaEroareEmail("ALERTA SCADENTE FINALE LUNARE  ",$e->getMessage(),$e,$user));
        return response()->json(['message' => $e->getMessage()], 500);
      }   
          
    }
     public function alertescadentefinalezilnice()
    {
         
     try{
        $data=Carbon::today()->addDays(1)->format("Y-m-d");
        $dataZiLucratoare=ultimazilucratoare($data,'2000-01-01');
        if(!esteZiLucratoare(Carbon::today())){
            return false;
        }
            $stoparicreante=collect(DB::select(DB::raw("select contracte.agentia, contracte.nr_contract, contracte.data_contract, contracte.data_acordarii, contracte.nume, ultimele_scadente_zilnice.maxofdata_scadenta as ultima_scadenta, contracte.scadenta_finala_sg as scadenta_finala_sg, contracte.status
                    from (
                    (
                        select max(graficrambursare.data_scadenta) as maxofdata_scadenta, graficrambursare.agentia, graficrambursare.nr_contract, graficrambursare.data_contract, contracte.nume
                        from graficrambursare inner join contracte on graficrambursare.nr_contract = contracte.nr_contract
                        where (((contracte.status) not like 'Finalizat%'))
                        group by graficrambursare.agentia, graficrambursare.nr_contract, graficrambursare.data_contract, contracte.nume
                    )
                    AS ultimele_scadente_zilnice right join contracte on ultimele_scadente_zilnice.nr_contract = contracte.nr_contract) left join 
                    (
                        select ultima_stopare_scadente_finale.nr_contract, ultima_stopare_scadente_finale.maxofdata
                        from 
                        (
                            select antetacteaditionale.nr_contract, detaliuacteaditionale.tip_modificare, max(detaliuacteaditionale.data) as maxofdata
                            from antetacteaditionale inner join detaliuacteaditionale on antetacteaditionale.id=detaliuacteaditionale.antetacteaditionale_id
                            group by antetacteaditionale.nr_contract, detaliuacteaditionale.tip_modificare
                            having (((detaliuacteaditionale.tip_modificare) like '%stopare creanta%'))
                        )
                        AS ultima_stopare_scadente_finale left join 
                        (
                             select antetacteaditionale.nr_contract, detaliuacteaditionale.tip_modificare, max(detaliuacteaditionale.data) as maxofdata
                            from antetacteaditionale inner join detaliuacteaditionale on antetacteaditionale.id=detaliuacteaditionale.antetacteaditionale_id
                            group by antetacteaditionale.nr_contract, detaliuacteaditionale.tip_modificare
                            having (((detaliuacteaditionale.tip_modificare) like '%srepunere credit pe curent%'))
                        )
                        AS ultima_repunere_pe_curent_scadente_finale on ultima_stopare_scadente_finale.nr_contract=ultima_repunere_pe_curent_scadente_finale.nr_contract
                        where (((ultima_repunere_pe_curent_scadente_finale.maxofdata) is null)) or (((ultima_repunere_pe_curent_scadente_finale.maxofdata)<ultima_stopare_scadente_finale.maxofdata))
                        group by ultima_stopare_scadente_finale.nr_contract, ultima_stopare_scadente_finale.maxofdata
                    )
                    AS stopari_creante_scadente_finale on ultimele_scadente_zilnice.nr_contract = stopari_creante_scadente_finale.nr_contract
                    where (((ultimele_scadente_zilnice.maxofdata_scadenta)='".$dataZiLucratoare."' and (ultimele_scadente_zilnice.maxofdata_scadenta) is not null) and ((contracte.status) not like 'Finalizat') and ((stopari_creante_scadente_finale.nr_contract) is null)) or 
                        (((contracte.scadenta_finala_sg)='".$dataZiLucratoare."' and (contracte.scadenta_finala_sg) is not null) and ((contracte.status) not like 'Finalizat') and ((stopari_creante_scadente_finale.nr_contract) is null))
                    order by contracte.agentia, ultimele_scadente_zilnice.maxofdata_scadenta;")));

         
         
        $adreseEmail=collect(DB::select(DB::raw("SELECT users.email
                                        FROM (notificationuser INNER JOIN users ON notificationuser.user_id = users.id) INNER JOIN notificationtype ON notificationuser.notificationtype_id = notificationtype.id
                                        WHERE (((notificationtype.denumire)='Scadente finale zilnice'));")))->pluck("email");
       
        

        
        
             $antetTabel=[
                            
                          ['col'=>'agentia','denumire'=>'Agentia','type'=>'','align'=>'center','width'=>'10%'],
                        ['col'=>'nr_contract','denumire'=>'Nr contract','type'=>'','align'=>'center','width'=>'10%'],
                        ['col'=>'data_contract','denumire'=>'Data contract','type'=>'Date','align'=>'center','width'=>'10%'],
                        ['col'=>'data_acordarii','denumire'=>'Data acordarii','type'=>'Date','align'=>'center','width'=>'10%'],
                        ['col'=>'nume','denumire'=>'Nume','type'=>'','align'=>'center','width'=>'10%'],
                        ['col'=>'ultima_scadenta','denumire'=>'Ultima scadenta','type'=>'Date','align'=>'center','width'=>'10%'],
                        ['col'=>'scadenta_finala_sg','denumire'=>'Scadenta finala sg','type'=>'Date','align'=>'center','width'=>'10%'],
                        ['col'=>'status','denumire'=>'Status','type'=>'','align'=>'center','width'=>'10%']
                            ];
                
            $tabel=collect($stoparicreante);
            $groupBy=[
                                                           
                           ];   
            $totalBy=[
            
                          ];

            $titluRaport="DianaIFNWeb Alerta Scadente finale zilnice  (".count($stoparicreante)." cazuri) in data de ".dateFormatAfisare($data);
            $company=Company::get()->first();
             
            $i=1;
            
          
               $company_id=$company->id;
             
                 $titluSheet="Scadente finale zilnice";
                
                $columnFormat=[
                                 // 'D' => NumberFormat::FORMAT_DATE_DDMMYYYY,
                                 // 'I' => NumberFormat::FORMAT_CURRENCY_EUR_SIMPLE
                               ];
               
               
           $numefis='public/'.$company->slug.'/scadente_finale_zilnice_'.time().".xls";      
          Excel::store( 
             (new SablonExport)->forCompany($company_id,$titluSheet,$tabel,$antetTabel,$groupBy,$totalBy,$columnFormat,$titluRaport),$numefis);
              
           Mail::to($adreseEmail)
                 ->send(
                    new AlertaSablonEmail(
                            $antetTabel,
                            $titluRaport,
                            $tabel,
                            $groupBy,
                            $totalBy,
                            $i,
                            $company,
                            $numefis
                        )
                 );
         
        
        
      } catch (\Exception $e) {
        
        $user=User::where("email","stefan.voinea@gmail.com")->first();
        Mail::to("stefan.voinea@gmail.com")
        ->send(new AlertaEroareEmail("ALERTA SCADENTE FINALE ZILNICE  ",$e->getMessage(),$e,$user));
        return response()->json(['message' => $e->getMessage()], 500);
      }   
          
    }
    public function alerteverificarenotificarioug50()
    {
            
     try{
        $data=Carbon::today()->format("Y-m-d");
        $dataminus30=Carbon::today()->addDays(-30)->format("Y-m-d");
        $dataminus10=Carbon::today()->addDays(-10)->format("Y-m-d");
        Log::info("Alerteverificarenotificarioug50 PAS 1");
            $stoparicreante=collect(DB::select(DB::raw("SELECT
    v.agentia,
    v.nr_contract,
    v.data_contract,
    v.nume,
    v.urmatoarea_scadenta,
    v.zile_intarziere,
    CASE
        WHEN v.zile_intarziere BETWEEN 25 AND 30 THEN 'Instiintare de plata'
        ELSE 'Comunicare de plata'
    END AS tip_notificare,
    c.tip_act AS notificare_transmisa,
    CASE
        WHEN c.tip_act IS NULL THEN 'Nu'
        WHEN (
            CASE
                WHEN v.zile_intarziere BETWEEN 25 AND 30 THEN 'Instiintare de plata'
                ELSE 'Comunicare de plata'
            END
        ) <> c.tip_act THEN 'Eroare'
        ELSE 'Da'
    END AS ok,
    uc.max_data_document AS data_corespondenta
FROM
(
    SELECT
        contracte.agentia,
        contracte.nr_contract,
        contracte.data_contract,
        contracte.nume,
        psr.min_data_scadenta AS urmatoarea_scadenta,
        DATEDIFF('".$data."', psr.min_data_scadenta) AS zile_intarziere
    FROM contracte
    INNER JOIN
    (
        SELECT
            sps.nr_contract,
            MIN(sps.data_scadenta) AS min_data_scadenta
        FROM
        (
            SELECT
                solduriclienti.nr_contract,
                solduriclienti.data_scadenta,
                ROUND(SUM(
                    COALESCE(rata,0) +
                    COALESCE(dob_rem,0) +
                    COALESCE(dob_rem_reesalonata,0)
                ), 2) AS sold
            FROM solduriclienti
            WHERE solduriclienti.data_incasarii IS NULL
               OR solduriclienti.data_incasarii <= '".$data."'
            GROUP BY
                solduriclienti.nr_contract,
                solduriclienti.data_scadenta
            HAVING ROUND(SUM(
                COALESCE(rata,0) +
                COALESCE(dob_rem,0) +
                COALESCE(dob_rem_reesalonata,0)
            ), 2) > 0.01
        ) sps
        GROUP BY sps.nr_contract
    ) psr ON contracte.nr_contract = psr.nr_contract
    WHERE
        contracte.data_contract < '2016-09-30'
        AND contracte.status NOT IN ('Finalizat', 'Anulat', 'Retragere')
        AND (
            DATEDIFF('".$data."', psr.min_data_scadenta) BETWEEN 5 AND 10
            OR
            DATEDIFF('".$data."', psr.min_data_scadenta) BETWEEN 25 AND 30
        )
) v
LEFT JOIN
(
    SELECT
        c.nr_contract,
        MAX(c.data_document) AS max_data_document
    FROM corespondenta c
    INNER JOIN
    (
        SELECT
            contracte.nr_contract,
            psr.min_data_scadenta AS urmatoarea_scadenta
        FROM contracte
        INNER JOIN
        (
            SELECT
                sps.nr_contract,
                MIN(sps.data_scadenta) AS min_data_scadenta
            FROM
            (
                SELECT
                    solduriclienti.nr_contract,
                    solduriclienti.data_scadenta,
                    ROUND(SUM(
                        COALESCE(rata,0) +
                        COALESCE(dob_rem,0) +
                        COALESCE(dob_rem_reesalonata,0)
                    ), 2) AS sold
                FROM solduriclienti
                WHERE solduriclienti.data_incasarii IS NULL
                   OR solduriclienti.data_incasarii <= '".$data."'
                GROUP BY
                    solduriclienti.nr_contract,
                    solduriclienti.data_scadenta
                HAVING ROUND(SUM(
                    COALESCE(rata,0) +
                    COALESCE(dob_rem,0) +
                    COALESCE(dob_rem_reesalonata,0)
                ), 2) > 0.01
            ) sps
            GROUP BY sps.nr_contract
        ) psr ON contracte.nr_contract = psr.nr_contract
        WHERE
            contracte.data_contract < '2016-09-30'
            AND contracte.status NOT IN ('Finalizat', 'Anulat', 'Retragere')
            AND (
                DATEDIFF('".$data."', psr.min_data_scadenta) BETWEEN 5 AND 10
                OR
                DATEDIFF('".$data."', psr.min_data_scadenta) BETWEEN 25 AND 30
            )
    ) v2 ON v2.nr_contract = c.nr_contract
    WHERE
        c.tip_act NOT IN ('Comunicare expirare polita<20', 'Comunicare reinnoire polita>10')
        AND (
            DATEDIFF(c.data_document, v2.urmatoarea_scadenta) BETWEEN 5 AND 10
            OR
            DATEDIFF(c.data_document, v2.urmatoarea_scadenta) BETWEEN 25 AND 30
        )
    GROUP BY c.nr_contract
) uc ON v.nr_contract = uc.nr_contract
LEFT JOIN corespondenta c
    ON c.nr_contract = uc.nr_contract
   AND c.data_document = uc.max_data_document
WHERE
    c.tip_act IS NULL
    OR c.tip_act NOT IN ('Comunicare expirare polita<20', 'Comunicare reinnoire polita>10')
ORDER BY
    v.agentia,
    v.nume;;")));

         
         Log::info("Alerteverificarenotificarioug50 PAS 2");
        $adreseEmail=collect(DB::select(DB::raw("SELECT users.email
                                        FROM (notificationuser INNER JOIN users ON notificationuser.user_id = users.id) INNER JOIN notificationtype ON notificationuser.notificationtype_id = notificationtype.id
                                        WHERE (((notificationtype.denumire)='Verificare notificari OUG50'));")))->pluck("email");
       
        

        
        
             $antetTabel=[
                            
                            ['col'=>'agentia','denumire'=>'Agentia','type'=>'','align'=>'center','width'=>'10%'],
                            ['col'=>'nr_contract','denumire'=>'Nr contract','type'=>'','align'=>'center','width'=>'10%'],
                            ['col'=>'data_contract','denumire'=>'Data contract','type'=>'','align'=>'center','width'=>'10%'],
                            ['col'=>'nume','denumire'=>'Nume imprumutat','type'=>'','align'=>'center','width'=>'10%'],
                            ['col'=>'urmatoarea_scadenta','denumire'=>'Urmatoarea scadenta','type'=>'','align'=>'center','width'=>'10%'],
                            ['col'=>'zile_intarziere','denumire'=>'Zile intarziere','type'=>'','align'=>'center','width'=>'10%'],
                            ['col'=>'tip_notificare','denumire'=>'Tip notificare','type'=>'','align'=>'center','width'=>'10%'],
                            ['col'=>'notificare_transmisa','denumire'=>'Notificare transmisa','type'=>'','align'=>'center','width'=>'10%'],
                            ['col'=>'ok','denumire'=>'Ok','type'=>'','align'=>'center','width'=>'10%'],
                            ['col'=>'data_corespondenta','denumire'=>'Data corespondenta','type'=>'','align'=>'center','width'=>'10%'],
                            ];
                
            $tabel=collect($stoparicreante);
            $groupBy=[
                                                           
                           ];   
            $totalBy=[
            
                          ];

            $titluRaport="DianaIFNWeb Alerta Verificare notificari OUG 50  (".count($stoparicreante)." cazuri) in data de ".dateFormatAfisare($data);
            $company=Company::get()->first();
             
            $i=1;
            
          
               $company_id=$company->id;
             
                 $titluSheet="Verificare notificari OUG 50";
                
                $columnFormat=[
                                 // 'D' => NumberFormat::FORMAT_DATE_DDMMYYYY,
                                 // 'I' => NumberFormat::FORMAT_CURRENCY_EUR_SIMPLE
                               ];
               
               
           $numefis='public/'.$company->slug.'/verificare_notificari_oug50_'.time().".xls";      
          Excel::store( 
             (new SablonExport)->forCompany($company_id,$titluSheet,$tabel,$antetTabel,$groupBy,$totalBy,$columnFormat,$titluRaport),$numefis);
              
           Mail::to($adreseEmail)
                 ->send(
                    new AlertaSablonEmail(
                            $antetTabel,
                            $titluRaport,
                            $tabel,
                            $groupBy,
                            $totalBy,
                            $i,
                            $company,
                            $numefis
                        )
                 );
         
        
        
      } catch (\Exception $e) {
        
        $user=User::where("email","stefan.voinea@gmail.com")->first();
        Mail::to("stefan.voinea@gmail.com")
        ->send(new AlertaEroareEmail("ALERTA VERIFICARE NOTIFICARI OUG 50  ",$e->getMessage(),$e,$user));
        return response()->json(['message' => $e->getMessage()], 500);
      }   
          
    }
    public function alerteverificarenotificarioug52()
    {
            
     try{
        $data=Carbon::today()->format("Y-m-d");
        $dataminus30=Carbon::today()->addDays(-30)->format("Y-m-d");
        $dataminus10=Carbon::today()->addDays(-10)->format("Y-m-d");
        Log::info("Alerteverificarenotificarioug52 PAS 1");
            $stoparicreante=collect(DB::select(DB::raw("SELECT verificare_notificari_de_transmis.agentia, verificare_notificari_de_transmis.nr_contract, verificare_notificari_de_transmis.data_contract, verificare_notificari_de_transmis.nume, verificare_notificari_de_transmis.urmatoarea_scadenta, verificare_notificari_de_transmis.zile_intarziere, IF(zile_intarziere>=140,'Avertizare executare',IF(zile_intarziere>=65,'Solutii debitor',IF(zile_intarziere>=50,'Instiintare de plata','Comunicare de plata'))) AS tip_notificare, corespondenta.tip_act AS notificare_transmisa, IF(IsNull(tip_act),'Nu',IF(IF(zile_intarziere>=140,'Avertizare executare',IF(zile_intarziere>=65,'Solutii debitor',IF(zile_intarziere>=50,'Instiintare de plata','Comunicare de plata')))<>tip_act,'Eroare','Da')) AS ok, MaxOfdata_document AS data_corespondenta

            FROM 
            (
                SELECT contracte.agentia, contracte.nr_contract, contracte.data_contract, contracte.nume, prima_scadenta_restanta.MinOfdata_scadenta AS urmatoarea_scadenta, DateDiff('".$data."',minofdata_scadenta) AS zile_intarziere
                FROM contracte INNER JOIN 
                (
                SELECT solduri_pe_scadenta.nr_contract, Min(solduri_pe_scadenta.data_scadenta) AS MinOfdata_scadenta
                FROM 
                (
                SELECT solduriclienti.nr_contract, solduriclienti.data_scadenta,Round(Sum(coalesce(rata,0)+coalesce(dob_rem,0)+coalesce(dob_rem_reesalonata,0)),2) AS Sold
                FROM solduriclienti
                WHERE (((solduriclienti.data_incasarii) Is Null Or (solduriclienti.data_incasarii)<='".$data."'))
                GROUP BY solduriclienti.nr_contract, solduriclienti.data_scadenta
                HAVING (((Round(Sum(coalesce(rata,0)+coalesce(dob_rem,0)+coalesce(dob_rem_reesalonata,0)),2))>0.01))

                )
                AS solduri_pe_scadenta
                GROUP BY solduri_pe_scadenta.nr_contract

                )
                AS prima_scadenta_restanta ON contracte.nr_contract = prima_scadenta_restanta.nr_contract
                WHERE (((contracte.data_contract)>='2016-09-30') AND ((DateDiff('".$data."',minofdata_scadenta))>=25 And (DateDiff('".$data."',minofdata_scadenta))<=30) AND ((contracte.status) Not Like 'Finalizat' And (contracte.status) Not Like 'Anulat' And (contracte.status) Not Like 'Retragere')) OR 
                (((contracte.data_contract)>='2016-09-30') AND ((DateDiff('".$data."',minofdata_scadenta))>=50 And (DateDiff('".$data."',minofdata_scadenta))<=55) AND ((contracte.status) Not Like 'Finalizat' And (contracte.status) Not Like 'Anulat' And (contracte.status) Not Like 'Retragere')) OR 
                (((contracte.data_contract)>='2016-09-30') AND ((DateDiff('".$data."',minofdata_scadenta))>=65 And (DateDiff('".$data."',minofdata_scadenta))<=70) AND ((contracte.status) Not Like 'Finalizat' And (contracte.status) Not Like 'Anulat' And (contracte.status) Not Like 'Retragere')) OR 
                (((contracte.data_contract)>='2016-09-30') AND ((DateDiff('".$data."',minofdata_scadenta))>=140 And (DateDiff('".$data."',minofdata_scadenta))<=145) AND ((contracte.status) Not Like 'Finalizat' And (contracte.status) Not Like 'Anulat' And (contracte.status) Not Like 'Retragere'))

            )
            AS verificare_notificari_de_transmis LEFT JOIN (corespondenta RIGHT JOIN 
                (
                    SELECT corespondenta.agentia, corespondenta.nr_contract, corespondenta.data_contract, Max(corespondenta.data_document) AS MaxOfData_document
                    FROM 
                     (
                SELECT contracte.agentia, contracte.nr_contract, contracte.data_contract, contracte.nume, prima_scadenta_restanta.MinOfdata_scadenta AS urmatoarea_scadenta, DateDiff('".$data."',minofdata_scadenta) AS zile_intarziere
                FROM contracte INNER JOIN 
                (
                SELECT solduri_pe_scadenta.nr_contract, Min(solduri_pe_scadenta.data_scadenta) AS MinOfdata_scadenta
                FROM 
                (
                SELECT solduriclienti.nr_contract, solduriclienti.data_scadenta,Round(Sum(coalesce(rata,0)+coalesce(dob_rem,0)+coalesce(dob_rem_reesalonata,0)),2) AS Sold
                FROM solduriclienti
                WHERE (((solduriclienti.data_incasarii) Is Null Or (solduriclienti.data_incasarii)<='".$data."'))
                GROUP BY solduriclienti.nr_contract, solduriclienti.data_scadenta
                HAVING (((Round(Sum(coalesce(rata,0)+coalesce(dob_rem,0)+coalesce(dob_rem_reesalonata,0)),2))>0.01))

                )
                AS solduri_pe_scadenta
                GROUP BY solduri_pe_scadenta.nr_contract

                )
                AS prima_scadenta_restanta ON contracte.nr_contract = prima_scadenta_restanta.nr_contract
                WHERE (((contracte.data_contract)>='2016-09-30') AND ((DateDiff('".$data."',minofdata_scadenta))>=25 And (DateDiff('".$data."',minofdata_scadenta))<=30) AND ((contracte.status) Not Like 'Finalizat' And (contracte.status) Not Like 'Anulat' And (contracte.status) Not Like 'Retragere')) OR 
                (((contracte.data_contract)>='2016-09-30') AND ((DateDiff('".$data."',minofdata_scadenta))>=50 And (DateDiff('".$data."',minofdata_scadenta))<=55) AND ((contracte.status) Not Like 'Finalizat' And (contracte.status) Not Like 'Anulat' And (contracte.status) Not Like 'Retragere')) OR 
                (((contracte.data_contract)>='2016-09-30') AND ((DateDiff('".$data."',minofdata_scadenta))>=65 And (DateDiff('".$data."',minofdata_scadenta))<=70) AND ((contracte.status) Not Like 'Finalizat' And (contracte.status) Not Like 'Anulat' And (contracte.status) Not Like 'Retragere')) OR 
                (((contracte.data_contract)>='2016-09-30') AND ((DateDiff('".$data."',minofdata_scadenta))>=140 And (DateDiff('".$data."',minofdata_scadenta))<=145) AND ((contracte.status) Not Like 'Finalizat' And (contracte.status) Not Like 'Anulat' And (contracte.status) Not Like 'Retragere'))

            )
            AS 
            verificare_notificari_de_transmis INNER JOIN corespondenta ON verificare_notificari_de_transmis.nr_contract = corespondenta.nr_contract
                        WHERE (((DateDiff(data_document,urmatoarea_scadenta))>=25 And (DateDiff(data_document,urmatoarea_scadenta))<=30) AND ((verificare_notificari_de_transmis.zile_intarziere)>=25 And (verificare_notificari_de_transmis.zile_intarziere)<=30) AND ((corespondenta.tip_act) Not Like 'Comunicare expirare polita<20' And (corespondenta.tip_act) Not Like 'Comunicare reinnoire polita>10')) OR (((DateDiff(data_document,urmatoarea_scadenta))>=50 And (DateDiff(data_document,urmatoarea_scadenta))<=55) AND ((verificare_notificari_de_transmis.zile_intarziere)>=50 And (verificare_notificari_de_transmis.zile_intarziere)<=55) AND ((corespondenta.tip_act) Not Like 'Comunicare expirare polita<20' And (corespondenta.tip_act) Not Like 'Comunicare reinnoire polita>10')) OR (((DateDiff(data_document,urmatoarea_scadenta))>=65 And (DateDiff(data_document,urmatoarea_scadenta))<=70) AND ((verificare_notificari_de_transmis.zile_intarziere)>=65 And (verificare_notificari_de_transmis.zile_intarziere)<=70) AND ((corespondenta.tip_act) Not Like 'Comunicare expirare polita<20' And (corespondenta.tip_act) Not Like 'Comunicare reinnoire polita>10')) OR (((DateDiff(data_document,urmatoarea_scadenta))>=140 And (DateDiff(data_document,urmatoarea_scadenta))<=145) AND ((verificare_notificari_de_transmis.zile_intarziere)>=140 And (verificare_notificari_de_transmis.zile_intarziere)<=145) AND ((corespondenta.tip_act) Not Like 'Comunicare expirare polita<20' And (corespondenta.tip_act) Not Like 'Comunicare reinnoire polita>10'))
                    GROUP BY corespondenta.agentia, corespondenta.nr_contract, corespondenta.data_contract


                )

                AS ultima_corespondenta_transmisa ON (corespondenta.nr_contract = ultima_corespondenta_transmisa.nr_contract) AND (corespondenta.data_document = ultima_corespondenta_transmisa.MaxOfdata_document)) ON verificare_notificari_de_transmis.nr_contract = ultima_corespondenta_transmisa.nr_contract
           
            GROUP BY verificare_notificari_de_transmis.agentia, verificare_notificari_de_transmis.nr_contract, verificare_notificari_de_transmis.data_contract, verificare_notificari_de_transmis.nume, verificare_notificari_de_transmis.urmatoarea_scadenta, verificare_notificari_de_transmis.zile_intarziere, IF(zile_intarziere>=140,'Avertizare executare',IF(zile_intarziere>=65,'Solutii debitor',IF(zile_intarziere>=50,'Instiintare de plata','Comunicare de plata'))), corespondenta.tip_act,  IF(IsNull(tip_act),'Nu',IF(IF(zile_intarziere>=140,'Avertizare executare',IF(zile_intarziere>=65,'Solutii debitor',IF(zile_intarziere>=50,'Instiintare de plata','Comunicare de plata')))<>tip_act,'Eroare','Da')), ultima_corespondenta_transmisa.MaxOfdata_document
            HAVING (((corespondenta.tip_act) Not Like 'Comunicare expirare polita<20' And (corespondenta.tip_act) Not Like 'Comunicare reinnoire polita>10')) OR (((corespondenta.tip_act) Is Null));")));

         
         Log::info("Alerteverificarenotificarioug52 PAS 2");
        $adreseEmail=collect(DB::select(DB::raw("SELECT users.email
                                        FROM (notificationuser INNER JOIN users ON notificationuser.user_id = users.id) INNER JOIN notificationtype ON notificationuser.notificationtype_id = notificationtype.id
                                        WHERE (((notificationtype.denumire)='Verificare notificari OUG52'));")))->pluck("email");
       
        

        
        
             $antetTabel=[
                            
                            ['col'=>'agentia','denumire'=>'Agentia','type'=>'','align'=>'center','width'=>'10%'],
                            ['col'=>'nr_contract','denumire'=>'Nr contract','type'=>'','align'=>'center','width'=>'10%'],
                            ['col'=>'data_contract','denumire'=>'Data contract','type'=>'','align'=>'center','width'=>'10%'],
                            ['col'=>'nume','denumire'=>'Nume imprumutat','type'=>'','align'=>'center','width'=>'10%'],
                            ['col'=>'urmatoarea_scadenta','denumire'=>'Urmatoarea scadenta','type'=>'','align'=>'center','width'=>'10%'],
                            ['col'=>'zile_intarziere','denumire'=>'Zile intarziere','type'=>'','align'=>'center','width'=>'10%'],
                            ['col'=>'tip_notificare','denumire'=>'Tip notificare','type'=>'','align'=>'center','width'=>'10%'],
                            ['col'=>'notificare_transmisa','denumire'=>'Notificare transmisa','type'=>'','align'=>'center','width'=>'10%'],
                            ['col'=>'ok','denumire'=>'Ok','type'=>'','align'=>'center','width'=>'10%'],
                            ['col'=>'data_corespondenta','denumire'=>'Data corespondenta','type'=>'','align'=>'center','width'=>'10%'],
                            ];
                
            $tabel=collect($stoparicreante);
            $groupBy=[
                                                           
                           ];   
            $totalBy=[
            
                          ];

            $titluRaport="DianaIFNWeb Alerta Verificare notificari OUG 52  (".count($stoparicreante)." cazuri) in data de ".dateFormatAfisare($data);
            $company=Company::get()->first();
             
            $i=1;
            
          
               $company_id=$company->id;
             
                 $titluSheet="Verificare notificari OUG 52";
                
                $columnFormat=[
                                 // 'D' => NumberFormat::FORMAT_DATE_DDMMYYYY,
                                 // 'I' => NumberFormat::FORMAT_CURRENCY_EUR_SIMPLE
                               ];
               
               
           $numefis='public/'.$company->slug.'/verificare_notificari_oug52_'.time().".xls";      
          Excel::store( 
             (new SablonExport)->forCompany($company_id,$titluSheet,$tabel,$antetTabel,$groupBy,$totalBy,$columnFormat,$titluRaport),$numefis);
              
           Mail::to($adreseEmail)
                 ->send(
                    new AlertaSablonEmail(
                            $antetTabel,
                            $titluRaport,
                            $tabel,
                            $groupBy,
                            $totalBy,
                            $i,
                            $company,
                            $numefis
                        )
                 );
         
        
        
      } catch (\Exception $e) {
        
        $user=User::where("email","stefan.voinea@gmail.com")->first();
        Mail::to("stefan.voinea@gmail.com")
        ->send(new AlertaEroareEmail("ALERTA VERIFICARE NOTIFICARI OUG 52  ",$e->getMessage(),$e,$user));
        return response()->json(['message' => $e->getMessage()], 500);
      }   
          
    }
    public function alertenotificarireinnoirepolite()
    {
          
     try{
        $data=Carbon::today()->format("Y-m-d");
        $dataminus30=Carbon::today()->addDays(-30)->format("Y-m-d");
        $dataminus10=Carbon::today()->addDays(-10)->format("Y-m-d");
        
            $stoparicreante=collect(DB::select(DB::raw("SELECT politeasig.nr_polita, politeasig.data_polita, politeasig.datascadenta AS data_expirarii, politeasig.agentia, politeasig.nr_contract, politeasig.data_contract, politeasig.nume, politeasig.asigurator, politeasig.suma_asigurata, politeasig.sumaanuala AS valoare_polita_in_lei, politeasig.curs, politeasig.suma_valuta AS valoare_polita_in_eur, 'Comunicare reinnoire polita>10' AS tip_notificare, IF(IsNull(notificari_reinnoire_polite_pentru_alerte.nr_contract),'Nu','Da') AS notificare_transmisa
                FROM (politeasig LEFT JOIN contracte ON politeasig.nr_contract = contracte.nr_contract) LEFT JOIN 
                (
                    SELECT corespondenta.nr_contract, corespondenta.tip_act, corespondenta.data_document
                    FROM corespondenta
                    WHERE corespondenta.tip_act='Comunicare reinnoire polita>10' AND corespondenta.data_document>='".$dataminus30."'

                )
                AS notificari_reinnoire_polite_pentru_alerte ON politeasig.nr_contract = notificari_reinnoire_polite_pentru_alerte.nr_contract
                WHERE politeasig.datascadenta='".$dataminus10."' AND contracte.status Not Like 'Finalizat' AND politeasig.reinnoita_de Is Null Or politeasig.reinnoita_de='' Or politeasig.reinnoita_de Like '%easy%';")));

         
         
        $adreseEmail=collect(DB::select(DB::raw("SELECT users.email
                                        FROM (notificationuser INNER JOIN users ON notificationuser.user_id = users.id) INNER JOIN notificationtype ON notificationuser.notificationtype_id = notificationtype.id
                                        WHERE (((notificationtype.denumire)='Verificare notificari reinnoire polite de asigurare'));")))->pluck("email");
       
        
             $antetTabel=[
                            
                            ['col'=>'nr_polita','denumire'=>'Nr polita','type'=>'','align'=>'center','width'=>'10%'],
                            ['col'=>'data_polita','denumire'=>'Data polita','type'=>'Date','align'=>'center','width'=>'10%'],
                            ['col'=>'data_expirarii','denumire'=>'Data expirarii','type'=>'Date','align'=>'center','width'=>'10%'],
                            ['col'=>'agentia','denumire'=>'Agentia','type'=>'','align'=>'left','width'=>'10%'],
                            ['col'=>'nr_contract','denumire'=>'Nr contract','type'=>'','align'=>'center','width'=>'10%'],
                            ['col'=>'data_contract','denumire'=>'Data contract','type'=>'Date','align'=>'center','width'=>'10%'],
                            ['col'=>'nume','denumire'=>'Nume','type'=>'','align'=>'left','width'=>'10%'],
                            ['col'=>'asigurator','denumire'=>'Asigurator','type'=>'','align'=>'left','width'=>'10%'],
                            ['col'=>'suma_asigurata','denumire'=>'Suma asigurata','type'=>'','align'=>'center','width'=>'10%'],
                            ['col'=>'valoare_polita_in_lei','denumire'=>'Valoare polita in lei','type'=>'','align'=>'center','width'=>'10%'],
                            ['col'=>'curs','denumire'=>'Curs','type'=>'','align'=>'center','width'=>'10%'],
                            ['col'=>'valoare_polita_in_eur','denumire'=>'Valoare polita in eur','type'=>'','align'=>'center','width'=>'10%'],
                            ['col'=>'tip_notificare','denumire'=>'Tip notificare','type'=>'','align'=>'center','width'=>'10%'],
                            ['col'=>'notificare_transmisa','denumire'=>'Notificare transmisa','type'=>'','align'=>'center','width'=>'10%'],
                            ];
            $groupBy=[
                                                           
                           ];   
            $totalBy=[
            
                          ];
            $company=Company::get()->first();
        foreach($stoparicreante->groupBy("agentia") as $filtrat){
            $tabel=collect($filtrat);
            $agentia=Gestiune::where("denumire",$filtrat[0]->agentia)->first();
        
            $titluRaport="DianaIFNWeb Alerta Verificare notificari reinnoire polite de asigurare  (".count($filtrat)." cazuri) in data de ".dateFormatAfisare($data);
                

             
            $i=1;
            
          
               $company_id=$company->id;
             
                 $titluSheet="Verificare notificari reinnoire polite de asigurare";
                
                $columnFormat=[
                                 // 'D' => NumberFormat::FORMAT_DATE_DDMMYYYY,
                                 // 'I' => NumberFormat::FORMAT_CURRENCY_EUR_SIMPLE
                               ];
               
               
           $numefis='public/'.$company->slug.'/verificare_notificari_reinnoire_polite_'.time().".xls";      
          Excel::store( 
             (new SablonExport)->forCompany($company_id,$titluSheet,$tabel,$antetTabel,$groupBy,$totalBy,$columnFormat,$titluRaport),$numefis);
              
           Mail::to($adreseEmail)
                 ->cc($agentia->email)   
                 ->send(
                    new AlertaSablonEmail(
                            $antetTabel,
                            $titluRaport,
                            $tabel,
                            $groupBy,
                            $totalBy,
                            $i,
                            $company,
                            $numefis
                        )
                 );
         
        }
        
      } catch (\Exception $e) {
        
        $user=User::where("email","stefan.voinea@gmail.com")->first();
        Mail::to("stefan.voinea@gmail.com")
        ->send(new AlertaEroareEmail("ALERTA VERIFICARE NOTIFICARI REINNOIRE POLITE  ",$e->getMessage(),$e,$user));
        return response()->json(['message' => $e->getMessage()], 500);
      }   
          
    }
      public function alertenotificariexpirarepolite()
    {
           
     try{
        $data=Carbon::today()->format("Y-m-d");
        $dataplus20=Carbon::today()->addDays(20)->format("Y-m-d");
        $dataminus30=Carbon::today()->addDays(-30)->format("Y-m-d");
        
            $stoparicreante=collect(DB::select(DB::raw("SELECT politeasig.nr_polita, politeasig.data_polita, politeasig.datascadenta AS data_expirarii, politeasig.agentia, politeasig.nr_contract, politeasig.data_contract, politeasig.nume, politeasig.asigurator, politeasig.suma_asigurata, politeasig.sumaanuala AS valoare_polita_in_lei, politeasig.curs, politeasig.suma_valuta AS valoare_polita_ineur, 'Comunicare expirare polita<20' AS tip_notificare, IF(IsNull(notificare_expirare_polite_pentru_alerte.nr_contract),'Nu','Da') AS notificare_transmisa
                FROM (politeasig LEFT JOIN contracte ON politeasig.nr_contract = contracte.nr_contract) LEFT JOIN 
                (
                    SELECT corespondenta.nr_contract, corespondenta.tip_act, corespondenta.data_document
                    FROM corespondenta
                    WHERE corespondenta.tip_act='Comunicare expirare polita<20' AND corespondenta.data_document>='".$dataminus30."'

                )
                AS notificare_expirare_polite_pentru_alerte ON politeasig.nr_contract = notificare_expirare_polite_pentru_alerte.nr_contract
                WHERE politeasig.datascadenta=".$dataplus20." AND politeasig.data_reinnoirii Is Null AND contracte.status Not Like 'Finalizat';")));

         
         
        $adreseEmail=collect(DB::select(DB::raw("SELECT users.email
                                        FROM (notificationuser INNER JOIN users ON notificationuser.user_id = users.id) INNER JOIN notificationtype ON notificationuser.notificationtype_id = notificationtype.id
                                        WHERE (((notificationtype.denumire)='Verificare notificari expirare polite de asigurare'));")))->pluck("email");
       
        

        
        
             $antetTabel=[
                            ['col'=>'nr_polita','denumire'=>'Nr polita','type'=>'','align'=>'center','width'=>'10%'],
                            ['col'=>'data_polita','denumire'=>'Data polita','type'=>'Date','align'=>'center','width'=>'10%'],
                            ['col'=>'data_expirarii','denumire'=>'Data expirarii','type'=>'Date','align'=>'center','width'=>'10%'],
                            ['col'=>'agentia','denumire'=>'Agentia','type'=>'','align'=>'left','width'=>'10%'],
                            ['col'=>'nr_contract','denumire'=>'Nr contract','type'=>'','align'=>'center','width'=>'10%'],
                            ['col'=>'data_contract','denumire'=>'Data contract','type'=>'Date','align'=>'center','width'=>'10%'],
                            ['col'=>'nume','denumire'=>'Nume','type'=>'','align'=>'left','width'=>'10%'],
                            ['col'=>'asigurator','denumire'=>'Asigurator','type'=>'','align'=>'left','width'=>'10%'],
                            ['col'=>'suma_asigurata','denumire'=>'Suma asigurata','type'=>'','align'=>'center','width'=>'10%'],
                            ['col'=>'valoare_polita_in_lei','denumire'=>'Valoare polita in lei','type'=>'','align'=>'center','width'=>'10%'],
                            ['col'=>'curs','denumire'=>'Curs','type'=>'','align'=>'center','width'=>'10%'],
                            ['col'=>'valoare_polita_in_eur','denumire'=>'Valoare polita in eur','type'=>'','align'=>'center','width'=>'10%'],
                            ['col'=>'tip_notificare','denumire'=>'Tip notificare','type'=>'','align'=>'center','width'=>'10%'],
                            ['col'=>'notificare_transmisa','denumire'=>'Notificare transmisa','type'=>'','align'=>'center','width'=>'10%'],
                            ];
            foreach($stoparicreante->groupBy("agentia") as $filtrat){
            $agentia=Gestiune::where("denumire",$filtrat[0]->agentia)->first();   
            $tabel=collect($filtrat);
            $groupBy=[
                                                           
                           ];   
            $totalBy=[
            
                          ];

            $titluRaport="DianaIFNWeb Alerta Verificare notificari expirare polite de asigurare  (".count($filtrat)." cazuri) in data de ".dateFormatAfisare($data);
            $company=Company::get()->first();
             
            $i=1;
            
          
               $company_id=$company->id;
             
                 $titluSheet="Verificare notificari expirare polite de asigurare";
                
                $columnFormat=[
                                 // 'D' => NumberFormat::FORMAT_DATE_DDMMYYYY,
                                 // 'I' => NumberFormat::FORMAT_CURRENCY_EUR_SIMPLE
                               ];
               
               
           $numefis='public/'.$company->slug.'/verificare_notificari_expirare_polite_'.time().".xls";      
          Excel::store( 
             (new SablonExport)->forCompany($company_id,$titluSheet,$tabel,$antetTabel,$groupBy,$totalBy,$columnFormat,$titluRaport),$numefis);
              
           Mail::to($adreseEmail)
                ->cc($agentia->email)
                 ->send(
                    new AlertaSablonEmail(
                            $antetTabel,
                            $titluRaport,
                            $tabel,
                            $groupBy,
                            $totalBy,
                            $i,
                            $company,
                            $numefis
                        )
                 );
         
        }
        
      } catch (\Exception $e) {
        
        $user=User::where("email","stefan.voinea@gmail.com")->first();
        Mail::to("stefan.voinea@gmail.com")
        ->send(new AlertaEroareEmail("ALERTA VERIFICARE NOTIFICARI EXPIRARE POLITE  ",$e->getMessage(),$e,$user));
        return response()->json(['message' => $e->getMessage()], 500);
      }   
          
    }
      public function alerteverificarenotificaribc()
    {
           
     try{
        $data=Carbon::today()->format("Y-m-d");
        
        
        $stoparicreante=collect(DB::select(DB::raw("SELECT contracte.agentia, contracte.nr_contract, contracte.data_contract, contracte.nume, contracte.status, Min(solduricontracte.data_scadenta) AS prima_scadenta_restanta
                                                FROM contracte INNER JOIN 
                                                (
                                                    SELECT solduriclienti.nr_contract, round(Sum(solduriclienti.rata),2) AS SumOfrata, round(Sum(solduriclienti.dob_rem),2) AS SumOfdob_rem, solduriclienti.data_scadenta
                                                    FROM solduriclienti
                                                    GROUP BY solduriclienti.nr_contract, solduriclienti.data_scadenta
                                                    HAVING (((round(Sum(solduriclienti.rata),2))>0) AND ((solduriclienti.data_scadenta) Is Not Null)) OR (((round(Sum(solduriclienti.dob_rem),2))>0) AND ((solduriclienti.data_scadenta) Is Not Null))

                                                    )
                                                AS solduricontracte ON contracte.nr_contract = solduricontracte.nr_contract
                                                WHERE contracte.tip_client='PF' and contracte.tip_client='Persoana fizica' 
                                                GROUP BY contracte.agentia, contracte.nr_contract, contracte.data_contract, contracte.nume, contracte.status
                                                HAVING (((contracte.data_contract)>='2016-09-30') AND ((contracte.status) Not Like 'Anulat%' And (contracte.status) Not Like 'Finalizat%' ));")));
         
         $stoparicreante=$stoparicreante->filter(function ($item) use ($data) {
                                            $item->nr_zile_intarziere=Carbon::parse($item->prima_scadenta_restanta)->diffInDays($data);
                                            return $item->prima_scadenta_restanta < $data && $item->nr_zile_intarziere>=5 && $item->nr_zile_intarziere<=15;
                                        });
         
        $adreseEmail=collect(DB::select(DB::raw("SELECT users.email
                                        FROM (notificationuser INNER JOIN users ON notificationuser.user_id = users.id) INNER JOIN notificationtype ON notificationuser.notificationtype_id = notificationtype.id
                                        WHERE (((notificationtype.denumire)='Verificare notificari BC'));")))->pluck("email");
       
        

        
        
             $antetTabel=[
            ["col"=>"agentia","denumire"=>"Agentia","type"=>"","align"=>"left","width"=>"20%"], 
            ["col"=>"nume","denumire"=>"Partener","type"=>"","align"=>"center","width"=>"10%"],
            ["col"=>"nr_contract","denumire"=>"Nr contract","type"=>"","align"=>"center","width"=>"10%"],
            ["col"=>"data_contract","denumire"=>"Data contract","type"=>"Date","align"=>"center","width"=>"10%"],
            ["col"=>"nr_zile_intarziere","denumire"=>"Nr zile intarziere","type"=>"","align"=>"center","width"=>"10%"],
            ];
                
            $tabel=collect($stoparicreante);
            $groupBy=[
                                                           
                           ];   
            $totalBy=[
            
                          ];

            $titluRaport="DianaIFNWeb Alerta Verificare notificari de transmis BC  (".count($stoparicreante)." cazuri) in data de ".dateFormatAfisare($data);
            $company=Company::get()->first();
             
            $i=1;
            
          
               $company_id=$company->id;
             
                 $titluSheet="Verificare notificari de transmis BC";
                
                $columnFormat=[
                                 // 'D' => NumberFormat::FORMAT_DATE_DDMMYYYY,
                                 // 'I' => NumberFormat::FORMAT_CURRENCY_EUR_SIMPLE
                               ];
               
               
           $numefis='public/'.$company->slug.'/verificare_notificari_bc_'.time().".xls";      
          Excel::store( 
             (new SablonExport)->forCompany($company_id,$titluSheet,$tabel,$antetTabel,$groupBy,$totalBy,$columnFormat,$titluRaport),$numefis);
              
           Mail::to($adreseEmail)
                 ->send(
                    new AlertaSablonEmail(
                            $antetTabel,
                            $titluRaport,
                            $tabel,
                            $groupBy,
                            $totalBy,
                            $i,
                            $company,
                            $numefis
                        )
                 );
         
        
        
      } catch (\Exception $e) {
        
        $user=User::where("email","stefan.voinea@gmail.com")->first();
        Mail::to("stefan.voinea@gmail.com")
        ->send(new AlertaEroareEmail("ALERTA VERIFICARE NOTIFICARI BC  ",$e->getMessage(),$e,$user));
        return response()->json(['message' => $e->getMessage()], 500);
      }   
          
    }
     public function alertedobandadepoliticamonetara()
    {
           
     try{
        $data=Carbon::today()->format("Y-m-d");
        
        
        $contracte=collect(DB::select(DB::raw("SELECT contracte.agentia, contracte.nr_contract, contracte.data_contract, contracte.nume, proc_dob_rem*12 AS rata_anuala_a_dobanzii, capitalsocial.dobanda_de_politica_monetara
                                            FROM contracte, capitalsocial
                                            WHERE (((contracte.data_contract)>='2025-06-01') AND ((proc_dob_rem*12)<dobanda_de_politica_monetara));")));
         
        
        $adreseEmail=collect(DB::select(DB::raw("SELECT users.email
                                        FROM (notificationuser INNER JOIN users ON notificationuser.user_id = users.id) INNER JOIN notificationtype ON notificationuser.notificationtype_id = notificationtype.id
                                        WHERE (((notificationtype.denumire)='Dobanda de politica monetara'));")))->pluck("email");
       
        

        
        
             $antetTabel=[
            ["col"=>"agentia","denumire"=>"Agentia","type"=>"","align"=>"left","width"=>"10%"], 
            ["col"=>"nr_contract","denumire"=>"Nr contract","type"=>"","align"=>"center","width"=>"10%"],
            ["col"=>"data_contract","denumire"=>"Data contract","type"=>"Date","align"=>"center","width"=>"10%"],
            ["col"=>"nume","denumire"=>"Partener","type"=>"","align"=>"Left","width"=>"10%"],
            ["col"=>"rata_anuala_a_dobanzii","denumire"=>"Rata anuala a dobanzii","type"=>"","align"=>"Left","width"=>"10%"],
            ["col"=>"dobanda_de_politica_monetara","denumire"=>"Dobanda de politica monetara","type"=>"","align"=>"Left","width"=>"10%"],
            ];
                
            $tabel=collect($contracte);
            $groupBy=[
                                                           
                           ];   
            $totalBy=[
            
                          ];

            $titluRaport="DianaIFNWeb Alerta contracte sub dobanda de politica monetara (".count($contracte)." cazuri) in data de ".dateFormatAfisare($data);
            $company=Company::get()->first();
             
            $i=1;
            
          
               $company_id=$company->id;
             
                 $titluSheet="Contracte sub dobanda de politica monetara";
                
                $columnFormat=[
                                 // 'D' => NumberFormat::FORMAT_DATE_DDMMYYYY,
                                 // 'I' => NumberFormat::FORMAT_CURRENCY_EUR_SIMPLE
                               ];
               
               
           $numefis='public/'.$company->slug.'/contracte_sub_dobanda_de_politica_monetara_'.time().".xls";      
          Excel::store( 
             (new SablonExport)->forCompany($company_id,$titluSheet,$tabel,$antetTabel,$groupBy,$totalBy,$columnFormat,$titluRaport),$numefis);
              
           Mail::to($adreseEmail)
                 ->send(
                    new AlertaSablonEmail(
                            $antetTabel,
                            $titluRaport,
                            $tabel,
                            $groupBy,
                            $totalBy,
                            $i,
                            $company,
                            $numefis
                        )
                 );
         
        
        
      } catch (\Exception $e) {
        
        $user=User::where("email","stefan.voinea@gmail.com")->first();
        Mail::to("stefan.voinea@gmail.com")
        ->send(new AlertaEroareEmail("ALERTA CONTRACTE SUB DOBANDA DE POLITICA MONETARA  ",$e->getMessage(),$e,$user));
        return response()->json(['message' => $e->getMessage()], 500);
      }   
          
    }
    public function alertedeclararicreditscadentoug50()
    {
          
     try{
        $data=Carbon::today()->format("Y-m-d");
        
        
        $declararicreditscadent=collect(DB::select(DB::raw("SELECT contracte.agentia, contracte.nr_contract, contracte.data_contract, contracte.nume, contracte.status, Min(solduricontracte.data_declarare_scadent) AS prima_declarare_scadent
                                                FROM contracte INNER JOIN 
                                                (
                                                    SELECT solduriclienti.nr_contract, round(Sum(solduriclienti.rata),2) AS SumOfrata, round(Sum(solduriclienti.dob_rem),2) AS SumOfdob_rem, solduriclienti.data_declarare_scadent
                                                    FROM solduriclienti
                                                    GROUP BY solduriclienti.nr_contract, solduriclienti.data_declarare_scadent 
                                                    HAVING (((round(Sum(solduriclienti.rata),2))>0) AND ((solduriclienti.data_declarare_scadent) Is Not Null)) OR (((round(Sum(solduriclienti.dob_rem),2))>0) AND ((solduriclienti.data_declarare_scadent) Is Not Null))

                                                    )
                                                AS solduricontracte ON contracte.nr_contract = solduricontracte.nr_contract
                                                GROUP BY contracte.agentia, contracte.nr_contract, contracte.data_contract, contracte.nume, contracte.status
                                                HAVING contracte.data_contract<'2016-09-30' AND contracte.status Not Like 'Finalizat%' 
                                                ORDER BY contracte.agentia ASC,contracte.nume ASC;")));
         
         $declararicreditscadent=$declararicreditscadent->filter(function ($item) use ($data) {
                                            $item->nr_zile_intarziere=Carbon::parse($item->prima_declarare_scadent)->diffInDays($data);
                                            return $item->prima_declarare_scadent < $data && $item->nr_zile_intarziere==1;
                                        });
        
        $adreseEmail=collect(DB::select(DB::raw("SELECT users.email
                                        FROM (notificationuser INNER JOIN users ON notificationuser.user_id = users.id) INNER JOIN notificationtype ON notificationuser.notificationtype_id = notificationtype.id
                                        WHERE (((notificationtype.denumire)='Declarari credit scadent OUG 50'));")))->pluck("email");
       
        

        
        
             $antetTabel=[
                            ["col"=>"agentia","denumire"=>"Agentia","type"=>"","align"=>"left","width"=>"10%"], 
                            ["col"=>"nume","denumire"=>"Partener","type"=>"","align"=>"Left","width"=>"10%"],
                            ["col"=>"nr_contract","denumire"=>"Nr contract","type"=>"","align"=>"center","width"=>"10%"],
                          ];
                
            $tabel=collect($declararicreditscadent);
            $groupBy=[
                                                           
                           ];   
            $totalBy=[
            
                          ];

            $titluRaport="DianaIFNWeb Alerta declarari credit scadent OUG 50 (".count($declararicreditscadent)." cazuri) in data de ".dateFormatAfisare($data);
            $company=Company::get()->first();
             
            $i=1;
            
          
               $company_id=$company->id;
             
                 $titluSheet="Avertizare declarari credit scadent OUG 50";
                
                $columnFormat=[
                                 // 'D' => NumberFormat::FORMAT_DATE_DDMMYYYY,
                                 // 'I' => NumberFormat::FORMAT_CURRENCY_EUR_SIMPLE
                               ];
               
               
           $numefis='public/'.$company->slug.'/avertizare_declarari_credit_scadent_oug50_'.time().".xls";      
          Excel::store( 
             (new SablonExport)->forCompany($company_id,$titluSheet,$tabel,$antetTabel,$groupBy,$totalBy,$columnFormat,$titluRaport),$numefis);
              
           Mail::to($adreseEmail)
                 ->send(
                    new AlertaSablonEmail(
                            $antetTabel,
                            $titluRaport,
                            $tabel,
                            $groupBy,
                            $totalBy,
                            $i,
                            $company,
                            $numefis
                        )
                 );
         
        
        
      } catch (\Exception $e) {
        
        $user=User::where("email","stefan.voinea@gmail.com")->first();
        Mail::to("stefan.voinea@gmail.com")
        ->send(new AlertaEroareEmail("ALERTA DECLARARI CRREDIT SCADENT OUG 50  ",$e->getMessage(),$e,$user));
        return response()->json(['message' => $e->getMessage()], 500);
      }   
          
    }
      public function alertedeclararicreditscadentoug52()
    {
           
     try{
        $data=Carbon::today()->format("Y-m-d");
        
        
        $declararicreditscadent=collect(DB::select(DB::raw("SELECT contracte.agentia, contracte.nr_contract, contracte.data_contract, contracte.nume, contracte.status, Min(solduricontracte.data_declarare_scadent) AS prima_declarare_scadent
                                                FROM contracte INNER JOIN 
                                                (
                                                    SELECT solduriclienti.nr_contract, round(Sum(solduriclienti.rata),2) AS SumOfrata, round(Sum(solduriclienti.dob_rem),2) AS SumOfdob_rem, solduriclienti.data_declarare_scadent
                                                    FROM solduriclienti
                                                    GROUP BY solduriclienti.nr_contract, solduriclienti.data_declarare_scadent 
                                                    HAVING (((round(Sum(solduriclienti.rata),2))>0) AND ((solduriclienti.data_declarare_scadent) Is Not Null)) OR (((round(Sum(solduriclienti.dob_rem),2))>0) AND ((solduriclienti.data_declarare_scadent) Is Not Null))

                                                    )
                                                AS solduricontracte ON contracte.nr_contract = solduricontracte.nr_contract
                                                GROUP BY contracte.agentia, contracte.nr_contract, contracte.data_contract, contracte.nume, contracte.status
                                                HAVING contracte.data_contract>='2016-09-30' AND contracte.status Not Like 'Finalizat%'
                                                ORDER BY contracte.agentia ASC,contracte.nume ASC ;")));
         
         $declararicreditscadent=$declararicreditscadent->filter(function ($item) use ($data) {
                                            $item->nr_zile_intarziere=Carbon::parse($item->prima_declarare_scadent)->diffInDays($data);
                                            return $item->prima_declarare_scadent < $data && $item->nr_zile_intarziere==1;
                                        });
        
        $adreseEmail=collect(DB::select(DB::raw("SELECT users.email
                                        FROM (notificationuser INNER JOIN users ON notificationuser.user_id = users.id) INNER JOIN notificationtype ON notificationuser.notificationtype_id = notificationtype.id
                                        WHERE (((notificationtype.denumire)='Declarari credit scadent OUG 52'));")))->pluck("email");
       
        

        
        
             $antetTabel=[
            ["col"=>"agentia","denumire"=>"Agentia","type"=>"","align"=>"left","width"=>"10%"], 
            ["col"=>"nume","denumire"=>"Partener","type"=>"","align"=>"Left","width"=>"10%"],
            ["col"=>"nr_contract","denumire"=>"Nr contract","type"=>"","align"=>"center","width"=>"10%"],
            ];
                
            $tabel=collect($declararicreditscadent);
            $groupBy=[
                                                           
                           ];   
            $totalBy=[
            
                          ];

            $titluRaport="DianaIFNWeb Alerta declarari credit scadent OUG 52 (".count($declararicreditscadent)." cazuri) in data de ".dateFormatAfisare($data);
            $company=Company::get()->first();
             
            $i=1;
            
          
               $company_id=$company->id;
             
                 $titluSheet="Avertizare declarari credit scadent OUG 52";
                
                $columnFormat=[
                                 // 'D' => NumberFormat::FORMAT_DATE_DDMMYYYY,
                                 // 'I' => NumberFormat::FORMAT_CURRENCY_EUR_SIMPLE
                               ];
               
               
           $numefis='public/'.$company->slug.'/avertizare_declarari_credit_scadent_oug52_'.time().".xls";      
          Excel::store( 
             (new SablonExport)->forCompany($company_id,$titluSheet,$tabel,$antetTabel,$groupBy,$totalBy,$columnFormat,$titluRaport),$numefis);
              
           Mail::to($adreseEmail)
                 ->send(
                    new AlertaSablonEmail(
                            $antetTabel,
                            $titluRaport,
                            $tabel,
                            $groupBy,
                            $totalBy,
                            $i,
                            $company,
                            $numefis
                        )
                 );
         
        
        
      } catch (\Exception $e) {
        
        $user=User::where("email","stefan.voinea@gmail.com")->first();
        Mail::to("stefan.voinea@gmail.com")
        ->send(new AlertaEroareEmail("ALERTA DECLARARI CRREDIT SCADENT OUG 52  ",$e->getMessage(),$e,$user));
        return response()->json(['message' => $e->getMessage()], 500);
      }   
          
    }
      public function alerteavertizarestoparecreanta()
    {
          
     try{
        $data=Carbon::today()->format("Y-m-d");
        
        
        $stoparicreante=collect(DB::select(DB::raw("SELECT contracte.agentia, contracte.nr_contract, contracte.data_contract, contracte.nume, contracte.status, Min(solduricontracte.data_scadenta) AS prima_scadenta_restanta
                                                FROM contracte INNER JOIN 
                                                (
                                                    SELECT solduriclienti.nr_contract, round(Sum(solduriclienti.rata),2) AS SumOfrata, round(Sum(solduriclienti.dob_rem),2) AS SumOfdob_rem, solduriclienti.data_scadenta
                                                    FROM solduriclienti
                                                    GROUP BY solduriclienti.nr_contract, solduriclienti.data_scadenta
                                                    HAVING (((round(Sum(solduriclienti.rata),2))>0) AND ((solduriclienti.data_scadenta) Is Not Null)) OR (((round(Sum(solduriclienti.dob_rem),2))>0) AND ((solduriclienti.data_scadenta) Is Not Null))

                                                    )
                                                AS solduricontracte ON contracte.nr_contract = solduricontracte.nr_contract
                                                GROUP BY contracte.agentia, contracte.nr_contract, contracte.data_contract, contracte.nume, contracte.status
                                                HAVING (((contracte.data_contract)>='2016-09-30') AND ((contracte.status) Not Like 'Anulat%' And (contracte.status) Not Like 'Finalizat%' 
                                                    And (contracte.status) Not Like 'Executare%'));")));
         
         $stoparicreante=$stoparicreante->filter(function ($item) use ($data) {
                                            $item->nr_zile_intarziere=Carbon::parse($item->prima_scadenta_restanta)->diffInDays($data);
                                            return $item->prima_scadenta_restanta < $data && $item->nr_zile_intarziere==265;
                                        });
         
        $adreseEmail=collect(DB::select(DB::raw("SELECT users.email
                                        FROM (notificationuser INNER JOIN users ON notificationuser.user_id = users.id) INNER JOIN notificationtype ON notificationuser.notificationtype_id = notificationtype.id
                                        WHERE (((notificationtype.denumire)='Avertizare stopare creanta'));")))->pluck("email");
       
        

        
        
             $antetTabel=[
            ["col"=>"agentia","denumire"=>"Agentia","type"=>"","align"=>"left","width"=>"20%"], 
            ["col"=>"nume","denumire"=>"Partener","type"=>"","align"=>"center","width"=>"10%"],
            ["col"=>"nr_contract","denumire"=>"Nr contract","type"=>"","align"=>"center","width"=>"10%"],
            ];
                
            $tabel=collect($stoparicreante);
            $groupBy=[
                                                           
                           ];   
            $totalBy=[
            
                          ];

            $titluRaport="DianaIFNWeb Alerta Avertizare Stopari Creanta (".count($stoparicreante)." cazuri) in data de ".dateFormatAfisare($data);
            $company=Company::get()->first();
             
            $i=1;
            
          
               $company_id=$company->id;
             
                 $titluSheet="Avertizare stopare creante";
                
                $columnFormat=[
                                 // 'D' => NumberFormat::FORMAT_DATE_DDMMYYYY,
                                 // 'I' => NumberFormat::FORMAT_CURRENCY_EUR_SIMPLE
                               ];
               
               
           $numefis='public/'.$company->slug.'/avertizare_stopare_creanta_'.time().".xls";      
          Excel::store( 
             (new SablonExport)->forCompany($company_id,$titluSheet,$tabel,$antetTabel,$groupBy,$totalBy,$columnFormat,$titluRaport),$numefis);
              
           Mail::to($adreseEmail)
                 ->send(
                    new AlertaSablonEmail(
                            $antetTabel,
                            $titluRaport,
                            $tabel,
                            $groupBy,
                            $totalBy,
                            $i,
                            $company,
                            $numefis
                        )
                 );
         
        
        
      } catch (\Exception $e) {
        
        $user=User::where("email","stefan.voinea@gmail.com")->first();
        Mail::to("stefan.voinea@gmail.com")
        ->send(new AlertaEroareEmail("ALERTA AVERTIZARE STOPARE CREANTA  ",$e->getMessage(),$e,$user));
        return response()->json(['message' => $e->getMessage()], 500);
      }   
          
    }
    public function alertecontractesupravegheate()
    {
       
        try{
            $data=Carbon::today()->format("Y-m-d");
            $contracteFinalizate=collect(DB::select(DB::raw("SELECT contracte.nr_contract, contracte.data_contract, contracte.agentia, contracte.nume, contracte.tip_client, contracte.status, contracte.data_finalizarii
                            FROM contracte INNER JOIN contractesubsupraveghere ON contracte.nr_contract = contractesubsupraveghere.nr_contract
                            WHERE contracte.status='Finalizat' AND contracte.data_finalizarii<='".$data."';")));
       

            $contracteRestante=collect(DB::select(DB::raw("SELECT contracte.agentia, contracte.nr_contract, contracte.data_contract, contracte.nume, contracte.tip_client,min(data_scadenta) as prima_scadenta_restanta
                                                    FROM contracte INNER JOIN 
                                                    (
                                                        SELECT solduriclienti.nr_contract, round(Sum(solduriclienti.rata),2) AS SumOfrata, round(Sum(solduriclienti.dob_rem),2) AS SumOfdob_rem, solduriclienti.data_scadenta
                                                        FROM solduriclienti INNER JOIN contractesubsupraveghere ON solduriclienti.nr_contract = contractesubsupraveghere.nr_contract
                                                        GROUP BY solduriclienti.nr_contract, solduriclienti.data_scadenta
                                                        HAVING (((round(Sum(solduriclienti.rata),2))>0) AND ((solduriclienti.data_scadenta) Is Not Null)) OR (((round(Sum(solduriclienti.dob_rem),2))>0) AND ((solduriclienti.data_scadenta) Is Not Null))
                                                    )

                                                    as solduricontracte ON contracte.nr_contract = solduricontracte.nr_contract
                                                    GROUP BY contracte.agentia, contracte.nr_contract, contracte.data_contract, contracte.nume, contracte.tip_client;")));

            $contracteRestante=$contracteRestante->filter(function ($item) use ($data) {
                                            $item->nr_zile_intarziere=Carbon::parse($item->prima_scadenta_restanta)->diffInDays($data);
                                            return $item->prima_scadenta_restanta < $data && $item->nr_zile_intarziere>=15;
                                        });
        $adreseEmail=collect(DB::select(DB::raw("SELECT users.email
                                        FROM (notificationuser INNER JOIN users ON notificationuser.user_id = users.id) INNER JOIN notificationtype ON notificationuser.notificationtype_id = notificationtype.id
                                        WHERE (((notificationtype.denumire)='Contracte supravegheate'));")))->pluck("email");
       
            $sheeturi=[];
             $antetTabel=[
            ["col"=>"agentia","denumire"=>"Agentia","type"=>"","align"=>"center","width"=>"10%"],
            ["col"=>"nr_contract","denumire"=>"Nr contract","type"=>"","align"=>"left","width"=>"20%"], 
            ["col"=>"data_contract","denumire"=>"Data contract","type"=>"Date","align"=>"center","width"=>"10%"],
            ["col"=>"nume","denumire"=>"Nume","type"=>"","align"=>"center","width"=>"5%"],
            ["col"=>"tip_client","denumire"=>"Tip client","type"=>"","align"=>"center","width"=>"10%"],
            ["col"=>"data_finalizarii","denumire"=>"Data finalizarii","type"=>"Date","align"=>"center","width"=>"10%"],
            
            ];
                
            $tabel=collect($contracteFinalizate);
            $groupBy=[
                                                           
                     ];   
            $totalBy=[
            
                     ];
            $titluRaport="DianaIFNWeb Alerta contracte supravegheate la data de ".dateFormatAfisare($data);
            $company=Company::get()->first();
             
            $i=1;
            
          
               $company_id=$company->id;
             
                 $titluSheet="Contracte supravegheate finalizate";
                 
                  $columnFormat=[
                                 // 'D' => NumberFormat::FORMAT_DATE_DDMMYYYY,
                                 // 'I' => NumberFormat::FORMAT_CURRENCY_EUR_SIMPLE
                               ];
               
               
           $numefis='public/'.$company->slug.'/contracte_supravegheate_'.time().".xls";      
                  $sheet= new \StdClass;
                  $sheet->titluSheet=$titluSheet;
                  $sheet->tabel=$tabel;
                  $sheet->antetTabel=$antetTabel;
                  $sheet->totalBy=$totalBy;    
                  $sheet->groupBy=$groupBy;
                  $sheet->columnFormat=$columnFormat;
                  array_push($sheeturi,$sheet);
            
             $antetTabel=[
            ["col"=>"agentia","denumire"=>"Agentia","type"=>"","align"=>"center","width"=>"10%"],
            ["col"=>"nr_contract","denumire"=>"Nr contract","type"=>"","align"=>"left","width"=>"20%"], 
            ["col"=>"data_contract","denumire"=>"Data contract","type"=>"Date","align"=>"center","width"=>"10%"],
            ["col"=>"nume","denumire"=>"Nume","type"=>"","align"=>"center","width"=>"5%"],
            ["col"=>"tip_client","denumire"=>"Tip client","type"=>"","align"=>"center","width"=>"10%"],
            ["col"=>"prima_scadenta_restanta","denumire"=>"Data scadenta restanta","type"=>"Date","align"=>"center","width"=>"10%"],
            ["col"=>"nr_zile_intarziere","denumire"=>"Nr zile intarziere","type"=>"","align"=>"center","width"=>"10%"],
            
            ];
                
            $tabel=collect($contracteRestante);
            $groupBy=[
                                                           
                     ];   
            $totalBy=[
            
                     ];
            
            $company=Company::get()->first();
             
            $i=1;
            
          
               $company_id=$company->id;
             
                 $titluSheet="Contracte supravegheate restante";
                 
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
                     
                 
                 
            Excel::store((new SablonMultipleSheetsExport)->forCompany($company_id,$sheeturi,$titluRaport),$numefis);
          
              
           Mail::to($adreseEmail)
                 ->send(
                    new AlertaSablonEmail(
                            [],
                            $titluRaport,
                            [],
                            [],
                            [],
                            $i,
                            $company,
                            $numefis
                        )
                 );
         
        
        
      } catch (\Exception $e) {
        
        $user=User::where("email","stefan.voinea@gmail.com")->first();
        Mail::to("stefan.voinea@gmail.com")
        ->send(new AlertaEroareEmail("ALERTA CONTRACTE SUPRAVEGHEATE ",$e->getMessage(),$e,$user));
        return response()->json(['message' => $e->getMessage()], 500);
      }   
          
    }
    public function alertacontractsemnatbroker($partener,$nr_contract,$sursa_de_informare,$user){
     try{ 
        
        
        $i=1;
        
        $company=Company::where("id",1)->get()->first(); 
        
        $adreseEmail=collect(DB::select(DB::raw("SELECT users.email
                                        FROM (notificationuser INNER JOIN users ON notificationuser.user_id = users.id) INNER JOIN notificationtype ON notificationuser.notificationtype_id = notificationtype.id
                                        WHERE (((notificationtype.denumire)='Alerta contract semnat broker'));")))->pluck("email");
                
        $contract=Contract::where("nr_contract",$nr_contract)->first();
        $agentia=Gestiune::where("denumire",$contract->agentia)->first();
        $subiect="DianaIFNWeb Alerta contract semnat broker ".$partener." nr contract: ".$nr_contract ." ". $sursa_de_informare;
        $mesaj=nl2br("DianaIFNWeb Alerta contract semnat broker ".$partener." nr contract: ".$nr_contract." ". $sursa_de_informare." <br><hr> Utilizator:".$user->name);
          $raspuns= Mail::to($adreseEmail)
                  //  ->cc($agentia->email)
                   ->send(
                     new AlertaEmail(
                                $subiect,
                                $mesaj)
                 );
                

        

      } catch (\Exception $e) {
        Mail::to("stefan.voinea@gmail.com")
        ->send(new AlertaEroareEmail("Alerta contract semnat broker ",$e->getMessage(),$e,$user));
        return response()->json(['message' => $e->getMessage()], 500);
      }  

          //return json_encode($numefis);
  }
     public function alertastoparedobanzipenoug52($partener,$nr_contract,$user){
     try{ 
        
        
        $i=1;
        
        $company=Company::where("id",1)->get()->first(); 
       
        $adreseEmail=collect(DB::select(DB::raw("SELECT users.email
                                        FROM (notificationuser INNER JOIN users ON notificationuser.user_id = users.id) INNER JOIN notificationtype ON notificationuser.notificationtype_id = notificationtype.id
                                        WHERE (((notificationtype.denumire)='Alerta stopare dobanzi penalizatoare OUG 52'));")))->pluck("email");
                
        
        $subiect="DianaIFNWeb Alerta stopare dobanzi penalizatoare OUG 52 ".$partener." nr contract: ".$nr_contract;
        $mesaj=nl2br("DianaIFNWeb Alerta stopare dobanzi penalizatoare OUG 52 ".$partener." nr contract: ".$nr_contract." <br><hr> Utilizator:".$user->name);
          $raspuns= Mail::to($adreseEmail)
                   ->send(
                     new AlertaEmail(
                                $subiect,
                                $mesaj)
                 );
                

        

      } catch (\Exception $e) {
        Mail::to("stefan.voinea@gmail.com")
        ->send(new AlertaEroareEmail("Alerta stopare dobanzi penalizatoare OUG 52 ",$e->getMessage(),$e,$user));
        return response()->json(['message' => $e->getMessage()], 500);
      }  

          //return json_encode($numefis);
  }
     public function alertablocarenotecontabile($user){
     try{ 
        
        $perioadablocata=Perioadablocata::first();
     
        $i=1;
       
        $company=Company::where("id",1)->get()->first(); 
       
        $adreseEmail=collect(DB::select(DB::raw("SELECT users.email
                                        FROM (notificationuser INNER JOIN users ON notificationuser.user_id = users.id) INNER JOIN notificationtype ON notificationuser.notificationtype_id = notificationtype.id
                                        WHERE (((notificationtype.denumire)='Blocare note contabile'));")))->pluck("email");
                
        
        $subiect="DianaIFNWeb ALERTA BLOCARE NOTE CONTABILE  ".dateFormatAfisare($perioadablocata->data_note);
        $mesaj=nl2br("DianaIFNWeb ALERTA BLOCARE NOTE CONTABILE    <hr><br/><br/> Note contabile: ".dateFormatAfisare($perioadablocata->data_note)."<br/><br/> Contracte: ".dateFormatAfisare($perioadablocata->data_contracte)."<br/><br/>  Acte aditionale: ".dateFormatAfisare($perioadablocata->data_acte_aditionale). "<br/><br/> Facturi emise: ".dateFormatAfisare($perioadablocata->data_vanzari)."<br/><br/> Incasari: ".dateFormatAfisare($perioadablocata->data_incasari)."<br/><br/> Documente primite: ".dateFormatAfisare($perioadablocata->data_documenteprimite)."<br/><br/> Plati: ".dateFormatAfisare($perioadablocata->data_plati)."<br/><br/><hr> Utilizator: ".$user->name);
          $raspuns= Mail::to($adreseEmail)
                   ->send(
                     new AlertaEmail(
                                $subiect,
                                $mesaj)
                 );
                

        

      } catch (\Exception $e) {
        Mail::to("stefan.voinea@gmail.com")
        ->send(new AlertaEroareEmail("ALERTA BLOCARE PERIOADA ",$e->getMessage(),$e,$user));
        return response()->json(['message' => $e->getMessage()], 500);
      }  

          //return json_encode($numefis);
  }
    public function alertachitantaanulata($chitanta_id,$user){
     try{ 
        $articole=array();
        $chitanta=Antetincasaricontracte::where("id",$chitanta_id)->first();
        $incasareTotal = Incasaricontracte::where('antetincasaricontracte_id',$chitanta_id)  
                              ->with(["contract","nomtertiincasare"])
                              ->orderBy("id")
                              ->get();
        $i=1;
       
        $agentia=Gestiune::where("denumire",$chitanta->agentia)->get()->first();
        $company=Company::where("id",1)->get()->first(); 
        $datalistarii=datasioraFormatAfisare(Carbon::now());
         $numefis= storage_path('app/public/'.$company->slug.'/'.strtoupper($chitanta->tip_document.'_'.$chitanta->agentia."_".$chitanta->tip_valuta."_".$chitanta->nr_document."_".$chitanta->data_document."_".Str::slug($chitanta->nume,"_").'_anulata.pdf'));
        if(File::exists($numefis)){
                File::delete($numefis);
        };
        // ob_end_clean(); 
        // ob_start();   
        $viewname='';
        if(strtoupper($chitanta->tip_valuta)=="EUR"){
          $viewname='incasaricontracte.chitanta_in_valuta';
        }else{
         $viewname='incasaricontracte.chitanta';

        }
         
        $pdf = \Barryvdh\Snappy\Facades\SnappyPdf::loadView($viewname, [
            'company'=>$company,
            'agentia'=>$agentia,
            'datalistarii'=>$datalistarii,
            'incasare'=>$incasareTotal,
            'user'=>$user,
            'i' => $i
        ])->setPaper('a4')
        ->setOption('print-media-type', true)
        ->setOption('enable-local-file-access', true)
        ->setOrientation('portrait');
              
                  // $pdf->setOption('javascript-delay', 3000);

                $pdf->save($numefis);
          $antetTabel=[];  
           $titluRaport="DianaIFNWeb Chitanta ANULATA ".'_'.strtoupper($chitanta->agentia."_".$chitanta->tip_valuta."_".$chitanta->nr_document."_".$chitanta->data_document."_".Str::slug($chitanta->nume,"_").'_anulata.pdf');
           $tabel=[];
           $groupBy=[];
           $totalBy=[];
           $i=0;
           
        $adreseEmail=collect(DB::select(DB::raw("SELECT users.email
                                        FROM (notificationuser INNER JOIN users ON notificationuser.user_id = users.id) INNER JOIN notificationtype ON notificationuser.notificationtype_id = notificationtype.id
                                        WHERE (((notificationtype.denumire)='Chitanta anulata'));")));
                
        
        foreach ($adreseEmail as $adresa){
          $raspuns= Mail::to($adresa->email)
                 ->send(
                    new AlertaSablonCuAtasamentEmail(
                            $antetTabel,
                            $titluRaport,
                            $tabel,
                            $groupBy,
                            $totalBy,
                            $i,
                            $company,
                            $numefis
                        )
                 );
                

        }

      } catch (\Exception $e) {
        Mail::to("stefan.voinea@gmail.com")
        ->send(new AlertaEroareEmail("ALERTA CHITANTA ANULATA ",$e->getMessage(),$e,$user));
        return response()->json(['message' => $e->getMessage()], 500);
      }  

          //return json_encode($numefis);
  }
     public function alertaparticipantgradriscridicat($chitanta_id,$user){
     try{ 
        
        $chitanta=Antetincasaricontracte::where("id",$chitanta_id)->first();
     
        $i=1;
        $agentia=Gestiune::where("denumire",$chitanta->agentia)->get()->first();
        $company=Company::where("id",1)->get()->first(); 
        $datalistarii=datasioraFormatAfisare(Carbon::now());
        
       
        // ob_end_clean(); 
        // ob_start();   
     
           
        $adreseEmail=collect(DB::select(DB::raw("SELECT users.email
                                        FROM (notificationuser INNER JOIN users ON notificationuser.user_id = users.id) INNER JOIN notificationtype ON notificationuser.notificationtype_id = notificationtype.id
                                        WHERE (((notificationtype.denumire)='Incasare grad risc KYC ridicat'));")))->pluck("email");
                
        
        $subiect="DianaIFNWeb ALERTA INCASARE GRAD RISC KYC RIDICAT  ".$chitanta->agentia." ".$chitanta->nume;
        $mesaj="DianaIFNWeb ALERTA INCASARE GRAD RISC KYC RIDICAT  ".$chitanta->agentia." ".$chitanta->nume." Utilizator:".$user->name;
          $raspuns= Mail::to($adreseEmail)
                   ->cc($agentia->email)
                 ->send(
                     new AlertaEmail(
                                $subiect,
                                $mesaj)
                 );
                

        

      } catch (\Exception $e) {
        Mail::to("stefan.voinea@gmail.com")
        ->send(new AlertaEroareEmail("ALERTA INCASARE GRAD RISC KYC RIDICAT ",$e->getMessage(),$e,$user));
        return response()->json(['message' => $e->getMessage()], 500);
      }  

          //return json_encode($numefis);
  }
    public function alertaincasarebroker($chitanta_id,$user){
     try{ 
        
        $chitanta=Antetincasaricontracte::where("id",$chitanta_id)->first();
     
        $i=1;
        $agentia=Gestiune::where("denumire",$chitanta->agentia)->get()->first();
        $company=Company::where("id",1)->get()->first(); 
        $datalistarii=datasioraFormatAfisare(Carbon::now());
        
       
        // ob_end_clean(); 
        // ob_start();   
     
           
        $adreseEmail=collect(DB::select(DB::raw("SELECT users.email
                                        FROM (notificationuser INNER JOIN users ON notificationuser.user_id = users.id) INNER JOIN notificationtype ON notificationuser.notificationtype_id = notificationtype.id
                                        WHERE (((notificationtype.denumire)='Incasare broker'));")))->pluck("email");
                
        
        $subiect="DianaIFNWeb ALERTA INCASARE BROKER  ".$chitanta->agentia." ".$chitanta->nume;
        $mesaj="DianaIFNWeb ALERTA INCASARE BROKER  ".$chitanta->agentia." ".$chitanta->nume." Utilizator:".$user->name;
          $raspuns= Mail::to($adreseEmail)
                   ->cc($agentia->email)
                 ->send(
                     new AlertaEmail(
                                $subiect,
                                $mesaj)
                 );
                

        

      } catch (\Exception $e) {
        Mail::to("stefan.voinea@gmail.com")
        ->send(new AlertaEroareEmail("ALERTA INCASARE BROKER ",$e->getMessage(),$e,$user));
        return response()->json(['message' => $e->getMessage()], 500);
      }  

          //return json_encode($numefis);
  }
    public function alertaadjudecareimobil($chitanta_id,$user){
     try{ 
        
        $chitanta=Antetincasaricontracte::where("id",$chitanta_id)->first();
       $tarirezidenta=collect(DB::select(DB::raw("SELECT partisolicitare.tara_de_rezidenta, antetincasaricontracte.id
                    FROM ((antetincasaricontracte INNER JOIN incasaricontract ON antetincasaricontracte.id = incasaricontract.antetincasaricontracte_id) INNER JOIN contracte ON incasaricontract.contract_id = contracte.id) INNER JOIN partisolicitare ON contracte.solicitare_id = partisolicitare.solicitare_id
                    GROUP BY partisolicitare.tara_de_rezidenta, antetincasaricontracte.id
                    HAVING (((antetincasaricontracte.id)=".$chitanta_id."));")))->pluck("tara_de_rezidenta");
        $i=1;
        $agentia=Gestiune::where("denumire",$chitanta->agentia)->get()->first();
        $company=Company::where("id",1)->get()->first(); 
        $datalistarii=datasioraFormatAfisare(Carbon::now());
        
       
        // ob_end_clean(); 
        // ob_start();   
     
           
        $adreseEmail=collect(DB::select(DB::raw("SELECT users.email
                                        FROM (notificationuser INNER JOIN users ON notificationuser.user_id = users.id) INNER JOIN notificationtype ON notificationuser.notificationtype_id = notificationtype.id
                                        WHERE (((notificationtype.denumire)='Adjudecare imobil'));")))->pluck("email");
                
        
        $subiect="DianaIFNWeb ALERTA ADJUDECARE IMOBIL  ".$chitanta->agentia." ".$chitanta->nume;
        $mesaj="DianaIFNWeb ALERTA AJUDECARE IMOBIL  ".$chitanta->agentia." ".$chitanta->nume." ".$tarirezidenta." Utilizator:".$user->name;
          $raspuns= Mail::to($adreseEmail)
                  // ->cc($agentia->email)
                 ->send(
                     new AlertaEmail(
                                $subiect,
                                $mesaj)
                 );
                

        

      } catch (\Exception $e) {
        Mail::to("stefan.voinea@gmail.com")
        ->send(new AlertaEroareEmail("ALERTA ADJUDECARE IMOBIL ",$e->getMessage(),$e,$user));
        return response()->json(['message' => $e->getMessage()], 500);
      }  

          //return json_encode($numefis);
  }
    public function alertaincasarenumerarpeste10000($chitanta_id,$user){
     try{ 
        
        $chitanta=Antetincasaricontracte::where("id",$chitanta_id)->first();
       $tarirezidenta=collect(DB::select(DB::raw("SELECT partisolicitare.tara_de_rezidenta, antetincasaricontracte.id
                    FROM ((antetincasaricontracte INNER JOIN incasaricontract ON antetincasaricontracte.id = incasaricontract.antetincasaricontracte_id) INNER JOIN contracte ON incasaricontract.contract_id = contracte.id) INNER JOIN partisolicitare ON contracte.solicitare_id = partisolicitare.solicitare_id
                    GROUP BY partisolicitare.tara_de_rezidenta, antetincasaricontracte.id
                    HAVING (((antetincasaricontracte.id)=".$chitanta_id."));")))->pluck("tara_de_rezidenta");
        $i=1;
        $agentia=Gestiune::where("denumire",$chitanta->agentia)->get()->first();
        $company=Company::where("id",1)->get()->first(); 
        $datalistarii=datasioraFormatAfisare(Carbon::now());
        
       
        // ob_end_clean(); 
        // ob_start();   
     
           
        $adreseEmail=collect(DB::select(DB::raw("SELECT users.email
                                        FROM (notificationuser INNER JOIN users ON notificationuser.user_id = users.id) INNER JOIN notificationtype ON notificationuser.notificationtype_id = notificationtype.id
                                        WHERE (((notificationtype.denumire)='Incasare numerar peste 10000 EUR'));")))->pluck("email");
                
        
        $subiect="DianaIFNWeb ALERTA INCASARE NUMERAR PESTE 10.000 EURO  ".$chitanta->agentia." ".$chitanta->nume;
        $mesaj="DianaIFNWeb ALERTA INCASARE NUMERAR PESTE 10.000 EURO  ".$chitanta->agentia." ".$chitanta->nume." ".$tarirezidenta." Utilizator:".$user->name;
          $raspuns= Mail::to($adreseEmail)
                   ->cc($agentia->email)
                 ->send(
                     new AlertaEmail(
                                $subiect,
                                $mesaj)
                 );
                

        

      } catch (\Exception $e) {
        Mail::to("stefan.voinea@gmail.com")
        ->send(new AlertaEroareEmail("ALERTA INCASARE NUMERAR PESTE 10.000 EURO ",$e->getMessage(),$e,$user));
        return response()->json(['message' => $e->getMessage()], 500);
      }  

          //return json_encode($numefis);
  }
     public function alertarambursareanticipata($chitanta_id,$user){
     try{ 
        
        $chitanta=Antetincasaricontracte::where("id",$chitanta_id)->first();
        $tarirezidenta=collect(DB::select(DB::raw("SELECT partisolicitare.tara_de_rezidenta, antetincasaricontracte.id
                    FROM ((antetincasaricontracte INNER JOIN incasaricontract ON antetincasaricontracte.id = incasaricontract.antetincasaricontracte_id) INNER JOIN contracte ON incasaricontract.contract_id = contracte.id) INNER JOIN partisolicitare ON contracte.solicitare_id = partisolicitare.solicitare_id
                    GROUP BY partisolicitare.tara_de_rezidenta, antetincasaricontracte.id
                    HAVING (((antetincasaricontracte.id)=".$chitanta_id."));")))->pluck("tara_de_rezidenta");
        $i=1;
        $agentia=Gestiune::where("denumire",$chitanta->agentia)->get()->first();
        $company=Company::where("id",1)->get()->first(); 
        $datalistarii=datasioraFormatAfisare(Carbon::now());
        
       
        // ob_end_clean(); 
        // ob_start();   
     
           
        $adreseEmail=collect(DB::select(DB::raw("SELECT users.email
                                        FROM (notificationuser INNER JOIN users ON notificationuser.user_id = users.id) INNER JOIN notificationtype ON notificationuser.notificationtype_id = notificationtype.id
                                        WHERE (((notificationtype.denumire)='Rambursare anticipata'));")))->pluck("email");
                
        
        $subiect="DianaIFNWeb ALERTA RAMBURSARE ANTICIPATA ".$chitanta->agentia." ".$chitanta->nume;
        $mesaj="DianaIFNWeb ALERTA RAMBURSARE ANTICIPATA ".$chitanta->agentia." ".$chitanta->nume." ".$tarirezidenta." Utilizator:".$user->name;
          $raspuns= Mail::to($adreseEmail)
                    ->cc($agentia->email)
                 ->send(
                     new AlertaEmail(
                                $subiect,
                                $mesaj)
                 );
                

        

      } catch (\Exception $e) {
        Mail::to("stefan.voinea@gmail.com")
        ->send(new AlertaEroareEmail("ALERTA RAMBURSARE ANTICIPATA",$e->getMessage(),$e,$user));
        return response()->json(['message' => $e->getMessage()], 500);
      }  

          //return json_encode($numefis);
  }
    public function alertachitanta($chitanta_id,$user){
     try{ 
        $articole=array();
        $chitanta=Antetincasaricontracte::where("id",$chitanta_id)->first();
        $incasareTotal = Incasaricontracte::where('antetincasaricontracte_id',$chitanta_id)  
                              ->with(["contract","nomtertiincasare"])
                              ->orderBy("id")
                              ->get();
        $i=1;
       
        $agentia=Gestiune::where("denumire",$chitanta->agentia)->get()->first();
        $company=Company::where("id",1)->get()->first(); 
        $datalistarii=datasioraFormatAfisare(Carbon::now());
        
        $numefis= storage_path('app/public/'.$company->slug.'/'.strtoupper($chitanta->tip_document.'_'.$chitanta->agentia."_".$chitanta->tip_valuta."_".$chitanta->nr_document."_".$chitanta->data_document."_".Str::slug($chitanta->nume,"_").'_financiar.pdf'));
        
        if(File::exists($numefis)){
                File::delete($numefis);
        };
        // ob_end_clean(); 
        // ob_start();   
        $viewname='';
        if(strtoupper($chitanta->tip_valuta)=="EUR"){
          $viewname='incasaricontracte.chitanta_in_valuta';
        }else{
         $viewname='incasaricontracte.chitanta';

        }
         
        $pdf = \Barryvdh\Snappy\Facades\SnappyPdf::loadView($viewname, [
            'company'=>$company,
            'agentia'=>$agentia,
            'datalistarii'=>$datalistarii,
            'incasare'=>$incasareTotal,
            'user'=>$user,
            'i' => $i
        ])->setPaper('a4')
        ->setOption('print-media-type', true)
        ->setOption('enable-local-file-access', true)
        ->setOrientation('portrait');
                  // $pdf->setOption('javascript-delay', 3000);

                $pdf->save($numefis);
          $antetTabel=[];  
           $titluRaport="DianaIFNWeb ".strtoupper($chitanta->tip_document.'_'.$chitanta->agentia."_".$chitanta->tip_valuta."_".$chitanta->nr_document."_".$chitanta->data_document."_".Str::slug($chitanta->nume,"_"));
           $tabel=[];
           $groupBy=[];
           $totalBy=[];
           $i=0;
           
        $adreseEmail=collect(DB::select(DB::raw("SELECT users.email
                                        FROM (notificationuser INNER JOIN users ON notificationuser.user_id = users.id) INNER JOIN notificationtype ON notificationuser.notificationtype_id = notificationtype.id
                                        WHERE (((notificationtype.denumire)='Chitanta'));")));
                
        
        foreach ($adreseEmail as $adresa){
          $raspuns= Mail::to($adresa->email)
                 ->send(
                    new AlertaSablonCuAtasamentEmail(
                            $antetTabel,
                            $titluRaport,
                            $tabel,
                            $groupBy,
                            $totalBy,
                            $i,
                            $company,
                            $numefis
                        )
                 );
                

        }

      } catch (\Exception $e) {
        Mail::to("stefan.voinea@gmail.com")
        ->send(new AlertaEroareEmail("ALERTA CHITANTA INCASARE CONTRACT",$e->getMessage(),$e,$user));
        return response()->json(['message' => $e->getMessage()], 500);
      }  

          //return json_encode($numefis);
  }
     public function incasariPtrAlerta()
    {
       try{
        $data=Carbon::today()->format("Y-m-d");
        $sarbatoare=Sarbatorilegale::where("data",$data)->first();
        if(!$sarbatoare){
        $adreseEmail=collect(DB::select(DB::raw("SELECT users.email
                                        FROM (notificationuser INNER JOIN users ON notificationuser.user_id = users.id) INNER JOIN notificationtype ON notificationuser.notificationtype_id = notificationtype.id
                                        WHERE (((notificationtype.denumire)='Incasari'));")));
        $curs=cursBNR($data,"EUR");
         
           
        $incasari= collect(DB::select(DB::raw("SELECT note.data_doc, Sum(Round(coalesce(IF(note.tip_valuta Like 'EUR',suma_valuta,suma/".$curs."),0),2)) AS echivalent_eur, note.agentia
                      FROM note
                      WHERE ((Not (note.nr_contract) Is Null And (note.nr_contract)<>0) AND ((note.contd) Like '101%' Or (note.contd) Like '271%' Or (note.contd)='999.01' Or (note.contd)='999.02' 
                        Or (note.contd)='999.03' Or (note.contd)='999.04') AND ((note.contc) Like '2817%' Or (note.contc) Like '2812%' Or (note.contc) Like '2037%' Or (note.contc) Like '2027%' 
                        Or (note.contc) Like '2067%' Or (note.contc) Like '2097%' Or (note.contc) Like '2822%' Or (note.contc) Like '2827%' Or (note.contc) Like '2837%' Or (note.contc) Like '3566.04' Or (note.contc) Like '3556.10' Or (note.contc) Like '7029.01'))
                      GROUP BY note.data_doc, note.agentia 
                      HAVING note.data_doc='".$data."' 
                      ORDER BY note.agentia;")));

        foreach ($adreseEmail as $alerta){                          
            

                Mail::to($alerta->email)  
                 ->send(new AlertaIncasariEmail($incasari));
            
        }
    }
      } catch (\Exception $e) {
         $user=User::where("id",1)->get()->first();
        Mail::to("stefan.voinea@gmail.com")
        ->send(new AlertaEroareEmail("ALERTA INCASARI",$e->getMessage(),$e,$user));
        return response()->json(['message' => $e->getMessage()], 500);
      }  
 
    }

    public function alerteGenerareNote($luna,$anul,$tipGenerare)
    {
       
       
        $adreseEmail=collect(DB::select(DB::raw("SELECT users.email
                                        FROM (notificationuser INNER JOIN users ON notificationuser.user_id = users.id) INNER JOIN notificationtype ON notificationuser.notificationtype_id = notificationtype.id
                                        WHERE (((notificationtype.denumire)='Alerta generare note'));")))->pluck("email");

        
        $userName="ROBOT";
        if(Auth::check()){
            $userName=Auth::user()->name;
        }
        
        Mail::to($adreseEmail)
                            ->send(
                            new AlertaEmail(
                                "DianaIFNWeb Generare note luna ".$luna." anul ".$anul,
                                "Au fost generate notele contabile ".$tipGenerare. " pentru luna ".$luna." anul ".$anul ." de catre ".$userName
                            )
        );
   
    }
    public function alertaObligatiiContractuale()
    {
       
       //TRANSMIT ALERTA ZILNIC 
       $data=Carbon::today()->format("Y-m-d");
       $adreseEmail=collect(DB::select(DB::raw("SELECT users.email
                                        FROM (notificationuser INNER JOIN users ON notificationuser.user_id = users.id) INNER JOIN notificationtype ON notificationuser.notificationtype_id = notificationtype.id
                                        WHERE (((notificationtype.denumire)='Alerta obligatii contractuale'));")))->pluck("email");
        
         $gestiuni=Gestiune::get();
        foreach($gestiuni as $gestiune){
                 
                    $records=DB::select(DB::raw("SELECT contracte.nr_contract, contracte.data_contract, contracte.agentia, contracte.nume, contracte.data_acordarii, contracte.obligatii_contractuale, contracte.termen_obligatii_contractuale
                                    FROM contracte
                                    WHERE (((contracte.termen_obligatii_contractuale)='".$data."') AND (ISNULL(contracte.data_indeplinire_obligatii_contractuale))) OR (((contracte.data_acordarii)='".$data."') AND (NOT ISNULL(contracte.termen_obligatii_contractuale) ) AND (ISNULL(contracte.data_indeplinire_obligatii_contractuale)));
                                        "));

                    $records=collect($records)->where("agentia",$gestiune->denumire);
                    if(count($records)!=0){
                    $recordsAll=collect($records); //->get();
    
        
      
        $antetTabel=[
                                   ["col"=>"agentia","denumire"=>"Agentia","type"=>"","align"=>"center","width"=>"10%"], 
                                   ["col"=>"nume","denumire"=>"Nume","type"=>"","align"=>"center","width"=>"10%"], 
                                   ["col"=>"nr_contract","denumire"=>"Nr contract","type"=>"","align"=>"center","width"=>"10%"], 
                                   ["col"=>"data_contract","denumire"=>"Data contract","type"=>"Date","align"=>"center","width"=>"10%"], 
                                   ["col"=>"data_acordarii","denumire"=>"Data acordarii","type"=>"Date","align"=>"center","width"=>"10%"], 
                                   ["col"=>"obligatii_contractuale","denumire"=>"Obligatii contractuale","type"=>"","align"=>"center","width"=>"10%"], 
                                   ["col"=>"termen_obligatii_contractuale","denumire"=>"Termen obligatii contractuale","type"=>"Date","align"=>"center","width"=>"10%"], 
                                   
                                ];
                
                 $tabel=collect($recordsAll); //->groupBy("contd");
                 $groupBy=[                                           
                              // ["col"=>"contd","denumire"=>"Cont","type"=>"","align"=>"center","width"=>"10%"],
                           ];   
                 $totalBy=[];
                 $titluRaport="DianaIFNWeb Situatie obligatii contractuale la data de " .dateFormatAfisare($data);
                 $company=Company::where("id",1)->first();
                 $company_id=1;
                 $titluSheet="Situatie obligatii contractuale ";
                 $fileName="situatie_obligatii_contractuale.xls";
                 $columnFormat=[
                                 // 'D' => NumberFormat::FORMAT_DATE_DDMMYYYY,
                                 // 'I' => NumberFormat::FORMAT_CURRENCY_EUR_SIMPLE
                               ];
               $i=0;
               $numefis='public/'.$company->slug.'/situatie_obligatii_contractuale_'.time().".xls";      
          Excel::store( 
             (new SablonExport)->forCompany($company_id,$titluSheet,$tabel,$antetTabel,$groupBy,$totalBy,$columnFormat,$titluRaport),$numefis);
           
           Mail::to($adreseEmail)
                  ->cc($gestiune->email)
                  ->send(
                    new AlertaSablonEmail(
                            $antetTabel,
                            $titluRaport,
                            $tabel,
                            $groupBy,
                            $totalBy,
                            $i,
                            $company,
                            $numefis
                        )
                 );
            } 
       }
        
          
    }
    public function alertaExpirareEvaluariGarantii()
    {
       
       //TRANSMIT ALERTA ZILNIC 
       $data=Carbon::today()->format("Y-m-d");
       $adreseEmail=collect(DB::select(DB::raw("SELECT users.email
                                        FROM (notificationuser INNER JOIN users ON notificationuser.user_id = users.id) INNER JOIN notificationtype ON notificationuser.notificationtype_id = notificationtype.id
                                        WHERE (((notificationtype.denumire)='Alerta expirare evaluari garantii'));")))->pluck("email");
        
        $gestiuni=Gestiune::get();
        foreach($gestiuni as $gestiune){
                 
                    $records=DB::select(DB::raw("
                                      SELECT '".$data."' AS data_afisare,grv.cod_garantie,g.proprietar,g.descriere,g.adresa,grv.data_raport AS data_ultimei_evaluari,DATE_ADD(grv.data_raport, INTERVAL 1 YEAR) AS data_scadentei,grv.data_raport,DATEDIFF(DATE_ADD(grv.data_raport, INTERVAL 12 MONTH), NOW()) AS zile_ramase, g.agentia
                                        FROM garantiireevaluare2024 AS grv
                                        INNER JOIN garantii2024 AS g ON grv.cod_garantie = g.cod_garantie INNER JOIN 
                                        (
                                          SELECT garantiireevaluare2024.cod_garantie, Max(garantiireevaluare2024.data_raport) AS maxOfdata_raport
                                          FROM garantiireevaluare2024
                                          GROUP BY garantiireevaluare2024.cod_garantie
                                        )
                                        AS  u ON grv.cod_garantie = u.cod_garantie AND grv.data_raport = u.maxOfdata_raport
                                        WHERE DATE_ADD(grv.data_raport, INTERVAL 1 YEAR) <= DATE_ADD('".$data."', INTERVAL 3 MONTH) AND g.status = 'ACTIV'
                                        ORDER BY DATE_ADD(grv.data_raport, INTERVAL 1 YEAR);
                                    "));

                    $records=collect($records)->whereIn("agentia",$gestiune->denumire);
                    if(count($records)!=0){
                    $recordsAll=collect($records); //->get();
                    $antetTabel=[
                                   ["col"=>"data_afisare","denumire"=>"Data afisare","type"=>"Date","align"=>"center","width"=>"10%"], 
                                   ["col"=>"cod_garantie","denumire"=>"Cod garantie","type"=>"","align"=>"center","width"=>"10%"], 
                                   ["col"=>"proprietar","denumire"=>"Proprietar","type"=>"","align"=>"center","width"=>"10%"], 
                                   ["col"=>"descriere","denumire"=>"Descriere","type"=>"","align"=>"center","width"=>"10%"], 
                                   ["col"=>"adresa","denumire"=>"Adresa","type"=>"","align"=>"center","width"=>"10%"], 
                                   ["col"=>"data_ultimei_evaluari","denumire"=>"Data ultimei evaluari","type"=>"Date","align"=>"center","width"=>"10%"], 
                                   ["col"=>"data_scadentei","denumire"=>"Data scadentei","type"=>"Date","align"=>"center","width"=>"10%"], 
                                   ["col"=>"zile_ramase","denumire"=>"Zile ramase","type"=>"","align"=>"center","width"=>"10%"], 
                                   ["col"=>"agentia","denumire"=>"Agentia","type"=>"","align"=>"center","width"=>"10%"], 
                                ];
                
                 $tabel=collect($recordsAll); //->groupBy("contd");
                 $groupBy=[                                           
                              // ["col"=>"contd","denumire"=>"Cont","type"=>"","align"=>"center","width"=>"10%"],
                           ];   
                 $totalBy=[];
                 $titluRaport="DianaIFNWeb Situatie expirare evaluari garantii agentia ".$gestiune->denumire." la data de " .dateFormatAfisare($data);
                 $company=Company::where("id",1)->first();
                 $company_id=1;
                 $titluSheet="Situatie expirare evaluari garantii ";
                 $fileName="situatie_expirare_evaluari_garantii.xls";
                 $columnFormat=[
                                 // 'D' => NumberFormat::FORMAT_DATE_DDMMYYYY,
                                 // 'I' => NumberFormat::FORMAT_CURRENCY_EUR_SIMPLE
                               ];
               $i=0;
               $numefis='public/'.$company->slug.'/situatie_expirare_evaluari_garantii_'.$gestiune->denumire."_".time().".xls";      
          Excel::store( 
             (new SablonExport)->forCompany($company_id,$titluSheet,$tabel,$antetTabel,$groupBy,$totalBy,$columnFormat,$titluRaport),$numefis);
           
           Mail::to($adreseEmail)
                 ->cc($gestiune->email)   
                 ->send(
                    new AlertaSablonEmail(
                            $antetTabel,
                            $titluRaport,
                            $tabel,
                            $groupBy,
                            $totalBy,
                            $i,
                            $company,
                            $numefis
                        )
                 );
             }
        }
        
          
    }

    public function alertaGradederisc()
    {
       
       //TRANSMIT ALERTA PE DATA DE 20 CU CE TREBUIE ACTUALIZAT LUNA URMATOARE
       $datai=Carbon::today()->endOfMonth()->addDays(1);
       $datasf=Carbon::parse($datai)->copy()->endOfMonth();
    
    
     $adreseEmail=collect(DB::select(DB::raw("SELECT users.email
                                        FROM (notificationuser INNER JOIN users ON notificationuser.user_id = users.id) INNER JOIN notificationtype ON notificationuser.notificationtype_id = notificationtype.id
                                        WHERE (((notificationtype.denumire)='Alerta grad de risc'));")))->pluck("email");

    $records= collect(DB::select(DB::raw("select contracte.agentia, contracte.nr_contract, contracte.data_contract, contracte.nume, partisolicitare.tip_participant as tip_parte, partisolicitare.nume as nume_parte, partisolicitare.adresa, partisolicitare.cnp, contracte.status, partisolicitare.tara_de_rezidenta, partisolicitare.tara_de_origine, partisolicitare.grad_risc, partisolicitare.data_actualizare as data_ultimei_actualizari, if(grad_risc like 'mediu',DATE_ADD(data_actualizare,INTERVAL 2 YEAR),DATE_ADD(data_actualizare,INTERVAL 1 YEAR)) as data_urmatoarei_actualizari
                from partisolicitare inner join contracte on partisolicitare.solicitare_id = contracte.solicitare_id
                where contracte.status not like 'Finalizat' and contracte.status not like 'Anulat' and contracte.status not like 'Retras' and partisolicitare.data_actualizare>='".$datai."' and partisolicitare.data_actualizare<='".$datasf."';")));
   
  
   
    $antetTabel=[
                    ['col'=>'agentia','denumire'=>'Agentia','type'=>'','align'=>'center','width'=>'7%'],
                            ['col'=>'nr_contract','denumire'=>'Nr contract','type'=>'','align'=>'center','width'=>'7%'],
                            ['col'=>'data_contract','denumire'=>'Data contract','type'=>'Date','align'=>'center','width'=>'7%'],
                            ['col'=>'nume','denumire'=>'Nume','type'=>'','align'=>'center','width'=>'7%'],
                            ['col'=>'tip_parte','denumire'=>'Tip parte','type'=>'','align'=>'center','width'=>'7%'],
                            ['col'=>'nume_parte','denumire'=>'Nume parte','type'=>'','align'=>'center','width'=>'7%'],
                            ['col'=>'adresa','denumire'=>'Adresa','type'=>'','align'=>'center','width'=>'7%'],
                            ['col'=>'cnp','denumire'=>'Cnp','type'=>'','align'=>'center','width'=>'7%'],
                            ['col'=>'status','denumire'=>'Status','type'=>'','align'=>'center','width'=>'7%'],
                            ['col'=>'tara_de_rezidenta','denumire'=>'Tara de rezidenta','type'=>'','align'=>'center','width'=>'7%'],
                            ['col'=>'tara_de_origine','denumire'=>'Tara de origine','type'=>'','align'=>'center','width'=>'7%'],
                            ['col'=>'grad_risc','denumire'=>'Grad risc','type'=>'','align'=>'center','width'=>'7%'],
                            ['col'=>'data_ultimei_actualizari','denumire'=>'Data ultimei actualizari','type'=>'Date','align'=>'center','width'=>'7%'],
                            ['col'=>'data_urmatoarei_actualizari','denumire'=>'Data urmatoarei actualizari','type'=>'Date','align'=>'center','width'=>'7%'],
            
            ];                
        
     
            $tabel=collect($records);
            foreach($tabel->groupBy("agentia") as $filtrat){
            $tabel=collect($filtrat);
            $agentia=Gestiune::where("denumire",$filtrat[0]->agentia)->first();
            $groupBy=[
                                                           
                           ];   
            $totalBy=[
            
                          ];
            $titluRaport="Alerta actualizare grade de risc pentru perioada ".dateFormatAfisare($datai)." - ".dateFormatAfisare($datasf);
            $company=Company::get()->first();
             
            $i=1;
            
          
               $company_id=$company->id;
             
                 $titluSheet="Alerta actualizare grade de risc";
                 $fileName="alerta_actualizare_grade_de_risc.xls";
                $columnFormat=[
                                 // 'D' => NumberFormat::FORMAT_DATE_DDMMYYYY,
                                 // 'I' => NumberFormat::FORMAT_CURRENCY_EUR_SIMPLE
                               ];
               
               
           $numefis='public/'.$company->slug.'/alerta_actualizare_grade_de_risc_'.time().".xls";      
          Excel::store( 
             (new SablonExport)->forCompany($company_id,$titluSheet,$tabel,$antetTabel,$groupBy,$totalBy,$columnFormat,$titluRaport),$numefis);
              
           Mail::to($adreseEmail)
                ->cc($agentia->email)
                 ->send(
                    new AlertaSablonEmail(
                            $antetTabel,
                            $titluRaport,
                            $tabel,
                            $groupBy,
                            $totalBy,
                            $i,
                            $company,
                            $numefis
                        )
                 );
            }
        
          
    }
    public function alertaclientiacceptatipeagentii()
    {
          //  DB::beginTransaction();
     try{
       //TRANSMIT ALERTA ZILNIC CU CLIENTI ACCEPTATI NOAPTEA SI SEARA 17 si 18  
       $data=Carbon::today()->format("Y-m-d");
        if(!esteZiLucratoare(Carbon::today())){
        return false;
       }
       $clienti=clientiacceptati($data,null,null);
       Log::info("REZULTAT CLIENTI Acceptati pe agentii");
        //Log::info($clienti->contracte);

        $adreseEmail=collect(DB::select(DB::raw("SELECT users.email
                                        FROM (notificationuser INNER JOIN users ON notificationuser.user_id = users.id) INNER JOIN notificationtype ON notificationuser.notificationtype_id = notificationtype.id
                                        WHERE (((notificationtype.denumire)='Alerta clienti acceptati pe agentii'));")))->pluck("email");
      
        foreach(collect($clienti->contracte)->groupBy("agentia") as $peAgentie){
            $denumireAgentie=$peAgentie[0]->agentia;
            $agentia=Gestiune::where("denumire",$denumireAgentie)->first();
            $clientiAgentie=collect($clienti->contracte)->filter(function ($item) use($denumireAgentie){
                    return $item->agentia==$denumireAgentie;
                    });
       //DB::commit();
             Log::info("REZULTAT CLIENTI Acceptati PAS 1"); 
                //$company_id=session("company_id");
                $company=Company::where("id",1)->get()->first();
               Log::info("PAS 14 Clienti acceptati agentie");
                     
                $fileName="Centralizator_clienti_acceptati_".$denumireAgentie."_".time().".xls";
               
                 
            Excel::store((new CentralizatorClientiAcceptati)
                    ->forCompany(1,$clientiAgentie,$clienti->cursBNR,"","",[],[])
                    ,$fileName);
           $antetTabel=[];  
           $titluRaport="Centralizator clienti acceptati ".$denumireAgentie." ".dateFormatAfisare($data);
           $tabel=[];
           $groupBy=[];
           $totalBy=[];
           $i=0;
            Log::info("REZULTAT CLIENTI Acceptati PAS 2"); 
           Mail::to($adreseEmail)
                 ->cc($agentia->email)   
                 ->send(
                    new AlertaSablonEmail(
                            $antetTabel,
                            $titluRaport,
                            $tabel,
                            $groupBy,
                            $totalBy,
                            $i,
                            $company,
                            $fileName
                        )
                 );
              }   
            Log::info("REZULTAT CLIENTI Acceptati PAS 3"); 
          } catch (\Exception $e) {
      //  DB::rollback();
        $user=User::where("email","stefan.voinea@gmail.com")->first();
        Mail::to("stefan.voinea@gmail.com")
        ->send(new AlertaEroareEmail("ALERTA CLIENTI ACCEPTATI PE AGENTII",$e->getMessage(),$e,$user));
        return response()->json(['message' => $e->getMessage()], 500);
      }   
          
    }
    public function alertaclientiacceptati()
    {
          //  DB::beginTransaction();
     try{
       //TRANSMIT ALERTA ZILNIC CU CLIENTI ACCEPTATI NOAPTEA SI SEARA 17 si 18  
       $data=Carbon::today()->format("Y-m-d");
    
       $clienti=clientiacceptati($data,null,null);
       Log::info("REZULTAT CLIENTI Acceptati");
        //Log::info($clienti->contracte);

        $adreseEmail=collect(DB::select(DB::raw("SELECT users.email
                                        FROM (notificationuser INNER JOIN users ON notificationuser.user_id = users.id) INNER JOIN notificationtype ON notificationuser.notificationtype_id = notificationtype.id
                                        WHERE (((notificationtype.denumire)='Alerta clienti acceptati'));")))->pluck("email");

    
        Arhivaclientiacceptati::query()->delete();
        foreach($clienti->contracte as $contract){
            
            $client= Arhivaclientiacceptati::create([
                                    "company_id"=>1,
                                    "data"=>$data,
                                    "agentia"=>$contract->agentia,
                                    "data_cerere"=>$contract->data_cerere,
                                    "nr_contract"=>$contract->nr_contract,
                                    "nume"=>$contract->nume,
                                    "cnp"=>$contract->cnp,
                                    "nume_garant"=>$contract->nume_garant,
                                    "cnp_garant"=>$contract->cnp_garant,
                                    "tip_garantie"=>$contract->tip_garantie,
                                    "garantie"=>$contract->garantie,
                                    "valoare_justa"=>$contract->valoare_justa,
                                    "valoare_credit"=>$contract->valoare_credit_eur,
                                    "data_semnarii"=>$contract->data_semnarii,
                                    "perioada"=>$contract->perioada,
                                    "proc_dob"=>$contract->proc_dob,
                                    "proc_dob_cu_discount"=>nz($contract->proc_dob_cu_discount,0),
                                    "tip_rambursare"=>$contract->tip_rambursare,
                                    "dobanda_lunara"=>$contract->dobanda_lunara_eur,
                                    "dobanda_lunara_cu_discount"=>$contract->dobanda_cu_discount_eur,
                                    "rata_lunara"=>$contract->rata_principal_eur,
                                    "data_virament"=>$contract->data_virament?dateFormatStocare($contract->data_virament):null,
                                    "urmatoarea_scadenta"=>$contract->urmatoarea_scadenta?dateFormatStocare($contract->urmatoarea_scadenta):null,
                                    "zile_intarziere"=>$contract->zile_intarziere,
                                    "sursa_informare"=>$contract->sursa_informare,
                                    "debit_reesalonat"=>$contract->debit_reesalonat_eur,
                                    "scadenta_reala"=>$contract->scadenta_reala?dateFormatStocare($contract->scadenta_reala):null,
                                    "zi_scadenta"=>$contract->zi_scadenta,
                                    "bc"=>$contract->bc,
                                    "anaf"=>$contract->anaf,
                                    "accesat"=>$contract->accesat,
                                    "ultima_declarare_credit_scadent"=>$contract->ultima_declarare_credit_scadent?dateFormatStocare($contract->ultima_declarare_credit_scadent):null,
                                    "nr_zile_de_la_decl_scadent"=>$contract->nr_zile_de_la_decl_scadent,
                                    "suma_restanta"=>$contract->suma_restanta,
                                    "contract_id"=>$contract->contract_id,           
                                    "status"=>$contract->status,           
                                    "suma_acordata"=>$contract->suma_acordata,           
                                    "tip_valuta"=>$contract->tip_valuta,           
                                    "solicitare_id"=>$contract->solicitare_id,
                                    ]);
        }
       //DB::commit();
             Log::info("REZULTAT CLIENTI Acceptati PAS 1"); 
                //$company_id=session("company_id");
                $company=Company::where("id",1)->get()->first();
               Log::info("PAS 14 Clienti acceptati");
                     
                $fileName="Centralizator_clienti_acceptati_".time().".xls";
               
                 
            Excel::store((new CentralizatorClientiAcceptati)
                    ->forCompany(1,$clienti->contracte,$clienti->cursBNR,$clienti->valoarejustaLEI,$clienti->valoarejustaEUR,$clienti->totalpeAgentiisiValuta,$clienti->arhivaValori)
                    ,$fileName);
           $antetTabel=[];  
           $titluRaport="Centralizator clienti acceptati ".dateFormatAfisare($data);
           $tabel=[];
           $groupBy=[];
           $totalBy=[];
           $i=0;
            Log::info("REZULTAT CLIENTI Acceptati PAS 2"); 
           Mail::to($adreseEmail)
                 ->send(
                    new AlertaSablonEmail(
                            $antetTabel,
                            $titluRaport,
                            $tabel,
                            $groupBy,
                            $totalBy,
                            $i,
                            $company,
                            $fileName
                        )
                 );
            Log::info("REZULTAT CLIENTI Acceptati PAS 3"); 
          } catch (\Exception $e) {
      //  DB::rollback();
        $user=User::where("email","stefan.voinea@gmail.com")->first();
        Mail::to("stefan.voinea@gmail.com")
        ->send(new AlertaEroareEmail("ALERTA CLIENTI ACCEPTATI ",$e->getMessage(),$e,$user));
        return response()->json(['message' => $e->getMessage()], 500);
      }   
          
    }
      public function alerteClienticucreditescadente()
    {
       
       //TRANSMIT ALERTA ZILNIC CU CLIENTI DECLARATI SCADENTI lA 180 de zile si la 265 de zile de la declararea creditului scadent
       $data=Carbon::today()->format("Y-m-d");
    
    
     $adreseEmail=collect(DB::select(DB::raw("SELECT users.email
                                        FROM (notificationuser INNER JOIN users ON notificationuser.user_id = users.id) INNER JOIN notificationtype ON notificationuser.notificationtype_id = notificationtype.id
                                        WHERE (((notificationtype.denumire)='Alerta clienti cu credite scadente 180 si 265 de zile'));")))->pluck("email");

    $declarariCreditScadent=collect(DB::select(DB::raw("SELECT declarari_credit_scadent.agentia,declarari_credit_scadent.nr_contract,declarari_credit_scadent.data_contract,declarari_credit_scadent.nume,declarari_credit_scadent.data_declarare_scadent,contracte.status
        from (  
            (
             SELECT antetacteaditionale.agentia, antetacteaditionale.nr_contract, antetacteaditionale.data_contract, antetacteaditionale.nume, detaliuacteaditionale.tip_modificare, Max(detaliuacteaditionale.data) AS data_declarare_scadent
             FROM antetacteaditionale INNER JOIN detaliuacteaditionale ON antetacteaditionale.id = detaliuacteaditionale.antetacteaditionale_id
             GROUP BY antetacteaditionale.agentia, antetacteaditionale.nr_contract, antetacteaditionale.data_contract, antetacteaditionale.nume, detaliuacteaditionale.tip_modificare
             HAVING detaliuacteaditionale.tip_modificare Like 'Declarare credit scadent' AND Max(detaliuacteaditionale.data)<='".$data."'
             ) as declarari_credit_scadent left join contracte on declarari_credit_scadent.nr_contract=contracte.nr_contract)
            left join 
             (
                SELECT antetacteaditionale.agentia, antetacteaditionale.nr_contract, antetacteaditionale.data_contract, antetacteaditionale.nume, detaliuacteaditionale.tip_modificare, Max(detaliuacteaditionale.data) AS data_repunere_pe_curent
                FROM antetacteaditionale INNER JOIN detaliuacteaditionale ON antetacteaditionale.id = detaliuacteaditionale.antetacteaditionale_id
                GROUP BY antetacteaditionale.agentia, antetacteaditionale.nr_contract, antetacteaditionale.data_contract, antetacteaditionale.nume, detaliuacteaditionale.tip_modificare
                HAVING (((detaliuacteaditionale.tip_modificare) Like 'Repunere credit pe curent') AND ((Max(detaliuacteaditionale.data))<='".$data."'))

             ) as repuneri_credit_pe_curent on declarari_credit_scadent.nr_contract=repuneri_credit_pe_curent.nr_contract
            where  (declarari_credit_scadent.data_declarare_scadent>repuneri_credit_pe_curent.data_repunere_pe_curent or  (repuneri_credit_pe_curent.data_repunere_pe_curent is null)) 
            and contracte.status not like 'Anulat' 
            and contracte.status not like 'Retragere' 
            and contracte.status not like 'Finalizat' 
            and contracte.data_contract<='".$data."';")));
   
    foreach($declarariCreditScadent as $declarare){
        $declarare->nr_zile=Carbon::parse($declarare->data_declarare_scadent)->diffInDays($data);
    }
    $declarari=$declarariCreditScadent->filter(function ($item) {
                    return $item->nr_zile==180 || $item->nr_zile==265;
    });
  
   
    $antetTabel=[
            ["col"=>"agentia","denumire"=>"Agentia","type"=>"","align"=>"left","width"=>"10%"],
            ["col"=>"nr_contract","denumire"=>"Nr contract","type"=>"","align"=>"center","width"=>"10%"], 
            ["col"=>"data_contract","denumire"=>"Data contract","type"=>"Date","align"=>"center","width"=>"10%"], 
            ["col"=>"nume","denumire"=>"Nume","type"=>"","align"=>"left","width"=>"10%"],
            ["col"=>"status","denumire"=>"Status","type"=>"","align"=>"left","width"=>"10%"],
            ["col"=>"data_declarare_scadent","denumire"=>"Data declarare credit scadent","type"=>"Date","align"=>"center","width"=>"10%"],
            ["col"=>"nr_zile","denumire"=>"Nr zile de la declararea creditului scadent","type"=>"","align"=>"center","width"=>"10%"],
            
            ];                
        
     
            $tabel=collect($declarari);
            $groupBy=[
                                                           
                           ];   
            $totalBy=[
            
                          ];
            $titluRaport="Alerta clienti cu credite declarate scadente care au implinit 180 sau 265 de zile la data de ".dateFormatAfisare($data);
            $company=Company::get()->first();
             
            $i=1;
            
          
               $company_id=$company->id;
             
                 $titluSheet="Alerta clienti cu credite declarate scadente care au implinit 180 sau 265 de zile";
                 $fileName="alerta_clienti_cu_credite_scadente.xls";
                $columnFormat=[
                                 // 'D' => NumberFormat::FORMAT_DATE_DDMMYYYY,
                                 // 'I' => NumberFormat::FORMAT_CURRENCY_EUR_SIMPLE
                               ];
               
               
           $numefis='public/'.$company->slug.'/alerta_clienti_cu_credite_scadente_'.time().".xls";      
          Excel::store( 
             (new SablonExport)->forCompany($company_id,$titluSheet,$tabel,$antetTabel,$groupBy,$totalBy,$columnFormat,$titluRaport),$numefis);
              
           Mail::to($adreseEmail)
                 ->send(
                    new AlertaSablonEmail(
                            $antetTabel,
                            $titluRaport,
                            $tabel,
                            $groupBy,
                            $totalBy,
                            $i,
                            $company,
                            $numefis
                        )
                 );
        
        
          
    }
     public function alerteRiscoperational()
    {
       
       //TRANSMIT ALERTA PE DATA DE 01 a lunii pentru luna anterioara
       $datai=Carbon::today()->addDays(-1)->startOfMonth();
       $datasf=Carbon::today()->addDays(-1)->endOfMonth();
       $chitanteAnulate=collect(DB::select(DB::raw("SELECT note.agentia, Count(note.nr_doc) AS nr_chitante_anulate
                                              FROM note
                                             WHERE (((note.data_nota)>='".$datai."' And (note.data_nota)<='".$datasf."') AND ((note.suma)=0 Or (note.suma) Is Null) AND ((note.contd) Like '101%') AND ((note.tip_doc)='Chitanta'))
                                             GROUP BY note.agentia;")));
       
        $incasariNecuvenite=collect(DB::select(DB::raw("SELECT note.agentia, Count(note.nr_doc) AS nr_chitante_altele, Sum(note.suma) AS suma_in_lei_altele
                                                FROM note
                                                WHERE (((note.data_nota)>='".$datai."' And (note.data_nota)<='".$datasf."') AND ((note.expl)='Altele'))
                                                GROUP BY note.agentia;")));
        $nrReclamatii=collect(DB::select(DB::raw("SELECT registrusesizari.agentia, Count(registrusesizari.agentia) AS nr_reclamatii
                                            FROM  registrusesizari
                                            WHERE (((registrusesizari.data_intrare)>='".$datai."' And (registrusesizari.data_intrare)<='".$datasf."'))
                                            GROUP BY registrusesizari.agentia;")));

        $adreseEmail=collect(DB::select(DB::raw("SELECT users.email
                                        FROM (notificationuser INNER JOIN users ON notificationuser.user_id = users.id) INNER JOIN notificationtype ON notificationuser.notificationtype_id = notificationtype.id
                                        WHERE (((notificationtype.denumire)='Alerta risc operational'));")))->pluck("email");
       
        $gestiuni=Gestiune::where("denumire","<>","Sediu")
                            ->where("denumire","<>","Supraveghere")
                            ->where("denumire","<>","CT General Manu")
                            ->get();
        $agentii=[];
        foreach($gestiuni as $gestiune){
            $agentia= new \StdClass;
            $agentia->agentia=$gestiune->denumire;
            $agentia->nr_reclamatii=$nrReclamatii->where("agentia",$gestiune->agentia)->sum("nr_reclamatii");
            $agentia->nr_chitante_anulate=$chitanteAnulate->where("agentia",$gestiune->agentia)->sum("nr_chitante_anulate");
            $agentia->suma_in_lei_altele=$incasariNecuvenite->where("agentia",$gestiune->agentia)->sum("suma_in_lei_altele");
            $agentia->nr_chitante_altele=$incasariNecuvenite->where("agentia",$gestiune->agentia)->sum("nr_chitante_altele");
            array_push($agentii,$agentia);
        }                    
        
        $antetTabel=[
            ["col"=>"agentia","denumire"=>"Agentia","type"=>"","align"=>"left","width"=>"20%"], 
            ["col"=>"nr_reclamatii","denumire"=>"Nr reclamatii","type"=>"","align"=>"center","width"=>"10%"],
            ["col"=>"nr_chitante_anulate","denumire"=>"Nr chitante anulate","type"=>"","align"=>"center","width"=>"10%"],
            ["col"=>"nr_chitante_altele","denumire"=>"Nr incasari necuvenite","type"=>"","align"=>"center","width"=>"5%"],
            ["col"=>"suma_in_lei_altele","denumire"=>"Suma (LEI) incasari necuvenite","type"=>"","align"=>"center","width"=>"5%"],
            
            ];
                
            $tabel=collect($agentii);
            $groupBy=[
                                                           
                           ];   
            $totalBy=[
            
                          ];
            $titluRaport="Alerta risc operational in perioada ".dateFormatAfisare($datai)." - ".dateFormatAfisare($datasf);
            $company=Company::get()->first();
             
            $i=1;
            
          
               $company_id=$company->id;
             
                 $titluSheet="Alerta risc operational";
                 $fileName="alerta_risc_operational.xls";
                $columnFormat=[
                                 // 'D' => NumberFormat::FORMAT_DATE_DDMMYYYY,
                                 // 'I' => NumberFormat::FORMAT_CURRENCY_EUR_SIMPLE
                               ];
               
               
           $numefis='public/'.$company->slug.'/alerta_risc_operational_'.time().".xls";      
          Excel::store( 
             (new SablonExport)->forCompany($company_id,$titluSheet,$tabel,$antetTabel,$groupBy,$totalBy,$columnFormat,$titluRaport),$numefis);
              
           Mail::to($adreseEmail)
                 ->send(
                    new AlertaSablonEmail(
                            $antetTabel,
                            $titluRaport,
                            $tabel,
                            $groupBy,
                            $totalBy,
                            $i,
                            $company,
                            $numefis
                        )
                 );
        
        
          
    }
      public function alerteOrdinedeblocareanaf()
    {
       
        $continutVechi=Ordinedeblocareanafhtml::get()->first();
        $adreseEmail=collect(DB::select(DB::raw("SELECT users.email
                                        FROM (notificationuser INNER JOIN users ON notificationuser.user_id = users.id) INNER JOIN notificationtype ON notificationuser.notificationtype_id = notificationtype.id
                                        WHERE (((notificationtype.denumire)='Ordine de blocare ANAF'));")))->pluck("email");

        $html = file_get_contents('https://www.anaf.ro/anaf/internet/ANAF/info_ue/sanctiuni_internationale/ordin_blocare_debloare');

        $dom = new \DOMDocument;
        libxml_use_internal_errors(true);
        $dom->loadHTML($html);
        $divContent=$dom->getElementById("content");
        $continutNou=valoareTagXMLDomDocument($divContent, "ul" );
        
        
        if($continutVechi->continut!=$continutNou){
                  Mail::to($adreseEmail)
                            ->send(
                            new VerificareDosarInstantaEmail(
                                "Alerta Ordine de blocare ANAF ",
                                "A fost actualizata baza de date cu ordine de blocare ANAF <br>
                                <a href='https://www.anaf.ro/anaf/internet/ANAF/info_ue/sanctiuni_internationale/ordin_blocare_debloare'>ORDINE DE BLOCARE ANAF </a>"
                            )
        );

            $continutVechi->update(["continut"=>$continutNou]);
        }
           
    }
      public function alerteLicitatii()
    {
            DB::beginTransaction();
     try{
        $litigii=Licitatii::where("datasiora_licitatie",">=",Carbon::today()->addDays(5))
                            ->where("datasiora_licitatie","<=",Carbon::today()->addDays(6))
                            ->get();
       


        $adreseEmail=collect(DB::select(DB::raw("SELECT users.email
                                        FROM (notificationuser INNER JOIN users ON notificationuser.user_id = users.id) INNER JOIN notificationtype ON notificationuser.notificationtype_id = notificationtype.id
                                        WHERE (((notificationtype.denumire)='Licitatii'));")))->pluck("email");
       
        if(count($litigii)>0){
             $antetTabel=[
            ["col"=>"executor","denumire"=>"Executor","type"=>"","align"=>"left","width"=>"20%"], 
            ["col"=>"nr_dosar_executare","denumire"=>"Nr dosar executare","type"=>"","align"=>"center","width"=>"10%"],
            ["col"=>"pret_pornire","denumire"=>"Pret pornire","type"=>"","align"=>"center","width"=>"10%"],
            ["col"=>"datasiora_licitatie","denumire"=>"Data si ora licitatie","type"=>"","align"=>"center","width"=>"5%"],
            ["col"=>"observatii","denumire"=>"Observatii","type"=>"","align"=>"center","width"=>"10%"],
            ["col"=>"promovare_imobile","denumire"=>"Promovare imobile","type"=>"","align"=>"center","width"=>"10%"],
            
            ];
                
            $tabel=collect($litigii);
            $groupBy=[
                                                           
                           ];   
            $totalBy=[
            
                          ];
            $titluRaport="Alerta licitatii in data de ".dateFormatAfisare(Carbon::today()->addDays(5));
            $company=Company::get()->first();
             
            $i=1;
            
          
               $company_id=$company->id;
             
                 $titluSheet="Alerta licitatii";
                 $fileName="alerta_licitatii.xls";
                $columnFormat=[
                                 // 'D' => NumberFormat::FORMAT_DATE_DDMMYYYY,
                                 // 'I' => NumberFormat::FORMAT_CURRENCY_EUR_SIMPLE
                               ];
               
               
           $numefis='public/'.$company->slug.'/alerta_licitatii_'.time().".xls";      
          Excel::store( 
             (new SablonExport)->forCompany($company_id,$titluSheet,$tabel,$antetTabel,$groupBy,$totalBy,$columnFormat,$titluRaport),$numefis);
              
           Mail::to($adreseEmail)
                 ->send(
                    new AlertaSablonEmail(
                            $antetTabel,
                            $titluRaport,
                            $tabel,
                            $groupBy,
                            $totalBy,
                            $i,
                            $company,
                            $numefis
                        )
                 );
         }
        
        
      } catch (\Exception $e) {
        
        $user=User::where("email","stefan.voinea@gmail.com")->first();
        Mail::to("stefan.voinea@gmail.com")
        ->send(new AlertaEroareEmail("ALERTA LICITATII ",$e->getMessage(),$e,$user));
        return response()->json(['message' => $e->getMessage()], 500);
      }   
          
    }
     public function alerteVerificareLitigii()
    {
         DB::beginTransaction();
     try{
        $litigii=Litigiu::where("status",null)->get();
        
        foreach ($litigii as $litigiu){
            $dosarVerificat=preiaDosareInstanta($litigiu->numar_dosar,null,null,null,null,null);
            if(count($dosarVerificat)>0){
                foreach($dosarVerificat as $dosar){
                    $litigiuInVerificare= Litigiu::where("numar_dosar",$dosar->numar_dosar)
                                                 ->where("data_dosar",Carbon::parse($dosar->data_dosar)->format("Y-m-d"))
                                                ->get()->first();
                    Log::info(Carbon::parse($dosar->data_dosar)->format("Y-m-d"));                            
                    $modificari="Modificari intervenite la dosarul ".$dosar->numar_dosar.PHP_EOL;
                    $modificari=$modificari."PARTI DOSAR: ".$dosar->parti.PHP_EOL.PHP_EOL;  
                    $existaModificari=false;                     
                        
                    if($litigiuInVerificare){
                        $partiModificate=false; 
                        //actualizez eventuale modificari
                        $dosar->avocatul_apararii=$litigiu->avocatul_apararii??null;
                        $dosar->avocatul_acuzarii=$litigiu->avocatul_acuzarii??null;
                        $dosar->observatii=$litigiu->observatii??null;
                        $dosar->email_alerte=$litigiu->email_alerte??null;
                        $dosar->telefon_alerte=$litigiu->telefon_alerte??null;
                        $dosar->status=$litigiu->status??null;
                        $dosar->taxa_de_timbru=$litigiu->taxa_de_timbru??null;
                        $dosar->cheltuieli_de_judecata=$litigiu->cheltuieli_de_judecata??null;
                        $dosar->data_ultimei_verificari=Carbon::now()->format("Y-m-d H:i:s");
                        if($dosar->data_modificare!=$litigiu->data_modificare){
                          $modificari=$modificari."Dosarul a fost actualizat in data de: ".datasioraFormatAfisare($dosar->data_modificare).PHP_EOL;
                          $existaModificari=true;
                        }
                        if($dosar->institutie!=$litigiu->institutie){
                            $modificari=$modificari."A fost modificata institutia din : ".$litigiu->institutie." in ".$dosar->institutie.PHP_EOL;
                            $existaModificari=true;
                        }
                        if($dosar->departament!=$litigiu->departament){
                           $modificari=$modificari."A fost modificat departamentul din : ".$litigiu->departament." in ".$dosar->departament.PHP_EOL;
                           $existaModificari=true;
                        }
                        if($dosar->categorie_caz!=$litigiu->categorie_caz){
                            $modificari=$modificari."A fost modificata categoria cazului din : ".$litigiu->categorie_caz." in ".$dosar->categorie_caz.PHP_EOL;
                            $existaModificari=true;
                        }
                        if($dosar->stadiu_procesual!=$litigiu->stadiu_procesual){
                            $modificari=$modificari."A fost modificat stadiul procesual din : ".$litigiu->stadiu_procesual." in ".$dosar->stadiu_procesual.PHP_EOL;
                            $existaModificari=true;
                        }
                        if($dosar->parti!=$litigiu->parti){
                            $modificari=$modificari."Au fost modificate partile din : ".$litigiu->parti." in ".$dosar->parti.PHP_EOL;
                            $partiModificate=true;
                            $existaModificari=true;
                        }
                        if($existaModificari=true){
                            
                            $litigiuInVerificare->update(@json_decode(json_encode($dosar), true));

                        }
                        if($partiModificate){
                            Litigiiparti::where("litigiu_id",$litigiu->id)->delete();
                            foreach($dosar->litigiiparti as $parte){
                                $parte->litigiu_id=$litigiu->id;
                                Litigiiparti::create(@json_decode(json_encode($parte), true));
                            }
                        }   
                        foreach($dosar->litigiicaleatac as $caleatac){
                            $caledeatac=Litigiicaleatac::where("litigiu_id",$litigiu->id)
                                              ->where("data_declarare",$caleatac->data_declarare)
                                              ->where("parte_declaratoare",$caleatac->parte_declaratoare)
                                              ->where("tip_cale_atac",$caleatac->tip_cale_atac)
                                              ->get()->first();
                            if(!$caledeatac){
                                $existaModificari=true;
                                $modificari=$modificari."A fost inregistrata o noua cale de atac : ".PHP_EOL;
                                $modificari=$modificari."        Data declarare: ".dateFormatAfisare($caleatac->data_declarare).PHP_EOL;
                                $modificari=$modificari."    Parte declaratoare: ".$caleatac->parte_declaratoare.PHP_EOL;
                                $modificari=$modificari."         Tip cale atac: ".$caleatac->tip_cale_atac.PHP_EOL.PHP_EOL;
                                $caleatac->litigiu_id=$litigiu->id;
                                Litigiicaleatac::create(@json_decode(json_encode($caleatac), true));
                            }  
                        }
                        foreach($dosar->litigiisedinte as $sedinta){
                            $sedintaExistenta=Litigiisedinte::where("litigiu_id",$litigiu->id)
                                              ->where("data_sedinta",$sedinta->data_sedinta)
                                              ->where("ora_sedinta",$sedinta->ora_sedinta)
                                              ->get()->first();
                            if(!$sedintaExistenta){
                                $existaModificari=true;
                                $modificari=$modificari."A fost inregistrata o noua sedinta : ".PHP_EOL;
                                $modificari=$modificari."         Complet: ".$sedinta->complet.PHP_EOL;
                                $modificari=$modificari."    Data sedinta: ".dateFormatAfisare($sedinta->data_sedinta).PHP_EOL;
                                $modificari=$modificari."     Ora sedinta: ".$sedinta->ora_sedinta.PHP_EOL;
                                $modificari=$modificari." Data pronuntare: ".dateFormatAfisare($sedinta->data_pronuntare).PHP_EOL;
                                $modificari=$modificari."Document sedinta: ".$sedinta->document_sedinta.PHP_EOL;
                                $modificari=$modificari."  Numar document: ".$sedinta->numar_document.PHP_EOL;
                                $modificari=$modificari."   Data document: ".dateFormatAfisare($sedinta->data_document).PHP_EOL;
                                $modificari=$modificari."         Solutie: ".$sedinta->solutie.PHP_EOL;
                                $modificari=$modificari."   Solutie sumar: ".$sedinta->solutie_sumar.PHP_EOL.PHP_EOL;

                                $sedinta->litigiu_id=$litigiu->id;
                                Litigiisedinte::create(@json_decode(json_encode($sedinta), true));
                            }else{
                                if(Carbon::parse($sedinta->data_pronuntare)->format("Y-m-d H:i:s")!=Carbon::parse($sedintaExistenta->data_pronuntare)->format("Y-m-d H:i:s"))
                                {
                                    $modificari=$modificari."A fost pronuntata solutia in data de ".dateFormatAfisare($sedinta->data_pronuntare)." pentru sedinta din ".dateFormatAfisare($sedinta->data_sedinta)." ".$sedinta->ora_sedinta.PHP_EOL;
                                    $modificari=$modificari."         Complet: ".$sedinta->complet.PHP_EOL;
                                    $modificari=$modificari."    Data sedinta: ".dateFormatAfisare($sedinta->data_sedinta).PHP_EOL;
                                    $modificari=$modificari."     Ora sedinta: ".$sedinta->ora_sedinta.PHP_EOL;
                                    $modificari=$modificari." Data pronuntare: ".dateFormatAfisare($sedinta->data_pronuntare).PHP_EOL;
                                    $modificari=$modificari."Document sedinta: ".$sedinta->document_sedinta.PHP_EOL;
                                    $modificari=$modificari."  Numar document: ".$sedinta->numar_document.PHP_EOL;
                                    $modificari=$modificari."   Data document: ".dateFormatAfisare($sedinta->data_document).PHP_EOL;
                                    $modificari=$modificari."         Solutie: ".$sedinta->solutie.PHP_EOL;
                                    $modificari=$modificari."   Solutie sumar: ".$sedinta->solutie_sumar.PHP_EOL.PHP_EOL; 
                                    $sedinta->litigiu_id=$litigiu->id;
                                    $sedintaExistenta->update(@json_decode(json_encode($sedinta), true));                          
                                }
                            }  
                        }
                    }else{
                        $existaModificari=true;
                        //creez dosar la instanta noua
                        $dosar->avocatul_apararii=$litigiu->avocatul_apararii??null;
                        $dosar->avocatul_acuzarii=$litigiu->avocatul_acuzarii??null;
                        $dosar->observatii=$litigiu->observatii??null;
                        $dosar->email_alerte=$litigiu->email_alerte??null;
                        $dosar->telefon_alerte=$litigiu->telefon_alerte??null;
                        $dosar->status=$litigiu->status??null;
                        $dosar->taxa_de_timbru=$litigiu->taxa_de_timbru??null;
                        $dosar->cheltuieli_de_judecata=$litigiu->cheltuieli_de_judecata??null;
                        $dosar->data_ultimei_verificari=Carbon::now()->format("Y-m-d H:i:s");
                        $dosarSalvat= Litigiu::create(@json_decode(json_encode($dosar), true));
                        $modificari=$modificari."Dosarul a fost inregistrat la ".$dosar->institutie. " in data de: ".dateFormatAfisare($dosar->data_dosar).PHP_EOL;
                        $modificari=$modificari."   Departamentul: ".$dosar->departament.PHP_EOL;
                        $modificari=$modificari."   Categorie caz: ".$dosar->categorie_caz.PHP_EOL;
                        $modificari=$modificari."Stadiu procesual: ".$dosar->stadiu_procesual.PHP_EOL;
                        $modificari=$modificari."           Parti: ".$dosar->parti.PHP_EOL.PHP_EOL;
                        foreach($dosar->litigiiparti as $parte){
                                $parte->litigiu_id=$dosarSalvat->id;
                                Litigiiparti::create(@json_decode(json_encode($parte), true));

                        }
                        foreach($dosar->litigiicaleatac as $caleatac){
                                $caleatac->litigiu_id=$dosarSalvat->id;
                                Litigiicaleatac::create(@json_decode(json_encode($caleatac), true));
                                $modificari=$modificari."CALE DE ATAC : ".PHP_EOL;
                                $modificari=$modificari."        Data declarare: ".dateFormatAfisare($caleatac->data_declarare).PHP_EOL;
                                $modificari=$modificari."    Parte declaratoare: ".$caleatac->parte_declaratoare.PHP_EOL;
                                $modificari=$modificari."         Tip cale atac: ".$caleatac->tip_cale_atac.PHP_EOL.PHP_EOL;
                        }
                        foreach($dosar->litigiisedinte as $sedinta){
                                $sedinta->litigiu_id=$dosarSalvat->id;
                                Litigiisedinte::create(@json_decode(json_encode($sedinta), true));
                                $modificari=$modificari."SEDINTA: ".PHP_EOL;
                                $modificari=$modificari."         Complet: ".$sedinta->complet.PHP_EOL;
                                $modificari=$modificari."    Data sedinta: ".dateFormatAfisare($sedinta->data_sedinta).PHP_EOL;
                                $modificari=$modificari."     Ora sedinta: ".$sedinta->ora_sedinta.PHP_EOL;
                                $modificari=$modificari." Data pronuntare: ".dateFormatAfisare($sedinta->data_pronuntare).PHP_EOL;
                                $modificari=$modificari."Document sedinta: ".$sedinta->document_sedinta.PHP_EOL;
                                $modificari=$modificari."  Numar document: ".$sedinta->numar_document.PHP_EOL;
                                $modificari=$modificari."   Data document: ".dateFormatAfisare($sedinta->data_document).PHP_EOL;
                                $modificari=$modificari."         Solutie: ".$sedinta->solutie.PHP_EOL;
                                $modificari=$modificari."   Solutie sumar: ".$sedinta->solutie_sumar.PHP_EOL.PHP_EOL;
                        }
                    //creez dosar la instanta noua    
                    }  
                      
                    if($litigiu->email_alerte){
                        Mail::to(explode(';',$litigiu->email_alerte))
                            ->send(
                            new VerificareDosarInstantaEmail(
                                "Alerta dosar instanta ".$litigiu->numar_dosar,
                                $modificari
                            )
                        );   
                    } 
                }
                Litigiu::where("numar_dosar",$litigiu->numar_dosar)->update(["data_ultimei_verificari"=>Carbon::now()->format("Y-m-d H:i:s")]);
            }
        }
      
        DB::update("UPDATE litigii INNER JOIN litigii AS litigii_1 ON litigii.numar_dosar = litigii_1.numar_dosar SET litigii.status = 'Instanta anterioara'
                WHERE (((litigii.data_dosar)<litigii_1.data_dosar));");  
           DB::commit();
        //return $cod_firma;
        
      } catch (\Exception $e) {
        DB::rollback();
        $user=User::where("email","stefan.voinea@gmail.com")->first();
        Mail::to("stefan.voinea@gmail.com")
        ->send(new AlertaEroareEmail("ALERTA VERIFICARE LITIGII",$e->getMessage(),$e,$user));
        return response()->json(['message' => $e->getMessage()], 500);
      }  
          
    }
     public function alerteSesizariNesolutionate(){
        $sesizarinesolutionate=Registrusesizari::where("data_intrare",Carbon::today()->addDays(-25))
                                                 ->where("data_iesire",null)
                                                 ->get();
                                     
        foreach($sesizarinesolutionate as $sesizare){
        notificare(
                        $tip_notificare="Sesizari nesolutionate",
                        $object=$sesizare,
                        $from_id=130,
                        $email="",
                        $telefon="",
                        $mesaj=$sesizare->agentia." ".dateFormatAfisare($sesizare->data_intrare)." ". $sesizare->client." ".$sesizare->nr_contracte." ".$sesizare->obiect_cerere,
                        $link="",
                        $gestiune=$sesizare->agentia);
      }
    }
    public function alerteExpirareCarteDeIdentitate(){
        $cartideidentitateexpira=Partisolicitare::where("ci_data_expirare",Carbon::today()->addDays(1))
                                                    ->whereHas("contract", function($q) {
                                                     return $q->whereNotIn("status",["Anulat","Retragere","Finalizat"]);
                                                    })
                                                    ->with("contract")
                                                    ->get();
                                     
        foreach($cartideidentitateexpira as $cartedeidentitate){
        notificare(
                        $tip_notificare="Expirare CI",
                        $object=$cartedeidentitate,
                        $from_id=130,
                        $email="",
                        $telefon="",
                        $mesaj=dateFormatAfisare($cartedeidentitate->ci_data_expirare)." ". $cartedeidentitate->nume." ".$cartedeidentitate->contract->agentia." ".$cartedeidentitate->contract->nr_contract,
                        $link="",
                        $gestiune=$cartedeidentitate->agentia);
      }
    }
    public function alerteScadenteViitoare()
    {
       $alerte=Emailalerte::where("tip_alerta","Alerta scadente viitoare")->get();
       foreach ($alerte as $alerta) {
         $dataSold=Carbon::today();
         $solduriContracteFaraFiltru=solduricontracte(null,null,$dataSold);
         $solduriContracte=$solduriContracteFaraFiltru->filter(function ($value) use($dataSold) {
                                    if($dataSold->copy()->diffInDays($dataSold->copy()->endOfMonth())<7){
                                        $nrzileinplus=7-$dataSold->copy()->diffInDays($dataSold->copy()->endOfMonth());
                                    }else{
                                        $nrzileinplus=0;    
                                    } 

                                    return $value->data_scadenta <= $dataSold->copy()->endOfMonth()->addDays($nrzileinplus) &&
                                           $value->data_scadenta>=$dataSold ;
                                })->all();
         $antetTabel=[
            ["col"=>"partener","denumire"=>"Nume client","type"=>"","align"=>"left","width"=>"20%"], 
            ["col"=>"nr_contract","denumire"=>"Nr contract","type"=>"","align"=>"center","width"=>"10%"],
            ["col"=>"data_contract","denumire"=>"Data contract","type"=>"Date","align"=>"center","width"=>"10%"],
            ["col"=>"status","denumire"=>"Status","type"=>"","align"=>"center","width"=>"5%"],
            ["col"=>"explicatie","denumire"=>"Tip creanta","type"=>"","align"=>"left","width"=>"20%"],
            ["col"=>"data_scadenta","denumire"=>"Data scadenta","type"=>"Date","align"=>"center","width"=>"10%"],
            ["col"=>"suma","denumire"=>"Valoare","type"=>"Number","align"=>"right","width"=>"10%"],
            
            ];
                
            $tabel=collect($solduriContracte)->groupBy(["partener","nr_contract"]);
            $groupBy=[
                            ["col"=>"partener","denumire"=>"Nume client","type"=>"","align"=>"center","width"=>"100%"],
                            ["col"=>"nr_contract","denumire"=>"Nr contract","type"=>"","align"=>"center","width"=>"100%"],                                               
                           ];   
            $totalBy=[
                            "suma"
                          ];
            $titluRaport="Alerta scadente viitoare la data de ".dateFormatAfisare($dataSold);
            $company=Company::get()->first();
             
            $i=1;
            
          
               $company_id=$company->id;
             
                 $titluSheet="Alerta scadente viitoare";
                 $fileName="alerta_scadente_viitoare.xls";
                $columnFormat=[
                                 // 'D' => NumberFormat::FORMAT_DATE_DDMMYYYY,
                                 // 'I' => NumberFormat::FORMAT_CURRENCY_EUR_SIMPLE
                               ];
               
               
           $numefis='public/'.$company->slug.'/alerta_scadente_viitoare_'.time().".xls";      
          Excel::store( 
             (new SablonExport)->forCompany($company_id,$titluSheet,$tabel,$antetTabel,$groupBy,$totalBy,$columnFormat,$titluRaport),$numefis);
              
           Mail::to($alerta->email)
                 ->bcc(explode(',',$alerta->cc)) 
                 ->send(
                    new AlertaSablonEmail(
                            $antetTabel,
                            $titluRaport,
                            $tabel,
                            $groupBy,
                            $totalBy,
                            $i,
                            $company,
                            $numefis
                        )
                 );
         }
          
    }

    public function alerteClientiRestanti()
    {
        try{
       $alerte=Emailalerte::where("tip_alerta","Alerta clienti restanti")->get();
       foreach ($alerte as $alerta) {
         $dataSold=Carbon::today();
         $solduriContracteFaraFiltru=solduricontracte(null,null,$dataSold);
         $solduriContracte=$solduriContracteFaraFiltru->filter(function ($value) use($dataSold) {
                                    $procedura=Cererideexecutare::where("contract_id",$value->contract_id)
                                                      ->get()->first();  
                                    return ($value->data_scadenta == null || 
                                           $value->data_scadenta<=$dataSold)&&!$procedura ;
                                })->all();
         $antetTabel=[
            ["col"=>"partener","denumire"=>"Nume client","type"=>"","align"=>"left","width"=>"20%"], 
            ["col"=>"nr_contract","denumire"=>"Nr contract","type"=>"","align"=>"center","width"=>"10%"],
            ["col"=>"data_contract","denumire"=>"Data contract","type"=>"Date","align"=>"center","width"=>"10%"],
            ["col"=>"status","denumire"=>"Status","type"=>"","align"=>"center","width"=>"5%"],
            ["col"=>"explicatie","denumire"=>"Tip creanta","type"=>"","align"=>"left","width"=>"20%"],
            ["col"=>"data_scadenta","denumire"=>"Data scadenta","type"=>"Date","align"=>"center","width"=>"10%"],
            ["col"=>"suma","denumire"=>"Valoare","type"=>"Number","align"=>"right","width"=>"10%"],
            
            ];
                
            $tabel=collect($solduriContracte)->groupBy(["partener","nr_contract"]);
            $groupBy=[
                            ["col"=>"partener","denumire"=>"Nume client","type"=>"","align"=>"center","width"=>"100%"],
                            ["col"=>"nr_contract","denumire"=>"Nr contract","type"=>"","align"=>"center","width"=>"100%"],                                               
                           ];   
            $totalBy=[
                            "suma"
                          ];
            $titluRaport="Alerta solduri restante la data de ".dateFormatAfisare($dataSold);
            $company=Company::get()->first();
             
            $i=1;
            
          
               $company_id=$company->id;
             
                 $titluSheet="Alerta solduri restante";
                 $fileName="alerta_solduri_restante.xls";
                $columnFormat=[
                                 // 'D' => NumberFormat::FORMAT_DATE_DDMMYYYY,
                                 // 'I' => NumberFormat::FORMAT_CURRENCY_EUR_SIMPLE
                               ];
               
               
           $numefis='public/'.$company->slug.'/alerta_solduri_restante_'.time().".xls";      
          Excel::store( 
             (new SablonExport)->forCompany($company_id,$titluSheet,$tabel,$antetTabel,$groupBy,$totalBy,$columnFormat,$titluRaport),$numefis);
              
           Mail::to($alerta->email)
                 ->bcc(explode(',',$alerta->cc))  
                 ->send(
                    new AlertaSablonEmail(
                            $antetTabel,
                            $titluRaport,
                            $tabel,
                            $groupBy,
                            $totalBy,
                            $i,
                            $company,
                            $numefis
                        )
                 );
         }
          
     } catch (\Exception $e) {
            $user=User::where("id",1)->get()->first();
            Mail::to("stefan.voinea@gmail.com")
            ->send(new AlertaEroareEmail("ALERTA CLIENTI RESTANTI",$e->getMessage(),$e,$user));
            return response()->json(['message' => $e->getMessage()], 500);
        }  
    }
    
}