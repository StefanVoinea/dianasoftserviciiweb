<?php

    Route::middleware("auth:api")->group(function () {
	    Route::post("/societati", "Api\SocietatiController@indexPaginat")
            ->middleware("permission:viewSocietati");
        Route::get("/societati", "Api\SocietatiController@index")
            ->middleware("permission:viewSocietati");
	    Route::get("/societati/show/{societati}", "Api\SocietatiController@show")
            ->middleware("permission:viewSocietati");

	    Route::post("/societati/store", "Api\SocietatiController@store")
            ->middleware("permission:addSocietati");

	    Route::post("/societati/delete/{societati}", "Api\SocietatiController@destroy")
            ->middleware("permission:deleteSocietati");

	    Route::post("/societati/edit/{societati}", "Api\SocietatiController@update")
            ->middleware("permission:editSocietati");
      Route::get("/societati/export", "Api\SocietatiController@export")
            ->middleware("permission:exportSocietati");  

         Route::post("/societati/import", "Api\SocietatiController@import")
                     ->middleware("permission:importSocietati");       
 });
  