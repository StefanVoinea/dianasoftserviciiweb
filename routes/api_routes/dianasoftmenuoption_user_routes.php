
  <?php

    Route::middleware("auth:api")->group(function () {
	    Route::get("/dianasoftmenuoption_user", "Api\DianaSoftMenuOption_UserController@index")
	          ->middleware('permission:viewDianasoftmenuoption_user');

	    Route::get("/dianasoftmenuoption_user/show/{dianasoftmenuoption_user}", "Api\DianaSoftMenuOption_UserController@show")
	          ->middleware('permission:viewDianasoftmenuoption_user');

	    Route::post("/dianasoftmenuoption_user/store", "Api\DianaSoftMenuOption_UserController@store")
	          ->middleware('permission:addDianasoftmenuoption_user');

	    Route::post("/dianasoftmenuoption_user/delete/{dianasoftmenuoption_user}", "Api\DianaSoftMenuOption_UserController@destroy")
	          ->middleware('permission:deleteDianasoftmenuoption_user');
	          
	    Route::post("/dianasoftmenuoption_user/edit/{dianasoftmenuoption_user}", "Api\DianaSoftMenuOption_UserController@update")
	         ->middleware('permission:editDianasoftmenuoption_user');
 });
  