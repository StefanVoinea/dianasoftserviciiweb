<?php

namespace App\Services\Anaf\Declaratii;

use Exception;

class DeclaratieException extends Exception
{
    /**
     * Firma pentru care s-a produs eroarea, cand se stie.
     *
     * Se foloseste la instiintari: fara ea nu s-ar sti carui certificat — deci
     * carui grup de oameni — ii pasa de declaratia care a picat.
     */
    public $cui;
}
