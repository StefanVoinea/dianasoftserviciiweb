<?php

namespace App\Http\Controllers\Api;

use App\Models\Interogareanaf;
// use App\Events\InterogareanafUpdated;
use App\Models\Exports\InterogareanafExport;
use App\Models\Imports\InterogareanafImport;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class InterogareanafController extends Controller
{
    public function indexPaginat(Request $request)
    {
         $records= Interogareanaf::select('*');
          
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
          $interogareanaf= Interogareanaf::select('*')->with(['company','user']);
        $interogareanaf=filterRequest($interogareanaf,$request->searchModel,$request->cautareDupa,$request->sortModel,$request->filterModel);
        $interogareanaf=  $interogareanaf->paginate($request->pageLength,['page'=>$request->page]);
        
                              //::where("user_id",auth()->user()->id)
                                
        return json_encode($interogareanaf);
    }
    public function export() 
        {
             
            ob_end_clean(); 
            ob_start(); 
             $company_id=session("company_id");
            return Excel::download((new InterogareanafExport)->forCompany($company_id),"interogareanaf.xls");


        }

    public function import(Request $request) 
        {
          $fileName = "interogareanaf_".time().".".$request->file->getClientOriginalExtension();
          $request->file->move(public_path("upload"), $fileName);
          
          Excel::import(new InterogareanafImport, public_path("upload")."/".$fileName);

          
            $interogareanaf= Interogareanaf::where("company_id",session("company_id"))
                                        ::paginate($request->pageLength,['page'=>$request->page]);
         
            return json_encode($interogareanaf);
             
        
           
         }
        
    public function storeExcel() 
        {
            ob_end_clean(); 
            ob_start(); 
            $company_id=session("company_id");
            
            Excel::store((new InterogareanafExport)->forCompany($company_id), "interogareanaf.xls","public",null,[
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
    	  "company_id"=>["required"],]);

        // event(new InterogareanafUpdated());
         return Interogareanaf::create([
    	  "company_id"=>$request->company_id,
    	  "user_id"=>$request->user_id,
    	  "data"=>$request->data,
    	  "cui"=>$request->cui,
    	  "raspuns"=>$request->raspuns,           
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Interogareanaf  $interogareanaf
     * @return \Illuminate\Http\Response
     */
    public function show(Interogareanaf $interogareanaf)
    {
        $resp= Interogareanaf::where("id",$interogareanaf->id)
        											->get()->first();
                                
        return json_encode($resp);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Interogareanaf  $interogareanaf
     * @return \Illuminate\Http\Response
     */
    public function edit(Interogareanaf $interogareanaf)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Interogareanaf  $interogareanaf
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Interogareanaf $interogareanaf)
    {
        $interogareanaf->update([
    	  "company_id"=>$request->company_id,
    	  "user_id"=>$request->user_id,
    	  "data"=>$request->data,
    	  "cui"=>$request->cui,
    	  "raspuns"=>$request->raspuns,
        ]);
       // event(new InterogareanafUpdated());
        return $interogareanaf;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Interogareanaf  $interogareanaf
     * @return \Illuminate\Http\Response
     */
    public function destroy(Interogareanaf $interogareanaf)
    {
        $interogareanaf->delete();
      //  event(new InterogareanafUpdated());

    }
}