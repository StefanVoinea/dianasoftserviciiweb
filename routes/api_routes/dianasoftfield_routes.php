
  <?php

    Route::middleware("auth:api")->group(function () {
	    Route::get("/dianasoftfield", "Api\DianaSoftFieldController@index")
	    		->middleware('permission:viewDianasoftfield');

	    Route::get("/dianasoftfield/show/{dianasoftfield}", "Api\DianaSoftFieldController@show")
	    		->middleware('permission:viewDianasoftfield');

	    Route::post("/dianasoftfield/store", "Api\DianaSoftFieldController@store")
	    		->middleware('permission:addDianasoftfield');

	    Route::post("/dianasoftfield/delete/{dianasoftfield}", "Api\DianaSoftFieldController@destroy")
	    		->middleware('permission:deleteDianasoftfield');

	    Route::post("/dianasoftfield/edit/{dianasoftfield}", "Api\DianaSoftFieldController@update")
	    		->middleware('permission:editDianasoftfield');
 });
  