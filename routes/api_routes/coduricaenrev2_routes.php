<?php

    Route::middleware("auth:api")->group(function () {
	    Route::post("/coduricaenrev2", "Api\Coduricaenrev2Controller@indexPaginat")
            ->middleware("permission:viewCoduricaenrev2");
        Route::get("/coduricaenrev2", "Api\Coduricaenrev2Controller@index")
            ->middleware("permission:viewCoduricaenrev2");
	    Route::get("/coduricaenrev2/show/{coduricaenrev2}", "Api\Coduricaenrev2Controller@show")
            ->middleware("permission:viewCoduricaenrev2");

	    Route::post("/coduricaenrev2/store", "Api\Coduricaenrev2Controller@store")
            ->middleware("permission:addCoduricaenrev2");

	    Route::post("/coduricaenrev2/delete/{coduricaenrev2}", "Api\Coduricaenrev2Controller@destroy")
            ->middleware("permission:deleteCoduricaenrev2");

	    Route::post("/coduricaenrev2/edit/{coduricaenrev2}", "Api\Coduricaenrev2Controller@update")
            ->middleware("permission:editCoduricaenrev2");
      Route::get("/coduricaenrev2/export", "Api\Coduricaenrev2Controller@export")
            ->middleware("permission:exportCoduricaenrev2");  

         Route::post("/coduricaenrev2/import", "Api\Coduricaenrev2Controller@import")
                     ->middleware("permission:importCoduricaenrev2");       
 });
  