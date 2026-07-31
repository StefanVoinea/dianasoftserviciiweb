<?php

    Route::middleware("auth:api")->group(function () {
	    Route::post("/ipautorizat", "Api\IpautorizatController@indexPaginat")
            ->middleware("permission:viewIpautorizat");
        Route::get("/ipautorizat", "Api\IpautorizatController@index")
            ->middleware("permission:viewIpautorizat");
	    Route::get("/ipautorizat/show/{ipautorizat}", "Api\IpautorizatController@show")
            ->middleware("permission:viewIpautorizat");

	    Route::post("/ipautorizat/store", "Api\IpautorizatController@store")
            ->middleware("permission:addIpautorizat");

	    Route::post("/ipautorizat/delete/{ipautorizat}", "Api\IpautorizatController@destroy")
            ->middleware("permission:deleteIpautorizat");

	    Route::post("/ipautorizat/edit/{ipautorizat}", "Api\IpautorizatController@update")
            ->middleware("permission:editIpautorizat");
      Route::get("/ipautorizat/export", "Api\IpautorizatController@export")
            ->middleware("permission:exportIpautorizat");  

         Route::post("/ipautorizat/import", "Api\IpautorizatController@import")
                     ->middleware("permission:importIpautorizat");       
 });
  