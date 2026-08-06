<?php

namespace App\Http\Middleware;

use App\Models\AbonamentClient;
use App\Support\ContextCompanie;
use App\Support\ContextUtilizator;
use App\Support\Modul;
use Closure;

/**
 * Verifica daca clientul are voie sa foloseasca modulul cerut.
 *
 * Se uita la abonamentul lui: sa nu fie oprit, sa fie in perioada de proba sau
 * cu plata la zi, si sa aiba modulul acordat. Un client fara abonament scris
 * lucreaza ca pana acum — asa instalarile existente nu se opresc singure.
 *
 * Raspunsul poarta „motiv", ca interfata sa poata spune omului ce s-a intamplat
 * si de ce, nu doar ca nu are voie.
 */
class ModulPermis
{
    public function handle($request, Closure $next, string $modul)
    {
        // Administratorul aplicatiei intra oriunde, ca sa poata verifica.
        if (ContextUtilizator::esteSuperAdministrator()) {
            return $next($request);
        }

        $abonament = AbonamentClient::alClientului(ContextCompanie::curenta());

        if (!$abonament) {
            return $next($request);
        }

        if (!$abonament->activ()) {
            return response()->json([
                'success' => false,
                'message' => $abonament->motiv(),
                'abonament' => 'expirat',
            ], 402);
        }

        if (!$abonament->areModul($modul)) {
            return response()->json([
                'success' => false,
                'message' => 'Modulul nu este inclus în abonamentul dumneavoastră.',
                'abonament' => 'modul_lipsa',
            ], 403);
        }

        /*
         * Modulul poate fi al firmei, dar nu si al omului: administratorul ei
         * hotaraste cine cu ce lucreaza. Ascunderea din antet e doar inlesnire —
         * oprirea adevarata e aici, pentru oricine trimite cererea de-a dreptul.
         */
        $omul = ContextUtilizator::curent();

        if ($omul && !in_array($modul, Modul::vazuteDe($omul->id, ContextCompanie::curenta()), true)) {
            return response()->json([
                'success' => false,
                'message' => 'Nu aveți acces la acest modul. Cereți-l administratorului firmei.',
                'abonament' => 'modul_nedat',
            ], 403);
        }

        return $next($request);
    }
}
