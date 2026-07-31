<?php

    Route::middleware("auth:api")->group(function () {
	    Route::post("/tipvaluta", "Api\TipvalutaController@indexPaginat")
            ->middleware("permission:viewTipvaluta");
        Route::get("/tipvaluta", "Api\TipvalutaController@index")
            ->middleware("permission:viewTipvaluta");
	    Route::get("/tipvaluta/show/{tipvaluta}", "Api\TipvalutaController@show")
            ->middleware("permission:viewTipvaluta");

	    Route::post("/tipvaluta/store", "Api\TipvalutaController@store")
            ->middleware("permission:addTipvaluta");

	    Route::post("/tipvaluta/delete/{tipvaluta}", "Api\TipvalutaController@destroy")
            ->middleware("permission:deleteTipvaluta");

	    Route::post("/tipvaluta/edit/{tipvaluta}", "Api\TipvalutaController@update")
            ->middleware("permission:editTipvaluta");
      Route::get("/tipvaluta/export", "Api\TipvalutaController@export")
            ->middleware("permission:exportTipvaluta");  

         Route::post("/tipvaluta/import", "Api\TipvalutaController@import")
                     ->middleware("permission:importTipvaluta");       
 });
  