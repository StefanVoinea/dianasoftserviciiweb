<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Log;

class CheckRoutePermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $permision)
    {
        
        session()->put('company_id', $request->header('AuthorizationHeader'));
        session()->put('user_id', $request->user()->id);
        
        $company_id=session('company_id');
       
        
        if(!$request->user()->hasPermissionToCompany($company_id))
        {
            return response()->json(['error' => 'Not authorized'], 401);
        }

        if (! $request->user()->hasPermission($permision,$company_id) && !$request->user()->isOwner()) {
            
            return response()->json(['error' => 'Not authorized'], 401);
        }
        return $next($request);
    }
}
