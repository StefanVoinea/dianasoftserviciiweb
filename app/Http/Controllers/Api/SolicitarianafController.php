<?php

namespace App\Http\Controllers\Api;

use App\Models\Solicitarianaf;
// use App\Events\SolicitarianafUpdated;
use App\Models\Exports\SolicitarianafExport;
//use App\Models\Imports\SolicitarianafImport;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

use App\Exports\SablonExport;
use App\Models\Company;
use Barryvdh\Snappy\Facades\SnappyPdf;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Str;

use App\Exports\TemplateExport;
use App\Models\Capitalsocial;
use App\Models\Coduricaen;
use App\Models\Configopis;
use App\Models\Contract;
use App\Models\DianaSoftField;
use App\Models\DianaSoftModel;
use App\Models\Gestiune;
use App\Models\Notificationlog;
use App\Models\Notificationtype;
use App\Models\Partener;
use App\Models\TemplateProcessorMod;
use App\Models\User;
use App\Models\Venituripartisolicitari;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpWord\Element\Table;
use PhpOffice\PhpWord\SimpleType\TblWidth;
use PhpOffice\PhpWord\TemplateProcessor;
use Madnest\Madzipper\Facades\Madzipper;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException; 
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Mail;
use App\Mail\AlertaEroareEmail;


    

