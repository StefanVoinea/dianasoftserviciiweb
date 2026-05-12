
  <?php

    Route::middleware("auth:api")->group(function () {
	    Route::post("/portcomsms", "Api\PortcomsmsController@indexPaginat")
            ->middleware("permission:viewPortcomsms");
        Route::get("/portcomsms", "Api\PortcomsmsController@index")
            ->middleware("permission:viewPortcomsms");
	    Route::get("/portcomsms/show/{portcomsms}", "Api\PortcomsmsController@show")
            ->middleware("permission:viewPortcomsms");

	    Route::post("/portcomsms/store", "Api\PortcomsmsController@store")
            ->middleware("permission:addPortcomsms");

	    Route::post("/portcomsms/delete/{portcomsms}", "Api\PortcomsmsController@destroy")
            ->middleware("permission:deletePortcomsms");

	    Route::post("/portcomsms/edit/{portcomsms}", "Api\PortcomsmsController@update")
            ->middleware("permission:editPortcomsms");
      Route::get("/portcomsms/export", "Api\PortcomsmsController@export")
            ->middleware("permission:exportPortcomsms");  

         Route::post("/portcomsms/import", "Api\PortcomsmsController@import")
                     ->middleware("permission:importPortcomsms");       
 });
  