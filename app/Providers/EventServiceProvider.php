<?php

namespace App\Providers;

use App\Events\ChatEvents;
use App\Listeners\InsemneazaScrisoareaPlecata;
use Illuminate\Auth\Events\Registered;
use Illuminate\Mail\Events\MessageSent;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],

        /*
         * Scrisorile de marketing se insemneaza ca plecate abia cand serverul de
         * email le-a primit cu adevarat, nu cand au intrat in coada.
         */
        MessageSent::class => [
            InsemneazaScrisoareaPlecata::class,
        ],

    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        //

    //     Event::listen(function (ChatEvents $event) {
          
       
    // });
    }
}
