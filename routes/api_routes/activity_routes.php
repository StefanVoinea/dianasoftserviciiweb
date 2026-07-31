<?php

    Route::middleware("auth:api")->group(function () {
	    Route::post("/activity", "Api\ActivityController@indexPaginat")
            ->middleware("permission:viewActivity");

	    Route::get("/activity/show/{activity}", "Api\ActivityController@show")
            ->middleware("permission:viewActivity");

	    Route::post("/activity/store", "Api\ActivityController@store")
            ->middleware("permission:addActivity");

	    Route::post("/activity/delete/{activity}", "Api\ActivityController@destroy")
            ->middleware("permission:deleteActivity");

	    Route::post("/activity/edit/{activity}", "Api\ActivityController@update")
            ->middleware("permission:editActivity");
 });
  