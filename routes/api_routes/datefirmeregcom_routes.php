
  <?php

    Route::middleware("auth:api")->group(function () {
	    Route::post("/datefirmeregcom", "Api\DatefirmeregcomController@indexPaginat")
            ->middleware("permission:viewDatefirmeregcom");

	    Route::get("/datefirmeregcom/show/{datefirmeregcom}", "Api\DatefirmeregcomController@show")
            ->middleware("permission:viewDatefirmeregcom");

	    Route::post("/datefirmeregcom/store", "Api\DatefirmeregcomController@store")
            ->middleware("permission:addDatefirmeregcom");

	    Route::post("/datefirmeregcom/delete/{datefirmeregcom}", "Api\DatefirmeregcomController@destroy")
            ->middleware("permission:deleteDatefirmeregcom");

	    Route::post("/datefirmeregcom/edit/{datefirmeregcom}", "Api\DatefirmeregcomController@update")
            ->middleware("permission:editDatefirmeregcom");
      Route::get("/datefirmeregcom/export", "Api\DatefirmeregcomController@export")
            ->middleware("permission:exportDatefirmeregcom");  

         Route::post("/datefirmeregcom/import", "Api\DatefirmeregcomController@import")
                     ->middleware("permission:importDatefirmeregcom");       
 });
  