<?php

/**
 * [2026-09-04] Accesul MCP — varianta stdio (procesul Node din `mcp-dianasoft`).
 *
 * Autentificare: token personal Passport, deci fiecare modificare e atribuita
 * unui utilizator real si apare in `activities` cu numele lui. Token-ul se poate
 * revoca oricand fara sa afecteze pe altcineva.
 *
 * NU exista ruta de stergere. Nu se adauga una.
 */
Route::middleware(["auth:api", "mcp.context"])->prefix("mcp")->group(function () {
    Route::get("/tabele",      "Api\McpController@tabele");
    Route::post("/structura",  "Api\McpController@structura");
    Route::post("/cauta",      "Api\McpController@cauta");
    Route::post("/modifica",   "Api\McpController@modifica");
    Route::post("/adauga",     "Api\McpController@adauga");
    Route::get("/operatii",    "Api\McpController@operatii");
    Route::post("/anuleaza",   "Api\McpController@anuleaza");
});
