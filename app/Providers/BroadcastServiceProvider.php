<?php

namespace App\Providers;

use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\ServiceProvider;

class BroadcastServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Broadcast::routes();
Broadcast::routes(["prefix" => "api", "middleware" => "auth:api"]);
        Broadcast::channel('dianasoft_serviciiweb', function (){
                return true ;
                // return ! is_null($user->teams->find($teamId));
        });
        Broadcast::channel('channel-test', function (){
                return true ;
                // return ! is_null($user->teams->find($teamId));
        });
        Broadcast::channel('channel', function (){
                return true ;
                // return ! is_null($user->teams->find($teamId));
        });
        require base_path('routes/channels.php');
    }
}
