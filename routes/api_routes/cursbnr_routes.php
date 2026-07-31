<?php

    Route::middleware("auth:api")->group(function () {
	    Route::post("/cursbnr", "Api\CursbnrController@indexPaginat")
            ->middleware("permission:viewCursbnr");
        Route::get("/cursbnr", "Api\CursbnrController@index")
            ->middleware("permission:viewCursbnr");
      Route::post("/cursbnrptrziua", "Api\CursbnrController@cursbnrptrziua")
            ->middleware("permission:viewCursbnr");
	    Route::get("/cursbnr/show/{cursbnr}", "Api\CursbnrController@show")
            ->middleware("permission:viewCursbnr");

	    Route::post("/cursbnr/store", "Api\CursbnrController@store")
            ->middleware("permission:addCursbnr");

	    Route::post("/cursbnr/delete/{cursbnr}", "Api\CursbnrController@destroy")
            ->middleware("permission:deleteCursbnr");

	    Route::post("/cursbnr/edit/{cursbnr}", "Api\CursbnrController@update")
            ->middleware("permission:editCursbnr");
      Route::get("/cursbnr/export", "Api\CursbnrController@export")
            ->middleware("permission:exportCursbnr");  

         Route::post("/cursbnr/import", "Api\CursbnrController@import")
                     ->middleware("permission:importCursbnr");       
 });
  