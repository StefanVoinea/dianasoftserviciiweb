<?php

namespace App\Http\Controllers\Api;

use App\Models\Lastupdatetable;
// use App\Events\LastupdatetableUpdated;
use App\Models\Exports\LastupdatetableExport;
//use App\Models\Imports\LastupdatetableImport;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class LastupdatetableController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function indexPaginat(Request $request)
    {
          $records= Lastupdatetable::select('*')->where("company_id",session("company_id"));
          $records=filterRequest($records,$request->searchModel,$request->cautareDupa,$request->sortModel,$request->filterModel);
        $records=  $records->orderBy('id','desc');
        $records=  $records->paginate($request->pageLength,
                                                                    ["page"=>$request->page]);
        
                              //::where("user_id",auth()->user()->id)
                                
        return json_encode($records);
    }
     public function index()
    {
          $lastupdatetable= Lastupdatetable::where("company_id",session("company_id"))->get();
          return json_encode($lastupdatetable);
    }
    public function export() 
        {
             
            ob_end_clean(); 
            ob_start(); 
             $company_id=session("company_id");
            return Excel::download((new LastupdatetableExport)->forCompany($company_id),"lastupdatetable.xls");


        }

    public function import(Request $request) 
        {
          $fileName = "lastupdatetable_".time().".".$request->file->getClientOriginalExtension();
          $request->file->move(public_path("upload"), $fileName);
          
          Excel::import(new LastupdatetableImport, public_path("upload")."/".$fileName);

          
            $lastupdatetable= Lastupdatetable::where("company_id",session("company_id"))
                                                     ->paginate($request->pageLength,['page'=>$request->page]);
         
            return json_encode($lastupdatetable);
             
        
           
         }
        
    public function storeExcel() 
        {
            ob_end_clean(); 
            ob_start(); 
            $company_id=session("company_id");
            
            Excel::store((new LastupdatetableExport)->forCompany($company_id), "lastupdatetable.xls","public",null,[
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

        // event(new LastupdatetableUpdated());
         $data= Lastupdatetable::create([
        "company_id"=>session("company_id"),
        
    	  "tabel"=>$request->tabel,
        "data"=>$request->data?dateFormatStocare($request->data):null,           
        ]);
        return $data;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Lastupdatetable  $lastupdatetable
     * @return \Illuminate\Http\Response
     */
    public function show(Lastupdatetable $lastupdatetable)
    {
        $resp= Lastupdatetable::where("id",$lastupdatetable->id)
                               ->where("company_id",session("company_id"))
        					   ->get()->first();
                                
        return json_encode($resp);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Lastupdatetable  $lastupdatetable
     * @return \Illuminate\Http\Response
     */
    public function edit(Lastupdatetable $lastupdatetable)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Lastupdatetable  $lastupdatetable
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Lastupdatetable $lastupdatetable)
    {
        $lastupdatetable->update([
    	  "tabel"=>$request->tabel,
        "data"=>$request->data?dateFormatStocare($request->data):null,
        ]);
       // event(new LastupdatetableUpdated());
        return $lastupdatetable;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Lastupdatetable  $lastupdatetable
     * @return \Illuminate\Http\Response
     */
    public function destroy(Lastupdatetable $lastupdatetable)
    {
        $lastupdatetable->delete();
      //  event(new LastupdatetableUpdated());

    }
}