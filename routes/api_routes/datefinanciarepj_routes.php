
  <?php

    Route::middleware("auth:api")->group(function () {
	    Route::post("/datefinanciarepj", "Api\DatefinanciarepjController@indexPaginat")
            ->middleware("permission:viewDatefinanciarepj");
        Route::get("/datefinanciarepj", "Api\DatefinanciarepjController@index")
            ->middleware("permission:viewDatefinanciarepj");
	    Route::get("/datefinanciarepj/show/{datefinanciarepj}", "Api\DatefinanciarepjController@show")
            ->middleware("permission:viewDatefinanciarepj");

	    Route::post("/datefinanciarepj/store", "Api\DatefinanciarepjController@store")
            ->middleware("permission:addDatefinanciarepj");

	    Route::post("/datefinanciarepj/delete/{datefinanciarepj}", "Api\DatefinanciarepjController@destroy")
            ->middleware("permission:deleteDatefinanciarepj");

	    Route::post("/datefinanciarepj/edit/{datefinanciarepj}", "Api\DatefinanciarepjController@update")
            ->middleware("permission:editDatefinanciarepj");
      Route::get("/datefinanciarepj/export", "Api\DatefinanciarepjController@export")
            ->middleware("permission:exportDatefinanciarepj");  

         Route::post("/datefinanciarepj/import", "Api\DatefinanciarepjController@import")
                     ->middleware("permission:importDatefinanciarepj");       
 });
  