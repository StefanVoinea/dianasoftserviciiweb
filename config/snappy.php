<?php

/*
 * Calea catre wkhtmltopdf difera dupa masina: pe Windows sta in Program Files,
 * pe serverele Linux in /usr/local/bin. Ea se ia din .env — care nu e in git —
 * ca un push sa nu mai poata suprascrie setarea de pe server. Valorile de aici
 * sunt doar caderea de rezerva, pentru masinile de dezvoltare pe Windows.
 *
 * Pe server, in .env:
 *
 *     WKHTML_PDF_BINARY=/usr/local/bin/wkhtmltopdf
 *     WKHTML_IMG_BINARY=/usr/local/bin/wkhtmltoimage
 *
 * si apoi `php artisan config:clear` (sau `config:cache`, daca e folosit).
 */
return array(

    'pdf' => array(
        'enabled' => true,
        'binary' => env('WKHTML_PDF_BINARY', '"C:\Program Files\wkhtmltopdf\bin\wkhtmltopdf"'),
        'timeout' => 3600,
        'options' => ['encoding' => 'UTF-8'],
        'env'     => array(),
    ),
    'image' => array(
        'enabled' => true,
        'binary' => env('WKHTML_IMG_BINARY', '"C:\Program Files\wkhtmltopdf\bin\wkhtmltoimage"'),
        'timeout' => false,
        'options' => array(),
        'env'     => array(),
    ),

);
