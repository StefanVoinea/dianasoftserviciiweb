<?php

// API-ul de consultare a datelor firmelor (Reg. Com.) pentru celelalte aplicatii DianaSoft: cheie de aplicatie
// (regcom.cheie, config/regcom.php), fara utilizator; doar citire. Vezi RegcomApiController.
Route::middleware(['regcom.cheie', 'throttle:600,1'])->prefix('regcom')->group(function () {
    Route::get('/cui/{cui}', 'Api\RegcomApiController@dupaCui');
    Route::get('/cauta', 'Api\RegcomApiController@cauta');
    Route::post('/lista', 'Api\RegcomApiController@lista');
    Route::get('/{id}', 'Api\RegcomApiController@arata')->where('id', '[0-9]+');
});
