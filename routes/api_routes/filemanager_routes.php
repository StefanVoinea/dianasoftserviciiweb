
  <?php

    Route::middleware("auth:api")->group(function () {
	    Route::post("/filemanager", "Api\FilemanagerController@indexPaginat")
            ->middleware("permission:viewFilemanager");

        // Route::post("/filemanager?grupa=Teste de angajare", "Api\FilemanagerController@indexPaginat")
        //     ->middleware("permission:viewFilemanager");
    
        Route::get("/filemanager", "Api\FilemanagerController@index")
            ->middleware("permission:viewFilemanager");
	    Route::get("/filemanager/show/{filemanager}", "Api\FilemanagerController@show")
            ->middleware("permission:viewFilemanager");

	    Route::post("/filemanager/store", "Api\FilemanagerController@store")
            ->middleware("permission:addFilemanager");

	    Route::post("/filemanager/delete/{filemanager}", "Api\FilemanagerController@destroy")
            ->middleware("permission:deleteFilemanager");

	    Route::post("/filemanager/edit/{filemanager}", "Api\FilemanagerController@update")
            ->middleware("permission:editFilemanager");
      Route::get("/filemanager/export", "Api\FilemanagerController@export")
            ->middleware("permission:exportFilemanager");  

         Route::post("/filemanager/import", "Api\FilemanagerController@import")
                     ->middleware("permission:importFilemanager");       
         Route::post("/filemanager/uploadfile/{filemanager}", "Api\FilemanagerController@uploadfile")
                     ->middleware("permission:viewFilemanager");    
        Route::post("/filemanager/viewfile/{filemanager}", "Api\FilemanagerController@viewfile")
            ->middleware("permission:viewFilemanager");                             
 });
  