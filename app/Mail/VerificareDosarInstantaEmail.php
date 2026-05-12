<?php

namespace App\Mail;


use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class VerificareDosarInstantaEmail extends Mailable //implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public $mesaj,$subject;

    public function __construct($subject,$mesaj)
    {
        $this->subject = $subject;
        $this->mesaj = $mesaj;
        
     
    }
    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
       
       return $this ->replyTo("office@dianasoft.ro")
                    ->subject($this->subject)
                    ->markdown('emails.verificaredosarinstanta',
                                [
                                  "mesaj"=>$this->mesaj,
                                  'user'=>null
                                ]);
    }
}
