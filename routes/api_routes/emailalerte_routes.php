<?php

    Route::middleware("auth:api")->group(function () {
	    Route::post("/emailalerte", "Api\EmailalerteController@indexPaginat")
            ->middleware("permission:viewEmailalerte");
        Route::get("/emailalerte", "Api\EmailalerteController@index")
            ->middleware("permission:viewEmailalerte");
	    Route::get("/emailalerte/show/{emailalerte}", "Api\EmailalerteController@show")
            ->middleware("permission:viewEmailalerte");

	    Route::post("/emailalerte/store", "Api\EmailalerteController@store")
            ->middleware("permission:addEmailalerte");

	    Route::post("/emailalerte/delete/{emailalerte}", "Api\EmailalerteController@destroy")
            ->middleware("permission:deleteEmailalerte");

	    Route::post("/emailalerte/edit/{emailalerte}", "Api\EmailalerteController@update")
            ->middleware("permission:editEmailalerte");
      Route::get("/emailalerte/export", "Api\EmailalerteController@export")
            ->middleware("permission:exportEmailalerte");  

         Route::post("/emailalerte/import", "Api\EmailalerteController@import")
                     ->middleware("permission:importEmailalerte");       
 });
  