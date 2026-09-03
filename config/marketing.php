<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cine mai primeste, in copie ascunsa, fiecare scrisoare trimisa
    |--------------------------------------------------------------------------
    |
    | Adresa casei: asa se vede in cutia noastra ce a plecat si cum arata la
    | destinatar, fara sa fie nevoie sa deschida cineva evidenta. Se poate
    | schimba din .env (MARKETING_BCC), iar goala inseamna ca nu se trimite
    | nimanui in copie.
    |
    | De stiut: la o campanie de cateva mii de firme, tot atatea scrisori ajung
    | si in cutia aceasta.
    |
    */

    'copie_ascunsa' => env('MARKETING_BCC', 'office@dianasoft.ro'),

];
