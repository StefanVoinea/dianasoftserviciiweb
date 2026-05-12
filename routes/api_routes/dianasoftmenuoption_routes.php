
  <?php

    Route::middleware("auth:api")->group(function () {
	    Route::get("/dianasoftmenuoption", "Api\DianaSoftMenuOptionController@index")
	    		->middleware('permission:viewDianasoftmenuoption');
	     Route::post("/dianasoftmenuoption", "Api\DianaSoftMenuOptionController@indexPaginat")
	    		->middleware('permission:viewDianasoftmenuoption');		
	    Route::get("/dianasoftmenuoption/show/{dianasoftmenuoption}", "Api\DianaSoftMenuOptionController@show")
	    		->middleware('permission:viewDianasoftmenuoption');

	    Route::post("/dianasoftmenuoption/store", "Api\DianaSoftMenuOptionController@store")
	    		->middleware('permission:addDianasoftmenuoption');

	    Route::post("/dianasoftmenuoption/delete/{dianasoftmenuoption}", "Api\DianaSoftMenuOptionController@destroy")
	    		->middleware('permission:deleteDianasoftmenuoption');

	    Route::post("/dianasoftmenuoption/edit/{dianasoftmenuoption}", "Api\DianaSoftMenuOptionController@update")
	    		->middleware('permission:editDianasoftmenuoption');
 });
  