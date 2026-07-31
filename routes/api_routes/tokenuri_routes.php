<?php

    Route::middleware("auth:api")->group(function () {
	    Route::post("/tokenuri", "Api\TokenuriController@indexPaginat")
            ->middleware("permission:viewTokenuri");
        Route::get("/tokenuri", "Api\TokenuriController@index")
            ->middleware("permission:viewTokenuri");
	    Route::get("/tokenuri/show/{tokenuri}", "Api\TokenuriController@show")
            ->middleware("permission:viewTokenuri");

	    Route::post("/tokenuri/store", "Api\TokenuriController@store")
            ->middleware("permission:addTokenuri");

	    Route::post("/tokenuri/delete/{tokenuri}", "Api\TokenuriController@destroy")
            ->middleware("permission:deleteTokenuri");

	    Route::post("/tokenuri/edit/{tokenuri}", "Api\TokenuriController@update")
            ->middleware("permission:editTokenuri");
      Route::get("/tokenuri/export", "Api\TokenuriController@export")
            ->middleware("permission:exportTokenuri");  

         Route::post("/tokenuri/import", "Api\TokenuriController@import")
                     ->middleware("permission:importTokenuri");       
 });
  