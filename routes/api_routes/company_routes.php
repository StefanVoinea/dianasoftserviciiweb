<?php

    Route::middleware("auth:api")->group(function () {
	    Route::get("/company", "Api\CompanyController@index")
	    		->middleware('permission:viewCompany');
	    Route::post("/company", "Api\CompanyController@indexPaginat")
	    		->middleware('permission:viewCompany');
	    Route::get("/company/show/{company}", "Api\CompanyController@show")
	    		->middleware('permission:viewCompany');

	    Route::post("/company/store", "Api\CompanyController@store")
	    		->middleware('permission:addCompany');

	    Route::post("/company/delete/{company}", "Api\CompanyController@destroy")
	    		->middleware('permission:deleteCompany');

	    Route::post("/company/edit/{company}", "Api\CompanyController@update")
	    		->middleware('permission:editCompany');
 });
  