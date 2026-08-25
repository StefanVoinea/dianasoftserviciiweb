<?php

namespace App\Http\Controllers\Api;

use App\Models\Cursbnr;
use App\Exports\CursbnrExport;
use App\Http\Controllers\Controller;
use App\Imports\CursbnrImport;
use App\Mail\CursbnrEmail;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;
use App\Mail\AlertaEroareEmail;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class CursbnrController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function indexPaginat(Request $request)
    {
          $records= Cursbnr::select('*')->orderBy('created_at','DESC');
          $records=filterRequest($records,$request->searchModel,$request->cautareDupa,$request->sortModel,$request->filterModel);
          $records= $records->orderBy('id','desc')
                                          ->paginate($request->pageLength,
                                                      ['page'=>$request->page]);
          
                                //::where("user_id",auth()->user()->id)
                                  
          return json_encode($records);
    }
     public function index()
    {
          $cursbnr= Cursbnr::get();
          return json_encode($cursbnr);
    }
     public function cursbnrptrziua(Request $request)
    {
         
          $cursbnr= round(cursBNR(dateFormatStocare($request->data),$request->tip_valuta),4);
          return json_encode($cursbnr);
    }
    public function export() 
        {
            ob_end_clean(); 
            ob_start(); 
            $company_id=session("company_id");
            return Excel::download((new CursbnrExport)->forCompany($company_id),"cursbnr.xls");


        }

    public function import(Request $request) 
        {
          $fileName = "cursbnr_".time().".".$request->file->getClientOriginalExtension();
          $request->file->move(public_path("upload"), $fileName);
          
          Excel::import(new CursbnrImport, public_path("upload")."/".$fileName);

          
            $cursbnr= Cursbnr::where("company_id",session("company_id"))
                                                     ->paginate($request->pageLength,['page'=>$request->page]);
         
            return json_encode($cursbnr);
             
        
           
         }
        
    public function storeExcel() 
        {
            ob_end_clean(); 
            ob_start(); 
            $company_id=session("company_id");
            
            Excel::store((new CursbnrExport)->forCompany($company_id), "cursbnr.xls","public",null,[
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
         
    	  "data"=>["required"],]);

        // event(new CursbnrUpdated());
         $curs= Cursbnr::create([
    	  "data"=>$request->data,
    	  "data_comunicarii"=>$request->data_comunicarii,
    	  "tip_valuta"=>$request->tip_valuta,
    	  "curs"=>$request->curs,           
        ]);
        return $curs->paginate($request->pageLength,['page'=>$request->page]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Cursbnr  $cursbnr
     * @return \Illuminate\Http\Response
     */
    public function show(Cursbnr $cursbnr)
    {
        $resp= Cursbnr::where("id",$cursbnr->id)
                               ->get()->first();
                                
        return json_encode($resp);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Cursbnr  $cursbnr
     * @return \Illuminate\Http\Response
     */
    public function edit(Cursbnr $cursbnr)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Cursbnr  $cursbnr
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Cursbnr $cursbnr)
    {
        $cursbnr->update([
    	  "data"=>$request->data,
    	  "data_comunicarii"=>$request->data_comunicarii,
    	  "tip_valuta"=>$request->tip_valuta,
    	  "curs"=>$request->curs,
        ]);
       // event(new CursbnrUpdated());
        return $cursbnr->paginate($request->pageLength,['page'=>$request->page]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Cursbnr  $cursbnr
     * @return \Illuminate\Http\Response
     */
    public function destroy(Cursbnr $cursbnr)
    {
        $cursbnr->delete();
      //  event(new CursbnrUpdated());

    }

      /**
         * parseXMLDocument method
         *
         * @access        public
         * @return         void
         */
        function parseXMLDocument()
        {
             $xml = new \SimpleXMLElement($this->xmlDocument);
             
             $this->date=$xml->Header->PublishingDate;
             
             foreach($xml->Body->Cube->Rate as $line)    
             {                      
                 $this->currency[]=array("name"=>$line["currency"], "value"=>$line, "multiplier"=>$line["multiplier"]);
             }
        }
        
        /**
         * getCurs method
         * 
         * get current exchange rate: example getExchangeRate("USD")
         * 
         * @access        public
         * @return         double
         */
        function preiaCurs($currency)
        {
            foreach($this->currency as $line)
            {
                if($line["name"]==$currency)
                {
                    return $line["value"];
                }
            }
            
            return "Incorrect currency!";
        }

    
       
    public function preiaCursBNR()
    {    
        try{
         $this->xmlDocument = file_get_contents("https://curs.bnr.ro/nbrfxrates.xml");

         $this->parseXMLDocument();

         /*
          * Cursul anuntat azi se aplica maine. Scrierea nu dubleaza: comanda
          * ruleaza si la 13:30, si la 18:00, iar randul zilei se scrie o
          * singura data — a doua rulare doar l-ar rescrie cu aceeasi valoare.
          */
         $curs_EUR = Cursbnr::updateOrCreate(
             ['tip_valuta' => 'EUR', 'data' => dateFormatStocare(Carbon::tomorrow())],
             ['data_comunicarii' => $this->date, 'curs' => $this->preiaCurs("EUR")]
         );
         $curs_USD = Cursbnr::updateOrCreate(
             ['tip_valuta' => 'USD', 'data' => dateFormatStocare(Carbon::tomorrow())],
             ['data_comunicarii' => $this->date, 'curs' => $this->preiaCurs("USD")]
         );

         // Emailul pleaca o data pe zi, la primul curs scris, nu la fiecare rulare.
         if ($curs_EUR->wasRecentlyCreated || $curs_USD->wasRecentlyCreated) {
            Mail::to("stefan.voinea@gmail.com")
                 ->send(new CursbnrEmail($curs_EUR,$curs_USD));
         }

        } catch (\Exception $e) {
            $user=User::where("id",1)->get()->first();
            Mail::to("stefan.voinea@gmail.com")
            ->send(new AlertaEroareEmail("PREIA CURS BNR",$e->getMessage(),$e,$user));
            return response()->json(['message' => $e->getMessage()], 500);
        }           

    }
}