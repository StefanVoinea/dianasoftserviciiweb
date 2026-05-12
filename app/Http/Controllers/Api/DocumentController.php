<?php

namespace App\Http\Controllers\Api;
use App\Models\Company;
use App\Models\Contract;
use App\Models\Partisolicitare;
use App\Exports\SablonExport;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpWord\TemplateProcessor;

class DocumentController extends Controller
{
   public function exportXLS(Request $request) 
        {
          $modelName=  $request->modelName;
          Log::info($request->modelName);
          if($modelName=="clienti"||$modelName=="furnizori"||$modelName=="angajati"||$modelName=="actionari"){
            $modelName="partener";
          }
          $modelName="\\App\\Models\\".ucfirst($modelName);
          
          $inregistrari= new $modelName;
          $antetTabel=$inregistrari->antetTabelXLS;
          
          $groupBy=$inregistrari->groupByXLS;
          $totalBy=$inregistrari->totalByXLS;
          $titluRaport=$inregistrari->titluRaportXLS;
          $columnFormat=$inregistrari->columnFormatXLS;
          $relatii=$inregistrari->relatiiXLS??[];
           // $relatii=array_diff(getAllRelations($inregistrari),["documentsursa"]);
          
            $cautareDupa=$request->cautareDupa;
           $searchModel=$request->searchModel;
          if($request->modelName=="partisolicitare"){
           if($request->cautareDupa=='nume')
           {
            $inregistrari = Partisolicitare::query()
                                    ->join('contracte', 'partisolicitare.solicitare_id', '=', 'contracte.solicitare_id')
                                    ->where('partisolicitare.nume',"like", "%".$request->searchModel."%")
                                    ->select('partisolicitare.*','contracte.agentia','contracte.nr_contract','contracte.data_contract','contracte.nume as nume_titular','contracte.status')
                                    ->with('contract'); 
            $cautareDupa="";
            $searchModel="";
           }else{
            $inregistrari = Partisolicitare::query()
                                    ->join('contracte', 'partisolicitare.solicitare_id', '=', 'contracte.solicitare_id')
                                    ->select('partisolicitare.*','contracte.agentia','contracte.nr_contract','contracte.data_contract','contracte.nume as nume_titular','contracte.status')
                                    ->with('contract'); 
            
                                    
           }
           


          }else{

          
          $inregistrari= $inregistrari->select('*')
                                        ->where("company_id",session("company_id"))
                                        ->with($relatii);
            }                            
        
         
          $inregistrari=filterRequest($inregistrari,$searchModel,$cautareDupa,$request->sortModel,$request->filterModel);
         
          $rezultat=  $inregistrari->orderBy('id','DESC')->get();
          
          $tabel=collect($rezultat);
          $fileName=$titluRaport.".xls";
          $company_id=1;//session("company_id");
                ob_end_clean(); 
                 ob_start(); 
                     
               
                 
                return Excel::download((new SablonExport)->forCompany($company_id,$titluRaport,$tabel,$antetTabel,$groupBy,$totalBy,$columnFormat,$titluRaport),$fileName);
              


        }
    public function downloadPDF(Request $request)
    { 
         $company=Company::where("id",session("company_id"))->get()->first(); 
         $headers = array(
              'Content-Type: application/pdf',
            );

        ob_end_clean(); 
        ob_start(); 
        $file=storage_path('app/public/'.$company->slug.'/'.$request->numefis.'.pdf');
        return Response::download($file, $request->numefis.'.pdf',$headers);
     }

    public function acordGDPR_WORD(Request $request)
    {

        $company=Company::where("id",session("company_id"))->get()->first(); 
        
        $contract=Contract::where("id",$request->contract_id)->get()->first();    
        $datactr=Carbon::parse($contract->data_contract)->format('d.m.Y');
        
        $template = new TemplateProcessor(storage_path('app/public/'.$company->slug.'/acord_gdpr_template.docx'));
        $template->setValue('data_contract', $datactr);
        $template->setValue('nume', $contract->nume);
        $template->setValue('cnp', $contract->cnp);
       
        $template->saveAs(storage_path('app/public/'.$company->slug.'/acord_gdpr_'.$contract->nr_contract.'.docx'));
        $headers = array(
              'Content-Type: application/msword',
            );

            ob_end_clean(); 
            ob_start(); 
        $file=storage_path('app/public/'.$company->slug.'/acord_gdpr_'.$contract->nr_contract.'.docx');
        return Response::download($file, 'acord_gdpr_'.$contract->nr_contract.'.docx',$headers);
        

    }
     public function notificare_WORD(Request $request)
    {

        $company=Company::where("id",session("company_id"))->get()->first(); 
        $contract=Contract::where("id",$request->contract_id)->get()->first();    
        $datactr=Carbon::parse($contract->data_contract)->format('d.m.Y');
        $data_sold=Carbon::today()->format('d.m.Y');
       // $nr_rate_de_achitat=Carbon::today()->diffInMonths(Carbon::parse($contract->prima_scadenta));
         $nr_rate_de_achitat=(Carbon::today()->year-Carbon::parse($contract->prima_scadenta)->year)*12+
                             (Carbon::today()->month-Carbon::parse($contract->prima_scadenta)->month);
         if(Carbon::today()->day>=Carbon::parse($contract->prima_scadenta)->day){
            $nr_rate_de_achitat=$nr_rate_de_achitat+1;
         }                    
       
        $suma_restanta=round(($contract->valoare_avans+$nr_rate_de_achitat*$contract->valoare_rata)-$contract->totalincasat(),2);
        
        if($suma_restanta>$contract->valoare_plan-$contract->totalincasat()){
            $suma_restanta=$contract->valoare_plan-$contract->totalincasat();
        }
        if($contract->prima_scadenta>Carbon::today()||$suma_restanta>$contract->valoare_plan||$suma_restanta<0){
            $suma_restanta=0;
        }

        // Log::info("Nr_rate".$nr_rate_de_achitat)
        $template = new TemplateProcessor(storage_path('app/public/'.$company->slug.'/notificare_template.docx'));
        $template->setValue('nr_contract', $contract->nr_contract);
        $template->setValue('data_contract', $datactr);
        $template->setValue('nume', $contract->nume);
        $template->setValue('valoare_plan', $contract->valoare_plan);
        $template->setValue('tip_valuta', $contract->tip_valuta);
        $template->setValue('total_plati', $contract->totalincasat());
        $template->setValue('rest_de_plata', $contract->valoare_plan-$contract->totalincasat());
        $template->setValue('suma_restanta', $suma_restanta);
        $template->setValue('data_sold', $data_sold);
        $template->saveAs(storage_path('app/public/'.$company->slug.'/notificare_'.$contract->nr_contract.'.docx'));
        $headers = array(
              'Content-Type: application/msword',
            );

            ob_end_clean(); 
            ob_start(); 
        $file=storage_path('app/public/'.$company->slug.'/notificare_'.$contract->nr_contract.'.docx');
        return Response::download($file, 'notificare_'.$contract->nr_contract.'.docx',$headers);
        

    }
}