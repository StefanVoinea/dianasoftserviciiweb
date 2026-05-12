
  <?php

    Route::middleware("auth:api")->group(function () {
	    Route::post("/litigiiparti", "Api\LitigiipartiController@indexPaginat")
            ->middleware("permission:viewLitigiiparti");
        Route::get("/litigiiparti", "Api\LitigiipartiController@index")
            ->middleware("permission:viewLitigiiparti");
	    Route::get("/litigiiparti/show/{litigiiparti}", "Api\LitigiipartiController@show")
            ->middleware("permission:viewLitigiiparti");

	    Route::post("/litigiiparti/store", "Api\LitigiipartiController@store")
            ->middleware("permission:addLitigiiparti");

	    Route::post("/litigiiparti/delete/{litigiiparti}", "Api\LitigiipartiController@destroy")
            ->middleware("permission:deleteLitigiiparti");

	    Route::post("/litigiiparti/edit/{litigiiparti}", "Api\LitigiipartiController@update")
            ->middleware("permission:editLitigiiparti");
      Route::get("/litigiiparti/export", "Api\LitigiipartiController@export")
            ->middleware("permission:exportLitigiiparti");  

         Route::post("/litigiiparti/import", "Api\LitigiipartiController@import")
                     ->middleware("permission:importLitigiiparti");       
 });
  