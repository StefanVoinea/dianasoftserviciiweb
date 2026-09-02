<?php

namespace App\Services\Anaf\Spv;

/**
 * Apelul n-a ajuns la ANAF fiindca tokenul isi asteapta PIN-ul.
 *
 * Pana acum asta arata la fel cu un server picat sau cu o retea proasta: omul
 * cauta vina in legatura, in vreme ce pe calculatorul clientului statea
 * deschisa o fereastra care aspecta pe cineva sa scrie codul.
 *
 * Se spune deosebit tocmai fiindca se dezleaga cu totul altfel: nu are ce
 * incerca din nou serverul, ci trebuie ca cineva sa se duca la calculatorul
 * acela — sau sa intre pe el de la distanta — si sa scrie PIN-ul.
 */
class PinAsteaptaException extends SpvException
{
    /** Ce scrie pe fereastra deschisa, asa cum a citit-o programul local. */
    public $fereastra;

    /** Programul care a deschis-o: dupa el se cunoaste furnizorul tokenului. */
    public $proces;

    /** Certificatul al carui token asteapta, cand se stie care e. */
    public $certificat;

    public function __construct(string $mesaj, string $fereastra = '', string $proces = '', $certificat = null)
    {
        parent::__construct($mesaj);

        $this->fereastra = $fereastra;
        $this->proces = $proces;
        $this->certificat = $certificat;
    }
}
