
  <?php

    Route::middleware("auth:api")->group(function () {
	    Route::post("/litigiu", "Api\LitigiuController@indexPaginat")
            ->middleware("permission:viewLitigiu");
        Route::get("/litigiu", "Api\LitigiuController@index")
            ->middleware("permission:viewLitigiu");
	    Route::get("/litigiu/show/{litigiu}", "Api\LitigiuController@show")
            ->middleware("permission:viewLitigiu");

	    Route::post("/litigiu/store", "Api\LitigiuController@store")
            ->middleware("permission:addLitigiu");
        Route::post("/situatielitigii", "Api\LitigiuController@situatielitigii")
            ->middleware("permission:viewLitigiu");
	    Route::post("/litigiu/delete/{litigiu}", "Api\LitigiuController@destroy")
            ->middleware("permission:deleteLitigiu");
        Route::post("/litigiu/preiaNumarDosar", "Api\LitigiuController@preiaNumarDosar")
            ->middleware("permission:viewLitigiu");
	    Route::post("/litigiu/edit/{litigiu}", "Api\LitigiuController@update")
            ->middleware("permission:editLitigiu");
      Route::get("/litigiu/export", "Api\LitigiuController@export")
            ->middleware("permission:exportLitigiu");  

         Route::post("/litigiu/import", "Api\LitigiuController@import")
                     ->middleware("permission:importLitigiu");       
 });
  