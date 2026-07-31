<?php

namespace App\Console\Commands;

use App\Models\AnafCertificat;
use App\Services\Anaf\Declaratii\MonitorizareFolder;
use App\Support\ContextCompanie;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Ia declaratiile puse in dosarele urmarite, le valideaza si le semneaza.
 *
 * Trece prin fiecare client in parte, ca datele sa nu se amestece: certificatele
 * sunt izolate pe client, iar declaratiile scrise trebuie sa cada in dreptul
 * clientului potrivit.
 */
class MonitorizeazaDeclaratii extends Command
{
    protected $signature = 'anaf:monitorizeaza-declaratii
                            {--client= : doar clientul cu acest id}';

    protected $description = 'Preia, validează și semnează declarațiile din dosarele urmărite';

    public function handle(MonitorizareFolder $monitorizare): int
    {
        $clienti = $this->clienti();

        if ($clienti === []) {
            $this->line('Niciun certificat cu dosar urmărit.');

            return 0;
        }

        $total = ['gasite' => 0, 'semnate' => 0, 'esuate' => 0];

        foreach ($clienti as $companie) {
            ContextCompanie::pentru($companie, function () use ($monitorizare, &$total) {
                $certificate = AnafCertificat::where('monitorizare_activa', true)
                    ->whereNotNull('monitorizare_cale')
                    ->get();

                foreach ($certificate as $certificat) {
                    $rezultat = $monitorizare->pentruCertificat($certificat);

                    foreach (['gasite', 'semnate', 'esuate'] as $cheie) {
                        $total[$cheie] += $rezultat[$cheie];
                    }

                    if ($rezultat['gasite'] > 0) {
                        $this->line(sprintf(
                            '  %s: %d găsite, %d semnate, %d eșuate',
                            $certificat->cn,
                            $rezultat['gasite'],
                            $rezultat['semnate'],
                            $rezultat['esuate']
                        ));
                    }

                    foreach ($rezultat['erori'] as $eroare) {
                        $this->warn('    ' . $eroare);
                    }
                }
            });
        }

        $this->info(sprintf(
            'Gata: %d fișiere găsite, %d semnate, %d eșuate.',
            $total['gasite'],
            $total['semnate'],
            $total['esuate']
        ));

        return 0;
    }

    /** @return array<int, int> clientii care au macar un dosar urmarit */
    protected function clienti(): array
    {
        if ($this->option('client')) {
            return [(int) $this->option('client')];
        }

        return DB::table('anaf_certificate')
            ->where('monitorizare_activa', true)
            ->whereNotNull('monitorizare_cale')
            ->distinct()
            ->pluck('company_id')
            ->filter()
            ->map(function ($id) {
                return (int) $id;
            })
            ->all();
    }
}
