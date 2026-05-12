
  <?php

    Route::middleware("auth:api")->group(function () {
	    Route::post("/notificationlog", "Api\NotificationlogController@indexPaginat")
            ->middleware("permission:viewNotificationlog");
        Route::get("/notificationlog", "Api\NotificationlogController@index")
            ->middleware("permission:viewNotificationlog");

         Route::get("/notificariusercurent", "Api\NotificationlogController@notificariusercurent")
            ->middleware("permission:viewNotificationlog");   
        Route::post("/markasread", "Api\NotificationlogController@markAsRead")
            ->middleware("permission:viewNotificationlog");       
	    Route::get("/notificationlog/show/{notificationlog}", "Api\NotificationlogController@show")
            ->middleware("permission:viewNotificationlog");

	    Route::post("/notificationlog/store", "Api\NotificationlogController@store")
            ->middleware("permission:addNotificationlog");

	    Route::post("/notificationlog/delete/{notificationlog}", "Api\NotificationlogController@destroy")
            ->middleware("permission:deleteNotificationlog");

	    Route::post("/notificationlog/edit/{notificationlog}", "Api\NotificationlogController@update")
            ->middleware("permission:editNotificationlog");
      Route::get("/notificationlog/export", "Api\NotificationlogController@export")
            ->middleware("permission:exportNotificationlog");  

         Route::post("/notificationlog/import", "Api\NotificationlogController@import")
                     ->middleware("permission:importNotificationlog");       
 });
  