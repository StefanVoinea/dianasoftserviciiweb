<?php

namespace App\Http\Controllers\Api;

use App\Models\Litigiiparti;
// use App\Events\LitigiipartiUpdated;
use App\Models\Exports\LitigiipartiExport;
//use App\Models\Imports\LitigiipartiImport;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class LitigiipartiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function indexPaginat(Request $request)
    {
          $records= Litigiiparti::select('*')->where("company_id",session("company_id"));
          $records=filterRequest($records,$request->searchModel,$request->cautareDupa,$request->sortModel,$request->filterModel);
        $records=  $records->orderBy('id','desc');
        $records=  $records->paginate($request->pageLength,
                                                                    ["page"=>$request->page]);
        
                              //::where("user_id",auth()->user()->id)
                                
        return json_encode($records);
    }
     public function index()
    {
          $litigiiparti= Litigiiparti::where("company_id",session("company_id"))->get();
          return json_encode($litigiiparti);
    }
    public function export() 
        {
             
            ob_end_clean(); 
            ob_start(); 
             $company_id=session("company_id");
            return Excel::download((new LitigiipartiExport)->forCompany($company_id),"litigiiparti.xls");


        }

    public function import(Request $request) 
        {
          $fileName = "litigiiparti_".time().".".$request->file->getClientOriginalExtension();
          $request->file->move(public_path("upload"), $fileName);
          
          Excel::import(new LitigiipartiImport, public_path("upload")."/".$fileName);

          
            $litigiiparti= Litigiiparti::where("company_id",session("company_id"))
                                                     ->paginate($request->pageLength,['page'=>$request->page]);
         
            return json_encode($litigiiparti);
             
        
           
         }
        
    public function storeExcel() 
        {
            ob_end_clean(); 
            ob_start(); 
            $company_id=session("company_id");
            
            Excel::store((new LitigiipartiExport)->forCompany($company_id), "litigiiparti.xls","public",null,[
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

        // event(new LitigiipartiUpdated());
         $calitate= Litigiiparti::create([
        "company_id"=>session("company_id"),
        
    	  "litigiu_id"=>$request->litigiu_id,
    	  "nume"=>$request->nume,
    	  "calitate"=>$request->calitate,           
        ]);
        return $calitate;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Litigiiparti  $litigiiparti
     * @return \Illuminate\Http\Response
     */
    public function show(Litigiiparti $litigiiparti)
    {
        $resp= Litigiiparti::where("id",$litigiiparti->id)
                               ->where("company_id",session("company_id"))
        					   ->get()->first();
                                
        return json_encode($resp);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Litigiiparti  $litigiiparti
     * @return \Illuminate\Http\Response
     */
    public function edit(Litigiiparti $litigiiparti)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Litigiiparti  $litigiiparti
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Litigiiparti $litigiiparti)
    {
        $litigiiparti->update([
    	  "litigiu_id"=>$request->litigiu_id,
    	  "nume"=>$request->nume,
    	  "calitate"=>$request->calitate,
        ]);
       // event(new LitigiipartiUpdated());
        return $litigiiparti;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Litigiiparti  $litigiiparti
     * @return \Illuminate\Http\Response
     */
    public function destroy(Litigiiparti $litigiiparti)
    {
        $litigiiparti->delete();
      //  event(new LitigiipartiUpdated());

    }
}