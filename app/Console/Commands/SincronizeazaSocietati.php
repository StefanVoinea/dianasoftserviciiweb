<?php

namespace App\Console\Commands;

use App\Models\AnafCertificat;
use App\Services\Anaf\Spv\SocietatiService;
use App\Services\Anaf\Spv\SolicitareService;
use App\Services\Anaf\Spv\SpvException;
use App\Support\ContextCompanie;
use Illuminate\Console\Command;

/**
 * Actualizeaza registrul de societati si preia din SPV datele lipsa.
 * Poate fi programata (ex. zilnic) ca denumirile sa fie mereu la zi.
 */
class SincronizeazaSocietati extends Command
{
    protected $signature = 'anaf:societati
                            {--client= : Doar pentru clientul (company) indicat}
                            {--fara-solicitari : Doar actualizează lista, fără cereri noi în SPV}
                            {--preia : Preia și răspunsurile deja disponibile în SPV}';

    protected $description = 'Sincronizează societățile cu drept de semnătură și datele lor din SPV';

    public function handle(SocietatiService $societati, SolicitareService $solicitari): int
    {
        // Fara client selectat se trece prin fiecare, ca datele sa nu se amestece.
        $clienti = $this->option('client')
            ? [(int) $this->option('client')]
            : AnafCertificat::query()->toateCompaniile()
                ->whereNotNull('company_id')->distinct()->pluck('company_id')->all();

        if ($clienti === []) {
            $this->warn('Niciun client cu certificate înregistrate.');

            return 0;
        }

        $cod = 0;

        foreach ($clienti as $client) {
            $this->line('=== Client ' . $client . ' ===');

            $cod = max($cod, ContextCompanie::pentru($client, function () use ($societati, $solicitari) {
                return $this->pentruClient($societati, $solicitari);
            }));
        }

        return $cod;
    }

    protected function pentruClient(SocietatiService $societati, SolicitareService $solicitari): int
    {
        try {
            $rezultat = $societati->sincronizeaza();
        } catch (SpvException $e) {
            $this->error($e->getMessage());

            return 1;
        }

        $this->info(sprintf(
            'Certificat: %d CIF-uri (%d noi, %d fără drepturi acum).',
            $rezultat['gasite'],
            $rezultat['noi'],
            $rezultat['dezactivate']
        ));

        if ($this->option('preia')) {
            $preluate = $solicitari->preiaRaspunsuri();
            $this->line(sprintf(
                'Răspunsuri SPV: %d verificate, %d preluate.',
                $preluate['verificate'],
                $preluate['preluate']
            ));
        }

        if ($this->option('fara-solicitari')) {
            return 0;
        }

        $cereri = $societati->solicitaDocumente();

        $this->line(sprintf(
            'Solicitări: %d trimise, %d sărite, %d documente reinterpretate.',
            $cereri['trimise'],
            $cereri['sarite'],
            $cereri['reinterpretate']
        ));

        foreach ($cereri['erori'] as $eroare) {
            $this->warn('  ' . $eroare);
        }

        return 0;
    }
}
