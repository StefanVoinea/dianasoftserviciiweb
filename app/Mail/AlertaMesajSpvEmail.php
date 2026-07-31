<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

/**
 * Instiintarea ca a intrat in SPV un document de tipul urmarit.
 */
class AlertaMesajSpvEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $tip;
    public $cif;
    public $denumire;
    public $detalii;
    public $dataCreare;

    public function __construct(string $tip, ?string $cif, ?string $denumire, ?string $detalii, $dataCreare = null)
    {
        $this->tip = $tip;
        $this->cif = $cif;
        $this->denumire = $denumire;
        $this->detalii = $detalii;
        $this->dataCreare = $dataCreare;
    }

    public function build()
    {
        $firma = $this->denumire ?: ($this->cif ?: 'o firmă înrolată');

        return $this->replyTo('office@dianasoft.ro')
            ->subject('SPV: ' . $this->tip . ' pentru ' . $firma)
            ->markdown('emails.alertamesajspv', [
                'tip' => $this->tip,
                'cif' => $this->cif,
                'denumire' => $this->denumire,
                'detalii' => $this->detalii,
                'dataCreare' => $this->dataCreare,
            ]);
    }
}
