<?php

namespace App\Console\Commands;


use App\Http\Controllers\Api\AlerteSablonController;
use Illuminate\Console\Command;

class Alertaordinedeblocareanaf extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'verificare:ordinedeblocareanaf';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Transmite alerta actualizare Ordin de blocare ANAF';

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
        $alerta= new AlerteSablonController;
        $alerta->alerteOrdinedeblocareanaf();
    }
}