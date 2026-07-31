<?php

    Route::middleware("auth:api")->group(function () {
	     Route::get("/judet", "Api\JudetController@index")
            ->middleware("permission:viewJudet");
       Route::post("/judet", "Api\JudetController@indexPaginat")
            ->middleware("permission:viewJudet");
	    Route::get("/judet/show/{judet}", "Api\JudetController@show")
            ->middleware("permission:viewJudet");

	    Route::post("/judet/store", "Api\JudetController@store")
            ->middleware("permission:addJudet");

	    Route::post("/judet/delete/{judet}", "Api\JudetController@destroy")
            ->middleware("permission:deleteJudet");

	    Route::post("/judet/edit/{judet}", "Api\JudetController@update")
            ->middleware("permission:editJudet");
      Route::get("/judet/export", "Api\JudetController@export")
            ->middleware("permission:exportJudet");  

         Route::post("/judet/import", "Api\JudetController@import")
                     ->middleware("permission:importJudet");       
 });
  