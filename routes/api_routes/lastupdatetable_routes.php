
  <?php

    Route::middleware("auth:api")->group(function () {
	    Route::post("/lastupdatetable", "Api\LastupdatetableController@indexPaginat")
            ->middleware("permission:viewLastupdatetable");
        Route::get("/lastupdatetable", "Api\LastupdatetableController@index")
            ->middleware("permission:viewLastupdatetable");
	    Route::get("/lastupdatetable/show/{lastupdatetable}", "Api\LastupdatetableController@show")
            ->middleware("permission:viewLastupdatetable");

	    Route::post("/lastupdatetable/store", "Api\LastupdatetableController@store")
            ->middleware("permission:addLastupdatetable");

	    Route::post("/lastupdatetable/delete/{lastupdatetable}", "Api\LastupdatetableController@destroy")
            ->middleware("permission:deleteLastupdatetable");

	    Route::post("/lastupdatetable/edit/{lastupdatetable}", "Api\LastupdatetableController@update")
            ->middleware("permission:editLastupdatetable");
      Route::get("/lastupdatetable/export", "Api\LastupdatetableController@export")
            ->middleware("permission:exportLastupdatetable");  

         Route::post("/lastupdatetable/import", "Api\LastupdatetableController@import")
                     ->middleware("permission:importLastupdatetable");       
 });
  