<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * [2026-09-01] Inregistrarea dinamica a clientului OAuth (RFC 7591).
 *
 * Conectorul Claude isi creeaza singur un client inainte de a porni autorizarea;
 * Passport nu are endpointul asta din fabrica.
 *
 * De ce e acceptabil ca endpointul sa fie public: un client inregistrat nu poate
 * face NIMIC singur. Ca sa obtina un token, cineva trebuie sa se autentifice si
 * sa aprobe explicit accesul pe `/oauth/authorize`. Clientul e doar o pereche de
 * identificatori, nu un drept.
 *
 * Clientii creati asa sunt PUBLICI (fara secret, cu PKCE) si se recunosc dupa
 * numele lor, ca sa poata fi curatati sau revocati in bloc la nevoie.
 */
class McpInregistrareController extends Controller
{
    const PREFIX_NUME = "MCP conector";

    public function inregistreaza(Request $request)
    {
        $redirecturi = (array) ($request->input("redirect_uris") ?? []);
        $redirecturi = array_values(array_filter(array_map("trim", $redirecturi)));

        if (empty($redirecturi)) {
            return response()->json([
                "error"             => "invalid_redirect_uri",
                "error_description" => "Lipseste `redirect_uris`.",
            ], 400);
        }
        foreach ($redirecturi as $uri) {
            if (!filter_var($uri, FILTER_VALIDATE_URL) || !Str::startsWith($uri, "https://")) {
                return response()->json([
                    "error"             => "invalid_redirect_uri",
                    "error_description" => "Adresele de retur trebuie sa fie HTTPS: " . $uri,
                ], 400);
            }
        }

        $nume = self::PREFIX_NUME . " — " . Str::limit($request->input("client_name") ?: "fara nume", 40, "");
        // `oauth_clients.id` e auto_increment in schema asta, nu UUID
        $id = DB::table("oauth_clients")->insertGetId([
            "user_id"                => null,
            "name"                   => $nume,
            "secret"                 => null,          // client public: PKCE, fara secret
            "provider"               => null,
            "redirect"               => implode(",", $redirecturi),
            "personal_access_client" => 0,
            "password_client"        => 0,
            "revoked"                => 0,
            "created_at"             => now(),
            "updated_at"             => now(),
        ]);

        return response()->json([
            "client_id"                  => (string) $id,
            "client_name"                => $nume,
            "redirect_uris"              => $redirecturi,
            "grant_types"                => ["authorization_code", "refresh_token"],
            "response_types"             => ["code"],
            "token_endpoint_auth_method" => "none",
            "client_id_issued_at"        => now()->timestamp,
        ], 201);
    }
}
