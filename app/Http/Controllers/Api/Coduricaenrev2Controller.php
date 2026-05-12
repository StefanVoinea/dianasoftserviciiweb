<?php

namespace App\Http\Controllers\Api;

use App\Models\Coduricaenrev2;
// use App\Events\Coduricaenrev2Updated;
use App\Models\Exports\Coduricaenrev2Export;
//use App\Models\Imports\Coduricaenrev2Import;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Mail;
use App\Mail\AlertaEroareEmail;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class Coduricaenrev2Controller extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function indexPaginat(Request $request)
    {
          try{
          $records= Coduricaenrev2::select('*')->where("company_id",session("company_id"));
          $records=filterRequest($records,$request->searchModel,$request->cautareDupa,$request->sortModel,$request->filterModel);
        $records=  $records->orderBy('id','desc');
        $records=  $records->paginate($request->pageLength,
                                                                    ["page"=>$request->page]);
        
                              //::where("user_id",auth()->user()->id)
                                
        return json_encode($records);

      } catch (\Exception $e) {
        $methodName = __FUNCTION__;
          $fileName = basename(__FILE__);
        Mail::to("stefan.voinea@gmail.com")
        ->send(new AlertaEroareEmail($methodName." ".$fileName,$e->getMessage(),$e,Auth::user()));
        return response()->json(["message" => $e->getMessage()], 500);
      }  
    }
     public function index()
    {
      try{
          $coduricaenrev2= collect(DB::select(DB::raw("SELECT coduricaenrev2.cod_caen, coduricaenrev2.descriere, 
                                                      Max(corespondentacaen.cod_caen_rev3) AS   cod_caen_rev3
                      FROM coduricaenrev2 INNER JOIN corespondentacaen ON coduricaenrev2.cod_caen = corespondentacaen.cod_caen_rev2
                      GROUP BY coduricaenrev2.cod_caen, coduricaenrev2.descriere;")));

            //Coduricaenrev2::select("cod_caen","descriere")->where("company_id",session("company_id"))->get();
          return json_encode($coduricaenrev2);

      } catch (\Exception $e) {
        $methodName = __FUNCTION__;
        $fileName = basename(__FILE__);
        Mail::to("stefan.voinea@gmail.com")
        ->send(new AlertaEroareEmail($methodName." ".$fileName,$e->getMessage(),$e,Auth::user()));
        return response()->json(["message" => $e->getMessage()], 500);
      }      
    }
    public function export() 
        {
             
            ob_end_clean(); 
            ob_start(); 
             $company_id=session("company_id");
            return Excel::download((new Coduricaenrev2Export)->forCompany($company_id),"coduricaenrev2.xls");


        }

    public function import(Request $request) 
        {
          $fileName = "coduricaenrev2_".time().".".$request->file->getClientOriginalExtension();
          $request->file->move(public_path("upload"), $fileName);
          
          Excel::import(new Coduricaenrev2Import, public_path("upload")."/".$fileName);

          
            $coduricaenrev2= Coduricaenrev2::where("company_id",session("company_id"))
                                                     ->paginate($request->pageLength,['page'=>$request->page]);
         
            return json_encode($coduricaenrev2);
             
        
           
         }
        
    public function storeExcel() 
        {
            ob_end_clean(); 
            ob_start(); 
            $company_id=session("company_id");
            
            Excel::store((new Coduricaenrev2Export)->forCompany($company_id), "coduricaenrev2.xls","public",null,[
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

        // event(new Coduricaenrev2Updated());
      DB::beginTransaction();
      try{  

         $procent= Coduricaenrev2::create([
        "company_id"=>session("company_id"),
        
    	  "cod_caen"=>$request->cod_caen,
    	  "descriere"=>$request->descriere,
    	  "procent"=>$request->procent,           
        ]);
        DB::commit();
        return $procent;
        
      } catch (\Exception $e) {
        DB::rollback();
        $methodName = __FUNCTION__;
          $fileName = basename(__FILE__);
        Mail::to("stefan.voinea@gmail.com")
        ->send(new AlertaEroareEmail($methodName." ".$fileName,$e->getMessage(),$e,Auth::user()));
        return response()->json(["message" => $e->getMessage()], 500);
      }  
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Coduricaenrev2  $coduricaenrev2
     * @return \Illuminate\Http\Response
     */
    public function show(Coduricaenrev2 $coduricaenrev2)
    {
      try{
        $resp= Coduricaenrev2::where("id",$coduricaenrev2->id)
                               ->where("company_id",session("company_id"))
        					   ->get()->first();
                                
        return json_encode($resp);

      } catch (\Exception $e) {
        $methodName = __FUNCTION__;
          $fileName = basename(__FILE__);
        Mail::to("stefan.voinea@gmail.com")
        ->send(new AlertaEroareEmail($methodName." ".$fileName,$e->getMessage(),$e,Auth::user()));
        return response()->json(["message" => $e->getMessage()], 500);
      }  
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Coduricaenrev2  $coduricaenrev2
     * @return \Illuminate\Http\Response
     */
    public function edit(Coduricaenrev2 $coduricaenrev2)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Coduricaenrev2  $coduricaenrev2
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Coduricaenrev2 $coduricaenrev2)
    {
      DB::beginTransaction();
      try{
              $coduricaenrev2->update([
    	  "cod_caen"=>$request->cod_caen,
    	  "descriere"=>$request->descriere,
    	  "procent"=>$request->procent,
        ]);
       // event(new Coduricaenrev2Updated());
          DB::commit();
        return $coduricaenrev2;
        
      } catch (\Exception $e) {
        DB::rollback();
        $methodName = __FUNCTION__;
          $fileName = basename(__FILE__);
        Mail::to("stefan.voinea@gmail.com")
        ->send(new AlertaEroareEmail($methodName." ".$fileName,$e->getMessage(),$e,Auth::user()));
        return response()->json(["message" => $e->getMessage()], 500);
      }  
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Coduricaenrev2  $coduricaenrev2
     * @return \Illuminate\Http\Response
     */
    public function destroy(Coduricaenrev2 $coduricaenrev2)
    {
      DB::beginTransaction();
      try{
              $coduricaenrev2->delete();
      
      //  event(new Coduricaenrev2Updated());
        DB::commit();
        
      } catch (\Exception $e) {
        DB::rollback();
        $methodName = __FUNCTION__;
          $fileName = basename(__FILE__);
        Mail::to("stefan.voinea@gmail.com")
        ->send(new AlertaEroareEmail($methodName." ".$fileName,$e->getMessage(),$e,Auth::user()));
        return response()->json(["message" => $e->getMessage()], 500);
      }          
    }
}