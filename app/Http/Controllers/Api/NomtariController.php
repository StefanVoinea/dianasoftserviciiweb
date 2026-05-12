<?php

namespace App\Http\Controllers\Api;

use App\Models\Nomtari;
// use App\Events\NomtariUpdated;
use App\Models\Exports\NomtariExport;
//use App\Models\Imports\NomtariImport;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class NomtariController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function indexPaginat(Request $request)
    {
          $records= Nomtari::select('*')->where("company_id",session("company_id"));
          $records=filterRequest($records,$request->searchModel,$request->cautareDupa,$request->sortModel,$request->filterModel);
        $records=  $records->orderBy('id','desc');
        $records=  $records->paginate($request->pageLength,
                                                                    ["page"=>$request->page]);
        
                              //::where("user_id",auth()->user()->id)
                                
        return json_encode($records);
    }
     public function index()
    {
          $nomtari= Nomtari::where("company_id",session("company_id"))->get();
          return json_encode($nomtari);
    }
    public function export() 
        {
             
            ob_end_clean(); 
            ob_start(); 
             $company_id=session("company_id");
            return Excel::download((new NomtariExport)->forCompany($company_id),"nomtari.xls");


        }

    public function import(Request $request) 
        {
          $fileName = "nomtari_".time().".".$request->file->getClientOriginalExtension();
          $request->file->move(public_path("upload"), $fileName);
          
          Excel::import(new NomtariImport, public_path("upload")."/".$fileName);

          
            $nomtari= Nomtari::where("company_id",session("company_id"))
                                                     ->paginate($request->pageLength,['page'=>$request->page]);
         
            return json_encode($nomtari);
             
        
           
         }
        
    public function storeExcel() 
        {
            ob_end_clean(); 
            ob_start(); 
            $company_id=session("company_id");
            
            Excel::store((new NomtariExport)->forCompany($company_id), "nomtari.xls","public",null,[
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

        // event(new NomtariUpdated());
         $denumire= Nomtari::create([
        "company_id"=>session("company_id"),
        
    	  "cod"=>$request->cod,
    	  "denumire"=>$request->denumire,           
        ]);
        return $denumire;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Nomtari  $nomtari
     * @return \Illuminate\Http\Response
     */
    public function show(Nomtari $nomtari)
    {
        $resp= Nomtari::where("id",$nomtari->id)
                               ->where("company_id",session("company_id"))
        					   ->get()->first();
                                
        return json_encode($resp);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Nomtari  $nomtari
     * @return \Illuminate\Http\Response
     */
    public function edit(Nomtari $nomtari)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Nomtari  $nomtari
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Nomtari $nomtari)
    {
        $nomtari->update([
    	  "cod"=>$request->cod,
    	  "denumire"=>$request->denumire,
        ]);
       // event(new NomtariUpdated());
        return $nomtari;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Nomtari  $nomtari
     * @return \Illuminate\Http\Response
     */
    public function destroy(Nomtari $nomtari)
    {
        $nomtari->delete();
      //  event(new NomtariUpdated());

    }
}