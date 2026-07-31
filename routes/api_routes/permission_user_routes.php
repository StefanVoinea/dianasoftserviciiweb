<?php

    Route::middleware("auth:api")->group(function () {
	    Route::get("/permission_user", "Api\Permission_UserController@index")
	    		->middleware('permission:viewPermission_User');

	    Route::get("/permission_user/show/{permission_user}", "Api\Permission_UserController@show")	
	    		->middleware('permission:viewPermission_User');

	    Route::post("/permission_user/store", "Api\Permission_UserController@store")
	    		->middleware('permission:addPermission_User');

	    Route::post("/permission_user/delete/{permission_user}", "Api\Permission_UserController@destroy")
	    		->middleware('permission:deletePermission_User');

	    Route::post("/permission_user/edit/{permission_user}", "Api\Permission_UserController@update")
	    		->middleware('permission:editPermission_User');
 });
  