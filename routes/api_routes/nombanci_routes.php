
  <?php

    Route::middleware("auth:api")->group(function () {
	    Route::post("/nombanci", "Api\NombanciController@indexPaginat")
            ->middleware("permission:viewNombanci");
          Route::get("/nombanci", "Api\NombanciController@index")
            ->middleware("permission:viewNombanci");      
      Route::post("/nombanci/verifica", "Api\NombanciController@verifica")
            ->middleware("permission:viewNombanci");
      
	    Route::get("/nombanci/show/{nombanci}", "Api\NombanciController@show")
            ->middleware("permission:viewNombanci");

	    Route::post("/nombanci/store", "Api\NombanciController@store")
            ->middleware("permission:addNombanci");

	    Route::post("/nombanci/delete/{nombanci}", "Api\NombanciController@destroy")
            ->middleware("permission:deleteNombanci");

	    Route::post("/nombanci/edit/{nombanci}", "Api\NombanciController@update")
            ->middleware("permission:editNombanci");
      Route::get("/nombanci/export", "Api\NombanciController@export")
            ->middleware("permission:exportNombanci");  

         Route::post("/nombanci/import", "Api\NombanciController@import")
                     ->middleware("permission:importNombanci");       
 });
  