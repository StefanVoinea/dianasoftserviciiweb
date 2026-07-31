<?php

    Route::middleware("auth:api")->group(function () {
	    Route::post("/litigiicaleatac", "Api\LitigiicaleatacController@indexPaginat")
            ->middleware("permission:viewLitigiicaleatac");
        Route::get("/litigiicaleatac", "Api\LitigiicaleatacController@index")
            ->middleware("permission:viewLitigiicaleatac");
	    Route::get("/litigiicaleatac/show/{litigiicaleatac}", "Api\LitigiicaleatacController@show")
            ->middleware("permission:viewLitigiicaleatac");

	    Route::post("/litigiicaleatac/store", "Api\LitigiicaleatacController@store")
            ->middleware("permission:addLitigiicaleatac");

	    Route::post("/litigiicaleatac/delete/{litigiicaleatac}", "Api\LitigiicaleatacController@destroy")
            ->middleware("permission:deleteLitigiicaleatac");

	    Route::post("/litigiicaleatac/edit/{litigiicaleatac}", "Api\LitigiicaleatacController@update")
            ->middleware("permission:editLitigiicaleatac");
      Route::get("/litigiicaleatac/export", "Api\LitigiicaleatacController@export")
            ->middleware("permission:exportLitigiicaleatac");  

         Route::post("/litigiicaleatac/import", "Api\LitigiicaleatacController@import")
                     ->middleware("permission:importLitigiicaleatac");       
 });
  