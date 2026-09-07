<?php

use Illuminate\Support\Facades\Route;

/**
 * [2026-09-01] Endpointul MCP remote, la radacina.
 *
 * Se incarca inaintea lui web.php (vezi RouteServiceProvider), altfel catch-all-ul
 * `Route::get('/{any}')` ar raspunde in locul lor cu pagina aplicatiei.
 *
 * Documentele de descoperire sunt publice — asta si e rostul lor, sa spuna unui
 * client nou cum sa se autentifice. Endpointul propriu-zis cere token.
 *
 * NU exista ruta de stergere, la fel ca la varianta stdio.
 */

Route::get("/.well-known/oauth-protected-resource", "Api\McpDescoperireController@resursaProtejata");
Route::get("/.well-known/oauth-protected-resource/mcp", "Api\McpDescoperireController@resursaProtejata");
Route::get("/.well-known/oauth-authorization-server", "Api\McpDescoperireController@serverAutorizare");

// Inregistrarea dinamica a clientului (RFC 7591): conectorul isi creeaza singur
// un client OAuth inainte de a porni autorizarea.
// Endpointul e public prin natura lui, deci e limitat: altfel oricine poate umple
// tabela `oauth_clients` cu randuri. Un client inregistrat oricum nu poate face
// nimic pana cand cineva nu autorizeaza explicit.
Route::post("/oauth/register", "Api\McpInregistrareController@inregistreaza")
    ->middleware("throttle:5,1");

Route::middleware(["auth:api", "mcp.context"])->group(function () {
    Route::post("/mcp", "Api\McpRpcController@rpc");
    // Unii clienti deschid intai un GET, pentru fluxul cu server-sent events.
    // Nu tinem sesiune, deci raspundem ca nu e nimic de ascultat.
    Route::get("/mcp", function () {
        return response()->json(["error" => "Foloseste POST pentru mesaje MCP."], 405);
    });
});
