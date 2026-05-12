<?php

namespace App\Mail;

use App\Cursbnr;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CursbnrEmail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public $curs_EUR,$curs_USD;

    public function __construct($curs_EUR,$curs_USD)
    {
        
        $this->curs_EUR = $curs_EUR;
        $this->curs_USD = $curs_USD;
     
    }
    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
       return $this->subject("Curs BNR: ".dateFormatAfisare($this->curs_EUR->data) ."  : 1 EUR = ".$this->curs_EUR->curs ." Lei ; 1 USD = ".$this->curs_USD->curs ." Lei" )
                    ->markdown('emails.cursbnr',[
                                  "user"=>"",
                                ]);
    }
}
