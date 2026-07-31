<?php

namespace App\Console\Commands;

use App\Services\Anaf\Bridge\Licente;
use Illuminate\Console\Command;

/**
 * Perechea de chei cu care serverul semnează licențele și jetoanele de comandă.
 *
 * Cheia privată rămâne pe server, în storage; cea publică pleacă în fiecare kit
 * de instalare. Schimbarea lor invalidează licențele deja emise, așa că se
 * generează o singură dată — de aceea rescrierea trebuie cerută anume.
 */
class CheiBridge extends Command
{
    protected $signature = 'anaf:chei-bridge {--forteaza : rescrie cheile existente}';

    protected $description = 'Generează cheile cu care se semnează licențele programului local';

    public function handle(Licente $licente): int
    {
        if ($this->option('forteaza') && $licente->areChei()) {
            $this->warn('Cheile existente se rescriu: licențele deja emise nu vor mai fi valabile,');
            $this->warn('iar fiecare calculator cu token va trebui să primească un kit nou.');

            if (!$this->confirm('Continuați?', false)) {
                return 1;
            }
        }

        if (!$licente->pregatesteCheile((bool) $this->option('forteaza'))) {
            $this->info('Cheile există deja. Folosiți --forteaza dacă vreți altele.');

            return 0;
        }

        $this->info('Chei generate în storage/app/bridge/.');
        $this->line('Cheia privată nu părăsește serverul. Cea publică intră în kiturile noi.');

        return 0;
    }
}
