<?php

    Route::middleware("auth:api")->group(function () {
	    Route::post("/litigiisedinte", "Api\LitigiisedinteController@indexPaginat")
            ->middleware("permission:viewLitigiisedinte");
        Route::get("/litigiisedinte", "Api\LitigiisedinteController@index")
            ->middleware("permission:viewLitigiisedinte");
	    Route::get("/litigiisedinte/show/{litigiisedinte}", "Api\LitigiisedinteController@show")
            ->middleware("permission:viewLitigiisedinte");

	    Route::post("/litigiisedinte/store", "Api\LitigiisedinteController@store")
            ->middleware("permission:addLitigiisedinte");

	    Route::post("/litigiisedinte/delete/{litigiisedinte}", "Api\LitigiisedinteController@destroy")
            ->middleware("permission:deleteLitigiisedinte");

	    Route::post("/litigiisedinte/edit/{litigiisedinte}", "Api\LitigiisedinteController@update")
            ->middleware("permission:editLitigiisedinte");
      Route::get("/litigiisedinte/export", "Api\LitigiisedinteController@export")
            ->middleware("permission:exportLitigiisedinte");  

         Route::post("/litigiisedinte/import", "Api\LitigiisedinteController@import")
                     ->middleware("permission:importLitigiisedinte");       
 });
  