
  <?php

    Route::middleware("auth:api")->group(function () {
	    Route::post("/facturiprimiteefactura", "Api\FacturiprimiteefacturaController@indexPaginat")
            ->middleware("permission:viewFacturiprimiteefactura");
        Route::get("/facturiprimiteefactura", "Api\FacturiprimiteefacturaController@index")
            ->middleware("permission:viewFacturiprimiteefactura");
	    Route::get("/facturiprimiteefactura/show/{facturiprimiteefactura}", "Api\FacturiprimiteefacturaController@show")
            ->middleware("permission:viewFacturiprimiteefactura");

	    Route::post("/facturiprimiteefactura/store", "Api\FacturiprimiteefacturaController@store")
            ->middleware("permission:addFacturiprimiteefactura");

	      Route::post("/verificareSPV", "Api\FacturiprimiteefacturaController@verificareSPV")
            ->middleware("permission:addFacturiprimiteefactura");
        Route::post("/importXMLSPV", "Api\FacturiprimiteefacturaController@importXMLSPV")
            ->middleware("permission:addFacturiprimiteefactura");

        Route::post("/facturiprimiteefactura/delete/{facturiprimiteefactura}", "Api\FacturiprimiteefacturaController@destroy")
            ->middleware("permission:deleteFacturiprimiteefactura");
        Route::post("/efacturafp/pdf/{facturiprimiteefactura}", "Api\FacturiprimiteefacturaController@listarepdf")
            ->middleware("permission:viewFacturiprimiteefactura");

	    Route::post("/facturiprimiteefactura/edit/{facturiprimiteefactura}", "Api\FacturiprimiteefacturaController@update")
            ->middleware("permission:editFacturiprimiteefactura");
      Route::get("/facturiprimiteefactura/export", "Api\FacturiprimiteefacturaController@export")
            ->middleware("permission:exportFacturiprimiteefactura");  

         Route::post("/facturiprimiteefactura/import", "Api\FacturiprimiteefacturaController@import")
                     ->middleware("permission:importFacturiprimiteefactura");       
 });
  