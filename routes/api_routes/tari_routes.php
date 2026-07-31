<?php

    Route::middleware("auth:api")->group(function () {
	    Route::get("/tari", "Api\TariController@index")
            ->middleware("permission:viewTari");
       Route::post("/tari", "Api\TariController@indexPaginat")
            ->middleware("permission:viewTari");
	    Route::get("/tari/show/{tari}", "Api\TariController@show")
            ->middleware("permission:viewTari");

	    Route::post("/tari/store", "Api\TariController@store")
            ->middleware("permission:addTari");

	    Route::post("/tari/delete/{tari}", "Api\TariController@destroy")
            ->middleware("permission:deleteTari");

	    Route::post("/tari/edit/{tari}", "Api\TariController@update")
            ->middleware("permission:editTari");
      Route::get("/tari/export", "Api\TariController@export")
            ->middleware("permission:exportTari");  

         Route::post("/tari/import", "Api\TariController@import")
                     ->middleware("permission:importTari");       
 });
  