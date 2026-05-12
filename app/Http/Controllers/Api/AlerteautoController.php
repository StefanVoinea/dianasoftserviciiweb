<?php
namespace App\Http\Controllers\Api;

use App\Models\Alerteauto;
use App\Models\Emailalerte;
use App\Models\Exports\AlerteautoExport;
use App\Http\Controllers\Controller;
use App\Models\Imports\AlerteautoImport;
use App\Models\Mail\AlertaAutoEmail;
use App\Models\Parcauto;
use App\Models\Revizii;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;

class AlerteautoController extends Controller
{
     public function alerteAutoPtrAlerta()
    {
        
         $alerte=Emailalerte::where("tip_alerta","Alerta auto")->get();
         foreach ($alerte as $alerta) {
           
          $alertaAuto= Alerteauto:: where("data_expirare","<=",Carbon::today()->addDays(7))
                                ->where("company_id",$alerta->company_id)
                                ->with("parcauto")    
                                ->get();
          $revizii= Parcauto::where("company_id",$alerta->company_id)
                                ->with(["consumuriparcauto","revizii"])     
                                ->get();
         $reviziiFiltrat= new Parcauto;                       
         $reviziiFiltrat=$revizii->filter(function($item) {
               
            // Log::info($item->nr_inmatriculare." ".$item->revizii->max("data"));
            // return $item;
                      
                if($item->revizii()->count()!=0){
                        $item->datareviziaurmatoare=Carbon::parse($item->revizii->max("data"))->addMonth($item->nr_luni_revizie);
                    }else{
                        $item->datareviziaurmatoare=null;
                    }
                
                if($item->consumuriparcauto()->count()!=0){
                 
                    $item->ultimii_km_la_bord=$item->consumuriparcauto->max("km_la_bord");
               
                } else{
                  if($item->revizii()->count()!=0){
                      $item->ultimii_km_la_bord=$item->revizii->max("km_la_bord");
                   }else{
                      $item->ultimii_km_la_bord=0;
                   }  
                } 
                $item->km_urmatoarea_revizie=$item->ultimii_km_la_bord+$item->km_revizie;
                
           
              
                if((Carbon::today()->addDays(7)>=$item->datareviziaurmatoare)||$item->km_urmatoarea_revizie<=$item->ultimii_km_la_bord){
                                return $item;
                               }
            
            
           
            
           });                           
          
        
           Mail::to($alerta->email)
                 ->bcc($alerta->cc) 
                 ->send(new AlertaAutoEmail($alertaAuto,$reviziiFiltrat->values()));
         }
          
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
   public function indexPaginat(Request $request)
    {
         $records= Alerteauto::select('*')->where("comapny_id",session("company_id"));
          
          $records=filterRequest($records,$request->searchModel,$request->cautareDupa,$request->sortModel,$request->filterModel);
          $records= $records->orderBy('id','desc')
                                          ->paginate($request->pageLength,
                                                      ['page'=>$request->page]);
          
                                //::where("user_id",auth()->user()->id)
                                  
          return json_encode($records);
    }
     public function index()
    {
          $alerteauto= Alerteauto::where("company_id",session("company_id"))->get();
          return json_encode($alerteauto);
    }
    public function export() 
        {
             
            ob_end_clean(); 
            ob_start(); 
             $company_id=session("company_id");
            return Excel::download((new AlerteautoExport)->forCompany($company_id),"alerteauto.xls");


        }

    public function import(Request $request) 
        {
          $fileName = "alerteauto_".time().".".$request->file->getClientOriginalExtension();
          $request->file->move(public_path("upload"), $fileName);
          
          Excel::import(new AlerteautoImport, public_path("upload")."/".$fileName);

          
            $alerteauto= Alerteauto::where("company_id",session("company_id"))
                                                     ->paginate($request->pageLength,['page'=>$request->page]);
         
            return json_encode($alerteauto);
             
        
           
         }
        
    public function storeExcel() 
        {
            ob_end_clean(); 
            ob_start(); 
            $company_id=session("company_id");
            
            Excel::store((new AlerteautoExport)->forCompany($company_id), "alerteauto.xls","public",null,[
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

        // event(new AlerteautoUpdated());
         $obs= Alerteauto::create([
    	  "company_id"=>$request->company_id,
    	  "parcauto_id"=>$request->parcauto_id,
    	  "tip_alerta"=>$request->tip_alerta,
    	  "data_intocmire"=>$request->data_intocmire,
    	  "data_expirare"=>$request->data_expirare,
    	  "obs"=>$request->obs,           
        ]);
        return $obs->paginate($request->pageLength,['page'=>$request->page]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Alerteauto  $alerteauto
     * @return \Illuminate\Http\Response
     */
    public function show(Alerteauto $alerteauto)
    {
        $resp= Alerteauto::where("id",$alerteauto->id)
                               ->where("company_id",session("company_id"))
        					   ->get()->first();
                                
        return json_encode($resp);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Alerteauto  $alerteauto
     * @return \Illuminate\Http\Response
     */
    public function edit(Alerteauto $alerteauto)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Alerteauto  $alerteauto
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Alerteauto $alerteauto)
    {
        $alerteauto->update([
    	  "company_id"=>$request->company_id,
    	  "parcauto_id"=>$request->parcauto_id,
    	  "tip_alerta"=>$request->tip_alerta,
    	  "data_intocmire"=>$request->data_intocmire,
    	  "data_expirare"=>$request->data_expirare,
    	  "obs"=>$request->obs,
        ]);
       // event(new AlerteautoUpdated());
        return $alerteauto;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Alerteauto  $alerteauto
     * @return \Illuminate\Http\Response
     */
    public function destroy(Alerteauto $alerteauto)
    {
        $alerteauto->delete();
      //  event(new AlerteautoUpdated());

    }
}