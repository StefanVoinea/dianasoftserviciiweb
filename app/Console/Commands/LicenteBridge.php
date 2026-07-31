<?php

namespace App\Console\Commands;

use App\Models\AbonamentClient;
use App\Models\AnafCertificat;
use App\Services\Anaf\Bridge\Licente;
use App\Services\Anaf\Bridge\LicentiereBridge;
use App\Support\ContextCompanie;
use Illuminate\Console\Command;

/**
 * Reînnoiește licențele programelor locale.
 *
 * Licența ține treizeci de zile și se reînnoiește singură cât timp clientul are
 * abonamentul în regulă. Când nu-l mai are, comanda nu mai emite nimic, iar
 * programul de la client se oprește de la sine când expiră licența pe care o
 * are — fără să fie nevoie de cineva care să meargă acolo și să-l închidă.
 */
class LicenteBridge extends Command
{
    protected $signature = 'anaf:licente-bridge
                            {--client= : doar pentru compania cu acest id}
                            {--forteaza : reînnoiește chiar dacă licența de acum mai are zile}';

    protected $description = 'Emite și reînnoiește licențele programelor locale (bridge)';

    public function handle(Licente $licente, LicentiereBridge $licentiere): int
    {
        if (!$licente->areChei()) {
            $this->error('Lipsesc cheile de semnare. Rulați mai întâi „php artisan anaf:chei-bridge".');

            return 1;
        }

        $companii = AnafCertificat::query()->toateCompaniile()
            ->when($this->option('client'), function ($query) {
                return $query->where('company_id', $this->option('client'));
            })
            ->distinct()
            ->pluck('company_id');

        $emise = 0;
        $sarite = 0;

        foreach ($companii as $companie) {
            $abonament = AbonamentClient::alClientului($companie);

            if ($abonament && !$abonament->activ()) {
                $this->warn('Clientul ' . $companie . ': ' . $abonament->motiv() . ' — nu se reînnoiește.');
                $sarite++;

                continue;
            }

            ContextCompanie::pentru($companie, function () use ($licentiere, &$emise, &$sarite) {
                foreach (AnafCertificat::where('activ', true)->get() as $certificat) {
                    $rezultat = $licentiere->reinnoieste($certificat, (bool) $this->option('forteaza'));

                    if ($rezultat['emisa']) {
                        $emise++;
                        $this->line('  ' . $certificat->cn . ': licență până la ' . $rezultat['expira']);

                        continue;
                    }

                    $sarite++;

                    if ($rezultat['motiv']) {
                        $this->line('  ' . $certificat->cn . ': ' . $rezultat['motiv']);
                    }
                }
            });
        }

        $this->info($emise . ' licențe emise, ' . $sarite . ' sărite.');

        return 0;
    }
}
