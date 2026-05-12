<?php

namespace App\Http\Controllers\Api;

use App\Exports\SablonMultipleSheetsExport;
use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Exports\LitigiuExport;
use App\Models\Litigiicaleatac;
use App\Models\Litigiiparti;
use App\Models\Litigiisedinte;
use App\Models\Litigiu;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;
use App\Mail\AlertaEroareEmail;

    

class LitigiuController extends Controller
{
    public function situatielitigii(Request $request)
  {
    try{
         $data=Carbon::parse($request->data);
         $datai=Carbon::parse($request->data)->firstofMonth();
         $datasf=Carbon::parse($request->data)->lastofMonth();
         $luna=Carbon::parse($datasf)->month;
         $anul=Carbon::parse($datasf)->year;
         $curs=cursBNR(Carbon::parse($datasf)->adddays(1),'EUR');
         $user=User::where("id",session("user_id"))->get()->first();
         $selectie="";
          Log::info("PAS 1 ".Carbon::now());
         
         $records=collect(DB::select(DB::raw("SELECT litigii.parti, litigii.avocatul_apararii, litigii.avocatul_acuzarii, litigii.institutie, litigii.numar_dosar, litigii.numar_vechi, litigii.stadiu_procesual, litigii.obiect, litigii.data_modificare, litigii.data_ultimei_verificari, litigii.observatii, CONCAT(DATE_FORMAT(litigiisedinte.data_sedinta,'%d.%m.%Y'),' ', litigiisedinte.ora_sedinta) as datasiora_sedinta, litigiisedinte.solutie, litigiisedinte.solutie_sumar, litigii.taxa_de_timbru, litigii.cheltuieli_de_judecata
            FROM (litigii LEFT JOIN 
                  (SELECT litigiisedinte.litigiu_id, Max(litigiisedinte.data_sedinta) AS MaxOfdata_sedinta
                    FROM litigiisedinte
                    GROUP BY litigiisedinte.litigiu_id
                   )
                 AS ultimasedinta ON litigii.id = ultimasedinta.litigiu_id) LEFT JOIN litigiisedinte ON (ultimasedinta.MaxOfdata_sedinta = litigiisedinte.data_sedinta) AND (ultimasedinta.litigiu_id = litigiisedinte.litigiu_id)
          ;")));
           Log::info($records);
     
    Log::info("PAS 2");   
          
      $antetTabel=[
                    ['col'=>'parti','denumire'=>'Parti','type'=>'','align'=>'center','width'=>'10%'],
                    ['col'=>'avocatul_apararii','denumire'=>'Avocatul apararii','type'=>'','align'=>'center','width'=>'10%'],
                    ['col'=>'avocatul_acuzarii','denumire'=>'Avocatul acuzarii','type'=>'','align'=>'center','width'=>'10%'],
                    ['col'=>'institutie','denumire'=>'Instanta','type'=>'','align'=>'center','width'=>'10%'],
                    ['col'=>'obiect','denumire'=>'Obiect','type'=>'','align'=>'center','width'=>'10%'],
                    ['col'=>'stadiu_procesual','denumire'=>'Stadiu procesual','type'=>'','align'=>'center','width'=>'10%'],
                    ['col'=>'numar_dosar','denumire'=>'Dosar instanta','type'=>'','align'=>'center','width'=>'10%'],
                    ['col'=>'datasiora_sedinta','denumire'=>'Termen','type'=>'','align'=>'center','width'=>'10%'],
                    ['col'=>'solutie','denumire'=>'Solutie','type'=>'','align'=>'center','width'=>'10%'],
                    ['col'=>'solutie_sumar','denumire'=>'Solutia pe scurt','type'=>'','align'=>'center','width'=>'10%'],
                    ['col'=>'observatii','denumire'=>'Observatii','type'=>'','align'=>'center','width'=>'10%'],
                    ['col'=>'taxa_de_timbru','denumire'=>'Taxa de timbru','type'=>'','align'=>'center','width'=>'10%'],
                    ['col'=>'cheltuieli_de_judecata','denumire'=>'Cheltuieli de judecata','type'=>'','align'=>'center','width'=>'10%'],
                    
            ];
                
                 $tabel=collect($records);
                 $groupBy=[
                                                                       
                           ];   
                 $totalBy=[
                            
                           ];
                 $titluRaport="Situatie dosare instanta la data de  " .dateFormatAfisare($data);
                  $titluSheet="Situatie dosare instanta";
                  $columnFormat=[];

                  $sheeturi=[];
                  $sheet= new \StdClass;
                  $sheet->titluSheet=$titluSheet;
                  $sheet->tabel=$tabel;
                  $sheet->antetTabel=$antetTabel;
                  $sheet->totalBy=$totalBy;    
                  $sheet->groupBy=$groupBy;
                  $sheet->columnFormat=$columnFormat;
                  array_push($sheeturi,$sheet);

                  

                        

                   $company=Company::where("id",session("company_id"))->get()->first(); 
                   if($request->format_fisier=="Excel"){
                      $company_id=session("company_id");
                      ob_end_clean(); 
                      ob_start(); 
                               
                      $fileName="situatie_dosare_instanta.xls";
                     return Excel::download((new SablonMultipleSheetsExport)->forCompany($company_id,$sheeturi,$titluRaport),$fileName);
                    } 
          if($request->format_fisier=="PDF"){  
            $company=Company::where("id",session("company_id"))->get()->first();
             $numefis=storage_path('app/public/'.$company->slug.'/situatie_dosare_instanta_'.time().".pdf");
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

           
            return Response::download($numefis, 'situatie_dosare_instanta.pdf',$headers);
          }  

        } catch (\Exception $e) {
            Mail::to("stefan.voinea@gmail.com")
            ->send(new AlertaEroareEmail("SITUATIE LITIGIU",$e->getMessage(),$e,Auth::user()));
            return response()->json(['message' => $e->getMessage()], 500);
        }                 

  }
    function preiaNumarDosar(Request $request){
       try{
       $litigii=preiaDosareInstanta($request->numar_dosar,null,null,null,null,null);
       return json_encode($litigii);   
        } catch (\Exception $e) {
            Mail::to("stefan.voinea@gmail.com")
            ->send(new AlertaEroareEmail("PREIA NUMAR DOSAR LITIGIU",$e->getMessage(),$e,Auth::user()));
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
          $records= Litigiu::select('*')->where("company_id",session("company_id"));
          $records=filterRequest($records,$request->searchModel,$request->cautareDupa,$request->sortModel,$request->filterModel);
        $records=  $records->orderBy('id','desc');
        $records=  $records->paginate($request->pageLength,
                                                                    ["page"=>$request->page]);
        
                              //::where("user_id",auth()->user()->id)
                                
        
        return json_encode($records);

        } catch (\Exception $e) {
            Mail::to("stefan.voinea@gmail.com")
            ->send(new AlertaEroareEmail("INDEX PAGINAT LITIGIU",$e->getMessage(),$e,Auth::user()));
            return response()->json(['message' => $e->getMessage()], 500);
        }  
    }
     public function index()
    {
        try{
          $litigiu= Litigiu::where("company_id",session("company_id"))->get();
          return json_encode($litigiu);

        } catch (\Exception $e) {
            Mail::to("stefan.voinea@gmail.com")
            ->send(new AlertaEroareEmail("INDEX LITIGIU",$e->getMessage(),$e,Auth::user()));
            return response()->json(['message' => $e->getMessage()], 500);
        }  
    }
    public function export() 
        {
             
            ob_end_clean(); 
            ob_start(); 
             $company_id=session("company_id");
            return Excel::download((new LitigiuExport)->forCompany($company_id),"litigiu.xls");


        }

    public function import(Request $request) 
        {
          $fileName = "litigiu_".time().".".$request->file->getClientOriginalExtension();
          $request->file->move(public_path("upload"), $fileName);
          
          Excel::import(new LitigiuImport, public_path("upload")."/".$fileName);

          
            $litigiu= Litigiu::where("company_id",session("company_id"))
                                                     ->paginate($request->pageLength,['page'=>$request->page]);
         
            return json_encode($litigiu);
             
        
           
         }
        
    public function storeExcel() 
        {
            ob_end_clean(); 
            ob_start(); 
            $company_id=session("company_id");
            
            Excel::store((new LitigiuExport)->forCompany($company_id), "litigiu.xls","public",null,[
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

        // event(new LitigiuUpdated());
        DB::beginTransaction();
         try{
        foreach($request->litigii as $litigiu){
            $litigiu["avocatul_apararii"]=$request->avocatul_apararii??null;
            $litigiu["avocatul_acuzarii"]=$request->avocatul_acuzarii??null;
            $litigiu["observatii"]=$request->observatii??null;
            $litigiu["email_alerte"]=$request->email_alerte??null;
            $litigiu["telefon_alerte"]=$request->telefon_alerte??null;
            $litigiu["status"]=$request->status??null;
            $litigiu["taxa_de_timbru"]=$request->taxa_de_timbru??null;
            $litigiu["cheltuieli_de_judecata"]=$request->cheltuieli_de_judecata??null;
            $dosar= Litigiu::create($litigiu);
            foreach($litigiu["litigiiparti"] as $parte){
                $parte["litigiu_id"]=$dosar->id;
                Litigiiparti::create($parte);
            }
            foreach($litigiu["litigiicaleatac"] as $caleatac){
                $caleatac["litigiu_id"]=$dosar->id;
                Litigiicaleatac::create($caleatac);
            }
            foreach($litigiu["litigiisedinte"] as $sedinta){
                $sedinta["litigiu_id"]=$dosar->id;
                Litigiisedinte::create($sedinta);
            }
        }
         DB::update("UPDATE litigii INNER JOIN litigii AS litigii_1 ON litigii.numar_dosar = litigii_1.numar_dosar SET litigii.status = 'Instanta anterioara'
                WHERE (((litigii.data_dosar)<litigii_1.data_dosar));"); 
         DB::commit();
        return $dosar->fresh();
          
        } catch (\Exception $e) {
            DB::rollback();
            Mail::to("stefan.voinea@gmail.com")
            ->send(new AlertaEroareEmail("STORE LITIGIU",$e->getMessage(),$e,Auth::user()));
            return response()->json(['message' => $e->getMessage()], 500);
        } 
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Litigiu  $litigiu
     * @return \Illuminate\Http\Response
     */
    public function show(Litigiu $litigiu)
    {
        try{
        $resp= Litigiu::where("id",$litigiu->id)
                               ->where("company_id",session("company_id"))
        					   ->get()->first();
                                
        return json_encode($resp);

        } catch (\Exception $e) {
            Mail::to("stefan.voinea@gmail.com")
            ->send(new AlertaEroareEmail("SHOW LITIGIU",$e->getMessage(),$e,Auth::user()));
            return response()->json(['message' => $e->getMessage()], 500);
        } 
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Litigiu  $litigiu
     * @return \Illuminate\Http\Response
     */
    public function edit(Litigiu $litigiu)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Litigiu  $litigiu
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Litigiu $litigiu)
    {
        DB::beginTransaction();
        try{
        $litigiu->update([
    	  "numar_dosar"=>$request->numar_dosar,
    	  "numar_vechi"=>$request->numar_vechi,
    	  "data_dosar"=>$request->data_dosar,
    	  "institutie"=>$request->institutie,
    	  "departament"=>$request->departament,
    	  "categorie_caz"=>$request->categorie_caz,
    	  "stadiu_procesual"=>$request->stadiu_procesual,
    	  "avocatul_apararii"=>$request->avocatul_apararii,
    	  "avocatul_acuzarii"=>$request->avocatul_acuzarii,
    	  "observatii"=>$request->observatii,
    	  "status"=>$request->status,
    	  "taxa_de_timbru"=>$request->taxa_de_timbru,
    	  "cheltuieli_de_judecata"=>$request->cheltuieli_de_judecata,
    	  "parti"=>$request->parti,
        ]);
       // event(new LitigiuUpdated());
        DB::commit();
        return $litigiu;
        
        } catch (\Exception $e) {
            DB::rollback();
            Mail::to("stefan.voinea@gmail.com")
            ->send(new AlertaEroareEmail("UPDATE LITIGIU",$e->getMessage(),$e,Auth::user()));
            return response()->json(['message' => $e->getMessage()], 500);
        } 
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Litigiu  $litigiu
     * @return \Illuminate\Http\Response
     */
    public function destroy(Litigiu $litigiu)
    {
        DB::beginTransaction();
        try{
         foreach($litigiu->litigiiparti as $parte){
                $parte->delete();
                
            }
            foreach($litigiu->litigiicaleatac as $caleatac){
                $caleatac->delete();
                
            }
            foreach($litigiu->litigiisedinte as $sedinta){
                $sedinta->delete();
                
            }
        $litigiu->delete();
      //  event(new LitigiuUpdated());
        DB::commit();
          
        } catch (\Exception $e) {
            DB::rollback();
            Mail::to("stefan.voinea@gmail.com")
            ->send(new AlertaEroareEmail("STERGE LITIGIU",$e->getMessage(),$e,Auth::user()));
            return response()->json(['message' => $e->getMessage()], 500);
        } 
    }
}