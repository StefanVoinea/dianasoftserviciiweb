<?php

namespace App\Http\Controllers\Api;

use App\Models\CompanyUser;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CompanyUserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $companyuser= CompanyUser::get();
                              //::where("user_id",auth()->user()->id)
                                
        return json_encode($companyuser);
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
    	  "company_id"=>["required"],]);


         return CompanyUser::create([
    	  "company_id"=>$request->company_id,
    	  "user_id"=>$request->user_id,           
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\CompanyUser  $companyuser
     * @return \Illuminate\Http\Response
     */
    public function show(CompanyUser $companyuser)
    {
        $resp= CompanyUser::where("id",$companyuser->id)
        											->get()->first();
                                
        return json_encode($resp);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\CompanyUser  $companyuser
     * @return \Illuminate\Http\Response
     */
    public function edit(CompanyUser $companyuser)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\CompanyUser  $companyuser
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, CompanyUser $companyuser)
    {
        $companyuser->update([
    	  "company_id"=>$request->company_id,
    	  "user_id"=>$request->user_id,
        ]);
        return $companyuser;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\CompanyUser  $companyuser
     * @return \Illuminate\Http\Response
     */
    public function destroy(CompanyUser $companyuser)
    {
        $companyuser->delete();

    }
}