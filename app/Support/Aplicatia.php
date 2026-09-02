<?php

namespace App\Support;

/**
 * De unde a plecat lucrarea: din fila din browser, de pe telefon, sau singură.
 *
 * Contează pentru lucrurile care se cer înapoi omului — PIN-ul tokenului, de
 * pildă. Cine a apăsat butonul de pe telefon trebuie întrebat pe telefon, nu
 * într-o filă deschisă în altă parte, pe care poate n-o are nimeni în față.
 *
 * Aplicația de telefon se spune singură, printr-un antet. Fila din browser nu
 * spune nimic, și e bine așa: ea e cazul obișnuit, iar un antet în plus pe
 * fiecare cerere n-ar aduce nimic.
 */
class Aplicatia
{
    /** Fila din browser: cine nu spune altfel, de acolo vine. */
    public const WEB = 'web';

    /** Aplicația de telefon, care se spune singură. */
    public const MOBIL = 'mobil';

    /**
     * Lucrarea pornită de la sine: dosarul urmărit, sarcina de noapte, comanda
     * din consolă. N-a apăsat nimeni nimic, deci nu e nimeni anume de întrebat.
     */
    public const FUNDAL = 'fundal';

    /** Antetul prin care se spune aplicația de telefon. */
    public const ANTETUL = 'X-Aplicatia';

    /** De unde vine cererea de acum. */
    public static function curenta(): string
    {
        if (!app()->bound('request')) {
            return self::FUNDAL;
        }

        /*
         * Nu se intreaba daca aplicatia ruleaza din consola: si sarcina de
         * noapte, si proba scrisa ruleaza acolo, dar numai una dintre ele are
         * un om in spate. Lipsa omului le desparte oricum, mai jos.
         */

        $spus = strtolower(trim((string) request()->header(self::ANTETUL)));

        if ($spus === self::MOBIL) {
            return self::MOBIL;
        }

        /*
         * Fără niciun om în spate, cererea nu e a nimănui: așa vin lucrările
         * pornite de programul local sau de puntea lui, care poartă cod de
         * instalare, nu jetonul cuiva.
         */
        return ContextUtilizator::curent() === null ? self::FUNDAL : self::WEB;
    }
}
