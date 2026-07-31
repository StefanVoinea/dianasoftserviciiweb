<?php

    Route::middleware("auth:api")->group(function () {
	    Route::post("/interogareanaf", "Api\InterogareanafController@indexPaginat")
            ->middleware("permission:viewInterogareanaf");

	    Route::get("/interogareanaf/show/{interogareanaf}", "Api\InterogareanafController@show")
            ->middleware("permission:viewInterogareanaf");

	    Route::post("/interogareanaf/store", "Api\InterogareanafController@store")
            ->middleware("permission:addInterogareanaf");

	    Route::post("/interogareanaf/delete/{interogareanaf}", "Api\InterogareanafController@destroy")
            ->middleware("permission:deleteInterogareanaf");

	    Route::post("/interogareanaf/edit/{interogareanaf}", "Api\InterogareanafController@update")
            ->middleware("permission:editInterogareanaf");
      Route::get("/interogareanaf/export", "Api\InterogareanafController@export")
            ->middleware("permission:exportInterogareanaf");  

         Route::post("/interogareanaf/import", "Api\InterogareanafController@import")
                     ->middleware("permission:importInterogareanaf");       
 });
  