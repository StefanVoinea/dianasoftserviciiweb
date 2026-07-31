<?php

namespace App\Http\Middleware;

use App\Support\ContextUtilizator;
use Closure;

/**
 * Administrarea clientilor — un singur cont, cel din configuratie
 * (SUPER_ADMIN_EMAIL). Nu tine de drepturi si nu se poate acorda din aplicatie.
 */
class AdministratorServiciu
{
    public function handle($request, Closure $next)
    {
        if (!$request->user()) {
            return response()->json(['success' => false, 'message' => 'Autentificare necesară.'], 401);
        }

        if (!ContextUtilizator::esteSuperAdministrator()) {
            return response()->json([
                'success' => false,
                'message' => 'Această zonă este rezervată administratorului aplicației.',
            ], 403);
        }

        return $next($request);
    }
}
