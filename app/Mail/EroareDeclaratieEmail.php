<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

/**
 * Instiintarea ca o declaratie pusa in dosarul urmarit nu a putut fi prelucrata.
 */
class EroareDeclaratieEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $fisier;
    public $motiv;
    public $cui;
    public $certificat;

    public function __construct(string $fisier, string $motiv, ?string $cui = null, ?string $certificat = null)
    {
        $this->fisier = $fisier;
        $this->motiv = $motiv;
        $this->cui = $cui;
        $this->certificat = $certificat;
    }

    public function build()
    {
        return $this->replyTo('office@dianasoft.ro')
            ->subject('Declarația „' . $this->fisier . '" nu a putut fi prelucrată')
            ->markdown('emails.eroaredeclaratie', [
                'fisier' => $this->fisier,
                'motiv' => $this->motiv,
                'cui' => $this->cui,
                'certificat' => $this->certificat,
            ]);
    }
}
