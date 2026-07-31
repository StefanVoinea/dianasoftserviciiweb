<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

/**
 * Instiintarea ca cineva a incercat sa intre de la o adresa nepermisa.
 */
class AccesRespinsEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $email;
    public $nume;
    public $ip;
    public $permise;
    public $cand;
    public $agent;

    public function __construct(string $email, ?string $nume, ?string $ip, ?string $permise, ?string $agent = null)
    {
        $this->email = $email;
        $this->nume = $nume;
        $this->ip = $ip;
        $this->permise = $permise;
        $this->agent = $agent;
        $this->cand = now()->format('d.m.Y H:i:s');
    }

    public function build()
    {
        return $this->subject('Încercare de autentificare de la o adresă nepermisă: ' . $this->email)
            ->markdown('emails.accesrespins', [
                'email' => $this->email,
                'nume' => $this->nume,
                'ip' => $this->ip,
                'permise' => $this->permise,
                'cand' => $this->cand,
                'agent' => $this->agent,
            ]);
    }
}
