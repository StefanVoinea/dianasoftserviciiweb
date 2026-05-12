
  <?php

    Route::middleware("auth:api")->group(function () {
	    Route::post("/cfg", "Api\CfgController@indexPaginat")
            ->middleware("permission:viewCfg");
        Route::get("/cfg", "Api\CfgController@index")
            ->middleware("permission:viewCfg");
	    Route::get("/cfg/show/{cfg}", "Api\CfgController@show")
            ->middleware("permission:viewCfg");

	    Route::post("/cfg/store", "Api\CfgController@store")
            ->middleware("permission:addCfg");

	    Route::post("/cfg/delete/{cfg}", "Api\CfgController@destroy")
            ->middleware("permission:deleteCfg");

	    Route::post("/cfg/edit/{cfg}", "Api\CfgController@update")
            ->middleware("permission:editCfg");
      Route::get("/cfg/export", "Api\CfgController@export")
            ->middleware("permission:exportCfg");  

         Route::post("/cfg/import", "Api\CfgController@import")
                     ->middleware("permission:importCfg");       
 });
  