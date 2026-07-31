<?php

namespace App\Http\Middleware;

use App\Services\AccesIp;
use App\Support\ContextCompanie;
use Closure;

/**
 * Stabilește clientul (company) în numele căruia se execută cererea și verifică
 * dreptul utilizatorului asupra lui.
 *
 * Aplicația trimite id-ul clientului selectat în antetul „AuthorizationHeader”.
 * Fără acest middleware nu există context de client, iar datele s-ar amesteca
 * între clienți — de aceea toate rutele modulului trec prin el.
 */
class CompanieAnaf
{
    /**
     * @param  string|null  $permisiune  dreptul cerut de rută (ex. incarcareDeclaratiiAnaf)
     */
    public function handle($request, Closure $next, $permisiune = null)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Autentificare necesară.'], 401);
        }

        // Un cont blocat nu mai lucreaza, chiar daca mai are un token valabil.
        if ($user->blocat === 'Da') {
            return response()->json([
                'success' => false,
                'message' => 'Contul dumneavoastră a fost blocat. Luați legătura cu administratorul firmei.',
            ], 403);
        }

        /*
         * Adresele permise se verifica si aici, nu doar la autentificare: un
         * token luat de la o adresa buna nu are voie sa lucreze mai departe de
         * oriunde.
         */
        if (!AccesIp::arePermisiune($user, AccesIp::adresaCererii($request))) {
            return response()->json([
                'success' => false,
                'message' => 'Contul dumneavoastră nu are voie să lucreze de la această adresă.',
            ], 403);
        }

        $companie = $request->header('AuthorizationHeader') ?: $request->input('company_id');

        // Administratorul serviciului poate lucra fără client selectat: vede tot.
        if (!$companie) {
            if (ContextCompanie::esteAdministrator()) {
                session()->put('user_id', $user->id);

                return $next($request);
            }

            return response()->json([
                'success' => false,
                'message' => 'Nu este selectată nicio societate.',
            ], 400);
        }

        // Un utilizator poate lucra doar pentru clienții la care este arondat.
        if (!$user->hasPermissionToCompany($companie) && !ContextCompanie::esteAdministrator()) {
            return response()->json([
                'success' => false,
                'message' => 'Nu aveți acces la această societate.',
            ], 403);
        }

        if ($permisiune && !$this->areDreptul($user, $permisiune, $companie)) {
            return response()->json([
                'success' => false,
                'message' => 'Nu aveți dreptul „' . $permisiune . '” pentru această societate.',
            ], 403);
        }

        session()->put('company_id', $companie);
        session()->put('user_id', $user->id);
        ContextCompanie::fixeaza($companie);

        $raspuns = $next($request);

        ContextCompanie::elibereaza();

        return $raspuns;
    }

    /**
     * Drepturile se acordă per client, ca în restul aplicației. Proprietarul
     * societății și administratorul serviciului nu au nevoie de ele.
     */
    protected function areDreptul($user, string $permisiune, $companie): bool
    {
        if (ContextCompanie::esteAdministrator() || $user->isOwner()) {
            return true;
        }

        return $user->hasPermission($permisiune, $companie);
    }
}
