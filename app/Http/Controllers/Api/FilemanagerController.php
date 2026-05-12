<?php

namespace App\Http\Controllers\Api;

use App\Models\Filemanager;
// use App\Events\FilemanagerUpdated;
use App\Models\Exports\FilemanagerExport;
//use App\Models\Imports\FilemanagerImport;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\SablonExport;
use App\Models\Capitalsocial;
use App\Models\Company;
use App\Models\DianaSoftField;
use App\Models\DianaSoftModel;
use App\Models\Gestiune;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpWord\Element\Table;
use PhpOffice\PhpWord\SimpleType\TblWidth;
use PhpOffice\PhpWord\TemplateProcessor;
use Illuminate\Support\Facades\Mail;
use App\Mail\AlertaEroareEmail;

class FilemanagerController extends Controller
{
     public function viewfile(Request $request,Filemanager $filemanager)
    {
        try{
        $company=Company::where("id",session("company_id"))->get()->first();
        ob_end_clean(); 
        ob_start(); 
        return Response::download(storage_path('/app/public/'.$company->slug.'/filemanager/'.$filemanager->grupa.'/'.$filemanager->fisier));
    } catch (\Exception $e) {
            $methodName = __FUNCTION__;
            $fileName = basename(__FILE__);
            Mail::to("stefan.voinea@gmail.com")
            ->send(new AlertaEroareEmail($methodName." ".$fileName,$e->getMessage(),$e,Auth::user()));
            return response()->json(['message' => $e->getMessage()], 500);
        }  
    }
    public function uploadfile(Request $request,Filemanager $filemanager)
    {
        try{
        ob_end_clean(); 
        ob_start(); 
        $company=Company::where("id",session("company_id"))->get()->first();
        $user=User::where("id",session("user_id"))->get()->first();
        $file=$request->file;
                $fileName = $file->getClientOriginalName()."_".time().".".$file->getClientOriginalExtension();
                $file->move(storage_path('app/public/'.$company->slug.'/filemanager/'.$filemanager->grupa.'/'), $fileName);
                $contents = file_get_contents(storage_path('app/public/'.$company->slug.'/filemanager/'.$filemanager->grupa.'/'. $fileName));
                $path = "/filemanager/".$filemanager->grupa."/".$fileName;
                $upload = Storage::disk('dropbox')->put($path, $contents);
                $filemanager->update([
                                     "fisier"=>$fileName,
                                     "fisier_original"=>$file->getClientOriginalName(),
                                     "tip_fisier"=>$file->getClientOriginalExtension(),
                                     
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
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function indexPaginat(Request $request)
    {
        //Log::info(substr($request->grupa,0,strpos($request->grupa,"?")));
        try{
        $records= Filemanager::select('*')
                                   ->where("grupa",substr($request->grupa,0,strpos($request->grupa,"?"))) 
                                    ->where("company_id",session("company_id"));
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
          $filemanager= Filemanager::where("company_id",session("company_id"))->get();
          return json_encode($filemanager);
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
            return Excel::download((new FilemanagerExport)->forCompany($company_id),"filemanager.xls");


        }

    public function import(Request $request) 
        {
          $fileName = "filemanager_".time().".".$request->file->getClientOriginalExtension();
          $request->file->move(public_path("upload"), $fileName);
          
          Excel::import(new FilemanagerImport, public_path("upload")."/".$fileName);

          
            $filemanager= Filemanager::where("company_id",session("company_id"))
                                                     ->paginate($request->pageLength,['page'=>$request->page]);
         
            return json_encode($filemanager);
             
        
           
         }
        
    public function storeExcel() 
        {
            ob_end_clean(); 
            ob_start(); 
            $company_id=session("company_id");
            
            Excel::store((new FilemanagerExport)->forCompany($company_id), "filemanager.xls","public",null,[
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

        // event(new FilemanagerUpdated());
        DB::beginTransaction();
        try{
                 $data_sfarsit= Filemanager::create([
        "company_id"=>session("company_id"),
        
    	  "gestiune_id"=>$request->gestiune_id,
    	  "grupa"=>$request->grupa,
    	  "denumire"=>$request->denumire,
        "data_ultimei_revizii"=>$request->data_ultimei_revizii?dateFormatStocare($request->data_ultimei_revizii):null,
    	  "status"=>$request->status,
    	  "obs"=>$request->obs,
    	  "fisier"=>$request->fisier,
    	  "fisier_original"=>$request->fisier_original,
    	  "tip_fisier"=>$request->tip_fisier,
        "data_inceput"=>$request->data_inceput?dateFormatStocare($request->data_inceput):null,
        "data_sfarsit"=>$request->data_sfarsit?dateFormatStocare($request->data_sfarsit):null,           
        ]);
            DB::commit();
        return $data_sfarsit;
           
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
     * @param  \App\Models\Filemanager  $filemanager
     * @return \Illuminate\Http\Response
     */
    public function show(Filemanager $filemanager)
    {
       try{
        $resp= Filemanager::where("id",$filemanager->id)
                               ->where("company_id",session("company_id"))
        					   ->get()->first();
                                
        return json_encode($resp);
           
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
     * @param  \App\Filemanager  $filemanager
     * @return \Illuminate\Http\Response
     */
    public function edit(Filemanager $filemanager)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Filemanager  $filemanager
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Filemanager $filemanager)
    {
        DB::beginTransaction();
        try{
        $filemanager->update([
    	  "gestiune_id"=>$request->gestiune_id,
    	  "grupa"=>$request->grupa,
    	  "denumire"=>$request->denumire,
        "data_ultimei_revizii"=>$request->data_ultimei_revizii?dateFormatStocare($request->data_ultimei_revizii):null,
    	  "status"=>$request->status,
    	  "obs"=>$request->obs,
    	  "fisier"=>$request->fisier,
    	  "fisier_original"=>$request->fisier_original,
    	  "tip_fisier"=>$request->tip_fisier,
        "data_inceput"=>$request->data_inceput?dateFormatStocare($request->data_inceput):null,
        "data_sfarsit"=>$request->data_sfarsit?dateFormatStocare($request->data_sfarsit):null,
        ]);
       // event(new FilemanagerUpdated());
            DB::commit();
        return $filemanager;
           
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
     * @param  \App\Filemanager  $filemanager
     * @return \Illuminate\Http\Response
     */
    public function destroy(Filemanager $filemanager)
    {
        try{
        $filemanager->delete();
      //  event(new FilemanagerUpdated());

        } catch (\Exception $e) {
            $methodName = __FUNCTION__;
            $fileName = basename(__FILE__);
            Mail::to("stefan.voinea@gmail.com")
            ->send(new AlertaEroareEmail($methodName." ".$fileName,$e->getMessage(),$e,Auth::user()));
            return response()->json(['message' => $e->getMessage()], 500);
        }  
    }
}
