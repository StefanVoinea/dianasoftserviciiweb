<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DianaSoftMenuOption;
use App\Models\Permission;
use App\Models\Permission_User;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Log;

class PermissionController extends Controller
{
     public function indexPaginat(Request $request)
    {
          if(session("user_id")==1){
          $permission= Permission::select('*')->with(["dianasoftmenuoption"])   ;
          }else{
            $permission= Permission::select('*')->whereHas('dianasoftmenuoption', function($q)
                                                                    {
                                                                        $q->where('isdisabled', 0);

                                                                    });
          }
          $permission=filterRequest($permission,$request->searchModel,$request->cautareDupa,$request->sortModel,$request->filterModel);
          $permission= $permission->orderBy('id','desc')
                                          ->paginate($request->pageLength,
                                                      ['page'=>$request->page]);
          
                                //::where("user_id",auth()->user()->id)

          return json_encode($permission);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        
        $permission= Permission::get();
                              //::where("user_id",auth()->user()->id)
                                
        return json_encode($permission);
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
    	  "name"=>["required"],
    	  "display_name"=>["required"],]);


         $permission= Permission::create([
    	   "name"=>$request->name,
          "display_name"=>$request->display_name,
          "dianasoftmenuoption_id"=>$request->dianasoftmenuoption?$request->dianasoftmenuoption["id"]:null         
        ]);

         $currentuser=$request->user();
         $permission->users()->attach($currentuser->id,['isactive'=>true,
                                                        'company_id'=>$request->user()->companies->first()->id
                                                       ]);
         $allUsers=User::get();
         foreach ($allUsers as $user) {
            if($user->id != $request->user()->id )
            {
                foreach($user->companies as $company)
                {
                $permission->users()->attach($user->id,['isactive'=>false,
                                                        'company_id'=>$company->id  
                                                       ]);    
            }
            }
            
         
            
         }
         return $permission;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Permission  $permission
     * @return \Illuminate\Http\Response
     */
    public function show(Permission $permission)
    {
        $resp= Permission::where("id",$permission->id)
        											->get()->first();
                                
        return json_encode($resp);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Permission  $permission
     * @return \Illuminate\Http\Response
     */
    public function edit(Permission $permission)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Permission  $permission
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Permission $permission)
    {
      
        $permission->update([
    	  "name"=>$request->name,
    	  "display_name"=>$request->display_name,
          "dianasoftmenuoption_id"=>$request->dianasoftmenuoption?$request->dianasoftmenuoption["id"]:null 
        ]);
        return $permission;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Permission  $permission
     * @return \Illuminate\Http\Response
     */
    public function destroy(Permission $permission)
    {

         // foreach ($permission->users as $user) {

            // $company_id=session('company_id');
            Permission_User::where('permission_id',$permission->id)
                             ->delete();       
           
        // }
        $permission->delete();

    }
}