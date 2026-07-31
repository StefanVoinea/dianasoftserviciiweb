<?php

    Route::middleware("auth:api")->group(function () {
	    Route::post("/notificationuser", "Api\NotificationuserController@indexPaginat")
            ->middleware("permission:viewNotificationuser");
        Route::get("/notificationuser", "Api\NotificationuserController@index")
            ->middleware("permission:viewNotificationuser");
	    Route::get("/notificationuser/show/{notificationuser}", "Api\NotificationuserController@show")
            ->middleware("permission:viewNotificationuser");

	    Route::post("/notificationuser/store", "Api\NotificationuserController@store")
            ->middleware("permission:addNotificationuser");

	    Route::post("/notificationuser/delete/{notificationuser}", "Api\NotificationuserController@destroy")
            ->middleware("permission:deleteNotificationuser");

	    Route::post("/notificationuser/edit/{notificationuser}", "Api\NotificationuserController@update")
            ->middleware("permission:editNotificationuser");
      Route::get("/notificationuser/export", "Api\NotificationuserController@export")
            ->middleware("permission:exportNotificationuser");  

         Route::post("/notificationuser/import", "Api\NotificationuserController@import")
                     ->middleware("permission:importNotificationuser");       
 });
  