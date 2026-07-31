<?php

    Route::middleware("auth:api")->group(function () {

	    Route::post("/task", "Api\TaskController@indexPaginat")
            ->middleware("permission:viewTask");
        Route::get("/task", "Api\TaskController@index")
            ->middleware("permission:viewTask");
         Route::get("/taskuri", "Api\TaskController@taskuriusercurent")
            ->middleware("permission:viewTask");   
	    Route::get("/task/show/{task}", "Api\TaskController@show")
            ->middleware("permission:viewTask");

	    Route::post("/task/store", "Api\TaskController@store")
            ->middleware("permission:addTask");

	    Route::post("/task/delete/{task}", "Api\TaskController@destroy")
            ->middleware("permission:deleteTask");

	    Route::post("/task/edit/{task}", "Api\TaskController@update")
            ->middleware("permission:editTask");
      Route::get("/task/export", "Api\TaskController@export")
            ->middleware("permission:exportTask");  

         Route::post("/task/import", "Api\TaskController@import")
                     ->middleware("permission:importTask");       
 });
  