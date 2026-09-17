<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Unde ajung cererile de demonstratie de pe pagina de prezentare
    |--------------------------------------------------------------------------
    |
    | Pagina spvcurier.ro are un formular „Solicită demo". Ce se completeaza
    | acolo pleaca pe email de la adresa casei (MAIL_FROM_ADDRESS) catre adresa
    | de mai jos, cu raspunsul indreptat spre cel care a cerut demonstratia, ca
    | sa se poata raspunde direct din cutia postala.
    |
    | Se schimba din .env (CERERE_DEMO_EMAIL). Se pot trece mai multe adrese,
    | despartite prin virgula.
    |
    */

    'email_demo' => env('CERERE_DEMO_EMAIL', 'office@dianasoft.ro'),

];
