<?php

namespace App\Services\Anaf\Spv\Contracts;

use Illuminate\Http\Client\Response;

interface SpvTransport
{
    public function get($path, array $query = array()): Response;

    /**
     * Cere programului local sa aduca documentul din SPV de-a dreptul in arhiva
     * de pe calculatorul clientului, fara sa mai treaca prin server.
     *
     * @param string $id         numarul mesajului la ANAF
     * @param array  $destinatie firma, dosar, nume (fara extensie), inlocuieste, text
     *
     * @return array{cale: string, extensie: string, marime: int, hash: string, text?: string}
     */
    public function descarcaInArhiva(string $id, array $destinatie): array;
}
