<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Contract;
use App\Models\Exports\MonitorizaredocumenteExport;
use App\Models\Monitorizaredocumente;
use App\Models\User;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use App\Mail\AlertaEroareEmail;
use App\Mail\AlertaEmail;
use Illuminate\Support\Facades\DB;

class MonitorizaredocumenteController extends Controller
{
    public function viewfile(Request $request,Monitorizaredocumente $monitorizaredocumente)
    {
        try{
        $company=Company::where("id",session("company_id"))->get()->first();
        ob_end_clean(); 
        ob_start(); 
        return Response::download(storage_path('app/public/'.$company->slug.'/'.'monitorizaredocumente/'.$monitorizaredocumente->fisier));

        } catch (\Exception $e) {
            Mail::to("stefan.voinea@gmail.com")
            ->send(new AlertaEroareEmail("VIEW FILE MONITORIZARE DOCUMENTE",$e->getMessage(),$e,Auth::user()));
            return response()->json(['message' => $e->getMessage()], 500);
        }  
    }
    public function uploadfile(Request $request,Monitorizaredocumente $monitorizaredocumente)
    {
        try{
        Log::info("PAS 1");    
        $company=Company::where("id",session("company_id"))->get()->first();
        Log::info("PAS 2");
        $fileName = $monitorizaredocumente->tip_document."_".$monitorizaredocumente->gestiune->denumire."_".time().".".$request->file->getClientOriginalExtension();
        Log::info("PAS 3");
        $request->file->move(storage_path('app/public/'.$company->slug.'/'.'monitorizaredocumente'), $fileName);
        Log::info("PAS 4");
        $numeClient="";
        Log::info("PAS 5");
        $contract=Contract::where("id",$monitorizaredocumente->contract_id)->get()->first();
        Log::info("PAS 6");
        if($contract){
            $numeClient=$contract->nume;
        }
        Log::info("PAS 7");
        $contents = file_get_contents(storage_path('app/public/'.$company->slug.'/'.'monitorizaredocumente/'. $fileName));
        Log::info("PAS 8");
        $path = "/".$fileName;
        $upload = Storage::disk('dropbox')->put($path, $contents);
        Log::info("PAS 9");
        $monitorizaredocumente->update(["fisier"=>$fileName]);
        Log::info("PAS 10");
        $mesaj="A fost incarcat documentul ".$monitorizaredocumente->tip_document." de catre agentia ".$monitorizaredocumente->gestiune->denumire;
        //$email=$monitorizaredocumente->user->email;
        Log::info("PAS 11");
            Mail::to("financiar@easycredit.ro")->send(new AlertaEmail("Monitorizare documente->Documentul ".$monitorizaredocumente->tip_document." pentru clientul " . $numeClient . " a fost incarcat de catre ".Auth::user()->name." ".datasioracurenta(),$mesaj));
        Log::info("PAS 12");    
        } catch (\Exception $e) {
            Log::info("PAS 13");
            Mail::to("stefan.voinea@gmail.com")
            ->send(new AlertaEroareEmail("UPLOAD FILE MONITORIZARE DOCUMENTE",$e->getMessage(),$e,Auth::user()));
            return response()->json(['message' => $e->getMessage()], 500);
        }  
        // Log::info(Storage::disk('dropbox')->allFiles('/'));
        // Log::info("UPLOAD:".$upload);
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function indexPaginat(Request $request)
    {
        try{
        $user=User::where("id",session("user_id"))->get()->first();
        $gestiunipermise=$user->gestiuniPermiseCompany()->pluck("id");
        $records= Monitorizaredocumente::select('*')->where("company_id",session("company_id"))
                                                    ->whereIn("gestiune_id",$gestiunipermise);
        $records=filterRequest($records,$request->searchModel,$request->cautareDupa,$request->sortModel,$request->filterModel);
        $records=$records->whereIn("gestiune_id",$gestiunipermise);
        $records=  $records->orderBy('id','desc');
        $records=  $records->paginate($request->pageLength,
                                                                    ["page"=>$request->page]);
        
                              //::where("user_id",auth()->user()->id)
                                
        return json_encode($records);

        } catch (\Exception $e) {
            Mail::to("stefan.voinea@gmail.com")
            ->send(new AlertaEroareEmail("INDEX PAGINAT MONITORIZARE DOCUMENTE",$e->getMessage(),$e,Auth::user()));
            return response()->json(['message' => $e->getMessage()], 500);
        }  
    }
     public function index()
    {
        try{
          $monitorizaredocumente= Monitorizaredocumente::where("company_id",session("company_id"))->get();
          return json_encode($monitorizaredocumente);

        } catch (\Exception $e) {
            Mail::to("stefan.voinea@gmail.com")
            ->send(new AlertaEroareEmail("INDEX MONITORIZARE DOCUMENTE",$e->getMessage(),$e,Auth::user()));
            return response()->json(['message' => $e->getMessage()], 500);
        }  
    }
    public function export() 
        {
             
            ob_end_clean(); 
            ob_start(); 
             $company_id=session("company_id");
            return Excel::download((new MonitorizaredocumenteExport)->forCompany($company_id),"monitorizaredocumente.xls");


        }

    public function import(Request $request) 
        {
          $fileName = "monitorizaredocumente_".time().".".$request->file->getClientOriginalExtension();
          $request->file->move(public_path("upload"), $fileName);
          
          Excel::import(new MonitorizaredocumenteImport, public_path("upload")."/".$fileName);

          
            $monitorizaredocumente= Monitorizaredocumente::where("company_id",session("company_id"))
                                                     ->paginate($request->pageLength,['page'=>$request->page]);
         
            return json_encode($monitorizaredocumente);
             
        
           
         }
        
    public function storeExcel() 
        {
            ob_end_clean(); 
            ob_start(); 
            $company_id=session("company_id");
            
            Excel::store((new MonitorizaredocumenteExport)->forCompany($company_id), "monitorizaredocumente.xls","public",null,[
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

        // event(new MonitorizaredocumenteUpdated());
        DB::beginTransaction();
        try{
        $company=Company::where("id",session("company_id"))->get()->first();
      
        $monitorizaredocumente= Monitorizaredocumente::create([
                                                 "company_id"=>session("company_id"),
                                                 "gestiune_id"=>valoareCamp("id",$request->gestiune),
    	                                         "contract_id"=>valoareCamp("id",$request->contract),
    	                                         "user_id"=>session("user_id"),
    	                                         "tip_document"=>$request->tip_document??null,
    	                                         "fisier"=>"",           
                                                 "obs"=>$request->obs??null,
                                                 "data_incasarii"=>$request->data_incasarii?dateFormatStocare($request->data_incasarii):null,
                                                 "suma_incasata"=>$request->suma_incasata??null,
                                                 "tip_valuta"=>$request->tip_valuta??null,
                                                 "banca"=>$request->banca??null,
                                                 "status"=>$request->status??null,

        ]);

            DB::commit();
            $numeClient="";
            $contract=Contract::where("id",$monitorizaredocumente->contract_id)->get()->first();
            if($contract){
                $numeClient=$contract->nume;
            }
            $mesaj="Aveti o solicitare noua de ".$monitorizaredocumente->tip_document ." pentru clientul ".$numeClient;
            $email=User::where("name",valoareCamp("denumire",$request->gestiune))->get()->first()->email;
            Mail::to($email)->send(new AlertaEmail("Monitorizare documente->Solicitare document pentru clientul ".$numeClient ." de la ".Auth::user()->name." ".datasioracurenta(),$mesaj));

        return json_encode($monitorizaredocumente->fresh());
           
        } catch (\Exception $e) {
            DB::rollback();
            Mail::to("stefan.voinea@gmail.com")
            ->send(new AlertaEroareEmail("STORE MONITORIZARE DOCUMENTE",$e->getMessage(),$e,Auth::user()));
            return response()->json(['message' => $e->getMessage()], 500);
        }  
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Monitorizaredocumente  $monitorizaredocumente
     * @return \Illuminate\Http\Response
     */
    public function show(Monitorizaredocumente $monitorizaredocumente)
    {
        try{
        $resp= Monitorizaredocumente::where("id",$monitorizaredocumente->id)
                               ->where("company_id",session("company_id"))
        					   ->get()->first();
                                
        return json_encode($resp);

        } catch (\Exception $e) {
            Mail::to("stefan.voinea@gmail.com")
            ->send(new AlertaEroareEmail("SHOW MONITORIZARE DOCUMENTE",$e->getMessage(),$e,Auth::user()));
            return response()->json(['message' => $e->getMessage()], 500);
        }  
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Monitorizaredocumente  $monitorizaredocumente
     * @return \Illuminate\Http\Response
     */
    public function edit(Monitorizaredocumente $monitorizaredocumente)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Monitorizaredocumente  $monitorizaredocumente
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Monitorizaredocumente $monitorizaredocumente)
    {
        DB::beginTransaction();
        try{

        $monitorizaredocumente->update([
    	  "gestiune_id"=>valoareCamp("id",$request->gestiune),
          "contract_id"=>valoareCamp("id",$request->contract),
    	  "tip_document"=>$request->tip_document??null,
    	  "fisier"=>$request->fisier??null,
          "obs"=>$request->obs??null,
          "data_incasarii"=>$request->data_incasarii?dateFormatStocare($request->data_incasarii):null,
          "suma_incasata"=>$request->suma_incasata??null,
          "tip_valuta"=>$request->tip_valuta??null,
          "banca"=>$request->banca??null,
          "status"=>$request->status??null,
        ]);
       // event(new MonitorizaredocumenteUpdated());
            DB::commit();
        return $monitorizaredocumente;
           
        } catch (\Exception $e) {
            DB::rollback();
            Mail::to("stefan.voinea@gmail.com")
            ->send(new AlertaEroareEmail("UPDATE MONITORIZARE DOCUMENTE",$e->getMessage(),$e,Auth::user()));
            return response()->json(['message' => $e->getMessage()], 500);
        }  
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Monitorizaredocumente  $monitorizaredocumente
     * @return \Illuminate\Http\Response
     */
    public function destroy(Monitorizaredocumente $monitorizaredocumente)
    {
        try{
        $monitorizaredocumente->delete();
      //  event(new MonitorizaredocumenteUpdated());

        } catch (\Exception $e) {
            Mail::to("stefan.voinea@gmail.com")
            ->send(new AlertaEroareEmail("STERGE MONITORIZARE DOCUMENTE",$e->getMessage(),$e,Auth::user()));
            return response()->json(['message' => $e->getMessage()], 500);
        }  

    }
}