class SolicitarianafController extends Controller
{
    public function raportnumarinterogarianaf(Request $request)
    {     
         
    try{
    $datai=Carbon::parse($request->datai);
    $datasf=Carbon::parse($request->datasf);
    $user=User::where("id",session("user_id"))->get()->first();
    $gestiunipermise=$user->gestiuniPermiseCompany()->pluck("denumire");
    $records=Solicitarianaf::where("data",">=",$datai)
                            ->where("data","<=",$datasf)
                            ->get();        

   

    $recordsAll=collect($records);
    $i=1;
    foreach($recordsAll as $record){
        $record->nr_crt=$i++;
    }
    $antetTabel=[
                    ["col"=>"nr_crt","denumire"=>"Nr crt","type"=>"","align"=>"center","width"=>"5%"],
                    ["col"=>"data","denumire"=>"Data","type"=>"Date","align"=>"center","width"=>"10%"], 
                    ["col"=>"req_id","denumire"=>"Req id","type"=>"","align"=>"center","width"=>"10%"],
                    ["col"=>"tip","denumire"=>"Tip persoana","type"=>"","align"=>"center","width"=>"10%"],
                    ["col"=>"cnp","denumire"=>"CNP/CUI","type"=>"","align"=>"center","width"=>"10%"],
                    ["col"=>"nume","denumire"=>"Nume","type"=>"","align"=>"center","width"=>"10%"],
                    ["col"=>"nr_aut","denumire"=>"Nr acord","type"=>"","align"=>"center","width"=>"5%"],
                    ["col"=>"data_aut","denumire"=>"Data acord","type"=>"Date","align"=>"center","width"=>"5%"],
                ];
                
                 $tabel=collect($recordsAll);
                 $groupBy=[                                           
                           ];   
                 $totalBy=[];
                 $titluRaport="Raport interogari ANAF (".$records->count().") efectuate in perioada " .dateFormatAfisare($datai)." - ".dateFormatAfisare($datasf);
              if($request->format_fisier=="Excel"){
               $company_id=session("company_id");
                ob_end_clean(); 
                 ob_start(); 
                     
                 $titluSheet="Raport nr interogari ANAF";
                 $fileName="raport_nr_interogari_anaf.xls";
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
             $numefis=storage_path('app/public/'.$company->slug.'/raport_nr_interogari_anaf'.time().".pdf");
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

           
            return Response::download($numefis, 'raport_nr_interogari_anaf.pdf',$headers);
          }                  
            
        } catch (\Exception $e) {
            
            Mail::to("stefan.voinea@gmail.com")
            ->send(new AlertaEroareEmail("RAPORT NUMAR INTEROGARI SOLICITARI ANAF",$e->getMessage(),$e,Auth::user()));
            return response()->json(['message' => $e->getMessage()], 500);
        }    
  
    }
    public function raportareanaf(Request $request){
   try{
    $datai=Carbon::parse($request->data)->firstofMonth()->format("Y-m-d");
    $datasf=Carbon::parse($request->data)->lastofMonth()->format("Y-m-d");
    $luna=Carbon::parse($request->data)->month;
    $anul=Carbon::parse($request->data)->year;
    
    $user=User::where("id",session("user_id"))->get()->first();
    $gestiunipermise=$user->gestiuniPermiseCompany()->pluck("denumire");

    $persoaneANAFContracte=collect(DB::select(DB::raw("SELECT contracte.nume, contracte.cnp, '1' AS cerere_aprobata,'' AS data_cerere
                            FROM contracte
                            WHERE 
                                  (contracte.status Not Like 'Finalizat' 
                                    AND contracte.data_acordarii<='".$datasf."' 
                                    AND contracte.anaf='Da') 
                                 OR (contracte.data_finalizarii>'".$datasf."' 
                                    AND contracte.data_acordarii<='".$datasf."' 
                                    AND contracte.status Like 'Finalizat' 
                                    AND contracte.anaf='Da')
                            GROUP BY contracte.nume, contracte.cnp, '1','';")));

     $persoaneANAFSolicitari=collect(DB::select(DB::raw("SELECT  solicitarianaf.nume, solicitarianaf.cnp,'0' AS cerere_aprobata,
                                max(solicitarianaf.data_aut) as data_cerere
                                FROM solicitarianaf
                                WHERE solicitarianaf.data_aut>='".$datai."' And solicitarianaf.data_aut<='".$datasf."'
                                GROUP BY solicitarianaf.cnp, solicitarianaf.nume, '0';")));
    $cnpPersoaneANAFContracte =$persoaneANAFContracte->pluck("cnp");
    $persoaneANAFSolicitari=$persoaneANAFSolicitari->whereNotIn("cnp",$cnpPersoaneANAFContracte);
   
    $persoaneANAF=$persoaneANAFContracte->merge($persoaneANAFSolicitari);
    
    $contracteANAF=collect(DB::select(DB::raw("SELECT contracte.cnp, '1' AS status_cerere, contracte.data_cerere, '' AS motiv, if(contracte.categoria_credit Like 'FACTORING' Or contracte.categoria_credit Like 'FINANTARE','7',if(contracte.categoria_credit Like 'CC GRMI','1',if(contracte.categoria_credit Like 'CC IP' Or contracte.categoria_credit Like 'CT IP','2',if(contracte.categoria_credit Like 'CI IP' Or contracte.categoria_credit Like 'CINV IP','4',if(contracte.linie_credit Like 'Da','6','7'))))) AS tip_contract, contracte.credit_acordat, contracte.nr_contract, contracte.tip_valuta, contracte.data_acordarii, scadenta_anaf.MaxOfscadenta_reala AS data_scadenta, coalesce(reclasificarilunare.principal,0)+coalesce(reclasificarilunare.dobanda,0)+coalesce(reclasificarilunare.penalitati,0)+coalesce(reclasificarilunare.dob_reesalonata,0)+coalesce(reclasificarilunare.pen_reesalonate,0) AS sold, if(contracte.status Like 'executare' and round(coalesce(reclasificarilunare.principal,0)-coalesce(reclasificarilunare.psub_0,0),0)>0,'4',if(reclasificarilunare.serviciul_datoriei>0 and round(coalesce(reclasificarilunare.principal,0)-coalesce(reclasificarilunare.psub_0,0),0)>0,'5',if(contracte.status Like '%finalizat%' And contracte.data_finalizarii<='".$datasf."','2','1'))) AS status, reclasificarilunare.serviciul_datoriei AS nr_zile, round(coalesce(reclasificarilunare.principal,0)-coalesce(reclasificarilunare.psub_0,0),0) AS sold_principal_restant, round(coalesce(reclasificarilunare.dobanda,0)+coalesce(reclasificarilunare.penalitati,0)+coalesce(reclasificarilunare.dob_reesalonata,0)+coalesce(reclasificarilunare.pen_reesalonate,0),0) AS suma_accesorii, '' AS den_cesionar, '' AS cui_cesionar, 0 AS valoare_reziduala, garantii_anaf.tip_garantie_anaf AS tip_garantie, garantii_anaf.val_garantie, garantii_anaf.moneda_garantie, garantii_anaf.adresa_bun, '' AS cui_banca, contracte.cnp AS cui_garantie, if(coalesce(den_garantie_anaf,'')<>'',den_garantie_anaf,contracte.nume) AS den_garantie, contracte.data_finalizarii
        FROM ((contracte LEFT JOIN 
            (
               SELECT graficrambursare.nr_contract, Max(graficrambursare.scadenta_reala) AS MaxOfscadenta_reala
                FROM graficrambursare
                WHERE (((graficrambursare.rata_lunara)<>0))
                GROUP BY graficrambursare.nr_contract
 
            ) AS scadenta_anaf ON contracte.nr_contract = scadenta_anaf.nr_contract) LEFT JOIN 
            reclasificarilunare ON contracte.nr_contract = reclasificarilunare.nr_contract) LEFT JOIN 
            (
                SELECT garantii.nr_contract, if(Max(tip_garantie) Like 'garantie reala mobiliara%','7',If(Max(tip_constructiv) Like '%apartament%' Or Max(tip_constructiv) Like '%garsoniera%','1',If(Max(tip_constructiv) Like '%casa%teren%','2',If(Max(tip_constructiv) Like '%tere%','3','3')))) AS tip_garantie_anaf, Round(coalesce(Max(valoarea_justa),0),0) AS val_garantie, Max(garantii.tip_valuta) AS moneda_garantie, Max(garantii.adresa) AS adresa_bun, Max(garantii.proprietar) AS den_garantie_anaf
                FROM garantii
                GROUP BY garantii.nr_contract

            ) AS  garantii_anaf ON contracte.nr_contract = garantii_anaf.nr_contract
            WHERE (reclasificarilunare.luna='".$luna."' 
                    AND reclasificarilunare.anul='".$anul."' 
                    AND contracte.status Not Like 'Finalizat' 
                    AND contracte.data_acordarii<='".$datasf."' 
                    AND contracte.anaf='Da') 
                OR (contracte.data_finalizarii>'".$datasf."' 
                    AND reclasificarilunare.luna=".$luna." 
                    AND reclasificarilunare.anul=".$anul."
                    AND contracte.data_acordarii<='".$datasf."' 
                    AND contracte.status Like 'Finalizat' 
                    AND contracte.anaf='Da');")));


 
 $tranzactiiANAF=collect(DB::select(DB::raw("select contracte.cnp, contracte.nr_contract, contracte.nume, contracte.data_acordarii, detaliuacteaditionale.tip_modificare, detaliuacteaditionale.data, detaliuacteaditionale.suma, detaliuacteaditionale.tip_valuta, if(tip_modificare like 'rambursare%','0','1') as debit_credit 
    from contracte inner join (antetacteaditionale inner join detaliuacteaditionale on antetacteaditionale.id = detaliuacteaditionale.antetacteaditionale_id) on contracte.nr_contract = antetacteaditionale.nr_contract
    where (((contracte.anaf)='da'))
    group by contracte.cnp, contracte.nr_contract, contracte.nume, contracte.data_acordarii, detaliuacteaditionale.tip_modificare, detaliuacteaditionale.data, detaliuacteaditionale.suma, detaliuacteaditionale.tip_valuta, if(tip_modificare like 'rambursare%','0','1')
    having (((contracte.data_acordarii)<='".$datasf."') and ((detaliuacteaditionale.tip_modificare) like 'rambursare%' or (detaliuacteaditionale.tip_modificare) like 'suplimentare%') and ((detaliuacteaditionale.data)>='".$datai."' and (detaliuacteaditionale.data)<='".$datasf."'));")));
  
    ob_end_clean(); 
        ob_start(); 
    $company=Company::where("id",session("company_id"))->get()->first();
    $folderraportarianaf=storage_path('app\\public\\'.$company->slug.'\\raportarianaf\\');
    $numefis = "raportare_anaf_" . lunainlitere(Carbon::parse($datasf)->month). "_" . Carbon::parse($datasf)->year."_".time().".xml";
    $numefisStocare = "public\\".$company->slug."\\raportarianaf\\raportare_anaf_" . lunainlitere(Carbon::parse($datasf)->month). "_" . Carbon::parse($datasf)->year."_".time().".xml";
    $linie = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>";
    Storage::append($numefisStocare,$linie);
    $linie = "<F3100 " .
            campXML("an_r",Carbon::parse($datasf)->year) .
            campXML("luna_r", Carbon::parse($datasf)->month) .
            campXML("cui_ifn", "25613809") .
            campXML("totalPlata_A", count($persoaneANAF)+count($persoaneANAFSolicitari) + count($contracteANAF) + count($tranzactiiANAF)) .
            "xmlns:xs=\"http://www.w3.org/2001/XMLSchema\" xmlns=\"mfp:anaf:dgti:f3100:declaratie:v1\" >";
    Storage::append($numefisStocare,$linie);

foreach($persoaneANAF as $persoana){
 // PERSOANE
    
     If (strlen($persoana->cnp) != 13 || str_starts_with($persoana->cnp,"9")){
       $linie = "<persoane " .
                campXML("cnp_cui", $persoana->cnp) .
                campXML("rectific", "") .
                campXML("denumire", substr(sirfaraspeciale($persoana->nume),0, 50)) .
                campXML("data_nastere", "") .
                " >";

    }else{
     
        $linie = "<persoane " .
                    campXML("cnp_cui", $persoana->cnp) .
                    campXML("rectific", "") .
                    campXML("denumire", substr(sirfaraspeciale($persoana->nume),0, 50)) .
                    campXML("data_nastere", substr($persoana->cnp, 5, 2) . "." . substr($persoana->cnp, 3, 2) . "." . (substr($persoana->cnp, 0, 1)=="1" ||substr($persoana->cnp, 0, 1)=="2"?"19":"20").substr($persoana->cnp, 1, 2)) .
                    " >";
    }  
    Storage::append($numefisStocare,$linie);

foreach(collect($persoaneANAFSolicitari)->where("cnp",$persoana->cnp) as $persoanaSolicitare){
       //SOLICITARI NECONTRACTATE
    
        $linie = "<contract " .
                  campXML("status_cerere", "0") .
                  campXML("data_cerere", dateFormatAfisare($persoanaSolicitare->data_cerere)) .
                  campXML("motiv","Neindeplinirea conditiilor de creditare") .
                  campXML("tip_contract", "") .
                  campXML("nr_contract", "") . 
                  campXML("data_acordare", "") .
                  campXML("suma_contract", "") . 
                  campXML("valuta", "") .
                  campXML("data_f", "") . 
                  campXML("sold", "") .
                  campXML("status", "") . 
                  campXML("nr_zile", "") .
                  campXML("suma_principal_restant", "") . 
                  campXML("suma_accesorii", "") .
                  campXML("den_cesionar", "") . 
                  campXML("cui_cesionar","") .
                  campXML("suma_rambursata", "") . 
                  campXML("data_rambursare", "") .
                  campXML("valoare_reziduala", "") .
                  "> ";  
        Storage::append($numefisStocare,$linie);
         $linie = "</contract>";
    Storage::append($numefisStocare,$linie);
}

foreach($contracteANAF->where("cnp",$persoana->cnp) as $contract){
   //CONTRACTE PERSOANA
   
    $linie = "<contract " .
              campXML("status_cerere", $contract->status_cerere) . 
              campXML("data_cerere", dateFormatAfisare($contract->data_cerere)) .
              campXML("motiv", $contract->motiv) .
              campXML("tip_contract", $contract->tip_contract) .
              campXML("nr_contract", $contract->nr_contract) . 
              campXML("data_acordare", dateFormatAfisare($contract->data_acordarii)) .
              campXML("suma_contract", round(nz($contract->credit_acordat, 0), 0)) . 
              campXML("valuta", $contract->tip_valuta) .
              campXML("data_f", dateFormatAfisare($contract->data_scadenta)) . 
              campXML("sold", round(nz($contract->sold, 0),0)) .
              campXML("status", $contract->status) . 
              campXML("nr_zile", nz($contract->nr_zile, 0)) .
              campXML("suma_principal_restant", round(nz($contract->sold_principal_restant, 0),0)) . 
              campXML("suma_accesorii", round(nz($contract->suma_accesorii, 0),0)) .
              campXML("den_cesionar", $contract->den_cesionar) . 
              campXML("cui_cesionar", $contract->cui_cesionar) .
              campXML("suma_rambursata", round(nz($tranzactiiANAF->where("nr_contract",$contract->nr_contract)->where("tip_modificare","like","%rambursare%")->sum("suma"), 0), 0)) . 
              campXML("data_rambursare", nz(dateFormatAfisare($tranzactiiANAF->where("nr_contract",$contract->nr_contract)->where("tip_modificare","like","%rambursare%")->max("data")),"")) .
              campXML("valoare_reziduala", round(nz($contract->valoare_reziduala, 0),0)) .
              "> ";  
    Storage::append($numefisStocare,$linie);
    
    $linie = "<garant " .
              campXML("tip_garantie", $contract->tip_garantie) .
              campXML("val_garantie", round(nz($contract->val_garantie,0),0)) .
              campXML("moneda_garantie", $contract->moneda_garantie) .
              campXML("adresa_bun", sirfaraspeciale(substr($contract->adresa_bun,0, 200))) .
              campXML("cui_banca", $contract->cui_banca) .
              campXML("cui_garantie", $contract->cui_garantie) .
              campXML("den_garantie", sirfaraspeciale(substr($contract->den_garantie,0, 50))) .
              " />";
    Storage::append($numefisStocare,$linie);
    
    foreach($tranzactiiANAF->where("nr_contract",$contract->nr_contract) as $tranzactie){
        //TRANZACTIE
        
            $linie = "<tranzactie " .
                       campXML("tip_tranzactie", "3") .
                       campXML("debit_credit", round(nz($tranzactie->debit_credit,0),0)) .
                       campXML("data_tranzactie", dateFormatAfisare($tranzactie->data)) .
                       campXML("adresa_detalii", "") .
                       campXML("cod_banca", "") .
                       campXML("denumire", substr(sirfaraspeciale($tranzactie->nume),0, 50)) .
                       campXML("explicatie", $tranzactie->tip_modificare) .
                       campXML("suma", round(nz($tranzactie->suma, 0), 0)) .
                       campXML("cont_partener", "") .
                       campXML("nr_cont_partener", "") .
                       campXML("banca_partener", "") .
                       campXML("tara_banca", "") .
                       " />";
            Storage::append($numefisStocare,$linie);
    }
    $linie = "</contract>";
    Storage::append($numefisStocare,$linie);
    }   
    $linie = "</persoane>";
    Storage::append($numefisStocare,$linie);
    } 
    $linie = " </F3100>";
    Storage::append($numefisStocare,$linie);
    $headers = array(
                                'Content-Type: application/xml',
                            );
    return Response::download( $folderraportarianaf.$numefis, 'raportare_anaf.xml',$headers);
        } catch (\Exception $e) {
            Mail::to("stefan.voinea@gmail.com")
            ->send(new AlertaEroareEmail("RAPORTARE ANAF SOLICITARI ANAF",$e->getMessage(),$e,Auth::user()));
            return response()->json(['message' => $e->getMessage()], 500);
        }    


}

    public function creezinterogareanaf(Request $request){
       try{
        $data=Carbon::today();
        $data_creare_mesaj=Carbon::now()->format("YmdHi");
        
        $zipFileRaspuns='interogari_anaf_'.$data_creare_mesaj. '.zip'; 

        
        //PRELUCREZ PERSOANE FIZICE

        $solicitari=Solicitarianaf::where("data",null)
                                    ->where("tip","Persoana fizica")
                                    ->where("acordpdf","<>",null)->get();
     
        ob_end_clean(); 
        ob_start();   
        
        $company=Company::where("id",session("company_id"))->get()->first();
        $folderPrelucrare=storage_path('app\\public\\'.$company->slug.'\\prelucraresolicitarianaf\\');
        $folderSolicitariAnaf=storage_path('app\\public\\'.$company->slug.'\\solicitarianaf\\');
        $folderAcorduriAnaf=storage_path('app\\public\\'.$company->slug.'\\acordurianaf\\');
        $caleDUK=storage_path('app\\public\\'.$company->slug.'\\dist\\DUKIntegrator.jar');
        $file = new Filesystem;
        $file->cleanDirectory($folderPrelucrare);
        $solicitariPrelucrate=[];
        if(count($solicitari)>0){

            $req_id =Solicitarianaf::get()->max("req_id")+1;   
            
            $numefis = 'pf_'.$req_id. '_' .$data_creare_mesaj. '.xml';
            $numefisStocare='public\\'.$company->slug.'\\prelucraresolicitarianaf\\'.$numefis;
            if(File::exists(storage_path($folderPrelucrare,$numefis))){
                File::delete(storage_path($folderPrelucrare,$numefis));
            };
            
            $linie = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>";
            Storage::append($numefisStocare, $linie);
            $linie = "<F2101 Req_id=\"" .$req_id. "\" Cod_banca=\"IFN00032\" Cui_banca=\"25613809\" Data_creare_mesaj=\"".$data_creare_mesaj. "\" xmlns:xs=\"http://www.w3.org/2001/XMLSchema\" xmlns=\"mfp:anaf:dgti:banci:formulare:v1\" >";
            Storage::append($numefisStocare, $linie);
            $zipFile=$folderPrelucrare.$req_id.".zip";
            foreach($solicitari as $solicitare){
              
                $linie = "<persoane_fizice optiuni=\"3\" cnp=\"" .$solicitare->cnp . "\" Nr_aut=\"" .$solicitare->nr_aut. "\" Data_aut=\"" .dateFormatAfisare($solicitare->data_aut). "\" />";
                Storage::append($numefisStocare, $linie);
              
                File::copy($folderAcorduriAnaf.$solicitare->acordpdf,$folderPrelucrare.$solicitare->cnp.".pdf");
                
                 Madzipper::make($zipFile)->add($folderPrelucrare.$solicitare->cnp.".pdf")->close();
            }
            $linie = " </F2101>";
            Storage::append($numefisStocare, $linie);
                
          
            $comanda='java -jar '.$caleDUK.' -s F2101  ' .$folderPrelucrare.$numefis . ' $ 0 ' .
                                 $zipFile.  ' $ ' . $request->parola_token . ' athena';

            $process = Process::fromShellCommandline($comanda); 
            
            $process->run(); 
            if (!$process->isSuccessful()) { 
                 throw new ProcessFailedException($process); 
            } 
            Log::info($process->getOutput());
            
            if(File::exists($folderPrelucrare.'pf_'.$req_id. '_' .$data_creare_mesaj. '-semnat.pdf')){
              $headers = array(
                                'Content-Type: application/pdf',
                            );
               File::copy($folderPrelucrare.'pf_'.$req_id. '_' .$data_creare_mesaj. '-semnat.pdf',$folderSolicitariAnaf.'pf_'.$req_id. '_' .$data_creare_mesaj. '-semnat.pdf');

               Madzipper::make($zipFileRaspuns)->add($folderPrelucrare.'pf_'.$req_id. '_' .$data_creare_mesaj. '-semnat.pdf')->close();
               array_push($solicitariPrelucrate,$solicitare->id);
            }else{
                 $headers = array(
                                'Content-Type: application/pdf',
                            );
                 Madzipper::make($zipFileRaspuns)->add($folderPrelucrare.'pf_'.$req_id. '_' .$data_creare_mesaj. '.xml.err.txt')->close();
              
            }
            Solicitarianaf::whereIn("id",$solicitariPrelucrate)
                            ->update(["data"=>$data,
                                     "data_creare_mesaj"=>$data_creare_mesaj,
                                     "req_id"=>$req_id]);
        }

        //PRELUCREZ PERSOANE JURIDICE SI PFA

        $solicitari=Solicitarianaf::where("data",null)
                                    ->where("tip","<>","Persoana fizica")
                                    ->where("acordpdf","<>",null)->get();
     
        ob_end_clean(); 
        ob_start();   
        
        $company=Company::where("id",session("company_id"))->get()->first();
        $folderPrelucrare=storage_path('app\\public\\'.$company->slug.'\\prelucraresolicitarianaf\\');
        $folderSolicitariAnaf=storage_path('app\\public\\'.$company->slug.'\\solicitarianaf\\');
        $folderAcorduriAnaf=storage_path('app\\public\\'.$company->slug.'\\acordurianaf\\');
        $caleDUK=storage_path('app\\public\\'.$company->slug.'\\dist\\DUKIntegrator.jar');
        
        
        if(count($solicitari)>0){

            foreach($solicitari as $solicitare){
                $data_creare_mesaj=Carbon::now()->format("YmdHi");

                $req_id =Solicitarianaf::get()->max("req_id")+1;   
                $file = new Filesystem;
                $file->cleanDirectory($folderPrelucrare);
                $numefis = 'pj_'.$solicitare->cnp.'_'.$req_id. '_' .$data_creare_mesaj. '.xml';
                $numefisStocare='public\\'.$company->slug.'\\prelucraresolicitarianaf\\'.$numefis;
                if(File::exists(storage_path($folderPrelucrare,$numefis))){
                    File::delete(storage_path($folderPrelucrare,$numefis));
                };
                
                $linie = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>";
                Storage::append($numefisStocare, $linie);
                $linie = "<F2101 Req_id=\"" .$req_id. "\" Cod_banca=\"IFN00032\" Cui_banca=\"25613809\" Data_creare_mesaj=\"".$data_creare_mesaj. "\" xmlns:xs=\"http://www.w3.org/2001/XMLSchema\" xmlns=\"mfp:anaf:dgti:banci:formulare:v1\" >";
                Storage::append($numefisStocare, $linie);
                $zipFile=$folderPrelucrare.$req_id.".zip";
                    if ($solicitare->tip== "Persoana juridica"){
                         
                            $linie = "<persoane_juridice_pfa cui=\"" .$solicitare->cnp . "\" Nr_aut_cui=\"" .$solicitare->nr_aut . "\" Data_aut_cui=\"" .dateFormatAfisare($solicitare->data_aut). "\" pfa=\"0\" format=\"XLSX\"/>";
                            Storage::append($numefisStocare, $linie);
                    }
                    if ($solicitare->tip == "PFA"){
                            $linie = "<persoane_juridice_pfa cui=\"" .$solicitare->cnp. "\" Nr_aut_cui=\"" .$solicitare->nr_aut. "\" Data_aut_cui=\"" .dateFormatAfisare($solicitare->data_aut) . "\" pfa=\"1\" format=\"XLSX\"/>";
                            Storage::append($numefisStocare, $linie);
                    }
                  
                    File::copy($folderAcorduriAnaf.$solicitare->acordpdf,$folderPrelucrare.$solicitare->cnp.".pdf");
                    
                     Madzipper::make($zipFile)->add($folderPrelucrare.$solicitare->cnp.".pdf")->close();
                $linie = " </F2101>";
                Storage::append($numefisStocare, $linie);
                    
              
                $comanda='java -jar '.$caleDUK.' -s F2101  ' .$folderPrelucrare.$numefis . ' $ 0 ' .
                                     $zipFile.  ' $ ' . $request->parola_token . ' athena';

                $process = Process::fromShellCommandline($comanda); 
                
                $process->run(); 
                if (!$process->isSuccessful()) { 
                     throw new ProcessFailedException($process); 
                } 
                Log::info($process->getOutput());
                
                if(File::exists($folderPrelucrare.'pj_'.$solicitare->cnp.'_'.$req_id. '_' .$data_creare_mesaj. '-semnat.pdf')){
                  $headers = array(
                                    'Content-Type: application/pdf',
                                );
                   File::copy($folderPrelucrare.'pj_'.$solicitare->cnp.'_'.$req_id. '_' .$data_creare_mesaj. '-semnat.pdf',$folderSolicitariAnaf.'pj_'.$solicitare->cnp.'_'.$req_id. '_' .$data_creare_mesaj. '-semnat.pdf');

                   Madzipper::make($zipFileRaspuns)->add($folderPrelucrare.'pj_'.$solicitare->cnp.'_'.$req_id. '_' .$data_creare_mesaj. '-semnat.pdf')->close();
                   
                }else{
                     $headers = array(
                                    'Content-Type: application/pdf',
                                );
                     Madzipper::make($zipFileRaspuns)->add($folderPrelucrare.'pj_'.$solicitare->cnp.'_'.$req_id. '_' .$data_creare_mesaj. '.xml.err.txt')->close();
                  
                }
            Solicitarianaf::where("id",$solicitare->id)
                            ->update(["data"=>$data,
                                     "data_creare_mesaj"=>$data_creare_mesaj,
                                     "req_id"=>$req_id]);
            }
        }
        return Response::download($zipFileRaspuns, 'solicitari_anaf.zip',$headers);
        } catch (\Exception $e) {
            Mail::to("stefan.voinea@gmail.com")
            ->send(new AlertaEroareEmail("CREEZ INTEROGARE SOLICITARI ANAF",$e->getMessage(),$e,Auth::user()));
            return response()->json(['message' => $e->getMessage()], 500);
        }    

    }
    public function afisezacordanafpdf(Solicitarianaf $solicitarianaf)
    {
        try{
        ob_end_clean(); 
        ob_start(); 
        $headers = array(
                            'Content-Type: application/pdf',
                        );
        $company=Company::where("id",session("company_id"))->get()->first();
        $fileName = $solicitarianaf->acordpdf;
        $fisier=storage_path('app/public/'.$company->slug.'/acordurianaf/'.$fileName);   
        return Response::download($fisier, 'acord_anaf.pdf',$headers);
        } catch (\Exception $e) {
            Mail::to("stefan.voinea@gmail.com")
            ->send(new AlertaEroareEmail("AFISEZ ACORD ANAF PDF SOLICITARI ANAF",$e->getMessage(),$e,Auth::user()));
            return response()->json(['message' => $e->getMessage()], 500);
        }    

    }
     public function uploadacordanafpdf(Request $request,Solicitarianaf $solicitarianaf)
    {

       try{
        $solicitare=$solicitarianaf;
        $fileName = strtolower($solicitare->id."_".$solicitare->cnp."_".Str::slug($solicitare->nume).".".$request->file->getClientOriginalExtension());
       
        $company=Company::where("id",session("company_id"))->get()->first();
        $request->file->move(storage_path('app/public/'.$company->slug.'/'.'acordurianaf'), $fileName);
       
        $contents = file_get_contents(storage_path('app/public/'.$company->slug.'/'.'acordurianaf/'. $fileName));
        $path = "/acordurianaf/".$fileName;
        $upload = Storage::disk('dropbox')->put($path, $contents);
        $solicitarianaf->update(["acordpdf"=>$fileName]);
        return "";
        } catch (\Exception $e) {
            Mail::to("stefan.voinea@gmail.com")
            ->send(new AlertaEroareEmail("UPLOAD ACORD ANAF PDF SOLICITARI ANAF",$e->getMessage(),$e,Auth::user()));
            return response()->json(['message' => $e->getMessage()], 500);
        }    

    }
    public function centralizatoranaf(Request $request)
  {
   try{
    $datai=Carbon::parse($request->data)->firstofMonth()->format("Y-m-d");
    $datasf=Carbon::parse($request->data)->lastofMonth()->format("Y-m-d");
   Log::info($request);
    $user=User::where("id",session("user_id"))->get()->first();
    $gestiunipermise=$user->gestiuniPermiseCompany()->pluck("denumire");

    $records=collect(DB::select(DB::raw("SELECT contracte.agentia, contracte.nr_contract, contracte.data_contract, contracte.nume, contracte.cnp,           contracte.adresa, contracte.ci, contracte.tip_valuta, contracte.credit_acordat, contracte.status, contracte.anaf, contracte.nr_acord_anaf, contracte.data_acord_anaf
            FROM contracte
            WHERE (((contracte.anaf)='Da'));")));
    
    $records=$records->whereIn("agentia",$gestiunipermise);
    $records=collect($records)->sortBy("nume");
    $antetTabel=[
                    ['col'=>'agentia','denumire'=>'Agentia','type'=>'','align'=>'center','width'=>'10%'],
                    ['col'=>'nr_contract','denumire'=>'Nr contract','type'=>'','align'=>'center','width'=>'10%'],
                    ['col'=>'data_contract','denumire'=>'Data contract','type'=>'Date','align'=>'center','width'=>'10%'],
                    ['col'=>'nume','denumire'=>'Nume','type'=>'','align'=>'center','width'=>'10%'],
                    ['col'=>'cnp','denumire'=>'Cnp','type'=>'','align'=>'center','width'=>'10%'],
                    ['col'=>'adresa','denumire'=>'Adresa','type'=>'','align'=>'center','width'=>'10%'],
                    ['col'=>'ci','denumire'=>'Ci','type'=>'','align'=>'center','width'=>'10%'],
                    ['col'=>'tip_valuta','denumire'=>'Tip valuta','type'=>'','align'=>'center','width'=>'10%'],
                    ['col'=>'credit_acordat','denumire'=>'Credit acordat','type'=>'','align'=>'center','width'=>'10%'],
                    ['col'=>'status','denumire'=>'Status','type'=>'','align'=>'center','width'=>'10%'],
                    ['col'=>'anaf','denumire'=>'Anaf','type'=>'','align'=>'center','width'=>'10%'],
                    ['col'=>'nr_acord_anaf','denumire'=>'Nr acord anaf','type'=>'','align'=>'center','width'=>'10%'],
                    ['col'=>'data_acord_anaf','denumire'=>'Data acord anaf','type'=>'Date','align'=>'center','width'=>'10%'],
            ];
             $totalBy=[];
           $tabel=collect($records);
                 $groupBy=[
                            
                                                                       
                           ];

            $titluRaport="Centralizator clienti ANAF";
            if($request->format_fisier=="Excel"){
               $company_id=session("company_id");
                ob_end_clean(); 
                ob_start(); 
                     
                $titluSheet="Centralizator clienti ANAF";
                $fileName="centralizator_clienti_anaf.xls";
                $columnFormat=[
                                 // 'D' => NumberFormat::FORMAT_DATE_DDMMYYYY,
                                 // 'I' => NumberFormat::FORMAT_CURRENCY_EUR_SIMPLE
                               ];
               
                 
                return Excel::download((new SablonExport)->forCompany($company_id,$titluSheet,$tabel,$antetTabel,$groupBy,$totalBy,$columnFormat,$titluRaport),$fileName);
              } 
          if($request->format_fisier=="PDF"){  
            $company=Company::where("id",session("company_id"))->get()->first();
             $numefis=storage_path('app/public/'.$company->slug.'/centralizator_anaf_'.time().".pdf");
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

           
            return Response::download($numefis, "centralizator_anaf.pdf",$headers);
          }  
        } catch (\Exception $e) {
            Mail::to("stefan.voinea@gmail.com")
            ->send(new AlertaEroareEmail("CENTRALIZATOR ANAF SOLICITARI ANAF",$e->getMessage(),$e,Auth::user()));
            return response()->json(['message' => $e->getMessage()], 500);
        }    
 
  }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function indexPaginat(Request $request)
    {
        try{
          $records= Solicitarianaf::select('*')->where("company_id",session("company_id"));
          $records=filterRequest($records,$request->searchModel,$request->cautareDupa,$request->sortModel,$request->filterModel);
        $records=  $records->orderBy('id','desc');
        $records=  $records->paginate($request->pageLength,
                                                                    ["page"=>$request->page]);
        
                              //::where("user_id",auth()->user()->id)
                                
        return json_encode($records);
        } catch (\Exception $e) {
            Mail::to("stefan.voinea@gmail.com")
            ->send(new AlertaEroareEmail("INDEX PAGINAT SOLICITARI ANAF",$e->getMessage(),$e,Auth::user()));
            return response()->json(['message' => $e->getMessage()], 500);
        }    

    }
     public function index()
    {
          $solicitarianaf= Solicitarianaf::where("company_id",session("company_id"))->get();
          return json_encode($solicitarianaf);
    }
    public function export() 
        {
             
            ob_end_clean(); 
            ob_start(); 
             $company_id=session("company_id");
            return Excel::download((new SolicitarianafExport)->forCompany($company_id),"solicitarianaf.xls");


        }

    public function import(Request $request) 
        {
          $fileName = "solicitarianaf_".time().".".$request->file->getClientOriginalExtension();
          $request->file->move(public_path("upload"), $fileName);
          
          Excel::import(new SolicitarianafImport, public_path("upload")."/".$fileName);

          
            $solicitarianaf= Solicitarianaf::where("company_id",session("company_id"))
                                                     ->paginate($request->pageLength,['page'=>$request->page]);
         
            return json_encode($solicitarianaf);
             
        
           
         }
        
    public function storeExcel() 
        {
            ob_end_clean(); 
            ob_start(); 
            $company_id=session("company_id");
            
            Excel::store((new SolicitarianafExport)->forCompany($company_id), "solicitarianaf.xls","public",null,[
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

        // event(new SolicitarianafUpdated());
        DB::beginTransaction();
        try{
         $data_nasterii= Solicitarianaf::create([
            "company_id"=>session("company_id"),
            "tip"=>$request->tip??null,
    	    "cnp"=>$request->cnp??null,
    	    "nr_aut"=>$request->nr_aut??null,
            "data_aut"=>$request->data_aut?dateFormatStocare($request->data_aut):null,
    	    "pfa"=>$request->pfa??null,
    	    "nume"=>$request->nume??null,
    	    "sex"=>$request->sex??null,
    	    "judet"=>$request->judet??null,
            "data_nasterii"=>$request->data_nasterii?dateFormatStocare($request->data_nasterii):null,           
        ]);
         DB::commit();
        return $data_nasterii;

        } catch (\Exception $e) {
            DB::rollback();
            Mail::to("stefan.voinea@gmail.com")
            ->send(new AlertaEroareEmail("STORE SOLICITARE ANAF",$e->getMessage(),$e,Auth::user()));
            return response()->json(['message' => $e->getMessage()], 500);
        }  
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Solicitarianaf  $solicitarianaf
     * @return \Illuminate\Http\Response
     */
    public function show(Solicitarianaf $solicitarianaf)
    {
        try{
        $resp= Solicitarianaf::where("id",$solicitarianaf->id)
                               ->where("company_id",session("company_id"))
        					   ->get()->first();
                                
        return json_encode($resp);

        } catch (\Exception $e) {
            Mail::to("stefan.voinea@gmail.com")
            ->send(new AlertaEroareEmail("SHOW SOLICITARE ANAF",$e->getMessage(),$e,Auth::user()));
            return response()->json(['message' => $e->getMessage()], 500);
        }  
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Solicitarianaf  $solicitarianaf
     * @return \Illuminate\Http\Response
     */
    public function edit(Solicitarianaf $solicitarianaf)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Solicitarianaf  $solicitarianaf
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Solicitarianaf $solicitarianaf)
    {
        DB::beginTransaction();
        try{
        $solicitarianaf->update([
            "tip"=>$request->tip??null,
            "cnp"=>$request->cnp??null,
            "nr_aut"=>$request->nr_aut??null,
            "data_aut"=>$request->data_aut?dateFormatStocare($request->data_aut):null,
            "pfa"=>$request->pfa??null,
            "nume"=>$request->nume??null,
            "sex"=>$request->sex??null,
            "judet"=>$request->judet??null,
            "data_nasterii"=>$request->data_nasterii?dateFormatStocare($request->data_nasterii):null,           
        ]);
       // event(new SolicitarianafUpdated());
            DB::commit();
        return $solicitarianaf;
           
        } catch (\Exception $e) {
            DB::rollback();
            Mail::to("stefan.voinea@gmail.com")
            ->send(new AlertaEroareEmail("UPDATE SOLICITARE ANAF",$e->getMessage(),$e,Auth::user()));
            return response()->json(['message' => $e->getMessage()], 500);
        }  
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Solicitarianaf  $solicitarianaf
     * @return \Illuminate\Http\Response
     */
    public function destroy(Solicitarianaf $solicitarianaf)
    {
        try{
        $solicitarianaf->delete();
      //  event(new SolicitarianafUpdated());

        } catch (\Exception $e) {
            Mail::to("stefan.voinea@gmail.com")
            ->send(new AlertaEroareEmail("STERGE SOLICITARE ANAF",$e->getMessage(),$e,Auth::user()));
            return response()->json(['message' => $e->getMessage()], 500);
        }  

    }
}