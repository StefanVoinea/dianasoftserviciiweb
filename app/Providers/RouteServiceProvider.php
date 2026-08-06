<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to the "home" route for your application.
     *
     * This is used by Laravel authentication to redirect users after login.
     *
     * @var string
     */
    public const HOME = '/home';
    protected $namespace = 'App\Http\Controllers';
    /**
     * The controller namespace for the application.
     *
     * When present, controller route declarations will automatically be prefixed with this namespace.
     *
     * @var string|null
     */
    // protected $namespace = 'App\\Http\\Controllers';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        $this->configureRateLimiting();

        $this->routes(function () {
            Route::prefix('api')
                ->middleware('api')
                ->namespace($this->namespace)
                ->group(base_path('routes/api.php'));

            Route::middleware('web')
                ->namespace($this->namespace)
                ->group(base_path('routes/web.php'));
        });
    }

    /**
     * Configure the rate limiters for the application.
     *
     * @return void
     */
    protected function configureRateLimiting()
    {
        RateLimiter::for('api', function (Request $request) {
            /*
             * Puntea catre programele locale nu poarta trafic de om.
             *
             * Un singur lot de mesaje din SPV trece prin ea cu doua-trei cereri
             * de fiecare document: panda care aduce comanda, corpul ei si
             * rezultatul. Cu limita obisnuita, chiar aplicatia noastra ii
             * raspundea agentului „bate mai rar la usa" (429) tocmai cand
             * clientul isi descarca mesajele, iar descarcarea se oprea.
             *
             * Ramane totusi o limita, ca un agent stricat sa nu bata la nesfarsit.
             */
            if ($request->is('api/punte/*')) {
                return Limit::perMinute(1200)->by($request->ip());
            }

            return Limit::perMinute(60)->by(optional($request->user())->id ?: $request->ip());
        });
    }
}
