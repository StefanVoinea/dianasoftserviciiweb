<?php

    Route::middleware("auth:api")->group(function () {
	    Route::post("/alertetransmise", "Api\AlertetransmiseController@indexPaginat")
            ->middleware("permission:viewAlertetransmise");
        Route::get("/alertetransmise", "Api\AlertetransmiseController@index")
            ->middleware("permission:viewAlertetransmise");
	    Route::get("/alertetransmise/show/{alertetransmise}", "Api\AlertetransmiseController@show")
            ->middleware("permission:viewAlertetransmise");

	    Route::post("/alertetransmise/store", "Api\AlertetransmiseController@store")
            ->middleware("permission:addAlertetransmise");

	    Route::post("/alertetransmise/delete/{alertetransmise}", "Api\AlertetransmiseController@destroy")
            ->middleware("permission:deleteAlertetransmise");

	    Route::post("/alertetransmise/edit/{alertetransmise}", "Api\AlertetransmiseController@update")
            ->middleware("permission:editAlertetransmise");
      Route::get("/alertetransmise/export", "Api\AlertetransmiseController@export")
            ->middleware("permission:exportAlertetransmise");  

         Route::post("/alertetransmise/import", "Api\AlertetransmiseController@import")
                     ->middleware("permission:importAlertetransmise");       
 });
  