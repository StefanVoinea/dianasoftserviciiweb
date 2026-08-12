<?php

namespace App\Console\Commands;

use App\Models\AnafJurnal;
use App\Models\SpvMesaj;
use App\Support\ContextCompanie;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * De ce n-au putut fi aduse documentele.
 *
 * Fila spune „nu s-a putut aduce” si atat. Pricina se scrie insa de doua ori —
 * pe fiecare mesaj, in „ultima_eroare”, si in jurnal, la sfarsitul aducerii —,
 * numai ca la niciuna nu se ajunge din aplicatie.
 *
 * S-a nascut dintr-un client cu doua sute cincizeci de entitati, unde din 568 de
 * documente s-au adus 390, iar restul au cazut unul dupa altul fara ca cineva sa
 * poata spune de ce. Cand toate cad cu aceeasi vorba, vorba aceea e raspunsul.
 */
class EsecurileDescarcarii extends Command
{
    protected $signature = 'anaf:esecuri
                            {--client= : doar pentru clientul cu acest id}
                            {--cif= : doar pentru această firmă}
                            {--exemple=2 : câte documente de arătat pentru fiecare pricină}';

    protected $description = 'Arată de ce n-au putut fi aduse documentele din SPV';

    public function handle(): int
    {
        $clienti = $this->option('client')
            ? [(int) $this->option('client')]
            : SpvMesaj::query()->toateCompaniile()
                ->whereNotNull('ultima_eroare')
                ->distinct()
                ->pluck('company_id')
                ->all();

        if ($clienti === []) {
            $this->info('Niciun document căzut, la niciun client.');

            return 0;
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

        ContextCompanie::pentru($client, function () {
            $intrebarea = SpvMesaj::whereNotNull('ultima_eroare')
                ->when($this->option('cif'), function ($q) {
                    return $q->where('cif', $this->option('cif'));
                });

            $cate = (clone $intrebarea)->count();

            if ($cate === 0) {
                $this->info('  Niciun document căzut.');

                return;
            }

            $this->line('  Documente căzute: ' . $cate);
            $this->line('');

            /*
             * Se grupeaza dupa pricina, nu se insira una cate una: cand o suta
             * de documente cad din acelasi motiv, motivul e unul singur, si el
             * trebuie citit. Insiruirea le-ar ascunde tocmai in multimea lor.
             */
            $pricini = (clone $intrebarea)
                ->select('ultima_eroare', DB::raw('count(*) as cate'), DB::raw('max(updated_at) as ultima'))
                ->groupBy('ultima_eroare')
                ->orderByDesc('cate')
                ->get();

            foreach ($pricini as $pricina) {
                $this->line('  ' . $pricina->cate . ' × „' . $this->pescurt($pricina->ultima_eroare) . '"');
                $this->line('      ultima dată: ' . $pricina->ultima);

                $exemple = (clone $intrebarea)
                    ->where('ultima_eroare', $pricina->ultima_eroare)
                    ->orderByDesc('updated_at')
                    ->limit((int) $this->option('exemple'))
                    ->get(['mesaj_id', 'cif', 'tip', 'incercari']);

                foreach ($exemple as $exemplu) {
                    $this->line('      ' . $exemplu->mesaj_id . '  ' . $exemplu->cif
                        . '  ' . $exemplu->tip . '  (încercări: ' . $exemplu->incercari . ')');
                }

                $this->line('');
            }

            $this->arataJurnalul();
        });
    }

    /**
     * Ultimele aduceri, ca sa se vada tiparul: cate s-au adus inainte de a se
     * strica ceva. Un numar mare urmat de esecuri seci spune altceva decat
     * esecuri de la bun inceput.
     */
    protected function arataJurnalul(): void
    {
        $intrari = AnafJurnal::where('actiune', 'mesaje_descarcare')
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        if ($intrari->isEmpty()) {
            return;
        }

        $this->line('  Ultimele aduceri:');

        foreach ($intrari as $intrare) {
            $this->line('    ' . $intrare->created_at->format('d.m.Y H:i') . '  '
                . ($intrare->reusit ? '✓' : '✗') . '  ' . $intrare->descriere);
        }
    }

    protected function pescurt(?string $vorba): string
    {
        $vorba = trim(preg_replace('/\s+/', ' ', (string) $vorba));

        return mb_strlen($vorba) > 160 ? mb_substr($vorba, 0, 160) . '…' : $vorba;
    }
}
