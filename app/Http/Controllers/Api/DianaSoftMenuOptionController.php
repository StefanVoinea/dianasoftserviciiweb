<?php

namespace App\Http\Controllers\Api;

use App\Models\DianaSoftMenuOption;
use App\Models\DianaSoftMenuOption_User;
use App\Events\DianaSoftMenuOptionUpdated;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class DianaSoftMenuOptionController extends Controller
{
    public function indexPaginat(Request $request)
    {
          $dianasoftmenuoptions= DianaSoftMenuOption::select('*')   ;
          
          $dianasoftmenuoptions=filterRequest($dianasoftmenuoptions,$request->searchModel,$request->cautareDupa,$request->sortModel,$request->filterModel);
          $dianasoftmenuoptions= $dianasoftmenuoptions->orderBy('id','desc')
                                          ->paginate($request->pageLength,
                                                      ['page'=>$request->page]);
          
                                //::where("user_id",auth()->user()->id)
                                  
          return json_encode($dianasoftmenuoptions);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $dianasoftmenuoption= DianaSoftMenuOption::get();
                              //::where("user_id",auth()->user()->id)
                                
        return json_encode($dianasoftmenuoption);
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
    	  "name"=>["required"]
          ,]);


         $dianasoftmenuoption=DianaSoftMenuOption::create([
    	  "name"=>$request->name,
    	  "url"=>$request->url,
    	  "slug"=>$request->slug,
    	  "icon"=>$request->icon,
    	  "tag"=>$request->tag,
    	  "tagcolor"=>$request->tagcolor,
    	  "i18n"=>$request->i18n,
    	  "dropdown"=>$request->dropdown,
    	  "parent"=>$request->parent,
    	  "position1"=>$request->position1,
    	  "position2"=>$request->position2,
    	  "isdisabled"=>$request->isdisabled,           
        ]);
         
         $currentuser=$request->user();
         foreach ($currentuser->companies as $company) {
            $dianasoftmenuoption->users()->attach($currentuser->id,['isactive'=>true,
                                                        'company_id'=>$company->id]);
         };
         $allUsers=User::get();
         foreach ($allUsers as $user) {
            if($user->id != $request->user()->id )
            {
                foreach($user->companies as $company)
                {
                 $dianasoftmenuoption->users()->attach($user->id,['isactive'=>false,
                                                        'company_id'=>$company->id]);    
               }
            }
            
         
            
         }
         event(new DianaSoftMenuOptionUpdated());
         return $dianasoftmenuoption;

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\DianaSoftMenuOption  $dianasoftmenuoption
     * @return \Illuminate\Http\Response
     */
    public function show(DianaSoftMenuOption $dianasoftmenuoption)
    {
        $resp= DianaSoftMenuOption::where("id",$dianasoftmenuoption->id)
        											->get()->first();
                                
        return json_encode($resp);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\DianaSoftMenuOption  $dianasoftmenuoption
     * @return \Illuminate\Http\Response
     */
    public function edit(DianaSoftMenuOption $dianasoftmenuoption)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\DianaSoftMenuOption  $dianasoftmenuoption
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, DianaSoftMenuOption $dianasoftmenuoption)
    {
        $dianasoftmenuoption->update([
    	  "name"=>$request->name,
    	  "url"=>$request->url,
    	  "slug"=>$request->slug,
    	  "icon"=>$request->icon,
    	  "tag"=>$request->tag,
    	  "tagcolor"=>$request->tagcolor,
    	  "i18n"=>$request->i18n,
    	  "dropdown"=>$request->dropdown,
    	  "parent"=>$request->parent,
    	  "position1"=>$request->position1,
    	  "position2"=>$request->position2,
    	  "isdisabled"=>$request->isdisabled,
        ]);
        event(new DianaSoftMenuOptionUpdated());
        return $dianasoftmenuoption;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\DianaSoftMenuOption  $dianasoftmenuoption
     * @return \Illuminate\Http\Response
     */
    public function destroy(DianaSoftMenuOption $dianasoftmenuoption)
    {
         // foreach ($dianasoftmenuoption->users as $user) {

            // $company_id=session('company_id');
            DianaSoftMenuOption_User::where('dianasoftmenuoption_id',$dianasoftmenuoption->id)
                                      ->delete();       
           
        // }
       
        $dianasoftmenuoption->delete();
        event(new DianaSoftMenuOptionUpdated());

    }
}