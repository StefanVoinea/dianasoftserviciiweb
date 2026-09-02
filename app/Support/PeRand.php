<?php

namespace App\Support;

/**
 * Randurile, luate pe rand de la fiecare certificat.
 *
 * Pauza ceruta de ANAF se tine pe fiecare certificat in parte, fiindca si ANAF
 * numara apelurile tot asa. Din asta nu se castiga insa nimic daca lucrarile
 * merg in bloc: toate ale unui token, apoi toate ale celuilalt. Asa, fiecare
 * apel isi asteapta pauza intreaga, si un client cu doua tokene tine exact cat
 * unul cu unul singur.
 *
 * Luate pe rand — unul de la primul token, unul de la al doilea, iar unul de la
 * primul —, cele doua pauze curg deodata: cat asteapta unul, celalalt lucreaza.
 * La doua tokene, lucrarea tine pe jumatate; la trei, o treime.
 *
 * Ordinea dinauntrul fiecarui token ramane neatinsa: ce era intai tot intai
 * ramane, doar ca acum se intrepatrunde cu al celorlalti.
 */
class PeRand
{
    /**
     * @param  iterable  $randuri
     * @param  callable  $cheia  ce anume desparte randurile in cozi
     * @return array
     */
    public static function intercalat(iterable $randuri, callable $cheia): array
    {
        $cozi = [];

        foreach ($randuri as $rand) {
            $cozi[(string) $cheia($rand)][] = $rand;
        }

        // Cu o singura coada nu e nimic de intercalat, si nici de copiat.
        if (count($cozi) < 2) {
            return $cozi === [] ? [] : array_values(reset($cozi));
        }

        $iesit = [];

        while ($cozi !== []) {
            foreach (array_keys($cozi) as $cheiaCozii) {
                $iesit[] = array_shift($cozi[$cheiaCozii]);

                if ($cozi[$cheiaCozii] === []) {
                    unset($cozi[$cheiaCozii]);
                }
            }
        }

        return $iesit;
    }
}
