<?php

namespace App\Http\Controllers\Api;

use App\Models\Nomalerte;
// use App\Events\NomalerteUpdated;
use App\Models\Exports\NomalerteExport;
use App\Models\Imports\NomalerteImport;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class NomalerteController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function indexPaginat(Request $request)
    {
          $records= Nomalerte::select('*')->where("company_id",session("company_id"));
          $records=filterRequest($records,$request->searchModel,$request->cautareDupa,$request->sortModel,$request->filterModel);
          $records= $records->orderBy('id','desc')
                                          ->paginate($request->pageLength,
                                                      ['page'=>$request->page]);
          
                                //::where("user_id",auth()->user()->id)
                                  
          return json_encode($records);
    }
     public function index()
    {
          $nomalerte= Nomalerte::where("company_id",session("company_id"))->get();
          return json_encode($nomalerte);
    }
    public function export() 
        {
             
            ob_end_clean(); 
            ob_start(); 
             $company_id=session("company_id");
            return Excel::download((new NomalerteExport)->forCompany($company_id),"nomalerte.xls");


        }

    public function import(Request $request) 
        {
          $fileName = "nomalerte_".time().".".$request->file->getClientOriginalExtension();
          $request->file->move(public_path("upload"), $fileName);
          
          Excel::import(new NomalerteImport, public_path("upload")."/".$fileName);

          
            $nomalerte= Nomalerte::where("company_id",session("company_id"))
                                                     ->paginate($request->pageLength,['page'=>$request->page]);
         
            return json_encode($nomalerte);
             
        
           
         }
        
    public function storeExcel() 
        {
            ob_end_clean(); 
            ob_start(); 
            $company_id=session("company_id");
            
            Excel::store((new NomalerteExport)->forCompany($company_id), "nomalerte.xls","public",null,[
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
       
        // event(new NomalerteUpdated());
         $denumire= Nomalerte::create([
    	  "company_id"=>session("company_id"),
    	  "denumire"=>$request->denumire,           
        ]);
        return $denumire->paginate($request->pageLength,['page'=>$request->page]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Nomalerte  $nomalerte
     * @return \Illuminate\Http\Response
     */
    public function show(Nomalerte $nomalerte)
    {
        $resp= Nomalerte::where("id",$nomalerte->id)
                               ->where("company_id",session("company_id"))
        					   ->get()->first();
                                
        return json_encode($resp);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Nomalerte  $nomalerte
     * @return \Illuminate\Http\Response
     */
    public function edit(Nomalerte $nomalerte)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Nomalerte  $nomalerte
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Nomalerte $nomalerte)
    {
        $nomalerte->update([
    	  "denumire"=>$request->denumire,
        ]);
       // event(new NomalerteUpdated());
        return $nomalerte;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Nomalerte  $nomalerte
     * @return \Illuminate\Http\Response
     */
    public function destroy(Nomalerte $nomalerte)
    {
        $nomalerte->delete();
      //  event(new NomalerteUpdated());

    }
}