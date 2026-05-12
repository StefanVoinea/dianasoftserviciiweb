<?php

namespace App\Http\Controllers\Api;

use App\Models\DianaSoftField;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DianaSoftFieldController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $dianasoftfield= DianaSoftField::get();
                              //::where("user_id",auth()->user()->id)
                                
        return json_encode($dianasoftfield->load('dianasoftmodel'));
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
    	  "display_name"=>[required],]);


         return DianaSoftField::create([
    	  "dianasoftmodel_id"=>$request->dianasoftmodel_id,
    	  "name"=>$request->name,
    	  "type"=>$request->type,
    	  "length"=>$request->length,
    	  "nullable"=>$request->nullable,
    	  "default"=>$request->default,
    	  "fillable"=>$request->fillable,
    	  "required"=>$request->required,
    	  "indexed"=>$request->indexed,
    	  "frontendvalidation"=>$request->frontendvalidation,
    	  "backendvalidation"=>$request->backendvalidation,
    	  "faker"=>$request->faker,
    	  "display_name"=>$request->display_name,
    	  "input_type"=>$request->input_type,
    	  "input_source"=>$request->input_source,
    	  "input_source_type"=>$request->input_source_type,           
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\DianaSoftField  $dianasoftfield
     * @return \Illuminate\Http\Response
     */
    public function show(DianaSoftField $dianasoftfield)
    {
        $resp= DianaSoftField::where("id",$dianasoftfield->id)
        											->get()->first();
                                
        return json_encode($resp);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\DianaSoftField  $dianasoftfield
     * @return \Illuminate\Http\Response
     */
    public function edit(DianaSoftField $dianasoftfield)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\DianaSoftField  $dianasoftfield
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, DianaSoftField $dianasoftfield)
    {
        $dianasoftfield->update([
    	  "dianasoftmodel_id"=>$request->dianasoftmodel_id,
    	  "name"=>$request->name,
    	  "type"=>$request->type,
    	  "length"=>$request->length,
    	  "nullable"=>$request->nullable,
    	  "default"=>$request->default,
    	  "fillable"=>$request->fillable,
    	  "required"=>$request->required,
    	  "indexed"=>$request->indexed,
    	  "frontendvalidation"=>$request->frontendvalidation,
    	  "backendvalidation"=>$request->backendvalidation,
    	  "faker"=>$request->faker,
    	  "display_name"=>$request->display_name,
    	  "input_type"=>$request->input_type,
    	  "input_source"=>$request->input_source,
    	  "input_source_type"=>$request->input_source_type,
        ]);
        return $dianasoftfield;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\DianaSoftField  $dianasoftfield
     * @return \Illuminate\Http\Response
     */
    public function destroy(DianaSoftField $dianasoftfield)
    {
        $dianasoftfield->delete();

    }
}