<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cine poate pune pe server o versiune noua a aplicatiei de telefon
    |--------------------------------------------------------------------------
    |
    | Adresele de email, despartite prin virgula, in MOBIL_PUBLICA.
    |
    | Goala inseamna nimeni, si asa e bine din start: arhiva pusa aici ajunge
    | singura pe telefoanele tuturor clientilor, deci dreptul acesta nu e unul
    | de administrator de firma — el priveste toti clientii deodata, nu firma
    | celui care apasa.
    |
    | Descarcarea nu se ingradeste asa: aceea e treaba oricarui client care
    | vrea aplicatia pe telefonul lui.
    |
    */

    'publica' => env('MOBIL_PUBLICA', ''),

    /*
    |--------------------------------------------------------------------------
    | Unde sta arhiva care merge o data cu codul
    |--------------------------------------------------------------------------
    |
    | Fata de radacina aplicatiei. Nu se schimba in viata de zi cu zi: sta aici
    | ca probele sa-si poata muta dosarul lor, fara sa dea peste arhiva
    | adevarata din depozit.
    |
    */

    'dosar_din_cod' => 'resources/mobil',

];
