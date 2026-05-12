
  <?php

    Route::middleware("auth:api")->group(function () {
	    Route::post("/monitorizaredocumente", "Api\MonitorizaredocumenteController@indexPaginat")
            ->middleware("permission:viewMonitorizaredocumente");
        Route::get("/monitorizaredocumente", "Api\MonitorizaredocumenteController@index")
            ->middleware("permission:viewMonitorizaredocumente");
	    Route::get("/monitorizaredocumente/show/{monitorizaredocumente}", "Api\MonitorizaredocumenteController@show")
            ->middleware("permission:viewMonitorizaredocumente");

	    Route::post("/monitorizaredocumente/store", "Api\MonitorizaredocumenteController@store")
            ->middleware("permission:addMonitorizaredocumente");

	    Route::post("/monitorizaredocumente/delete/{monitorizaredocumente}", "Api\MonitorizaredocumenteController@destroy")
            ->middleware("permission:deleteMonitorizaredocumente");
         Route::post("/monitorizaredocumente/uploadfile/{monitorizaredocumente}", "Api\MonitorizaredocumenteController@uploadfile")
            ->middleware("permission:addMonitorizaredocumente");    
         Route::post("/monitorizaredocumente/viewfile/{monitorizaredocumente}", "Api\MonitorizaredocumenteController@viewfile")
            ->middleware("permission:viewMonitorizaredocumente"); 
	    Route::post("/monitorizaredocumente/edit/{monitorizaredocumente}", "Api\MonitorizaredocumenteController@update")
            ->middleware("permission:editMonitorizaredocumente");
      Route::get("/monitorizaredocumente/export", "Api\MonitorizaredocumenteController@export")
            ->middleware("permission:exportMonitorizaredocumente");  

         Route::post("/monitorizaredocumente/import", "Api\MonitorizaredocumenteController@import")
                     ->middleware("permission:importMonitorizaredocumente");       
 });
  