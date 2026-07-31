<?php

    Route::middleware("auth:api")->group(function () {
        Route::get("/utilizatori", "Api\UtilizatoriController@index")
            ->middleware("permission:viewUtilizatori");
             Route::get("/utilizatoriOrdonati", "Api\UtilizatoriController@indexOrdonat")
            ->middleware("permission:viewUtilizatori");
       Route::get("/utilizatoriincasari", "Api\UtilizatoriController@utilizatori")
            ->middleware("permission:viewArticol");
     Route::get("/utilizatori/show/{user}", "Api\UtilizatoriController@show")
            ->middleware("permission:viewUtilizatori");
     Route::post("/utilizatori/cookiesLocal", "Api\UtilizatoriController@cookiesLocal")
             ->middleware("permission:viewArticol");
     Route::post("/utilizatori", "Api\UtilizatoriController@indexPaginat")
            ->middleware("permission:viewUtilizatori");
     Route::post("/utilizatori/updatedrepturi", "Api\UtilizatoriController@updatedrepturi")
            ->middleware("permission:editUtilizatori");   
        Route::post("/utilizatori/store", "Api\UtilizatoriController@store")
            ->middleware("permission:addUtilizatori");
     Route::post("/utilizatori/storegroup", "Api\UtilizatoriController@storegroup")
            ->middleware("permission:addUtilizatori");
        Route::post("/utilizatori/delete/{user}", "Api\UtilizatoriController@destroy")
            ->middleware("permission:deleteUtilizatori");
       Route::post("/utilizatori/fisautilizator/{user}", "Api\UtilizatoriController@fisautilizator")
            ->middleware("permission:viewUtilizatori");
        Route::post("/utilizatori/situatieDrepturiUtilizatori", "Api\UtilizatoriController@situatieDrepturiUtilizatori")
            ->middleware("permission:viewUtilizatori");
        Route::post("/utilizatori/edit/{user}", "Api\UtilizatoriController@update")
            ->middleware("permission:editUtilizatori");
       Route::post("/utilizatori/modificaparola", "Api\UtilizatoriController@modificaParola")
            ->middleware("permission:viewArticol");     
      Route::post("/utilizatori/copy", "Api\UtilizatoriController@copyDrepturi")
            ->middleware("permission:editUtilizatori");
         Route::post("/utilizatori/permisiunifiltrate", "Api\UtilizatoriController@permisiunifiltrate")
            ->middleware("permission:editUtilizatori");    

       Route::post("/utilizatori/importAvatar", "Api\UtilizatoriController@importAvatar")
                     ->middleware("permission:viewUtilizatori");      
        Route::post("/utilizatori/modificaAvatar", "Api\UtilizatoriController@modificaAvatar")
                     ->middleware("permission:viewUtilizatori");       
     });