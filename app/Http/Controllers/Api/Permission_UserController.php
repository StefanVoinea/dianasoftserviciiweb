<?php

namespace App\Http\Controllers\Api;

use App\Models\Permission_User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class Permission_UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $permission_user= Permission_User::get();
                              //::where("user_id",auth()->user()->id)
                                
        return json_encode($permission_user);
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
    	  "permission_id"=>["required"],]);


         return Permission_User::create([
    	  "permission_id"=>$request->permission_id,
    	  "user_id"=>$request->user_id,
    	  "isactive"=>$request->isactive,           
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Permission_User  $permission_user
     * @return \Illuminate\Http\Response
     */
    public function show(Permission_User $permission_user)
    {
        $resp= Permission_User::where("id",$permission_user->id)
        											->get()->first();
                                
        return json_encode($resp);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Permission_User  $permission_user
     * @return \Illuminate\Http\Response
     */
    public function edit(Permission_User $permission_user)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Permission_User  $permission_user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Permission_User $permission_user)
    {
        $permission_user->update([
    	  "permission_id"=>$request->permission_id,
    	  "user_id"=>$request->user_id,
    	  "isactive"=>$request->isactive,
        ]);
        return $permission_user;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Permission_User  $permission_user
     * @return \Illuminate\Http\Response
     */
    public function destroy(Permission_User $permission_user)
    {
        $permission_user->delete();

    }
}