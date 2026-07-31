<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Portal Just — serviciul web al instantelor
    |--------------------------------------------------------------------------
    |
    | Serviciul (SOAP) publicat de Ministerul Justitiei permite interogarea
    | dosarelor, partilor si sedintelor ECRIS la nivel national. Accesul se face
    | fara autentificare, deci nu exista chei de configurat.
    |
    */

    'url' => env('PORTAL_JUST_URL', 'http://portalquery.just.ro/Query.asmx'),

    // Spatiul de nume din WSDL — atentie, este fara "http://".
    'namespace' => env('PORTAL_JUST_NAMESPACE', 'portalquery.just.ro'),

    'timeout' => (int) env('PORTAL_JUST_TIMEOUT', 90),

    /*
     * Lista instantelor se citeste din WSDL si se pastreaza in cache: se
     * modifica foarte rar, iar WSDL-ul are aproape 90 KB.
     */
    'cache_institutii_minute' => (int) env('PORTAL_JUST_CACHE_INSTITUTII', 60 * 24 * 30),

    /*
     * Rezultatele cautarilor se pastreaza cateva minute, ca navigarea inainte
     * si inapoi in interfata sa nu reinterogheze serviciul de fiecare data.
     */
    'cache_rezultate_minute' => (int) env('PORTAL_JUST_CACHE_REZULTATE', 10),

    // Serviciul intoarce cel mult 1000 de dosare; atat afisam si noi.
    'maxim_dosare' => 1000,

    'monitorizare' => [
        // Pauza intre doua interogari succesive, ca sa nu impovaram serviciul.
        'pauza_ms' => (int) env('PORTAL_JUST_PAUZA_MS', 500),

        // Cate zile se pastreaza istoricul modificarilor.
        'zile_istoric' => (int) env('PORTAL_JUST_ZILE_ISTORIC', 365),
    ],
];
