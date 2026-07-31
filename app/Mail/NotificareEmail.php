<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

/**
 * Notificarea trimisa pe email de administratorul aplicatiei.
 */
class NotificareEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $titlu;
    public $mesaj;
    public $importanta;
    public $destinatar;

    public function __construct(string $titlu, string $mesaj, string $importanta = 'informare', ?string $destinatar = null)
    {
        $this->titlu = $titlu;
        $this->mesaj = $mesaj;
        $this->importanta = $importanta;
        $this->destinatar = $destinatar;
    }

    public function build()
    {
        return $this->replyTo('office@dianasoft.ro')
            ->subject($this->titlu)
            ->markdown('emails.notificare', [
                'titlu' => $this->titlu,
                'mesaj' => $this->mesaj,
                'importanta' => $this->importanta,
                'destinatar' => $this->destinatar,
            ]);
    }
}
