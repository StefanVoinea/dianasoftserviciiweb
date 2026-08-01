<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\TrimStrings as Middleware;

class TrimStrings extends Middleware
{
    /**
     * The names of the attributes that should not be trimmed.
     *
     * @var array
     */
    protected $except = [
        'password',
        'password_confirmation',
        /*
         * Parola noua a unui cont se scrie in campul „parola". Taiata aici, ea
         * s-ar salva fara spatiile de la capete, dar la autentificare pleaca
         * intreaga — campul „password" e scutit — si contul n-ar mai putea intra
         * niciodata. Spatiile fac parte din parola, ca orice alt caracter.
         */
        'parola',
        'parola_noua',
    ];
}
