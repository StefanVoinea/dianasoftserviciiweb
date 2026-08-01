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
     /*
      * Nomenclatoarele de pornire (județe, țări, opțiuni) le cere aplicația
      * imediat după autentificare, pentru oricine intră. Poarta de aici cerea
      * dreptul „viewArticol", care e al altui modul: utilizatorii unui client
      * SPV nu-l au, iar autentificarea lor se oprea cu „Not authorized" chiar
      * după parolă. Se cere doar societatea aleasă, ca peste tot.
      */
     Route::post("/utilizatori/cookiesLocal", "Api\UtilizatoriController@cookiesLocal")
             ->middleware("companie.anaf");
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
       /*
        * Parola proprie si-o schimba oricine, fara alt drept: e a lui, iar
        * cand a expirat, aplicatia il trimite aici inainte de orice altceva.
        * Dreptul „viewArticol", cerut inainte, il lasa pe un utilizator de
        * client blocat: nu putea nici salva parola, nici merge mai departe.
        */
       Route::post("/utilizatori/modificaparola", "Api\UtilizatoriController@modificaParola");
      Route::post("/utilizatori/copy", "Api\UtilizatoriController@copyDrepturi")
            ->middleware("permission:editUtilizatori");
         Route::post("/utilizatori/permisiunifiltrate", "Api\UtilizatoriController@permisiunifiltrate")
            ->middleware("permission:editUtilizatori");    

       Route::post("/utilizatori/importAvatar", "Api\UtilizatoriController@importAvatar")
                     ->middleware("permission:viewUtilizatori");      
        Route::post("/utilizatori/modificaAvatar", "Api\UtilizatoriController@modificaAvatar")
                     ->middleware("permission:viewUtilizatori");       
     });