
  <?php

    Route::middleware("auth:api")->group(function () {
	    Route::post("/optiunidropdown", "Api\OptiunidropdownController@indexPaginat")
            ->middleware("permission:viewOptiunidropdown");
        Route::get("/optiunidropdown", "Api\OptiunidropdownController@index")
            ->middleware("permission:viewOptiunidropdown");
	    Route::get("/optiunidropdown/show/{optiunidropdown}", "Api\OptiunidropdownController@show")
            ->middleware("permission:viewOptiunidropdown");

	    Route::post("/optiunidropdown/store", "Api\OptiunidropdownController@store")
            ->middleware("permission:addOptiunidropdown");

	    Route::post("/optiunidropdown/delete/{optiunidropdown}", "Api\OptiunidropdownController@destroy")
            ->middleware("permission:deleteOptiunidropdown");

	    Route::post("/optiunidropdown/edit/{optiunidropdown}", "Api\OptiunidropdownController@update")
            ->middleware("permission:editOptiunidropdown");
      Route::get("/optiunidropdown/export", "Api\OptiunidropdownController@export")
            ->middleware("permission:exportOptiunidropdown");  

         Route::post("/optiunidropdown/import", "Api\OptiunidropdownController@import")
                     ->middleware("permission:importOptiunidropdown");       
 });
  