<?php

namespace App\Http\Controllers\Api;

use App\Models\Nombanci;
// use App\Events\NombanciUpdated;
use App\Exports\NombanciExport;
use App\Imports\NombanciImport;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class NombanciController extends Controller
{
    public function indexPaginat(Request $request)
    {
         $records= Nombanci::select('*');
          
          $records=filterRequest($records,$request->searchModel,$request->cautareDupa,$request->sortModel,$request->filterModel);
          $records= $records->orderBy('id','desc')
                                          ->paginate($request->pageLength,
                                                      ['page'=>$request->page]);
          
                                //::where("user_id",auth()->user()->id)
                                  
          return json_encode($records);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
          $nombanci= Nombanci::get();
        // $nombanci=filterRequest($nombanci,$request->searchModel,$request->cautareDupa,$request->sortModel,$request->filterModel);
        // $nombanci=  $nombanci->paginate($request->pageLength,['page'=>$request->page]);
        
                              //::where("user_id",auth()->user()->id)
                                
        return json_encode($nombanci);
    }
    public function export() 
        {
             
            ob_end_clean(); 
            ob_start(); 
             $company_id=session("company_id");
            return Excel::download((new NombanciExport)->forCompany($company_id),"nombanci.xls");


        }

    public function import(Request $request) 
        {
          $fileName = "nombanci_".time().".".$request->file->getClientOriginalExtension();
          $request->file->move(public_path("upload"), $fileName);
          
          Excel::import(new NombanciImport, public_path("upload")."/".$fileName);

          
            $nombanci= Nombanci::paginate($request->pageLength,['page'=>$request->page]);
         
            return json_encode($nombanci);
             
        
           
         }
        
    public function storeExcel() 
        {
            ob_end_clean(); 
            ob_start(); 
            $company_id=session("company_id");
            
            Excel::store((new NombanciExport)->forCompany($company_id), "nombanci.xls","public",null,[
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
    	  "denumire"=>["required"],]);

        // event(new NombanciUpdated());
         return Nombanci::create([
    	  "denumire"=>$request->denumire,
    	  "cod"=>$request->cod,           
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Nombanci  $nombanci
     * @return \Illuminate\Http\Response
     */
    public function show(Nombanci $nombanci)
    {
        $resp= Nombanci::where("id",$nombanci->id)
        											->get()->first();
                                
        return json_encode($resp);
    }
    public function verifica()
    {
        $resp= Nombanci::whereIn("cod",$request->cod_banca)
                                                    ->get()->first();
                                
        return json_encode($resp);
    }
    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Nombanci  $nombanci
     * @return \Illuminate\Http\Response
     */
    public function edit(Nombanci $nombanci)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Nombanci  $nombanci
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Nombanci $nombanci)
    {
        $nombanci->update([
    	  "denumire"=>$request->denumire,
    	  "cod"=>$request->cod,
        ]);
       // event(new NombanciUpdated());
        return $nombanci;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Nombanci  $nombanci
     * @return \Illuminate\Http\Response
     */
    public function destroy(Nombanci $nombanci)
    {
        $nombanci->delete();
      //  event(new NombanciUpdated());

    }
}