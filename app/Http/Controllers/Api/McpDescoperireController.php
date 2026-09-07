<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

/**
 * [2026-09-01] Documentele de descoperire pentru conectorul remote.
 *
 * Cand adaugi un "custom connector", Claude intreaba intai serverul cum se face
 * autentificarea. Fara documentele astea primea pagina SPA-ului (HTML, HTTP 200),
 * nu JSON — de aia pasul "Find the authorization server" iesea *Skipped*.
 *
 * Endpointurile de OAuth sunt cele pe care le are deja Passport.
 */
class McpDescoperireController extends Controller
{
    /** RFC 9728 — cine protejeaza resursa si unde e serverul de autorizare. */
    public function resursaProtejata()
    {
        return response()->json([
            "resource"                 => url("/mcp"),
            "authorization_servers"    => [url("/")],
            "bearer_methods_supported" => ["header"],
            "resource_name"            => "DianaSoft — servicii web (SPV, ANAF, e-Factura)",
            "resource_documentation"   => url("/"),
        ]);
    }

    /** RFC 8414 — metadatele serverului de autorizare (Passport). */
    public function serverAutorizare()
    {
        return response()->json([
            "issuer"                                => url("/"),
            "authorization_endpoint"                => url("/oauth/authorize"),
            "token_endpoint"                        => url("/oauth/token"),
            "registration_endpoint"                 => url("/oauth/register"),
            "scopes_supported"                      => ["*"],
            "response_types_supported"              => ["code"],
            "grant_types_supported"                 => ["authorization_code", "refresh_token", "client_credentials"],
            "token_endpoint_auth_methods_supported" => ["client_secret_post", "client_secret_basic", "none"],
            "code_challenge_methods_supported"      => ["S256"],
        ]);
    }
}
