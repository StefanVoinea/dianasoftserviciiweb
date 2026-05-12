
  <?php

    Route::middleware("auth:api")->group(function () {
	    Route::post("/ordinedeblocareanaf", "Api\OrdinedeblocareanafController@indexPaginat")
            ->middleware("permission:viewOrdinedeblocareanaf");
        Route::get("/ordinedeblocareanaf", "Api\OrdinedeblocareanafController@index")
            ->middleware("permission:viewOrdinedeblocareanaf");
	    Route::get("/ordinedeblocareanaf/show/{ordinedeblocareanaf}", "Api\OrdinedeblocareanafController@show")
            ->middleware("permission:viewOrdinedeblocareanaf");

	    Route::post("/ordinedeblocareanaf/store", "Api\OrdinedeblocareanafController@store")
            ->middleware("permission:addOrdinedeblocareanaf");

	    Route::post("/ordinedeblocareanaf/delete/{ordinedeblocareanaf}", "Api\OrdinedeblocareanafController@destroy")
            ->middleware("permission:deleteOrdinedeblocareanaf");

	    Route::post("/ordinedeblocareanaf/edit/{ordinedeblocareanaf}", "Api\OrdinedeblocareanafController@update")
            ->middleware("permission:editOrdinedeblocareanaf");
      Route::get("/ordinedeblocareanaf/export", "Api\OrdinedeblocareanafController@export")
            ->middleware("permission:exportOrdinedeblocareanaf");  

         Route::post("/ordinedeblocareanaf/import", "Api\OrdinedeblocareanafController@import")
                     ->middleware("permission:importOrdinedeblocareanaf");       
 });
  