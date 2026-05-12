<?php

namespace App\Http\Controllers\Api;

use App\Models\Company;
use App\Models\Gestiune;
use App\Models\Gestiune_User;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;

class GestiuneController extends Controller
{
    public function raportdegestiune(Request $request)
    {
        $gestiune= collect($request->gestiune);
        
        $intrariNIR=DB::select(DB::raw("SELECT 'Intrari' as tip,'NIR' as tip_document,antetdocumenteprimite.nr_nir AS nr_document, antetdocumenteprimite.data_nir AS data_document, antetdocumenteprimite.furnizor AS partener, Sum(detaliudocumenteprimite.valoare_vanzare) AS total
            FROM detaliudocumenteprimite INNER JOIN antetdocumenteprimite ON detaliudocumenteprimite.antetdocumenteprimite_id = antetdocumenteprimite.id
            WHERE (((antetdocumenteprimite.nir)=True) AND ((antetdocumenteprimite.company_id)=".session("company_id").") AND ((antetdocumenteprimite.gestiune_id)=".$gestiune["id"]."))
                    GROUP BY antetdocumenteprimite.nr_nir, antetdocumenteprimite.data_nir, antetdocumenteprimite.furnizor
                    HAVING (((antetdocumenteprimite.data_nir)>='".Carbon::parse($request->datai)."' And (antetdocumenteprimite.data_nir)<='".Carbon::parse($request->datasf)."'));
                    "));
        $iesiriAMEF=DB::select(DB::raw("SELECT 'Iesiri' AS tip, 'Monetar' AS tip_document, borderouincasari.nr_document,    borderouincasari.data_document,'DIVERSI' as partener, Sum(borderouincasari.valoare) AS total
            FROM borderouincasari
            WHERE (((borderouincasari.company_id)=".session("company_id").") AND ((borderouincasari.tip_operatiune)='Cu AMEF') AND ((borderouincasari.gestiune_id)=".$gestiune["id"]."))
            GROUP BY 'Iesiri', 'Monetar', borderouincasari.nr_document, borderouincasari.data_document
            HAVING (((borderouincasari.data_document)>='".Carbon::parse($request->datai)."' And (borderouincasari.data_document)<='".Carbon::parse($request->datasf)."'));"));
        $iesiriFCT=DB::select(DB::raw("SELECT 'Iesiri' AS tip,antetvanzari.tip_document, antetvanzari.numar AS nr_document, antetvanzari.data AS data_document, antetvanzari.partener, Sum(detaliuvanzari.valoare) AS total
            FROM antetvanzari INNER JOIN detaliuvanzari ON antetvanzari.id = detaliuvanzari.antetvanzare_id
            WHERE (((antetvanzari.company_id)=".session("company_id").") AND ((antetvanzari.gestiune)='".$gestiune["denumire"]."'))
            GROUP BY antetvanzari.tip_document, antetvanzari.numar, antetvanzari.data, antetvanzari.partener
            HAVING (((antetvanzari.tip_document)='Factura') AND ((antetvanzari.data)>='".Carbon::parse($request->datai)."' And (antetvanzari.data)<='".Carbon::parse($request->datasf)."'));
            "));
        $soldNIR=DB::select(DB::raw("SELECT Sum(detaliudocumenteprimite.valoare_vanzare) AS total
                    FROM detaliudocumenteprimite INNER JOIN antetdocumenteprimite ON detaliudocumenteprimite.antetdocumenteprimite_id = antetdocumenteprimite.id
                    WHERE (((antetdocumenteprimite.nir)=True) AND ((antetdocumenteprimite.company_id)=".session("company_id")."  AND ((antetdocumenteprimite.data_nir)<'".Carbon::parse($request->datai)."' And (antetdocumenteprimite.data_nir)>='".Carbon::parse($gestiune["data_sold"])."') AND ((antetdocumenteprimite.gestiune_id)=".$gestiune["id"].")));
                    "));
         $soldAMEF=DB::select(DB::raw("SELECT Sum(borderouincasari.valoare) AS total
                        FROM borderouincasari
                        WHERE (((borderouincasari.company_id)=".session("company_id").") AND ((borderouincasari.tip_operatiune)='Cu AMEF') AND ((borderouincasari.data_document)<'".Carbon::parse($request->datai)."' And (borderouincasari.data_document)>='".Carbon::parse($gestiune["data_sold"])."') AND ((borderouincasari.gestiune_id)=".$gestiune["id"]."));
                        "));
         $soldFCT=DB::select(DB::raw("SELECT Sum(detaliuvanzari.valoare) AS total
                        FROM antetvanzari INNER JOIN detaliuvanzari ON antetvanzari.id = detaliuvanzari.antetvanzare_id
                        WHERE (((antetvanzari.company_id)=".session("company_id").") AND ((antetvanzari.tip_document)='Factura') AND ((antetvanzari.data)<'".Carbon::parse($request->datai)."' And (antetvanzari.data)>='".Carbon::parse($gestiune["data_sold"])."') AND ((antetvanzari.gestiune)='".$gestiune["denumire"]."'));"));
        $miscariArray=array_merge($intrariNIR,$iesiriAMEF,$iesiriFCT);
        $miscari=collect($miscariArray)->sortby("data_document");
       
        $soldini=$gestiune["sold_initial"]+($soldNIR[0]?$soldNIR[0]->total:0)-($soldAMEF[0]?$soldAMEF[0]->total:0)-($soldFCT[0]?$soldFCT[0]->total:0);
        
         $company=Company::where("id",session("company_id"))->get()->first(); 
           $numefis=storage_path('app\\public\\'.$company->slug.'\\raport_de_gestiune_'.time().".pdf");
           if(File::exists($numefis)){
                File::delete($numefis);
              };
            ob_end_clean(); 
            ob_start();   
            $i=1;
            $numeview="gestiune.raportdegestiune";
            $tippag='portrait';
            
            $pdf = \Barryvdh\Snappy\Facades\SnappyPdf::loadView($numeview, [
                'miscari' => $miscari,
                'soldini'=>$soldini,
                'datai'=>$request->datai,
                'gestiune'=>$gestiune["denumire"],
                'datasf'=>$request->datasf,
                'company'=>$company,
               'i' => $i
            ])->setPaper('a4')
            ->setOrientation($tippag)
            ->setOption('margin-top',5)
            ->setOption('margin-bottom',5)
            ->setOption('margin-left',10)
            ->setOption('margin-right',10)
            // ->setOption("header-html",View::make('vanzari.header_sdv'))
            ->setOption('footer-font-size', '8')
            ->setOption("footer-right","Pag. [page] / [topage]") 
            ->setOption("footer-left",Auth::user()->name." ".datasioracurenta()) ;
                 
                      // $pdf->setOption('javascript-delay', 3000);

            $pdf->save($numefis);
                  
             $headers = array(
                  'Content-Type: application/pdf',
                );

           
            return Response::download($numefis, 'raportdegestiune.pdf',$headers);

    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function indexPaginat(Request $request)
    {
          $records= Gestiune::select('*')->where("company_id",session("company_id"));
          $records=filterRequest($records,$request->searchModel,$request->cautareDupa,$request->sortModel,$request->filterModel);
          $records= $records->orderBy('denumire','asc')
                            ->paginate($request->pageLength,['page'=>$request->page]);
          
                                //::where("user_id",auth()->user()->id)
                                  
          return json_encode($records);
    }
    public function index()
    {
        $gestiune= Gestiune::where("company_id",session("company_id"))
                             ->get();
                                
        return json_encode($gestiune);
    }

     public function gestiuniPermise(Request $request)
    {  
        $user=User::where("id",session("user_id"))->get()->first();
        $gestiunipermise=$user->gestiuniPermiseCompany();
        return json_encode($gestiunipermise);
      
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
    	  "denumire"=>["required"],]);


         $gestiune= Gestiune::create([
    	  "company_id"=>session("company_id"),
          "denumire"=>$request->denumire??null,
    	  "adresa"=>$request->adresa??null,
    	  "localitate"=>$request->localitate??null,
    	  "judet"=>$request->judet??null,
    	  "telefon"=>$request->telefon??null,
    	  "email"=>$request->email??null,
    	  "tip_gestiune"=>$request->tip_gestiune??null,
    	  "cui"=>$request->cui??null,
    	  "gestionar"=>$request->gestionar??null,
    	  "cont_stoc"=>$request->cont_stoc??null,
    	  "cont_venit"=>$request->cont_venit??null,
    	  "cont_cheltuiala"=>$request->cont_cheltuiala??null,
    	  "cont_adaos"=>$request->cont_adaos??null,
    	  "cont_tva_neexigibil"=>$request->cont_tva_neexigibil??null,
    	  "cont_4426"=>$request->cont_4426??null,
    	  "cont_4427"=>$request->cont_4427??null,
    	  "cont_nesosite"=>$request->cont_nesosite??null,
    	  "cont_casierie"=>$request->cont_casierie??null,
          "casieria"=>$request->casieria??null,
    	  "sold_initial"=>$request->sold_initial??null,           
          "interior"=>$request->interior??null,
          "cod"=>$request->cod??null,
          "sef_gestiune"=>$request->sef_gestiune??null,
          "economist"=>$request->economist??null,
          "prescurtare"=>$request->prescurtare??null,
          "casier"=>$request->casier??null,
          "nr_ordine"=>$request->nr_ordine??null,
          "verificator"=>$request->verificator??null,
          "denumire_aplicacum"=>$request->denumire_aplicacum??null,
          "consilier_juridic"=>$request->consilier_juridic??null,
          "opc"=>$request->opc??null,
          "fax"=>$request->fax??null,
          "telefon_fix"=>$request->telefon_fix??null,
        ]);

         $currentuser=$request->user();
         $gestiune->users()
                  ->attach($currentuser->id,[
                                             'isactive'=>true,
                                             'company_id'=>session("company_id")
                                             ]);
         $allUsers=User::get();
         foreach ($allUsers as $user) {
            if($user->id != $request->user()->id )
            {
                
                $gestiune->users()->attach($user->id,['isactive'=>false,
                                                        'company_id'=>session("company_id")
                                                       ]);    
            
            }
            
         
            
         }
         return $gestiune;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Gestiune  $gestiune
     * @return \Illuminate\Http\Response
     */
    public function show(Gestiune $gestiune)
    {
        $resp= Gestiune::where("id",$gestiune->id)
        				->get()->first();
                                
        return json_encode($resp);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Gestiune  $gestiune
     * @return \Illuminate\Http\Response
     */
    public function edit(Gestiune $gestiune)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Gestiune  $gestiune
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Gestiune $gestiune)
    {
        $gestiune->update([
    	  "denumire"=>$request->denumire??null,
          "adresa"=>$request->adresa??null,
          "localitate"=>$request->localitate??null,
          "judet"=>$request->judet??null,
          "telefon"=>$request->telefon??null,
          "email"=>$request->email??null,
          "tip_gestiune"=>$request->tip_gestiune??null,
          "cui"=>$request->cui??null,
          "gestionar"=>$request->gestionar??null,
          "cont_stoc"=>$request->cont_stoc??null,
          "cont_venit"=>$request->cont_venit??null,
          "cont_cheltuiala"=>$request->cont_cheltuiala??null,
          "cont_adaos"=>$request->cont_adaos??null,
          "cont_tva_neexigibil"=>$request->cont_tva_neexigibil??null,
          "cont_4426"=>$request->cont_4426??null,
          "cont_4427"=>$request->cont_4427??null,
          "cont_nesosite"=>$request->cont_nesosite??null,
          "cont_casierie"=>$request->cont_casierie??null,
          "casieria"=>$request->casieria??null,
          "sold_initial"=>$request->sold_initial??null,           
          "interior"=>$request->interior??null,
          "cod"=>$request->cod??null,
          "sef_gestiune"=>$request->sef_gestiune??null,
          "economist"=>$request->economist??null,
          "prescurtare"=>$request->prescurtare??null,
          "casier"=>$request->casier??null,
          "nr_ordine"=>$request->nr_ordine??null,
          "verificator"=>$request->verificator??null,
          "denumire_aplicacum"=>$request->denumire_aplicacum??null,
          "consilier_juridic"=>$request->consilier_juridic??null,
          "opc"=>$request->opc??null,
          "fax"=>$request->fax??null,
          "telefon_fix"=>$request->telefon_fix??null,
        ]);
        return $gestiune;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Gestiune  $gestiune
     * @return \Illuminate\Http\Response
     */
    public function destroy(Gestiune $gestiune)
    {
       Gestiune_User::where('gestiune_id',$gestiune->id)
                             ->delete(); 
       $gestiune->delete();

    }
}