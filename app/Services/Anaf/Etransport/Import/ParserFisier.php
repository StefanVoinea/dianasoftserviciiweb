<?php

namespace App\Services\Anaf\Etransport\Import;

interface ParserFisier
{
    /**
     * @return array{linii: array, antet: array}
     */
    public function citeste(string $cale): array;
}
