
  <?php

    Route::middleware("auth:api")->group(function () {
	 Route::post("/localitati", "Api\LocalitatiController@indexPaginat")
            ->middleware("permission:viewLocalitati");
     Route::get("/localitati", "Api\LocalitatiController@index")
            ->middleware("permission:viewLocalitati");
      Route::post("/localitatiFiltrate", "Api\LocalitatiController@indexFiltrat")
            ->middleware("permission:viewLocalitati");
	    Route::get("/localitati/show/{localitati}", "Api\LocalitatiController@show")
            ->middleware("permission:viewLocalitati");

	    Route::post("/localitati/store", "Api\LocalitatiController@store")
            ->middleware("permission:addLocalitati");
      Route::post("/localitati/searchLocalitate", "Api\LocalitatiController@searchLocalitate")
            ->middleware("permission:viewLocalitati");
	    Route::post("/localitati/delete/{localitati}", "Api\LocalitatiController@destroy")
            ->middleware("permission:deleteLocalitati");

	    Route::post("/localitati/edit/{localitati}", "Api\LocalitatiController@update")
            ->middleware("permission:editLocalitati");
      Route::get("/localitati/export", "Api\LocalitatiController@export")
            ->middleware("permission:exportLocalitati");  

         Route::post("/localitati/import", "Api\LocalitatiController@import")
                     ->middleware("permission:importLocalitati");       
 });
  