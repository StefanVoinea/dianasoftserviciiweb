<?php

namespace App\Services\Anaf\Etransport\Import;

/**
 * [2026-10-15] Textul rapoartelor furnizorului, adus în UTF-8.
 *
 * Rapoartele Teddy (T01, T02, „TARIC ... .dat") ies dintr-un program vechi, la
 * imprimantă, în codificarea DOS CP850: „è" din „gilè" e octetul 0x8A, care nu
 * e UTF-8 valid. Tiparele de citire cer UTF-8 (/u) și, pe un asemenea rând, nu
 * se potrivesc deloc — rândul cade întreg, fără niciun semn. Așa lipseau din
 * retururi toate puloverele: 1.003,90 EUR din nota de credit 10076193 și
 * 472,18 EUR din 10076194.
 *
 * Textul care e deja UTF-8 rămâne neatins.
 */
class TextFurnizor
{
    public static function inUtf8(string $continut): string
    {
        if ($continut === '' || mb_check_encoding($continut, 'UTF-8')) {
            return $continut;
        }

        return mb_convert_encoding($continut, 'UTF-8', 'CP850');
    }
}
