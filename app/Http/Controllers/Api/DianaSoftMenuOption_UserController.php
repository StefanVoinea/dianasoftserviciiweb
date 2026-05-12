<?php

namespace App\Http\Controllers\Api;

use App\Models\DianaSoftMenuOption_User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DianaSoftMenuOption_UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $dianasoftmenuoption_user= DianaSoftMenuOption_User::get();
                              //::where("user_id",auth()->user()->id)
                                
        return json_encode($dianasoftmenuoption_user);
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
    	  "dianasoftmenuoption_id"=>["required"],
    	  "user_id"=>["required"],
    	  "isactive"=>["required"]
            ]);


         return DianaSoftMenuOption_User::create([
    	  "dianasoftmenuoption_id"=>$request->dianasoftmenuoption_id,
    	  "user_id"=>$request->user_id,
    	  "isactive"=>$request->isactive,  
          "company_id"=>session('company_id')         
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\DianaSoftMenuOption_User  $dianasoftmenuoption_user
     * @return \Illuminate\Http\Response
     */
    public function show(DianaSoftMenuOption_User $dianasoftmenuoption_user)
    {
        $resp= DianaSoftMenuOption_User::where("id",$dianasoftmenuoption_user->id)
        											->get()->first();
                                
        return json_encode($resp);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\DianaSoftMenuOption_User  $dianasoftmenuoption_user
     * @return \Illuminate\Http\Response
     */
    public function edit(DianaSoftMenuOption_User $dianasoftmenuoption_user)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\DianaSoftMenuOption_User  $dianasoftmenuoption_user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, DianaSoftMenuOption_User $dianasoftmenuoption_user)
    {
        $dianasoftmenuoption_user->update([
    	  "dianasoftmenuoption_id"=>$request->dianasoftmenuoption_id,
    	  "user_id"=>$request->user_id,
    	  "isactive"=>$request->isactive,
        ]);
        return $dianasoftmenuoption_user;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\DianaSoftMenuOption_User  $dianasoftmenuoption_user
     * @return \Illuminate\Http\Response
     */
    public function destroy(DianaSoftMenuOption_User $dianasoftmenuoption_user)
    {
        $dianasoftmenuoption_user->delete();

    }
}