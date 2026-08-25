<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

/**
 * Formularul cu codurile UIT, trimis transportatorului pe email.
 *
 * Fișierul cu câte o foaie pe magazin pleacă direct din aplicație, ca șoferul
 * să aibă codurile de scris pe CMR-uri. Prin coadă, ca celelalte emailuri.
 */
class FormularTransportatorEmail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $numeFisier;
    public $continut;
    public $foi;

    public function __construct(string $numeFisier, string $continut, int $foi)
    {
        $this->numeFisier = $numeFisier;
        // Continutul se tine base64: lucrarea de coada se scrie ca JSON, iar
        // octetii XLSX-ului nu au ce cauta acolo neimpachetati.
        $this->continut = base64_encode($continut);
        $this->foi = $foi;
    }

    public function build()
    {
        return $this->replyTo('office@dianasoft.ro')
            ->subject('Coduri UIT pentru transport — ' . $this->numeFisier)
            ->markdown('emails.formulartransportator', ['foi' => $this->foi])
            ->attachData(base64_decode($this->continut), $this->numeFisier, [
                'mime' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ]);
    }
}
