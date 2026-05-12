<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
foreach (File::allFiles(__DIR__ . '/api_routes') as $route_file) {
  require $route_file->getPathname();
}


Route::post('/login','Api\AuthController@login')->name('login')->middleware(['throttle:60,1','ipcheck']);
Route::post('/registerAPI','Api\AuthController@register');
Route::get("/efacturaparams", "Api\EfacturaparamsController@index"); 
Route::post("/gettoken", "Api\EfacturatokensController@gettoken");
Route::get("/callback", "Api\EfacturatokensController@callback");
Route::middleware('auth:api')->group(function () {

    Route::post('/logoutAPI','Api\AuthController@logout');
    Route::get('/user','Api\AuthController@user');

});
/*Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});*/
