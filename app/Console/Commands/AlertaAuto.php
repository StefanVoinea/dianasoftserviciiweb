<?php

namespace App\Console\Commands;


use App\Http\Controllers\Api\AlerteAutoController;
use Illuminate\Console\Command;

class AlertaAuto extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'alerta:auto';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Transmite alerta auto';

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
     * @return mixed
     */
    public function handle()
    {
        $alerta= new AlerteAutoController;
        $alerta->alerteAutoPtrAlerta();
    }
}
