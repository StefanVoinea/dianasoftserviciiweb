<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToArray;

/**
 * Citeste fisierul asa cum e, rand cu rand: interpretarea coloanelor se face in
 * App\Services\Just\ImportMonitorizari, care trebuie sa poata fi testata si
 * fara fisier.
 */
class PortalJustMonitorizariImport implements ToArray
{
    /** @var array */
    public $randuri = [];

    public function array(array $randuri)
    {
        $this->randuri = $randuri;
    }
}
