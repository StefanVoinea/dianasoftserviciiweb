<?php

    Route::middleware("auth:api")->group(function () {
	    Route::post("/coduricaen", "Api\CoduricaenController@indexPaginat")
            ->middleware("permission:viewCoduricaen");
        Route::get("/coduricaen", "Api\CoduricaenController@index")
            ->middleware("permission:viewCoduricaen");
	    Route::get("/coduricaen/show/{coduricaen}", "Api\CoduricaenController@show")
            ->middleware("permission:viewCoduricaen");

	    Route::post("/coduricaen/store", "Api\CoduricaenController@store")
            ->middleware("permission:addCoduricaen");

	    Route::post("/coduricaen/delete/{coduricaen}", "Api\CoduricaenController@destroy")
            ->middleware("permission:deleteCoduricaen");

	    Route::post("/coduricaen/edit/{coduricaen}", "Api\CoduricaenController@update")
            ->middleware("permission:editCoduricaen");
      Route::get("/coduricaen/export", "Api\CoduricaenController@export")
            ->middleware("permission:exportCoduricaen");  

         Route::post("/coduricaen/import", "Api\CoduricaenController@import")
                     ->middleware("permission:importCoduricaen");       
 });
  