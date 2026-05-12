 
  <?php
Route::middleware("auth:api")->group(function () {

      Route::post("/acord_gdpr_word", "Api\DocumentController@acordGDPR_WORD")
            ->middleware("permission:viewContract");
      Route::post("/downloadPDF", "Api\DocumentController@downloadPDF")
            ->middleware("permission:viewContract");
      Route::post("/notificare_word", "Api\DocumentController@notificare_WORD")
            ->middleware("permission:viewContract");
      Route::post("/exportXLS", "Api\DocumentController@exportXLS")
            ->middleware("permission:exportExcel");  

  });