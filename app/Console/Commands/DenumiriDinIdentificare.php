<?php

namespace App\Console\Commands;

use App\Models\AnafCertificat;
use App\Models\AnafSocietate;
use App\Models\SpvSolicitare;
use App\Services\Anaf\Spv\SolicitareService;
use App\Support\ContextCompanie;
use Illuminate\Console\Command;

/**
 * Ia denumirile firmelor din documentele de identificare deja descarcate.
 *
 * Aceeasi lucrare pe care o face butonul „Solicită datele lipsă", dar de la
 * linia de comanda si spunand pe rand ce gaseste: cate documente sunt, care s-a
 * ales pentru fiecare firma, ce nume a iesit din el si daca s-a schimbat ceva.
 *
 * S-a nascut dintr-un „documentele sunt descarcate dar nu actualizeaza nimic",
 * din care nu se putea sti unde se rupe lantul: la gasirea documentelor, la
 * citirea lor, sau la scrierea numelui.
 */
class DenumiriDinIdentificare extends Command
{
    protected $signature = 'anaf:denumiri
                            {--client= : doar pentru clientul cu acest id}
                            {--cif=* : doar pentru aceste coduri fiscale}
                            {--pe-uscat : arată ce s-ar face, fără să schimbe nimic}';

    protected $description = 'Ia denumirile firmelor din documentele „DATE IDENTIFICARE" deja descărcate';

    public function handle(): int
    {
        $clienti = $this->option('client')
            ? [(int) $this->option('client')]
            : AnafCertificat::query()->toateCompaniile()->distinct()->pluck('company_id')->all();

        if ($clienti === []) {
            $this->warn('Niciun client de cercetat.');

            return 1;
        }

        foreach ($clienti as $client) {
            $this->cerceteaza((int) $client);
        }

        return 0;
    }

    protected function cerceteaza(int $client): void
    {
        $this->line('');
        $this->line('=== Clientul ' . $client);

        ContextCompanie::pentru($client, function () use ($client) {
            $cifuri = (array) $this->option('cif');

            $documente = SpvSolicitare::where('tip_document', 'like', '%DATE IDENTIFICARE%')
                ->when($cifuri !== [], function ($intrebare) use ($cifuri) {
                    return $intrebare->whereIn('cif', $cifuri);
                })
                ->orderByDesc('created_at')
                ->get();

            $cuFisier = $documente->filter(function (SpvSolicitare $s) {
                return $s->cale_fisier || $s->arhiva_cale;
            });

            $this->line('  solicitări „DATE IDENTIFICARE": ' . $documente->count()
                . ', dintre care cu document adus: ' . $cuFisier->count());

            if ($cuFisier->isEmpty()) {
                $this->warn('  Nu e nimic de citit: documentele n-au ajuns pe server și nici în arhiva clientului.');

                return;
            }

            // Ce nume au firmele acum, ca sa se vada ce s-a schimbat.
            $inainte = AnafSocietate::pluck('denumire', 'cif')->all();

            if ($this->option('pe-uscat')) {
                foreach ($cuFisier->unique('cif') as $solicitare) {
                    $this->line('  ' . $solicitare->cif . ': s-ar citi documentul din '
                        . ($solicitare->cale_fisier ? 'server' : 'arhiva clientului')
                        . ', acum se numește „' . ($inainte[$solicitare->cif] ?? '—') . '"');
                }

                return;
            }

            $rezultat = app(SolicitareService::class)->citesteDenumirileDinIdentificare($cifuri);

            $this->line('  citite: ' . $rezultat['citite'] . ', denumiri puse: ' . $rezultat['denumiri']);

            $dupa = AnafSocietate::pluck('denumire', 'cif')->all();

            foreach ($dupa as $cif => $denumire) {
                $vechi = $inainte[$cif] ?? null;

                if ($vechi === $denumire) {
                    continue;
                }

                $this->info('  ' . $cif . ': „' . ($vechi ?: '—') . '" -> „' . $denumire . '"');
            }

            /*
             * Cand s-au citit documente si totusi nu s-a schimbat nimic, nu e o
             * defectiune: numele erau deja bune. Se spune, ca sa nu para ca
             * lucrarea n-a rulat.
             */
            if ($rezultat['citite'] > 0 && $inainte == $dupa) {
                $this->line('  Nimic de schimbat: numele erau deja cele din documente.');
            }
        });
    }
}
