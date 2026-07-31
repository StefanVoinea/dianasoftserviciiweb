<?php

    Route::middleware("auth:api")->group(function () {
	    Route::post("/notificationtype", "Api\NotificationtypeController@indexPaginat")
            ->middleware("permission:viewNotificationtype");
        Route::get("/notificationtype", "Api\NotificationtypeController@index")
            ->middleware("permission:viewNotificationtype");
	    Route::get("/notificationtype/show/{notificationtype}", "Api\NotificationtypeController@show")
            ->middleware("permission:viewNotificationtype");

	    Route::post("/notificationtype/store", "Api\NotificationtypeController@store")
            ->middleware("permission:addNotificationtype");

	    Route::post("/notificationtype/delete/{notificationtype}", "Api\NotificationtypeController@destroy")
            ->middleware("permission:deleteNotificationtype");

	    Route::post("/notificationtype/edit/{notificationtype}", "Api\NotificationtypeController@update")
            ->middleware("permission:editNotificationtype");
      Route::get("/notificationtype/export", "Api\NotificationtypeController@export")
            ->middleware("permission:exportNotificationtype");  

         Route::post("/notificationtype/import", "Api\NotificationtypeController@import")
                     ->middleware("permission:importNotificationtype");       
 });
  