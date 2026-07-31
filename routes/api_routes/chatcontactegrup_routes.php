<?php

    Route::middleware("auth:api")->group(function () {
	    Route::post("/chatcontactegrup", "Api\ChatcontactegrupController@indexPaginat")
            ->middleware("permission:viewChatcontactegrup");
        Route::get("/chatcontactegrup", "Api\ChatcontactegrupController@index")
            ->middleware("permission:viewChatcontactegrup");
	    Route::get("/chatcontactegrup/show/{chatcontactegrup}", "Api\ChatcontactegrupController@show")
            ->middleware("permission:viewChatcontactegrup");

	    Route::post("/chatcontactegrup/store", "Api\ChatcontactegrupController@store")
            ->middleware("permission:addChatcontactegrup");

	    Route::post("/chatcontactegrup/delete/{chatcontactegrup}", "Api\ChatcontactegrupController@destroy")
            ->middleware("permission:deleteChatcontactegrup");

	    Route::post("/chatcontactegrup/edit/{chatcontactegrup}", "Api\ChatcontactegrupController@update")
            ->middleware("permission:editChatcontactegrup");
      Route::get("/chatcontactegrup/export", "Api\ChatcontactegrupController@export")
            ->middleware("permission:exportChatcontactegrup");  

         Route::post("/chatcontactegrup/import", "Api\ChatcontactegrupController@import")
                     ->middleware("permission:importChatcontactegrup");       
 });
  