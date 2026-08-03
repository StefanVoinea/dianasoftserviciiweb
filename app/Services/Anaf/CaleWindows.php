<?php

namespace App\Services\Anaf;

/**
 * Caile de pe calculatorul clientului: arhiva de documente si dosarul urmarit.
 *
 * Se cere o cale intreaga — „D:\Documente fiscale" sau „\\server\arhiva" — nu
 * una socotita fata de dosarul programului: acolo unde ajunge programul local
 * nu se stie de aici, iar o cale pe jumatate ar scrie documentele cine stie
 * unde. Se opresc si salturile „..", care ar duce in afara dosarului ales.
 *
 * Verificarea se face pe text, nu cu tipare: intr-un tipar scris in PHP,
 * bara oblica inversa trebuie indoita de patru ori, iar o singura scapare face
 * tiparul sa nu mai fie citit deloc — cum s-a si intamplat, la amandoua caile.
 */
class CaleWindows
{
    /** E scrisa calea intreaga, de la un disc sau de la un server? */
    public static function esteIntreaga(?string $cale): bool
    {
        $cale = trim((string) $cale);

        if ($cale === '') {
            return false;
        }

        // Disc: o litera, doua puncte si o bara — „D:\" sau „D:/".
        if (preg_match('~^[A-Za-z]:[\\\\/]~', $cale) === 1) {
            return true;
        }

        // Server: doua bare, numele calculatorului, apoi inca o bara.
        return preg_match('~^\\\\\\\\[^\\\\/]+[\\\\/]~', $cale) === 1;
    }

    /** Are calea salturi „.." care ar scoate-o din dosarul ales? */
    public static function areSalturi(?string $cale): bool
    {
        return strpos((string) $cale, '..') !== false;
    }

    /**
     * Motivul pentru care calea nu e buna, sau null cand e in regula.
     *
     * @param  string  $cePrezinta  „Calea arhivei" sau „Dosarul urmărit"
     */
    public static function motivRefuz(?string $cale, string $cePrezinta): ?string
    {
        if (trim((string) $cale) === '') {
            return null;
        }

        if (self::areSalturi($cale)) {
            return $cePrezinta . ' nu poate conține „..".';
        }

        if (!self::esteIntreaga($cale)) {
            return $cePrezinta . ' trebuie scrisă întreagă, de exemplu D:\Documente fiscale'
                . ' sau \\\\server\arhiva.';
        }

        return null;
    }
}
