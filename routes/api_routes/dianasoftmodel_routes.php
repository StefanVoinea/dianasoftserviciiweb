
  <?php

    Route::middleware("auth:api")->group(function () {
    	
    	Route::post('/developerPanel/addModel', 'Api\DeveloperPanelController@addModel')->middleware('permission:addDianasoftmodel'); 
    	Route::post('/developerPanel/editModel', 'Api\DeveloperPanelController@editModel')->middleware('permission:editDianasoftmodel'); 

	    Route::get("/dianasoftmodel", "Api\DianaSoftModelController@index")
	            ->middleware('permission:viewDianasoftmodel');
      Route::post("/dianasoftmodel", "Api\DianaSoftModelController@indexPaginat")
	            ->middleware('permission:viewDianasoftmodel');
	    Route::get("/dianasoftmodel/show/{dianasoftmodel}", "Api\DianaSoftModelController@show")
	    	    ->middleware('permission:viewDianasoftmodel');

	     Route::post("/dianasoftmodel/store", "Api\DianaSoftModelController@store")
	     		->middleware('permission:addDianasoftmodel');
	     		
      Route::post("/importFileModel", "Api\DianaSoftModelController@importFileModel")
	     		->middleware('permission:addDianasoftmodel');
	    Route::post("/dianasoftmodel/delete/{dianasoftmodel}", "Api\DianaSoftModelController@destroy")
	    		->middleware('permission:deleteDianasoftmodel');	
	    		
	    Route::post("/dianasoftmodel/edit/{dianasoftmodel}", "Api\DianaSoftModelController@update")
	    		->middleware('permission:editDianasoftmodel');
 });
  