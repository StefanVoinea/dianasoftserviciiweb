<?php

namespace App\Http\Middleware;

use App\Models\Ipautorizat;
use App\Models\User;
use App\Notifications\AlerteazaAdministrator;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class IpMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $ipautorizat=Ipautorizat::where("ip",$request->ip())->get()->first();
        
        if ($ipautorizat) {
           return $next($request);
        
        }else{
            $user=User::where("id",1)->get()->first();
            $mesaj="ACCES NEAUTORIZAT DE LA IP ".$request->ip()." ".$request->path();
            try{
                $mesaj2=implode(" ",$request->toArray());
            }catch(\Exception $e){
             $mesaj2="";

            }
            
            $user->notify(new AlerteazaAdministrator($mesaj,$mesaj2));
          
            return redirect()->away('https://www.whatismyip.com/');
        }

    }
}

