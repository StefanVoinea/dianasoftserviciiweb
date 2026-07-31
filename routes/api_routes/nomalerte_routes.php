<?php

    Route::middleware("auth:api")->group(function () {
	    Route::post("/nomalerte", "Api\NomalerteController@indexPaginat")
            ->middleware("permission:viewNomalerte");
        Route::get("/nomalerte", "Api\NomalerteController@index")
            ->middleware("permission:viewNomalerte");
	    Route::get("/nomalerte/show/{nomalerte}", "Api\NomalerteController@show")
            ->middleware("permission:viewNomalerte");

	    Route::post("/nomalerte/store", "Api\NomalerteController@store")
            ->middleware("permission:addNomalerte");

	    Route::post("/nomalerte/delete/{nomalerte}", "Api\NomalerteController@destroy")
            ->middleware("permission:deleteNomalerte");

	    Route::post("/nomalerte/edit/{nomalerte}", "Api\NomalerteController@update")
            ->middleware("permission:editNomalerte");
      Route::get("/nomalerte/export", "Api\NomalerteController@export")
            ->middleware("permission:exportNomalerte");  

         Route::post("/nomalerte/import", "Api\NomalerteController@import")
                     ->middleware("permission:importNomalerte");       
 });
  