<?php

namespace App\Console\Commands;

use Illuminate\Support\Facades\Mail;
use App\Mail\AlertaEroareEmail;
use App\Mail\AlertaEmail;
use Illuminate\Console\Command;
use App\Models\Company;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;



class VerificareSPV extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'spv:verificare';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Verificare facturi primite in SPV';

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
          $user=User::where("email","stefan.voinea@gmail.com")->get()->first();
        try{ 
          $company=Company::get()->first();
          Log::info("PAS 1");
          $fisierRaport=verificareSPVFP($company);
            Log::info("PAS 2");
          $tipNotificare=Notificationtype::where("denumire","Verificare SPV")->with(["notificationuser"])->get()->first();
          Log::info("PAS 3");
          $users=User::whereIn("id",$tipNotificare->notificationuser->where("channel","Email")->pluck("user_id"))->get();
          Log::info("PAS 4");
          $mesaj=file_get_contents(storage_path("app".$fisierRaport));
          Log::info("PAS 5");
           Mail::to("stefan.voinea@gmail.com")
            ->send(new AlertaEmail("Verificare SPV efectuata la ".datasioraFormatStocare(Carbon::now()),$mesaj));
           Log::info("PAS 6"); 
            

        } catch (\Exception $e) {
            Log::info("EROARE:");
            Log::info($user);
            Log::info($e);
            Mail::to("stefan.voinea@gmail.com")
            ->send(new AlertaEroareEmail("VERIFICARE SPV",$e->getMessage(),$e,$user));
            return response()->json(['message' => $e->getMessage()], 500);
        }    

        
        return 0;
    }
}
