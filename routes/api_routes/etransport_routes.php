<?php

    Route::middleware("auth:api")->group(function () {
	    Route::post("/etransport/upload", "Api\EtransportController@upload")
            ->middleware("permission:uploadEtransport");

	    Route::get("/etransport/lista", "Api\EtransportController@lista")
            ->middleware("permission:viewEtransport");

	    Route::get("/etransport/stare", "Api\EtransportController@stareMesaj")
            ->middleware("permission:viewEtransport");

	    Route::get("/etransport/info", "Api\EtransportController@info")
            ->middleware("permission:viewEtransport");

        // ETRANSPORT Tokens management
        Route::post("/etransporttokens", "Api\EtransporttokensController@indexPaginat")
            ->middleware("permission:viewEtransporttokens");

        Route::get("/etransporttokens/show/{etransporttoken}", "Api\EtransporttokensController@show")
            ->middleware("permission:viewEtransporttokens");

        Route::post("/etransporttokens/store", "Api\EtransporttokensController@store")
            ->middleware("permission:addEtransporttokens");

        Route::post("/etransporttokens/edit/{etransporttoken}", "Api\EtransporttokensController@update")
            ->middleware("permission:editEtransporttokens");

        Route::post("/etransporttokens/delete/{etransporttoken}", "Api\EtransporttokensController@destroy")
            ->middleware("permission:deleteEtransporttokens");

        Route::get("/etransporttokens/export", "Api\EtransporttokensController@export")
            ->middleware("permission:exportEtransporttokens");

        Route::post("/etransporttokens/import", "Api\EtransporttokensController@import")
            ->middleware("permission:importEtransporttokens");
 });