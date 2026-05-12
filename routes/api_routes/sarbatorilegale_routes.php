
  <?php

    Route::middleware("auth:api")->group(function () {
	    Route::post("/sarbatorilegale", "Api\SarbatorilegaleController@indexPaginat")
            ->middleware("permission:viewSarbatorilegale");
        Route::get("/sarbatorilegale", "Api\SarbatorilegaleController@index")
            ->middleware("permission:viewSarbatorilegale");
	    Route::get("/sarbatorilegale/show/{sarbatorilegale}", "Api\SarbatorilegaleController@show")
            ->middleware("permission:viewSarbatorilegale");

	    Route::post("/sarbatorilegale/store", "Api\SarbatorilegaleController@store")
            ->middleware("permission:addSarbatorilegale");

	    Route::post("/sarbatorilegale/delete/{sarbatorilegale}", "Api\SarbatorilegaleController@destroy")
            ->middleware("permission:deleteSarbatorilegale");

	    Route::post("/sarbatorilegale/edit/{sarbatorilegale}", "Api\SarbatorilegaleController@update")
            ->middleware("permission:editSarbatorilegale");
      Route::get("/sarbatorilegale/export", "Api\SarbatorilegaleController@export")
            ->middleware("permission:exportSarbatorilegale");  

         Route::post("/sarbatorilegale/import", "Api\SarbatorilegaleController@import")
                     ->middleware("permission:importSarbatorilegale");       
 });
  