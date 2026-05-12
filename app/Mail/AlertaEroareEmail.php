<?php

namespace App\Mail;


use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;

class AlertaEroareEmail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public $mesaj,$user,$contex,$eroaret;

    public function __construct($context,$mesaj,$eroare,$user)
    {
        $this->context = $context;
        $this->mesaj = $mesaj;
        $this->user = $user;
        $this->eroare = $eroare;
        
     
    }
    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {  
        //." Total incasari : ".number_format($this->incasari->sum("suma"),2)." Lei"
       return $this->subject("Eroare ".$this->context." ".$this->user->name.datasioracurenta())
                    ->markdown('emails.raportareeroare',
                                [
                                  "context"=>$this->context,
                                  "mesaj"=>$this->mesaj,
                                  "eroare"=>$this->eroare,
                                  "user"=>$this->user
                                 ]);
    }
}