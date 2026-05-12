
  <?php

    Route::middleware("auth:api")->group(function () {
         Route::post("/calendareventsusercurent", "Api\CalendareventController@calendareventsusercurent")
            ->middleware("permission:viewCalendarevent");
	    Route::post("/calendarevent", "Api\CalendareventController@indexPaginat")
            ->middleware("permission:viewCalendarevent");
        Route::get("/calendarevent", "Api\CalendareventController@index")
            ->middleware("permission:viewCalendarevent");
	    Route::get("/calendarevent/show/{calendarevent}", "Api\CalendareventController@show")
            ->middleware("permission:viewCalendarevent");

	    Route::post("/calendarevent/store", "Api\CalendareventController@store")
            ->middleware("permission:addCalendarevent");

	    Route::post("/calendarevent/delete/{calendarevent}", "Api\CalendareventController@destroy")
            ->middleware("permission:deleteCalendarevent");

	    Route::post("/calendarevent/edit/{calendarevent}", "Api\CalendareventController@update")
            ->middleware("permission:editCalendarevent");
      Route::get("/calendarevent/export", "Api\CalendareventController@export")
            ->middleware("permission:exportCalendarevent");  

         Route::post("/calendarevent/import", "Api\CalendareventController@import")
                     ->middleware("permission:importCalendarevent");       
 });
  