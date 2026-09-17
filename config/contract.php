<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Datele casei, asa cum intra in contract
    |--------------------------------------------------------------------------
    |
    | Ce tine de Diana Soft se scrie o data, aici, si iese la fel in fiecare
    | contract. Ce tine de client se tine pe fiecare client in parte, in tabela
    | „contracte_clienti", si se completeaza din Administrare.
    |
    */

    'prestator' => [
        'denumire' => 'DIANA SOFT S.R.L.',
        'sediu' => 'Năvodari, Str. Bradului nr. 13, jud. Constanța',
        'reg_com' => 'J13/888/2003',
        'cui' => 'RO15208744',
        'iban' => env('CONTRACT_IBAN', ''),
        'banca' => env('CONTRACT_BANCA', ''),
        'email' => 'office@dianasoft.ro',
        'telefon' => '0744 476 969',
        'reprezentant' => env('CONTRACT_REPREZENTANT', ''),
        'functie' => env('CONTRACT_FUNCTIE', 'administrator'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Planurile, asa cum sunt publicate pe spvcurier.ro
    |--------------------------------------------------------------------------
    |
    | Aceleasi cifre ca pe pagina de prezentare. Cand se schimba acolo, se
    | schimba si aici — altfel contractul ar promite alt pret decat site-ul.
    |
    */

    'planuri' => [
        'START' => ['entitati' => 'maxim 5', 'lunar' => '19 lei', 'anual' => '190 lei'],
        'BIROU' => ['entitati' => 'maxim 30', 'lunar' => '59 lei', 'anual' => '590 lei'],
        'CABINET' => ['entitati' => 'maxim 100', 'lunar' => '129 lei', 'anual' => '1.290 lei'],
        'EXPERT' => ['entitati' => 'maxim 300', 'lunar' => '249 lei', 'anual' => '2.490 lei'],
        'ENTERPRISE' => ['entitati' => 'peste 300', 'lunar' => '0,70 lei / entitate, minim 249 lei', 'anual' => 'ofertă'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Termenele si nivelurile din anexa de serviciu
    |--------------------------------------------------------------------------
    |
    | Sunt angajamente: se citesc si de client, si de instanta. De aceea stau
    | aici, la vedere, si nu imprastiate prin sablonul contractului.
    |
    */

    'serviciu' => [
        'disponibilitate' => '99 %',
        'program_suport' => '9:00–17:00',
        'raspuns_blocant' => '2 ore',
        'raspuns_major' => '8 ore',
        'raspuns_minor' => '2 zile',
        'instalare_zile' => '5',
        'instruire_minute' => '60',
        'reducere_indisponibilitate' => '10 %',
        'penalitati_zi' => '0,1 %',
        'zile_plata' => '10',
        'zile_suspendare' => '15',
        'zile_stergere' => '60',
        'sistem_operare' => 'Windows 10 sau mai nou',
    ],

    /*
    |--------------------------------------------------------------------------
    | Subimputernicitii, pentru anexa de prelucrare a datelor
    |--------------------------------------------------------------------------
    */

    'subimputerniciti' => [
        ['nume' => 'DigitalOcean', 'serviciu' => 'Găzduirea aplicației web și a bazei de date', 'locatie' => 'Uniunea Europeană'],
        ['nume' => 'Zoho (ZeptoMail)', 'serviciu' => 'Transmiterea alertelor și a notificărilor pe email', 'locatie' => 'Uniunea Europeană'],
    ],

];
