<?php

namespace App\Mail;


use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;

class AlertaSablonEmail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public  $antetTabel,$titluRaport,$tabel,$groupBy,$totalBy,$i,$company,$numefis;

    public function __construct($antetTabel,$titluRaport,$tabel,$groupBy,$totalBy,$i,$company,$numefis)
    {
        
         $this->antetTabel=$antetTabel;
         $this->titluRaport=$titluRaport;
         $this->tabel=$tabel;
         $this->groupBy=$groupBy;
         $this->totalBy=$totalBy;
         $this->i=$i;
         $this->company=$company;
         if ($numefis){

         $this->numefis="app/".$numefis;
         }else{
            $this->numefis="";
         }  
        
     
    }
    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {  
        return $this->subject($this->titluRaport)
                    ->attach(storage_path($this->numefis))
                    ->markdown('emails.sablonemail',
                                [
                                  "antetTabel"=>$this->antetTabel,
                                  "tabel"=>$this->tabel,
                                  "titluRaport"=>$this->titluRaport,
                                  "groupBy"=>$this->groupBy,
                                  "totalBy"=>$this->totalBy,
                                  "i"=>$this->i,
                                  "company"=>$this->company,
                                 ]);
    }
}
