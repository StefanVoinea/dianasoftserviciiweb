<?php

namespace App\Console\Commands;

use App\Models\Cursbnr;
use Carbon\Carbon;
use Illuminate\Console\Command;

/**
 * Umple tabelul de cursuri cu un an intreg din arhiva BNR.
 *
 * Preluarea zilnica aduce doar cursul zilei; cand ea a stat (cum a stat din
 * aprilie pana in august), in urma ramane un gol, iar orice data din el
 * primeste cursul ultimei zile dinainte. Arhiva anuala a BNR
 * (bnr.ro/files/xml/years/nbrfxrates<an>.xml) are toate zilele; de aici se
 * scriu cele lipsa, fara sa se atinga cele existente.
 */
class UmpleCursBnr extends Command
{
    protected $signature = 'curs:umple {an? : anul de umplut (implicit cel curent)}';

    protected $description = 'Umple cursurile BNR lipsă dintr-un an, din arhiva anuală BNR';

    /** Valutele tinute in tabel; restul din arhiva se lasa deoparte. */
    protected const VALUTE = ['EUR', 'USD'];

    public function handle(): int
    {
        $an = (int) ($this->argument('an') ?: now()->year);

        // Arhiva sta pe gazda cursurilor (curs.bnr.ro); pe www.bnr.ro adresa
        // veche intoarce acum pagina de prezentare, nu XML-ul.
        $xml = @file_get_contents('https://curs.bnr.ro/files/xml/years/nbrfxrates' . $an . '.xml');

        if ($xml === false) {
            $this->error('Arhiva BNR pentru ' . $an . ' nu a putut fi adusă.');

            return 1;
        }

        $anterior = libxml_use_internal_errors(true);
        $document = simplexml_load_string($xml);
        libxml_use_internal_errors($anterior);

        if ($document === false || !isset($document->Body->Cube)) {
            $this->error('Arhiva BNR nu are forma așteptată.');

            return 1;
        }

        $scrise = 0;

        foreach ($document->Body->Cube as $zi) {
            $comunicat = (string) $zi['date'];

            if ($comunicat === '') {
                continue;
            }

            foreach ($zi->Rate as $rand) {
                $valuta = strtoupper((string) $rand['currency']);

                if (!in_array($valuta, self::VALUTE, true)) {
                    continue;
                }

                // Cursul anuntat intr-o zi se aplica in ziua urmatoare, ca la
                // preluarea zilnica.
                $curs = Cursbnr::firstOrCreate(
                    [
                        'tip_valuta' => $valuta,
                        'data' => Carbon::parse($comunicat)->addDay()->format('Y-m-d'),
                    ],
                    [
                        'data_comunicarii' => $comunicat,
                        'curs' => (float) $rand,
                    ]
                );

                if ($curs->wasRecentlyCreated) {
                    $scrise++;
                }
            }
        }

        $this->info('Anul ' . $an . ': ' . $scrise . ' cursuri lipsă scrise.');

        return 0;
    }
}
