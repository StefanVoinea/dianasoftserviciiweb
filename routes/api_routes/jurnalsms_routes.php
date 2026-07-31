<?php

    Route::middleware("auth:api")->group(function () {
	    Route::post("/jurnalsms", "Api\JurnalsmsController@indexPaginat")
            ->middleware("permission:viewJurnalsms");
        Route::get("/jurnalsms", "Api\JurnalsmsController@index")
            ->middleware("permission:viewJurnalsms");
	    Route::get("/jurnalsms/show/{jurnalsms}", "Api\JurnalsmsController@show")
            ->middleware("permission:viewJurnalsms");

	    Route::post("/jurnalsms/store", "Api\JurnalsmsController@store")
            ->middleware("permission:addJurnalsms");
            Route::post("/transmitesms", "Api\JurnalsmsController@transmitesms")
            ->middleware("permission:viewJurnalsms"); 
	    Route::post("/jurnalsms/delete/{jurnalsms}", "Api\JurnalsmsController@destroy")
            ->middleware("permission:deleteJurnalsms");

	    Route::post("/jurnalsms/edit/{jurnalsms}", "Api\JurnalsmsController@update")
            ->middleware("permission:editJurnalsms");
          Route::post("/raportsms", "Api\JurnalsmsController@raportsms")
                 ->middleware("permission:viewJurnalsms");    
      Route::get("/jurnalsms/export", "Api\JurnalsmsController@export")
            ->middleware("permission:exportJurnalsms");  

         Route::post("/jurnalsms/import", "Api\JurnalsmsController@import")
                     ->middleware("permission:importJurnalsms");       
 });
  