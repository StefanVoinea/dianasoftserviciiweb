
  <?php

    Route::middleware("auth:api")->group(function () {
	    Route::post("/chat", "Api\ChatController@indexPaginat")
            ->middleware("permission:viewChat");
        Route::get("/chat", "Api\ChatController@index")
            ->middleware("permission:viewChat");
	    Route::get("/chat/show/{chat}", "Api\ChatController@show")
            ->middleware("permission:viewChat");

	    Route::post("/chat/store", "Api\ChatController@store")
            ->middleware("permission:addChat");

	    Route::post("/chat/delete/{chat}", "Api\ChatController@destroy")
            ->middleware("permission:deleteChat");

	    Route::post("/chat/edit/{chat}", "Api\ChatController@update")
            ->middleware("permission:editChat");
      Route::get("/chat/export", "Api\ChatController@export")
            ->middleware("permission:exportChat");  
       Route::get("/chat/contacte", "Api\ChatController@contacte")
            ->middleware("permission:viewChat"); 
            Route::get("/chat/contacteChat", "Api\ChatController@contacteChat")
            ->middleware("permission:viewChat");
          Route::post("/chat/chats", "Api\ChatController@chats")
            ->middleware("permission:viewChat"); 
       Route::post("/chat/modificaStatus", "Api\ChatController@modificaStatus")
            ->middleware("permission:viewChat"); 
       Route::post("/chat/sendChatMessage", "Api\ChatController@sendChatMessage")
            ->middleware("permission:viewChat"); 
        Route::post("/chat/mark-all-seen", "Api\ChatController@markAllSeen")
            ->middleware("permission:viewChat");
        Route::post("/chat/set-pinned", "Api\ChatController@setPinned")
            ->middleware("permission:viewChat");
         Route::post("/chat/import", "Api\ChatController@import")
                     ->middleware("permission:importChat");       
 });
  