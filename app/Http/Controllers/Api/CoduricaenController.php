<?php

namespace App\Http\Controllers\Api;

use App\Models\Coduricaen;
// use App\Events\CoduricaenUpdated;
use App\Models\Exports\CoduricaenExport;
//use App\Models\Imports\CoduricaenImport;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class CoduricaenController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function indexPaginat(Request $request)
    {
          $records= Coduricaen::select('*')->where("company_id",session("company_id"));
          $records=filterRequest($records,$request->searchModel,$request->cautareDupa,$request->sortModel,$request->filterModel);
        $records=  $records->orderBy('id','desc');
        $records=  $records->paginate($request->pageLength,
                                                                    ["page"=>$request->page]);
        
                              //::where("user_id",auth()->user()->id)
                                
        return json_encode($records);
    }
     public function index()
    {
          $coduricaen= Coduricaen::where("company_id",session("company_id"))->get();
          return json_encode($coduricaen);
    }
    public function export() 
        {
             
            ob_end_clean(); 
            ob_start(); 
             $company_id=session("company_id");
            return Excel::download((new CoduricaenExport)->forCompany($company_id),"coduricaen.xls");


        }

    public function import(Request $request) 
        {
          $fileName = "coduricaen_".time().".".$request->file->getClientOriginalExtension();
          $request->file->move(public_path("upload"), $fileName);
          
          Excel::import(new CoduricaenImport, public_path("upload")."/".$fileName);

          
            $coduricaen= Coduricaen::where("company_id",session("company_id"))
                                                     ->paginate($request->pageLength,['page'=>$request->page]);
         
            return json_encode($coduricaen);
             
        
           
         }
        
    public function storeExcel() 
        {
            ob_end_clean(); 
            ob_start(); 
            $company_id=session("company_id");
            
            Excel::store((new CoduricaenExport)->forCompany($company_id), "coduricaen.xls","public",null,[
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

        // event(new CoduricaenUpdated());
         $denumire= Coduricaen::create([
        "company_id"=>session("company_id"),
        
    	  "cod"=>$request->cod,
    	  "denumire"=>$request->denumire,           
        ]);
        return $denumire;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Coduricaen  $coduricaen
     * @return \Illuminate\Http\Response
     */
    public function show(Coduricaen $coduricaen)
    {
        $resp= Coduricaen::where("id",$coduricaen->id)
                               ->where("company_id",session("company_id"))
        					   ->get()->first();
                                
        return json_encode($resp);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Coduricaen  $coduricaen
     * @return \Illuminate\Http\Response
     */
    public function edit(Coduricaen $coduricaen)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Coduricaen  $coduricaen
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Coduricaen $coduricaen)
    {
        $coduricaen->update([
    	  "cod"=>$request->cod,
    	  "denumire"=>$request->denumire,
        ]);
       // event(new CoduricaenUpdated());
        return $coduricaen;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Coduricaen  $coduricaen
     * @return \Illuminate\Http\Response
     */
    public function destroy(Coduricaen $coduricaen)
    {
        $coduricaen->delete();
      //  event(new CoduricaenUpdated());

    }
}