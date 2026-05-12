<?php

namespace App\Console\Commands;

use App\Http\Controllers\Api\JurnalsmsController;
use Illuminate\Console\Command;

class CheckStatusSMS extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sms:check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Verifica status SMS';

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
        $transmitSMS->checkStatusSMS();    

        return 0;
    }
}
