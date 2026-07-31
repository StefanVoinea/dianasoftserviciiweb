<?php

    Route::middleware("auth:api")->group(function () {
	    Route::post("/nomtari", "Api\NomtariController@indexPaginat")
            ->middleware("permission:viewNomtari");
        Route::get("/nomtari", "Api\NomtariController@index")
            ->middleware("permission:viewNomtari");
	    Route::get("/nomtari/show/{nomtari}", "Api\NomtariController@show")
            ->middleware("permission:viewNomtari");

	    Route::post("/nomtari/store", "Api\NomtariController@store")
            ->middleware("permission:addNomtari");

	    Route::post("/nomtari/delete/{nomtari}", "Api\NomtariController@destroy")
            ->middleware("permission:deleteNomtari");

	    Route::post("/nomtari/edit/{nomtari}", "Api\NomtariController@update")
            ->middleware("permission:editNomtari");
      Route::get("/nomtari/export", "Api\NomtariController@export")
            ->middleware("permission:exportNomtari");  

         Route::post("/nomtari/import", "Api\NomtariController@import")
                     ->middleware("permission:importNomtari");       
 });
  