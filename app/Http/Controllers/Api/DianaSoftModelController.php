<?php

namespace App\Http\Controllers\Api;

use App\Models\DianaSoftModel;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class DianaSoftModelController extends Controller
{
    public function importFileModel(Request $request){
        $fileName = "model_".time();
        $fileExt = $request->file->getClientOriginalExtension();
        $file = $fileName.".".$fileExt;
        $request->file->move(public_path("upload"), $file);
        $path = "/".$file;
            ob_end_clean(); 
            ob_start(); 
        $contents = file_get_contents(public_path("upload/". $file));
      //  Log::info(json_decode(trim($contents))->table_name);
        return $contents;

    }
public function indexPaginat(Request $request)
    {
          $dianasoftmodel= DianaSoftModel::select('*')
                                           ->with(['dianasoftfields','dianasoftrelationships'])   ;
          
          $dianasoftmodel=filterRequest($dianasoftmodel,$request->searchModel,$request->cautareDupa,$request->sortModel,$request->filterModel);
          $dianasoftmodel= $dianasoftmodel->orderBy('id','desc')
                                          ->paginate($request->pageLength,
                                                      ['page'=>$request->page]);
          
                                //::where("user_id",auth()->user()->id)
                                  
          return json_encode($dianasoftmodel);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $dianasoftmodel= DianaSoftModel::get();
                              //::where("user_id",auth()->user()->id)
                                
        return json_encode($dianasoftmodel->load(['dianasoftfields','dianasoftrelationships']));
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
    	  "model_name"=>[required],
    	  "table_name"=>[required],
    	  "model_type"=>[required],
    	  "display_name"=>[required],]);


         return DianaSoftModel::create([
    	  "model_name"=>$request->model_name,
    	  "table_name"=>$request->table_name,
    	  "model_type"=>$request->model_type,
    	  "display_name"=>$request->display_name,
    	  "master_model_name"=>$request->master_model_name,
    	  "detail_model_name"=>$request->detail_model_name,           
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\DianaSoftModel  $dianasoftmodel
     * @return \Illuminate\Http\Response
     */
    public function show(DianaSoftModel $dianasoftmodel)
    {
        $resp= DianaSoftModel::where("id",$dianasoftmodel->id)
        											->get()->first();
                                
        return json_encode($resp);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\DianaSoftModel  $dianasoftmodel
     * @return \Illuminate\Http\Response
     */
    public function edit(DianaSoftModel $dianasoftmodel)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\DianaSoftModel  $dianasoftmodel
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, DianaSoftModel $dianasoftmodel)
    {
        $dianasoftmodel->update([
    	  "model_name"=>$request->model_name,
    	  "table_name"=>$request->table_name,
    	  "model_type"=>$request->model_type,
    	  "display_name"=>$request->display_name,
    	  "master_model_name"=>$request->master_model_name,
    	  "detail_model_name"=>$request->detail_model_name,
        ]);
        return $dianasoftmodel;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\DianaSoftModel  $dianasoftmodel
     * @return \Illuminate\Http\Response
     */
    public function destroy(DianaSoftModel $dianasoftmodel)
    {
        $appPath= env('DEVELOPER_PANEL_APP_PATH');
        $tableName=$dianasoftmodel->table_name;
        $modelName=$dianasoftmodel->model_name;
        
        foreach ($dianasoftmodel->dianasoftfields as $camp) {
            $camp->delete();
        };
        
        foreach ($dianasoftmodel->dianasoftrelationships as $relation) {
            $relation->delete();
        };
        $dianasoftmodel->delete();

        
        // $filename="\database\migrations\\".Carbon::now()->format('Y_m_d_his')."_create_".strtolower($tableName)."_table.php";
         
        //  File::delete($appPath.$filename);

         $filename="\app\\".$modelName.".php";
        
         if (File::exists($appPath.$filename))
         {
           File::delete($appPath.$filename);
         }
         $filename="\app\Http\Controllers\Api\\".$modelName."Controller.php";
         
         
          if (File::exists($appPath.$filename))
         {
           File::delete($appPath.$filename);
         }
          $filename="\\routes\api_routes\\".strtolower($modelName)."_routes.php";
        
          if (File::exists($appPath.$filename))
         {
           File::delete($appPath.$filename);
         }

          $filename="\\resources\\js\\src\\views\\app_pages\\".$modelName.".vue";
          
          if (File::exists($appPath.$filename))
         {
           File::delete($appPath.$filename);
         }
           $filename="\\database\\factories\\".$modelName."Factory.php";
          
          if (File::exists($appPath.$filename))
         {
           File::delete($appPath.$filename);
         }

         $filename="\\resources\\js\\src\acl\details\\add".$modelName.".js";
         if (File::exists($appPath.$filename))
         {
           File::delete($appPath.$filename);
         }
         $filename="\\resources\\js\\src\acl\details\\edit".$modelName.".js";
         if (File::exists($appPath.$filename))
         {
           File::delete($appPath.$filename);
         }
         $filename="\\resources\\js\\src\acl\details\\delete".$modelName.".js";
         if (File::exists($appPath.$filename))
         {
           File::delete($appPath.$filename);
         }
         $filename="\\resources\\js\\src\acl\details\\import".$modelName.".js";
         if (File::exists($appPath.$filename))
         {
           File::delete($appPath.$filename);
         }
         $filename="\\resources\\js\\src\acl\details\\export".$modelName.".js";
         if (File::exists($appPath.$filename))
         {
           File::delete($appPath.$filename);
         }
         $filename="\app\\Imports\\".$modelName."Import.php";
         if (File::exists($appPath.$filename))
         {
           File::delete($appPath.$filename);
         }
         $filename="\app\\Exports\\".$modelName."Export.php";
         if (File::exists($appPath.$filename))
         {
           File::delete($appPath.$filename);
         }
    }
}