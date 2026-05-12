
  <?php

    Route::middleware("auth:api")->group(function () {
	    Route::post("/versiuni", "Api\VersiuniController@indexPaginat")
            ->middleware("permission:viewVersiuni");
        Route::get("/versiuni", "Api\VersiuniController@index")
            ->middleware("permission:viewVersiuni");
	    Route::get("/versiuni/show/{versiuni}", "Api\VersiuniController@show")
            ->middleware("permission:viewVersiuni");

	    Route::post("/versiuni/store", "Api\VersiuniController@store")
            ->middleware("permission:addVersiuni");

	    Route::post("/versiuni/delete/{versiuni}", "Api\VersiuniController@destroy")
            ->middleware("permission:deleteVersiuni");

	    Route::post("/versiuni/edit/{versiuni}", "Api\VersiuniController@update")
            ->middleware("permission:editVersiuni");
      Route::get("/versiuni/export", "Api\VersiuniController@export")
            ->middleware("permission:exportVersiuni");  

         Route::post("/versiuni/import", "Api\VersiuniController@import")
                     ->middleware("permission:importVersiuni");       
 });
  