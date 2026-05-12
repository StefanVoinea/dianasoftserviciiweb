<?php

namespace App\Console\Commands;


use App\Http\Controllers\Api\DatefirmeregcomController;
use Illuminate\Console\Command;

class PreiaDatefirmeregcom extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'datefirmeregcom:preia';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Preluare datefirmeregcom';

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
        $preiadate= new DatefirmeregcomController;
        $preiadate->importdepeserver();
    }
}