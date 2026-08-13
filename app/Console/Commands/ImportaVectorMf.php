<?php

namespace App\Console\Commands;

use App\Services\Anaf\ImportVectorMf;
use App\Services\Anaf\VectorMde;
use Illuminate\Console\Command;

/**
 * Aduce periodicitatile de depunere din programul vechi de contabilitate.
 *
 * Primeste fie chiar fisierul Access (vector.mde), fie CSV-ul tabelului
 * vectormf, scos in prealabil. Aceeasi lucrare se poate face si din
 * Administrare clienti, cu butonul „Import vector”.
 */
class ImportaVectorMf extends Command
{
    protected $signature = 'anaf:import-vectormf
        {fisier : Calea către vector.mde sau către CSV-ul tabelului vectormf}
        {--companie= : company_id al clientului căruia îi aparțin firmele}';

    protected $description = 'Importă periodicitățile declarațiilor din tabelul vectormf (vector.mde sau CSV)';

    public function handle(VectorMde $mde, ImportVectorMf $import): int
    {
        $cale = $this->argument('fisier');
        $companie = (int) $this->option('companie');

        if (!is_file($cale)) {
            $this->error('Fișierul nu există: ' . $cale);

            return 1;
        }

        if ($companie <= 0) {
            $this->error('Lipsește --companie (company_id al clientului).');

            return 1;
        }

        try {
            $rezultat = $import->importaCsv($mde->inCsv($cale), $companie);
        } catch (\Exception $e) {
            $this->error($e->getMessage());

            return 1;
        }

        $this->info(sprintf(
            '%d firme citite, %d periodicități scrise pentru compania %d.',
            $rezultat['firme'],
            $rezultat['scrise'],
            $companie
        ));

        if ($rezultat['sarite'] !== []) {
            $this->warn('Coloane fără nume de declarație, sărite (de adăugat manual dacă contează):');

            foreach ($rezultat['sarite'] as $sarita) {
                $this->line('  - ' . $sarita);
            }
        }

        return 0;
    }
}
