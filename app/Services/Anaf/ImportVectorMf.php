<?php

namespace App\Services\Anaf;

use App\Models\VectorDeclaratie;

/**
 * Aduce periodicitatile de depunere din programul vechi de contabilitate.
 *
 * Tabelul „vectormf” tine cate un rand pe firma, cu cate o coloana pe
 * declaratie si periodicitatea in ea: D112 „Lunar”, D100 „Trimestrial” si asa
 * mai departe. Randurile intra in vector_declaratii ca randuri "manuala":
 * cuvantul omului, care bate deductia aplicatiei si nu e rescris de ea.
 */
class ImportVectorMf
{
    /** Coloanele care chiar numesc o declaratie; D1–D5 sunt sloturi fara nume. */
    public const TIPURI = ['D112', 'D300', 'D394', 'D100', 'D101', 'D390', 'D205', 'D200', 'D208'];

    /**
     * Importa un CSV al tabelului vectormf pentru un client anume.
     *
     * Compania se scrie explicit pe fiecare rand si intra si in cheia de
     * cautare: importul se face si din administrare, unde nu exista un client
     * curent, iar fara companie in cheie randul unui client l-ar calca pe al
     * altuia.
     *
     * @return array{firme: int, scrise: int, sarite: array<int, string>}
     */
    public function importaCsv(string $cale, int $companie): array
    {
        $fisier = fopen($cale, 'r');

        if ($fisier === false) {
            throw new \RuntimeException('Fișierul nu a putut fi citit: ' . $cale);
        }

        $antet = fgetcsv($fisier);

        if ($antet === false) {
            fclose($fisier);

            throw new \RuntimeException('CSV-ul este gol.');
        }

        // BOM-ul UTF-8 din fata primei coloane, pus de unele exporturi, se curata.
        $antet[0] = preg_replace('/^\xEF\xBB\xBF/', '', $antet[0]);
        $antet = array_map(function ($coloana) {
            return strtolower(trim((string) $coloana));
        }, $antet);

        $firme = 0;
        $scrise = 0;
        $sarite = [];

        while (($rand = fgetcsv($fisier)) !== false) {
            $date = array_combine($antet, array_pad($rand, count($antet), ''));
            $cui = trim((string) ($date['cui'] ?? ''));

            if ($cui === '') {
                continue;
            }

            $firme++;

            foreach ($date as $coloana => $valoare) {
                $perfisc = ucfirst(strtolower(trim((string) $valoare)));

                if (!in_array($perfisc, VectorDeclaratie::PERIODICITATI, true)) {
                    continue;
                }

                $tip = strtoupper($coloana);

                // Sloturile fara nume (D1–D5) nu spun ce declaratie e: se sar,
                // dar se spun la sfarsit, ca omul sa hotarasca el.
                if (!in_array($tip, self::TIPURI, true)) {
                    $sarite[] = $cui . ' (' . trim((string) ($date['denumire'] ?? '')) . '): '
                        . $tip . ' = ' . $perfisc;

                    continue;
                }

                VectorDeclaratie::query()->toateCompaniile()->updateOrCreate(
                    [
                        'company_id' => $companie,
                        'cui' => $cui,
                        'tip' => $tip,
                        'sursa' => 'manuala',
                        'data_inceput' => null,
                    ],
                    [
                        'perfisc' => $perfisc,
                        'obligatii' => 'importat din vector.mde',
                    ]
                );

                $scrise++;
            }
        }

        fclose($fisier);

        return ['firme' => $firme, 'scrise' => $scrise, 'sarite' => $sarite];
    }
}
