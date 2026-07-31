<?php

namespace App\Support;

use Illuminate\Support\Facades\Auth;

/**
 * Clientul (company) în numele căruia se execută cererea curentă.
 *
 * În aplicație, compania vine din antetul „AuthorizationHeader” și este pusă în
 * sesiune de middleware. Aici se adaugă două situații proprii modulului ANAF:
 * rulările din consolă (sarcini programate), care trec explicit prin fiecare
 * client, și administratorul serviciului, care poate vedea toți clienții.
 */
class ContextCompanie
{
    /** Companie fixată programatic (comenzi artisan, administrare). */
    protected static $fixata;

    /** Când e adevărat, filtrarea pe companie e suspendată. */
    protected static $faraFiltrare = false;

    public static function curenta(): ?int
    {
        if (self::$faraFiltrare) {
            return null;
        }

        if (self::$fixata !== null) {
            return (int) self::$fixata;
        }

        $din_sesiune = session('company_id');

        return $din_sesiune ? (int) $din_sesiune : null;
    }

    public static function fixeaza($companieId): void
    {
        self::$fixata = $companieId;
        self::$faraFiltrare = false;
    }

    public static function elibereaza(): void
    {
        self::$fixata = null;
        self::$faraFiltrare = false;
    }

    /** Execută o operație în contextul unui client, apoi restaurează contextul. */
    public static function pentru($companieId, callable $operatie)
    {
        $anterioara = self::$fixata;
        $anteriorFara = self::$faraFiltrare;

        self::fixeaza($companieId);

        try {
            return $operatie();
        } finally {
            self::$fixata = $anterioara;
            self::$faraFiltrare = $anteriorFara;
        }
    }

    /** Execută o operație peste toți clienții (administrare, sarcini programate). */
    public static function toateCompaniile(callable $operatie)
    {
        $anterioara = self::$fixata;
        $anteriorFara = self::$faraFiltrare;

        self::$fixata = null;
        self::$faraFiltrare = true;

        try {
            return $operatie();
        } finally {
            self::$fixata = $anterioara;
            self::$faraFiltrare = $anteriorFara;
        }
    }

    /**
     * Administratorul serviciului: vede și administrează toți clienții.
     * Convenția aplicației — utilizatorul 1 și conturile de tip „Sistem”.
     */
    public static function esteAdministrator(): bool
    {
        $user = Auth::guard('api')->user() ?: Auth::user();

        if (!$user) {
            return false;
        }

        return (int) $user->id === 1 || in_array($user->user_type, ['Sistem', 'admin'], true);
    }
}
