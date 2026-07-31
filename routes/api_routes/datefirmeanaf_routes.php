<?php

    Route::middleware("auth:api")->group(function () {
	    Route::post("/datefirmeanaf", "Api\DatefirmeanafController@indexPaginat")
            ->middleware("permission:viewDatefirmeanaf");
        Route::get("/datefirmeanaf", "Api\DatefirmeanafController@index")
            ->middleware("permission:viewDatefirmeanaf");
	    Route::get("/datefirmeanaf/show/{datefirmeanaf}", "Api\DatefirmeanafController@show")
            ->middleware("permission:viewDatefirmeanaf");

	    Route::post("/datefirmeanaf/store", "Api\DatefirmeanafController@store")
            ->middleware("permission:addDatefirmeanaf");

	    Route::post("/datefirmeanaf/delete/{datefirmeanaf}", "Api\DatefirmeanafController@destroy")
            ->middleware("permission:deleteDatefirmeanaf");

	    Route::post("/datefirmeanaf/edit/{datefirmeanaf}", "Api\DatefirmeanafController@update")
            ->middleware("permission:editDatefirmeanaf");
      Route::get("/datefirmeanaf/export", "Api\DatefirmeanafController@export")
            ->middleware("permission:exportDatefirmeanaf");  

         Route::post("/datefirmeanaf/import", "Api\DatefirmeanafController@import")
                     ->middleware("permission:importDatefirmeanaf");       
 });
  