<?php

    Route::middleware("auth:api")->group(function () {
	    Route::get("/gestiune_user", "Api\Gestiune_UserController@index")
            ->middleware("permission:viewGestiune_User");

	    Route::get("/gestiune_user/show/{gestiune_user}", "Api\Gestiune_UserController@show")
            ->middleware("permission:viewGestiune_User");

	    Route::post("/gestiune_user/store", "Api\Gestiune_UserController@store")
            ->middleware("permission:addGestiune_User");

	    Route::post("/gestiune_user/delete/{gestiune_user}", "Api\Gestiune_UserController@destroy")
            ->middleware("permission:deleteGestiune_User");

	    Route::post("/gestiune_user/edit/{gestiune_user}", "Api\Gestiune_UserController@update")
            ->middleware("permission:editGestiune_User");
 });
  