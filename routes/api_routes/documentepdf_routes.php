<?php

    Route::middleware("auth:api")->group(function () {
	    Route::post("/documentepdf", "Api\DocumentepdfController@indexPaginat")
            ->middleware("permission:viewDocumentepdf");
         Route::post("/documentepdfarhiva", "Api\DocumentepdfController@indexPaginatArhiva")
            ->middleware("permission:viewDocumentepdf");
        Route::get("/documentepdf", "Api\DocumentepdfController@index")
            ->middleware("permission:viewDocumentepdf");
	    Route::post("/documentepdf/show", "Api\DocumentepdfController@show")
            ->middleware("permission:viewDocumentepdf");

	    Route::post("/documentepdf/store", "Api\DocumentepdfController@store")
            ->middleware("permission:addDocumentepdf");
         Route::post("/documentepdf/abrogare", "Api\DocumentepdfController@abrogare")
            ->middleware("permission:editDocumentepdf");    
	    Route::post("/documentepdf/delete/{documentepdf}", "Api\DocumentepdfController@destroy")
            ->middleware("permission:deleteDocumentepdf");

	    Route::post("/documentepdf/edit/{documentepdf}", "Api\DocumentepdfController@update")
            ->middleware("permission:editDocumentepdf");
      Route::get("/documentepdf/export", "Api\DocumentepdfController@export")
            ->middleware("permission:exportDocumentepdf");  

         Route::post("/documentepdf/import", "Api\DocumentepdfController@import")
                     ->middleware("permission:importDocumentepdf");     
        Route::post("/documentepdf/uploadfile/{documentepdf}", "Api\DocumentepdfController@uploadfile")
                     ->middleware("permission:viewDocumentepdf");                
 });
  