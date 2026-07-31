<?php

namespace App\Http\Middleware;

use Fideloper\Proxy\TrustProxies as Middleware;
use Illuminate\Http\Request;

class TrustProxies extends Middleware
{
    /**
     * The trusted proxies for this application.
     *
     * În producție aplicația stă în spatele nginx-ului de pe același server, deci
     * adresa reală a omului vine în X-Forwarded-For. Fără încrederea aceasta,
     * fiecare cerere ar părea că vine de la 127.0.0.1 — iar filtrarea pe IP-uri
     * autorizate ar bloca pe toată lumea sau, mai rău, ar lăsa pe oricine.
     *
     * @var array|string|null
     */
    protected $proxies = '*';

    /**
     * The headers that should be used to detect proxies.
     *
     * @var int
     */
    protected $headers = Request::HEADER_X_FORWARDED_ALL;
}
