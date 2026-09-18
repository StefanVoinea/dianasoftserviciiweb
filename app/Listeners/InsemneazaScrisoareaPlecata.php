<?php

namespace App\Listeners;

use App\Mail\ScrisoareMarketing;
use App\Models\MarketingTrimitere;
use Illuminate\Mail\Events\MessageSent;

/**
 * Insemneaza in evidenta scrisorile care chiar au plecat de pe server.
 *
 * Pana acum se scria „reusit" in clipa in care scrisoarea intra in coada. Dar
 * pana la plecarea adevarata mai e drum: lucratorul cozii trebuie sa fie
 * pornit, iar furnizorul de email trebuie s-o primeasca. Cand vreunul dintre ele
 * lipsea, fila spunea „trimise" si nu ajungea nimic nicaieri — nici macar o
 * eroare, fiindca esecul se intampla in alt proces, mai tarziu.
 *
 * Acum randul se insemneaza abia aici, cand plecarea s-a intamplat. Ce ramane
 * „in coada" mai mult de cateva minute spune singur ca ceva sta pe loc.
 */
class InsemneazaScrisoareaPlecata
{
    public function handle(MessageSent $eveniment): void
    {
        $antet = $eveniment->message->getHeaders()->get(ScrisoareMarketing::ANTETUL);

        if ($antet === null) {
            // Nu e o scrisoare de marketing: orice alt email trece pe aici.
            return;
        }

        $id = (int) $antet->getFieldBody();

        if ($id <= 0) {
            return;
        }

        MarketingTrimitere::where('id', $id)->update([
            'stare' => 'plecat',
            'reusit' => true,
            'plecat_la' => now(),
        ]);
    }
}
