<?php

namespace App\Console\Commands;

use App\Http\Controllers\Api\JurnalsmsController;
use Illuminate\Console\Command;



class SendSMS extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sms:send';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Transmite SMS';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $transmitSMS= new JurnalsmsController;
        $transmitSMS->sendSMS();    

        
        return 0;
    }
}
