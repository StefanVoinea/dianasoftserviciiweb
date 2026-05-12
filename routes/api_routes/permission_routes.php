
  <?php

    Route::middleware("auth:api")->group(function () {
	    Route::get("/permission", "Api\PermissionController@index")
	    		->middleware('permission:viewPermission');
      Route::post("/permission", "Api\PermissionController@indexPaginat")
	    		->middleware('permission:viewPermission');
	    Route::get("/permission/show/{permission}", "Api\PermissionController@show")
	    		->middleware('permission:viewPermission');

	    Route::post("/permission/store", "Api\PermissionController@store")
	    		->middleware('permission:addPermission');

	    Route::post("/permission/delete/{permission}", "Api\PermissionController@destroy")
	    		->middleware('permission:deletePermission');
	    		
	    Route::post("/permission/edit/{permission}", "Api\PermissionController@update")
	    		->middleware('permission:editPermission');;
 });
  