<?php

namespace App\Http\Middleware;

use App\Support\ContextUtilizator;
use Closure;

/**
 * Administrarea conturilor din firma clientului. O poate face administratorul
 * firmei — si, peste el, administratorul aplicatiei.
 */
class AdministratorClient
{
    public function handle($request, Closure $next)
    {
        if (ContextUtilizator::esteSuperAdministrator() || ContextUtilizator::esteAdministratorClient()) {
            return $next($request);
        }

        return response()->json([
            'success' => false,
            'message' => 'Doar administratorul firmei poate gestiona utilizatorii.',
        ], 403);
    }
}
