<?php

namespace App\Http\Controllers\Api;

use App\Models\Gestiune_User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class Gestiune_UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $gestiune_user= Gestiune_User::where("company_id",session("company_id"))
                              ->where("user_id",auth()->user()->id)
                              ->where("isactive","true")
                              ->get();
                                
        return json_encode($gestiune_user);
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
    	  "gestiune_id"=>["required"],]);


         return Gestiune_User::create([
    	  "gestiune_id"=>$request->gestiune_id,
    	  "user_id"=>$request->user_id,
    	  "company_id"=>$request->company_id,           
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Gestiune_User  $gestiune_user
     * @return \Illuminate\Http\Response
     */
    public function show(Gestiune_User $gestiune_user)
    {
        $resp= Gestiune_User::where("id",$gestiune_user->id)
        											->get()->first();
                                
        return json_encode($resp);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Gestiune_User  $gestiune_user
     * @return \Illuminate\Http\Response
     */
    public function edit(Gestiune_User $gestiune_user)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Gestiune_User  $gestiune_user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Gestiune_User $gestiune_user)
    {
        $gestiune_user->update([
    	  "gestiune_id"=>$request->gestiune_id,
    	  "user_id"=>$request->user_id,
    	  "company_id"=>$request->company_id,
        ]);
        return $gestiune_user;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Gestiune_User  $gestiune_user
     * @return \Illuminate\Http\Response
     */
    public function destroy(Gestiune_User $gestiune_user)
    {
        $gestiune_user->delete();

    }
}