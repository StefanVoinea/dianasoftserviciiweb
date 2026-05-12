<?php

namespace App\Http\Controllers\Api;

use App\Models\Ordinedeblocareanaf;
// use App\Events\OrdinedeblocareanafUpdated;
use App\Models\Exports\OrdinedeblocareanafExport;
//use App\Models\Imports\OrdinedeblocareanafImport;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Mail;
use App\Mail\AlertaEroareEmail;
use App\Models\User;
use Illuminate\Support\Facades\DB;

   

class OrdinedeblocareanafController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function indexPaginat(Request $request)
    {
        try{
          $records= Ordinedeblocareanaf::select('*')->where("company_id",session("company_id"));
          $records=filterRequest($records,$request->searchModel,$request->cautareDupa,$request->sortModel,$request->filterModel);
        $records=  $records->orderBy('id','desc');
        $records=  $records->paginate($request->pageLength,
                                                                    ["page"=>$request->page]);
        
                              //::where("user_id",auth()->user()->id)
                                
        return json_encode($records);

        } catch (\Exception $e) {
            Mail::to("stefan.voinea@gmail.com")
            ->send(new AlertaEroareEmail("INDEX PAGINAT ORDINE DE BLOCARE ANAF",$e->getMessage(),$e,Auth::user()));
            return response()->json(['message' => $e->getMessage()], 500);
        }  
    }
     public function index()
    {
        try{
          $ordinedeblocareanaf= Ordinedeblocareanaf::where("company_id",session("company_id"))->get();
          return json_encode($ordinedeblocareanaf);
         } catch (\Exception $e) {
            Mail::to("stefan.voinea@gmail.com")
            ->send(new AlertaEroareEmail("INDEX ORDINE DE BLOCARE ANAF",$e->getMessage(),$e,Auth::user()));
            return response()->json(['message' => $e->getMessage()], 500);
        }    
    }
    public function export() 
        {
             
            ob_end_clean(); 
            ob_start(); 
             $company_id=session("company_id");
            return Excel::download((new OrdinedeblocareanafExport)->forCompany($company_id),"ordinedeblocareanaf.xls");


        }

    public function import(Request $request) 
        {
          $fileName = "ordinedeblocareanaf_".time().".".$request->file->getClientOriginalExtension();
          $request->file->move(public_path("upload"), $fileName);
          
          Excel::import(new OrdinedeblocareanafImport, public_path("upload")."/".$fileName);

          
            $ordinedeblocareanaf= Ordinedeblocareanaf::where("company_id",session("company_id"))
                                                     ->paginate($request->pageLength,['page'=>$request->page]);
         
            return json_encode($ordinedeblocareanaf);
             
        
           
         }
        
    public function storeExcel() 
        {
            ob_end_clean(); 
            ob_start(); 
            $company_id=session("company_id");
            
            Excel::store((new OrdinedeblocareanafExport)->forCompany($company_id), "ordinedeblocareanaf.xls","public",null,[
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

        // event(new OrdinedeblocareanafUpdated());
        DB::beginTransaction();
        try{
                 $institutia= Ordinedeblocareanaf::create([
            "company_id"=>session("company_id"),
        
    	  "nr_ordin"=>$request->nr_ordin,
        "data_ordin"=>$request->data_ordin?dateFormatStocare($request->data_ordin):null,
    	  "suspect"=>$request->suspect,
    	  "date_de_identificare"=>$request->date_de_identificare,
    	  "bunuri_blocate"=>$request->bunuri_blocate,
    	  "ordin_de_revocare"=>$request->ordin_de_revocare,
        "data_revocarii"=>$request->data_revocarii?dateFormatStocare($request->data_revocarii):null,
    	  "institutia"=>$request->institutia,           
        ]);
         DB::commit();
        return $institutia;
         
        } catch (\Exception $e) {
            DB::rollback();
            Mail::to("stefan.voinea@gmail.com")
            ->send(new AlertaEroareEmail("STORE ORDINE DE BLOCARE ANAF",$e->getMessage(),$e,Auth::user()));
            return response()->json(['message' => $e->getMessage()], 500);
        }  
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Ordinedeblocareanaf  $ordinedeblocareanaf
     * @return \Illuminate\Http\Response
     */
    public function show(Ordinedeblocareanaf $ordinedeblocareanaf)
    {
        try{
        $resp= Ordinedeblocareanaf::where("id",$ordinedeblocareanaf->id)
                               ->where("company_id",session("company_id"))
        					   ->get()->first();
                                
        return json_encode($resp);
         } catch (\Exception $e) {
            Mail::to("stefan.voinea@gmail.com")
            ->send(new AlertaEroareEmail("SHOW ORDINE DE BLOCARE ANAF",$e->getMessage(),$e,Auth::user()));
            return response()->json(['message' => $e->getMessage()], 500);
        }  
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Ordinedeblocareanaf  $ordinedeblocareanaf
     * @return \Illuminate\Http\Response
     */
    public function edit(Ordinedeblocareanaf $ordinedeblocareanaf)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Ordinedeblocareanaf  $ordinedeblocareanaf
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Ordinedeblocareanaf $ordinedeblocareanaf)
    {
        DB::beginTransaction();
        try{
        $ordinedeblocareanaf->update([
    	  "nr_ordin"=>$request->nr_ordin,
        "data_ordin"=>$request->data_ordin?dateFormatStocare($request->data_ordin):null,
    	  "suspect"=>$request->suspect,
    	  "date_de_identificare"=>$request->date_de_identificare,
    	  "bunuri_blocate"=>$request->bunuri_blocate,
    	  "ordin_de_revocare"=>$request->ordin_de_revocare,
        "data_revocarii"=>$request->data_revocarii?dateFormatStocare($request->data_revocarii):null,
    	  "institutia"=>$request->institutia,
        ]);
       // event(new OrdinedeblocareanafUpdated());
         DB::commit();
        return $ordinedeblocareanaf;
           
        } catch (\Exception $e) {
            DB::rollback();
            Mail::to("stefan.voinea@gmail.com")
            ->send(new AlertaEroareEmail("UPDATE ORDINE DE BLOCARE ANAF",$e->getMessage(),$e,Auth::user()));
            return response()->json(['message' => $e->getMessage()], 500);
        }  
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Ordinedeblocareanaf  $ordinedeblocareanaf
     * @return \Illuminate\Http\Response
     */
    public function destroy(Ordinedeblocareanaf $ordinedeblocareanaf)
    {
        try{
        $ordinedeblocareanaf->delete();
      //  event(new OrdinedeblocareanafUpdated());
         } catch (\Exception $e) {
            Mail::to("stefan.voinea@gmail.com")
            ->send(new AlertaEroareEmail("STERGE ORDINE DE BLOCARE ANAF",$e->getMessage(),$e,Auth::user()));
            return response()->json(['message' => $e->getMessage()], 500);
        }  
    }
}