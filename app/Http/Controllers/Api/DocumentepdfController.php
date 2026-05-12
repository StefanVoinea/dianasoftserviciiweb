<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Documentepdf;
use App\Models\Exports\DocumentepdfExport;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\AlertaEroareEmail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

   
class DocumentepdfController extends Controller
{

 public function abrogare(Request $request)
    {
        DB::beginTransaction();
        try{
        $documentpdf= Documentepdf::where("id",$request->id)->first();
        $documentpdf->update(["status"=>"abrogat"]);
         
       // event(new DocumentepdfUpdated());
         DB::commit();
        return $documentpdf->fresh();
           
        } catch (\Exception $e) {
            DB::rollback();
            $methodName = __FUNCTION__;
            $fileName = basename(__FILE__);
            Mail::to("stefan.voinea@gmail.com")
            ->send(new AlertaEroareEmail($methodName." ".$fileName,$e->getMessage(),$e,Auth::user()));
            return response()->json(['message' => $e->getMessage()], 500);
        }  
    }
    public function uploadfile(Request $request,Documentepdf $documentepdf)
    {
        try{
        ob_end_clean(); 
        ob_start(); 
        $company=Company::where("id",session("company_id"))->get()->first();
        $user=User::where("id",session("user_id"))->get()->first();
        log::info($request);
        $file=$request->file;
        $fileName = $file->getClientOriginalName()."_".time(); //.".".$file->getClientOriginalExtension();
        log::info($fileName);
        $file->move(storage_path('app/public/'.$company->slug.'/documentegenerale/'), $fileName.".pdf");
        $contents = file_get_contents(storage_path('app/public/'.$company->slug.'/documentegenerale/'. $fileName.".pdf"));
        $path = "/documentegenerale/".$fileName.".pdf";
        $upload = Storage::disk('dropbox')->put($path, $contents);
        $documentepdf->update([
                                     "fisier"=>$fileName,
                                     // "fisier_original"=>$file->getClientOriginalName(),
                                     // "tip_fisier"=>$file->getClientOriginalExtension(),
                                     
                                    ]);

        } catch (\Exception $e) {
            $methodName = __FUNCTION__;
            $fileName = basename(__FILE__);
            Mail::to("stefan.voinea@gmail.com")
            ->send(new AlertaEroareEmail($methodName." ".$fileName,$e->getMessage(),$e,Auth::user()));
            return response()->json(['message' => $e->getMessage()], 500);
        }          
     
        // Log::info(Storage::disk('dropbox')->allFiles('/'));
        // Log::info("UPLOAD:".$upload);
    }
     public function indexPaginatArhiva(Request $request)
    {
         try{
         $user=User::where("id",session("user_id"))->get()->first();
         $document=Documentepdf::where("id",$request->id)->get()->first();
         if($document){

         $records=Documentepdf::select('*')
                               ->where("denumire","like",$document->denumire."%")
                               ->orderBy("grupa")
                               ->orderBy("denumire") ;
         }else{
            $records=Documentepdf::select('*')
                               ->orderBy("grupa")
                               ->orderBy("denumire") ;
         }
        
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
            return response()->json(['message' => $e->getMessage()], 500);
        }  
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
        
         $records=Documentepdf::select('*')
                               ->where("status","in vigoare")
                               ->where(function($q) use ($user) {
                                    $q->where("acces","Toti")
                                      ->orWhere("acces","like","%".$user->name."%" )
                                      ->orWhere("acces","like","%".$user->departament."%" );
                               });
                               // ->orderBy("grupa")
                               // ->orderBy("denumire") ;

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
            return response()->json(['message' => $e->getMessage()], 500);
        }  
    }
     public function index()
    {
        try{
          $documentepdf= Documentepdf::where("company_id",session("company_id"))->get();
          return json_encode($documentepdf);

        } catch (\Exception $e) {
            $methodName = __FUNCTION__;
            $fileName = basename(__FILE__);
            Mail::to("stefan.voinea@gmail.com")
            ->send(new AlertaEroareEmail($methodName." ".$fileName,$e->getMessage(),$e,Auth::user()));
            return response()->json(['message' => $e->getMessage()], 500);
        }  
    }
    public function export() 
        {
             
            ob_end_clean(); 
            ob_start(); 
             $company_id=session("company_id");
            return Excel::download((new DocumentepdfExport)->forCompany($company_id),"documentepdf.xls");


        }

    public function import(Request $request) 
        {
          $fileName = "documentepdf_".time().".".$request->file->getClientOriginalExtension();
          $request->file->move(public_path("upload"), $fileName);
          
          Excel::import(new DocumentepdfImport, public_path("upload")."/".$fileName);

          
            $documentepdf= Documentepdf::where("company_id",session("company_id"))
                                                     ->paginate($request->pageLength,['page'=>$request->page]);
         
            return json_encode($documentepdf);
             
        
           
         }
        
    public function storeExcel() 
        {
            ob_end_clean(); 
            ob_start(); 
            $company_id=session("company_id");
            
            Excel::store((new DocumentepdfExport)->forCompany($company_id), "documentepdf.xls","public",null,[
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

        // event(new DocumentepdfUpdated());
        DB::beginTransaction();

        try{
               $documentpdf= Documentepdf::create([
                    "company_id"=>session("company_id"),
                    "grupa"=>$request->grupa,
    	            "denumire"=>$request->denumire,
    	            //"descriere"=>$request->descriere,
    	            "data"=>$request->data?dateFormatStocare($request->data):null,
    	            "acces"=>$request->acces,           
                    "status"=>"in vigoare", 
                    ]);
         DB::commit();
        return $documentpdf;
           
        } catch (\Exception $e) {
            DB::rollback();
            $methodName = __FUNCTION__;
            $fileName = basename(__FILE__);
            Mail::to("stefan.voinea@gmail.com")
            ->send(new AlertaEroareEmail($methodName." ".$fileName,$e->getMessage(),$e,Auth::user()));
            return response()->json(['message' => $e->getMessage()], 500);
        }  
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Documentepdf  $documentepdf
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    { 
        try{
        $company=Company::where("id",session("company_id"))->get()->first(); 
        $numefis=storage_path('app/public/'.$company->slug.'/documentegenerale/'.$request->fisier.".pdf");
        ob_end_clean(); 
        ob_start();                        
        $headers = array(
                  'Content-Type: application/pdf',
                );

           
            return Response::download($numefis, $request->denumire.'.pdf',$headers);
         } catch (\Exception $e) {
            $methodName = __FUNCTION__;
            $fileName = basename(__FILE__);
            Mail::to("stefan.voinea@gmail.com")
            ->send(new AlertaEroareEmail($methodName." ".$fileName,$e->getMessage(),$e,Auth::user()));
            return response()->json(['message' => $e->getMessage()], 500);
        }      
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Documentepdf  $documentepdf
     * @return \Illuminate\Http\Response
     */
    public function edit(Documentepdf $documentepdf)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Documentepdf  $documentepdf
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Documentepdf $documentepdf)
    {
        DB::beginTransaction();
        try{
        $documentepdf->update([
    	  "grupa"=>$request->grupa,
    	  "denumire"=>$request->denumire." (versiune anterioara)",
    	 // "descriere"=>$request->descriere,
    	  //"fisier"=>$request->fisier,
          //"data"=>$request->data?dateFormatStocare($request->data):null,
    	  //"acces"=>$request->acces,
          "status"=>"abrogat",
        ]);
         $documentpdf= Documentepdf::create([
                                                "company_id"=>session("company_id"),
                                                "grupa"=>$request->grupa,
                                                "denumire"=>$request->denumire,
                                                //"descriere"=>$request->descriere,
                                                "data"=>$request->data?dateFormatStocare($request->data):null,
                                                "acces"=>$request->acces,           
                                                "status"=>"in vigoare", 
                                                ]);
       // event(new DocumentepdfUpdated());
         DB::commit();
        return $documentpdf;
           
        } catch (\Exception $e) {
            DB::rollback();
            $methodName = __FUNCTION__;
            $fileName = basename(__FILE__);
            Mail::to("stefan.voinea@gmail.com")
            ->send(new AlertaEroareEmail($methodName." ".$fileName,$e->getMessage(),$e,Auth::user()));
            return response()->json(['message' => $e->getMessage()], 500);
        }  
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Documentepdf  $documentepdf
     * @return \Illuminate\Http\Response
     */
    public function destroy(Documentepdf $documentepdf)
    {
        try{
        $documentepdf->delete();
      //  event(new DocumentepdfUpdated());
         } catch (\Exception $e) {
            $methodName = __FUNCTION__;
            $fileName = basename(__FILE__);
            Mail::to("stefan.voinea@gmail.com")
            ->send(new AlertaEroareEmail($methodName." ".$fileName,$e->getMessage(),$e,Auth::user()));
            return response()->json(['message' => $e->getMessage()], 500);
        }  
    }
}