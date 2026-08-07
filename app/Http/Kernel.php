<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    /**
     * The application's global HTTP middleware stack.
     *
     * These middleware are run during every request to your application.
     *
     * @var array
     */
    protected $middleware = [
        // \App\Http\Middleware\TrustHosts::class,
        \App\Http\Middleware\TrustProxies::class,
        \Fruitcake\Cors\HandleCors::class,
        \App\Http\Middleware\PreventRequestsDuringMaintenance::class,
        \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,
        \App\Http\Middleware\TrimStrings::class,
        \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
    ];

    /**
     * The application's route middleware groups.
     *
     * @var array
     */
    protected $middlewareGroups = [
        'web' => [
            \App\Http\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            // \Illuminate\Session\Middleware\AuthenticateSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \App\Http\Middleware\VerifyCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],

        'api' => [
            'throttle:api',
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],
    ];

    /**
     * Ordinea in care se aseaza middleware-ele, oricum ar fi scrise pe ruta.
     *
     * Cel mai important rand e „companie.anaf" inaintea legarii din rute.
     *
     * Legarea („SubstituteBindings") cauta modelul dupa id-ul din adresa. Cat
     * timp ea se facea mai devreme, cautarea se facea fara context de client,
     * iar filtrul pe companie — cel care tine datele clientilor despartite — nu
     * se aplica: orice utilizator putea cere, cu un id ghicit, declaratia,
     * solicitarea sau certificatul altui client, iar controlerul le primea de-a
     * gata si nu mai avea de unde sti ca nu sunt ale lui.
     *
     * Asezate asa, contextul e pus inainte, iar un id strain nu mai gaseste
     * nimic: cererea se incheie cu 404, adica exact ce scrie in App\Models     * Concerns\ApartineCompaniei — pana acum scria acolo ceva ce nu era adevarat.
     */
    protected $middlewarePriority = [
        \Illuminate\Cookie\Middleware\EncryptCookies::class,
        \Illuminate\Session\Middleware\StartSession::class,
        \Illuminate\View\Middleware\ShareErrorsFromSession::class,
        \Illuminate\Contracts\Auth\Middleware\AuthenticatesRequests::class,
        \Illuminate\Routing\Middleware\ThrottleRequests::class,
        \Illuminate\Routing\Middleware\ThrottleRequestsWithRedis::class,
        \Illuminate\Session\Middleware\AuthenticateSession::class,
        \App\Http\Middleware\CompanieAnaf::class,
        \Illuminate\Routing\Middleware\SubstituteBindings::class,
        \Illuminate\Auth\Middleware\Authorize::class,
    ];

    /**
     * The application's route middleware.
     *
     * These middleware may be assigned to groups or used individually.
     *
     * @var array
     */
    protected $routeMiddleware = [
        'auth' => \App\Http\Middleware\Authenticate::class,
        'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
        'cache.headers' => \Illuminate\Http\Middleware\SetCacheHeaders::class,
        'can' => \Illuminate\Auth\Middleware\Authorize::class,
        'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
        'password.confirm' => \Illuminate\Auth\Middleware\RequirePassword::class,
        'signed' => \Illuminate\Routing\Middleware\ValidateSignature::class,
        'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
        'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,
        'permission'=>\App\Http\Middleware\CheckRoutePermission::class,
        'ipcheck' => \App\Http\Middleware\IpMiddleware::class,
        // Izolarea pe client a modulului ANAF/SPV
        'companie.anaf' => \App\Http\Middleware\CompanieAnaf::class,
        // Acelasi filtru, sub un nume potrivit si pentru celelalte module
        'companie' => \App\Http\Middleware\CompanieAnaf::class,
        // Abonamentul clientului: proba, plata la zi si modulele acordate
        'modul' => \App\Http\Middleware\ModulPermis::class,
        // Administrarea clientilor, rezervata unui singur cont
        'administrator.serviciu' => \App\Http\Middleware\AdministratorServiciu::class,
        // Gestionarea conturilor din firma clientului
        'administrator.client' => \App\Http\Middleware\AdministratorClient::class,
    ];
}
