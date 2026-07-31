<?php

    Route::middleware("auth:api")->group(function () {
	    Route::get("/company_user", "Api\CompanyUserController@index")
	    		->middleware('permission:viewCompanyUser');

	    Route::get("/company_user/show/{company_user}", "Api\CompanyUserController@show")
	    		->middleware('permission:viewCompanyUser');

	    Route::post("/company_user/store", "Api\CompanyUserController@store")
	    		->middleware('permission:addCompanyUser');

	    Route::post("/company_user/delete/{company_user}", "Api\CompanyUserController@destroy")
	    		->middleware('permission:deleteCompanyUser');

	    Route::post("/company_user/edit/{company_user}", "Api\CompanyUserController@update")
	    		->middleware('permission:editCompanyUser');
 });
  