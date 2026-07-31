<?php

    Route::middleware("auth:api")->group(function () {
       Route::post("/gestiune", "Api\GestiuneController@indexPaginat")
            ->middleware("permission:viewGestiune");
	    Route::get("/gestiune", "Api\GestiuneController@index")
            ->middleware("permission:viewGestiune");
        Route::get("/gestiune/gestiunipermise", "Api\GestiuneController@gestiuniPermise")
             ->middleware("permission:viewGestiune");
	    Route::get("/gestiune/show/{gestiune}", "Api\GestiuneController@show")
            ->middleware("permission:viewGestiune");

	    Route::post("/gestiune/store", "Api\GestiuneController@store")
            ->middleware("permission:addGestiune");

	    Route::post("/gestiune/delete/{gestiune}", "Api\GestiuneController@destroy")
            ->middleware("permission:deleteGestiune");

	    Route::post("/gestiune/edit/{gestiune}", "Api\GestiuneController@update")
            ->middleware("permission:editGestiune");
      Route::post("/raportdegestiune", "Api\GestiuneController@raportdegestiune")
            ->middleware("permission:viewArticol");
      Route::post("/fisademagazie", "Api\GestiuneController@fisademagazie")
            ->middleware("permission:viewArticol");
 });
  