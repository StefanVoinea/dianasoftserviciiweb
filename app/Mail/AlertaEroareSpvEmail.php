<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

/**
 * Instiintarea despre o eroare din modulul SPV, cu tot ce trebuie ca sa poata
 * fi reparata fara sa mai fie cerute amanunte: clientul, certificatul, ce se
 * lucra si ce e de facut.
 */
class AlertaEroareSpvEmail extends Mailable
{
    use Queueable, SerializesModels;

    /** @var array<string, mixed> */
    public $date;

    public function __construct(array $date)
    {
        $this->date = $date;
    }

    public function build()
    {
        $unde = $this->date['unde'] ?? 'aplicație';
        $client = $this->date['client'] ?? null;

        return $this->subject('Eroare SPV: ' . $unde . ($client ? ' — ' . $client : ''))
            ->markdown('emails.alertaeroarespv', ['date' => $this->date]);
    }
}
