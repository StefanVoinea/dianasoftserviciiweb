<?php

namespace App\Mail;


use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;

class AlertaEmail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public $mesaj,$user,$contex,$eroaret;

    public function __construct($subiect,$mesaj)
    {
        $this->subiect = $subiect;
        $this->mesaj = $mesaj;
        
        
        
     
    }
    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {  
        //." Total incasari : ".number_format($this->incasari->sum("suma"),2)." Lei"
       return $this->subject($this->subiect)
                    ->markdown('emails.alertaemail',
                                [
                                  "mesaj"=>$this->mesaj
                                  
                                 ]);
    }
}