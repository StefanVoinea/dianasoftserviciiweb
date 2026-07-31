<?php

    Route::middleware("auth:api")->group(function () {
	    Route::get("/dianasoftrelationship", "Api\DianaSoftRelationshipController@index")
	    		->middleware('permission:viewDianasoftrelationship');

	    Route::get("/dianasoftrelationship/show/{dianasoftrelationship}", "Api\DianaSoftRelationshipController@show")
	    		->middleware('permission:viewDianasoftrelationship');

	    Route::post("/dianasoftrelationship/store", "Api\DianaSoftRelationshipController@store")
	    		->middleware('permission:addDianasoftrelationship');

	    Route::post("/dianasoftrelationship/delete/{dianasoftrelationship}", "Api\DianaSoftRelationshipController@destroy")
	    		->middleware('permission:deleteDianasoftrelationship');
	    		
	    Route::post("/dianasoftrelationship/edit/{dianasoftrelationship}", "Api\DianaSoftRelationshipController@update")
	    		->middleware('permission:editDianasoftrelationship');
 });
  