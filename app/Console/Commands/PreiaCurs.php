<?php

namespace App\Console\Commands;

use App\Http\Controllers\Api\CursbnrController;
use Illuminate\Console\Command;

class PreiaCurs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'curs:preia';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Preluare curs de la BNR';

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
        $preiacurs= new CursbnrController;
        $preiacurs->preiaCursBNR();
    }
}
