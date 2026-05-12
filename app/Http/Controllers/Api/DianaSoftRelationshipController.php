<?php

namespace App\Http\Controllers\Api;

use App\Models\DianaSoftRelationship;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DianaSoftRelationshipController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $dianasoftrelationship= DianaSoftRelationship::get();
                              //::where("user_id",auth()->user()->id)
                                
        return json_encode($dianasoftrelationship);
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
    	  "dianasoftmodel_id"=>[required],
    	  "name"=>[required],
    	  "type"=>[required],
    	  "model_name"=>[required],
    	  "foreign_key"=>[required],
    	  "local_key"=>[required],]);


         return DianaSoftRelationship::create([
    	  "dianasoftmodel_id"=>$request->dianasoftmodel_id,
    	  "name"=>$request->name,
    	  "type"=>$request->type,
    	  "model_name"=>$request->model_name,
    	  "foreign_key"=>$request->foreign_key,
    	  "local_key"=>$request->local_key,           
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\DianaSoftRelationship  $dianasoftrelationship
     * @return \Illuminate\Http\Response
     */
    public function show(DianaSoftRelationship $dianasoftrelationship)
    {
        $resp= DianaSoftRelationship::where("id",$dianasoftrelationship->id)
        											->get()->first();
                                
        return json_encode($resp);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\DianaSoftRelationship  $dianasoftrelationship
     * @return \Illuminate\Http\Response
     */
    public function edit(DianaSoftRelationship $dianasoftrelationship)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\DianaSoftRelationship  $dianasoftrelationship
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, DianaSoftRelationship $dianasoftrelationship)
    {
        $dianasoftrelationship->update([
    	  "dianasoftmodel_id"=>$request->dianasoftmodel_id,
    	  "name"=>$request->name,
    	  "type"=>$request->type,
    	  "model_name"=>$request->model_name,
    	  "foreign_key"=>$request->foreign_key,
    	  "local_key"=>$request->local_key,
        ]);
        return $dianasoftrelationship;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\DianaSoftRelationship  $dianasoftrelationship
     * @return \Illuminate\Http\Response
     */
    public function destroy(DianaSoftRelationship $dianasoftrelationship)
    {
        $dianasoftrelationship->delete();

    }
}