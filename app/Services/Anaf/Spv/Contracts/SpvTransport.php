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

    /**
     * Aceleasi documente, cerute toate deodata.
     *
     * Fiecare document insemna un drum intreg pana la calculatorul clientului:
     * comanda dusa, raspunsul adus inapoi. La cincizeci de documente, cincizeci
     * de drumuri pentru o lucrare care e una singura.
     *
     * Pauza ceruta de ANAF nu dispare — ea se tine acum acolo, unde e si apelul.
     *
     * @param  array<int, array>  $documente  fiecare cu id, firma, dosar, nume,
     *                                        inlocuieste, text
     * @param  int  $pauzaMs  ragazul dintre doua apeluri catre ANAF
     *
     * @return array<string, array>  ce a iesit, pe numarul mesajului
     */
    public function descarcaLotInArhiva(array $documente, int $pauzaMs): array;
}
