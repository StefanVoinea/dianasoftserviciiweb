<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Cheia de aplicatie a API-ului de consultare a datelor firmelor (config/regcom.php): Authorization: Bearer <cheie>.
 * Fara cheie sau cu o cheie necunoscuta raspunde 401 cu mesaj clar; nu exista nicio cheie configurata = API inchis.
 */
class CheieRegcom
{
    public function handle(Request $request, Closure $next)
    {
        $cheie = (string) $request->bearerToken();
        $acceptate = (array) config('regcom.chei', []);
        if ($cheie === '' || ! $acceptate) {
            return response()->json(['message' => 'API-ul datelor firmelor cere o cheie de aplicatie (Authorization: Bearer <cheie>).'], 401);
        }
        foreach ($acceptate as $a) {
            if (hash_equals((string) $a, $cheie)) {
                // eticheta cheii (partea dinaintea ":") ajunge in jurnal, ca sa se vada ce aplicatie intreaba
                $request->attributes->set('regcom_aplicatie', strstr((string) $a, ':', true) ?: 'necunoscuta');

                return $next($request);
            }
        }
        Log::warning('API regcom: cheie necunoscuta de la '.$request->ip());

        return response()->json(['message' => 'Cheia de aplicatie nu este acceptata.'], 401);
    }
}
