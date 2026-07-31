<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')->hourly();
       // $schedule->command('generarenote:noaptea')->withoutOverlapping();
       // $schedule->command('generarenote:lunacurenta')->withoutOverlapping();
       // $schedule->command('clienti:acceptati')->withoutOverlapping();
       // $schedule->command('calcul:reclasificare')->withoutOverlapping();
       // $schedule->command('solduriclienti:recalculare')->withoutOverlapping();
       // $schedule->command('raportarecrc:creezjson')->withoutOverlapping();

        // Avertizare pe email inainte de expirarea certificatelor digitale
        $schedule->command('anaf:certificate-expira')->dailyAt('08:00')->withoutOverlapping();

        // Verificarea dosarelor urmarite in Portal Just, din ora in ora.
        // withoutOverlapping opreste o pornire noua cat timp cea anterioara
        // inca ruleaza, ca listele lungi sa nu se suprapuna.
        $schedule->command('portaljust:monitorizeaza')->hourly()->withoutOverlapping();

        /*
         * Declarațiile puse în dosarele urmărite, luate din cinci în cinci
         * minute. Mai des n-ar avea rost: fișierul abia copiat e oricum lăsat
         * să se liniștească înainte de a fi citit.
         */
        $schedule->command('anaf:monitorizeaza-declaratii')
            ->everyFiveMinutes()
            ->withoutOverlapping();

        /*
         * Licențele programelor locale. Se reînnoiesc cu zece zile înainte de
         * expirare, cât timp clientul are abonamentul în regulă; când nu-l mai
         * are, licența nu se mai emite, iar programul de la el se oprește singur
         * când expiră ce are — fără să meargă cineva acolo să-l închidă.
         */
        $schedule->command('anaf:licente-bridge')->dailyAt('07:30')->withoutOverlapping();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
