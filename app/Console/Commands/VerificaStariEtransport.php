<?php

namespace App\Console\Commands;

use App\Models\EtransportDeclaratie;
use App\Services\Anaf\Etransport\EtransportException;
use App\Services\Anaf\Etransport\StareDeclaratie;
use App\Services\Anaf\Jurnal;
use App\Support\ContextCompanie;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * [2026-09-10] Întreabă ANAF ce s-a ales de declarațiile e-Transport depuse.
 *
 * La încărcare ANAF dă un cod UIT și atât; verdictul vine după prelucrare, care
 * ține de la câteva secunde la câteva minute. Fără comanda asta, o declarație
 * respinsă rămânea „depusă" până când se gândea cineva să apese „Verifică" sau
 * „Preia" — iar șoferul putea pleca la drum cu un UIT pe care ANAF nu-l
 * recunoaște.
 *
 * Trece prin fiecare client în parte, ca autorizarea OAuth și declarațiile să
 * cadă în dreptul clientului potrivit.
 */
class VerificaStariEtransport extends Command
{
    protected $signature = 'anaf:etransport-stari
                            {--client= : doar clientul cu acest id}
                            {--zile=7 : cât de în urmă se caută declarații nelămurite}';

    protected $description = 'Verifică la ANAF starea declarațiilor e-Transport depuse și încă nelămurite';

    public function handle(StareDeclaratie $stari): int
    {
        $clienti = $this->clienti();

        if ($clienti === []) {
            $this->line('Nicio declarație e-Transport care să aștepte verdictul ANAF.');

            return 0;
        }

        $total = ['verificate' => 0, 'validate' => 0, 'respinse' => 0, 'in_lucru' => 0, 'esuate' => 0];

        foreach ($clienti as $companie) {
            ContextCompanie::pentru($companie, function () use ($stari, &$total) {
                $declaratii = EtransportDeclaratie::inPrelucrare()
                    ->where('depusa_la', '>=', now()->subDays((int) $this->option('zile')))
                    ->get();

                foreach ($declaratii as $declaratie) {
                    $total['verificate']++;

                    try {
                        $rezultat = $stari->verifica($declaratie);
                    } catch (EtransportException $e) {
                        $total['esuate']++;
                        $this->warn('  #' . $declaratie->id . ': ' . $e->getMessage());

                        continue;
                    }

                    if ($rezultat['stare'] === 'validata') {
                        $total['validate']++;

                        continue;
                    }

                    if ($rezultat['stare'] !== 'respinsa') {
                        $total['in_lucru']++;

                        continue;
                    }

                    $total['respinse']++;
                    $motiv = implode(' | ', $rezultat['erori']);

                    $this->warn('  #' . $declaratie->id . ' (' . $declaratie->referinta_interna . ') respinsă: ' . $motiv);

                    Jurnal::esec(
                        'etransport_declaratie',
                        'ANAF a respins declarația e-Transport #' . $declaratie->id
                            . ' (' . $declaratie->referinta_interna . '): ' . $motiv,
                        $rezultat['raspuns'],
                        $declaratie->cif_declarant
                    );
                }
            });
        }

        $this->info(sprintf(
            'Gata: %d verificate, %d validate, %d respinse, %d încă în prelucrare, %d neîntrebate.',
            $total['verificate'],
            $total['validate'],
            $total['respinse'],
            $total['in_lucru'],
            $total['esuate']
        ));

        return 0;
    }

    /** @return array<int, int> clienții care au declarații depuse și nelămurite */
    protected function clienti(): array
    {
        if ($this->option('client')) {
            return [(int) $this->option('client')];
        }

        return DB::table('etransport_declaratii')
            ->where('stare', 'depusa')
            ->whereNotNull('index_incarcare')
            ->where('depusa_la', '>=', now()->subDays((int) $this->option('zile')))
            ->distinct()
            ->pluck('company_id')
            ->filter()
            ->map(function ($id) {
                return (int) $id;
            })
            ->all();
    }
}
