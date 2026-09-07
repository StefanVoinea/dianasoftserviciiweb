<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\DB;

/**
 * [2026-09-01] Pune in sesiune utilizatorul si firma pentru cererile MCP.
 *
 * Rutele MCP nu trec prin `permission:*`, care e locul unde aplicatia pune de
 * obicei `company_id` (din antetul trimis de interfata). Fara el, toate
 * interogarile ar filtra pe company_id NULL si n-ar gasi nimic.
 *
 * Sta ca middleware, nu in constructorul controllerului, pentru ca endpointul
 * remote instantiaza controllerul direct — iar middleware-ul din constructor
 * ruleaza doar cand ruta e servita de router.
 */
class ContextMcp
{
    public function handle($request, Closure $next)
    {
        $user = $request->user();
        if ($user) {
            session()->put("user_id", $user->id);
        }

        $firma = $request->header("AuthorizationHeader");
        if (!$firma && $user) {
            $firma = DB::table("company_user")->where("user_id", $user->id)->value("company_id");
        }
        session()->put("company_id", $firma ?: 1);

        return $next($request);
    }
}
