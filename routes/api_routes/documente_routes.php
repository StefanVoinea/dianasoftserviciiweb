<?php

    Route::middleware("auth:api")->group(function () {
	    Route::post("/documente", "Api\DocumenteController@indexPaginat")
            ->middleware("permission:viewDocumente");
        Route::get("/documente", "Api\DocumenteController@index")
            ->middleware("permission:viewDocumente");
	    Route::get("/documente/show/{documente}", "Api\DocumenteController@show")
            ->middleware("permission:viewDocumente");

	    Route::post("/documente/store", "Api\DocumenteController@store")
            ->middleware("permission:addDocumente");

	    Route::post("/documente/delete/{documente}", "Api\DocumenteController@destroy")
            ->middleware("permission:deleteDocumente");

	    Route::post("/documente/edit/{documente}", "Api\DocumenteController@update")
            ->middleware("permission:editDocumente");
      Route::get("/documente/export", "Api\DocumenteController@export")
            ->middleware("permission:exportDocumente");  

         Route::post("/documente/import", "Api\DocumenteController@import")
                     ->middleware("permission:importDocumente");       
 });
  