<?php

namespace App\Services\Anaf\Etransport\Import;

/**
 * [2026-10-09] Documentul de transport al mărfii gratuite (DT1_*).
 *
 * Odată cu facturile, Teddy trimite uneori și marfă dată gratis magazinelor:
 * fundaluri de Crăciun, bandă adezivă, hârtie pentru transferuri. Ea nu are
 * factură, ci un „D.O.T OF SHIPMENT" (DDT) cu mențiunea „Omaggio" și „FREE
 * GOODS, GOODS NOT FOR SALE". Pleacă în același camion, la același magazin, deci
 * intră în aceeași declarație ca factura magazinului, pe linii cu valoare zero.
 *
 * Documentul nu poartă cod vamal pe articol, nici greutatea fiecăruia: are doar
 * greutatea netă și brută a coletului. Codul vamal rămâne de completat de om, iar
 * greutatea se împarte pe articole după cantitate.
 */
class ImportDdtGratuit
{
    /** Scopul operațiunii pentru marfa primită gratuit. */
    public const SCOP_GRATUITATI = 301;

    /** E un document de transport de marfă gratuită? */
    public static function recunoaste(string $nume, string $continut): bool
    {
        $dupaNume = preg_match('/^DT1_/i', $nume) === 1;
        $dupaContinut = stripos($continut, 'D.O.T OF SHIPMENT') !== false;

        return ($dupaNume || $dupaContinut)
            && preg_match('/FREE\s+GOODS|Omaggio/i', $continut) === 1;
    }

    /**
     * @return array{numar: ?string, data: ?string, magazin_cod: ?string, linii: array<int, array>}
     */
    public function citeste(string $continut): array
    {
        $randuri = preg_split('/\r\n|\r|\n/', $continut);

        return [
            'numar' => $this->numarul($continut),
            'data' => $this->data($continut),
            'magazin_cod' => preg_match('/\b(NEG\d{4,})\b/', $continut, $m) ? mb_strtoupper($m[1]) : null,
            'linii' => $this->linii($randuri),
        ];
    }

    /** „127429/G   09.10.2026" — numărul fără zerourile din față. */
    protected function numarul(string $continut): ?string
    {
        if (preg_match('/^\s*(\d+)\/[A-Z]\s+\d{2}\.\d{2}\.\d{4}/m', $continut, $m)) {
            return ltrim($m[1], '0') ?: $m[1];
        }

        if (preg_match('/DDT_[A-Z]+_0*(\d+)_/i', $continut, $m)) {
            return $m[1];
        }

        return null;
    }

    protected function data(string $continut): ?string
    {
        if (preg_match('/^\s*\d+\/[A-Z]\s+(\d{2})\.(\d{2})\.(\d{4})/m', $continut, $m)) {
            return $m[3] . '-' . $m[2] . '-' . $m[1];
        }

        return null;
    }

    /**
     * Articolele, cu greutatea coletului împărțită după cantitate.
     *
     * Rândul unui articol: „  VIS 0000988 001   Christmas paper backgrounds 60 x 200   Pri   4,000".
     *
     * @param array<int, string> $randuri
     */
    protected function linii(array $randuri): array
    {
        $articole = [];
        [$neta, $bruta] = $this->greutatile($randuri);

        foreach ($randuri as $rand) {
            if (!preg_match('/^\s{1,6}([A-Z]{2,4})\s+(\d{5,8})\s+\d{3}\s+(.+?)\s{2,}\S+\s+([\d.]+,\d+)\s*$/', $rand, $m)) {
                continue;
            }

            $cantitate = (float) str_replace(',', '.', str_replace('.', '', $m[4]));

            if ($cantitate <= 0) {
                continue;
            }

            $articole[] = [
                'articol' => $m[1] . ' ' . $m[2],
                'denumire' => trim(preg_replace('/\s+/', ' ', $m[3])),
                'cantitate' => $cantitate,
            ];
        }

        $total = array_sum(array_column($articole, 'cantitate'));
        $linii = [];
        $netaRamasa = $neta;
        $brutaRamasa = $bruta;

        foreach ($articole as $i => $articol) {
            $ultimul = $i === count($articole) - 1;
            $parte = $total > 0 ? $articol['cantitate'] / $total : 0;

            // Ultimul ia restul, ca suma liniilor să fie exact greutatea coletului.
            $n = $ultimul ? $netaRamasa : round($neta * $parte, 3);
            $b = $ultimul ? $brutaRamasa : round($bruta * $parte, 3);
            $netaRamasa = round($netaRamasa - $n, 3);
            $brutaRamasa = round($brutaRamasa - $b, 3);

            $linii[] = [
                'cod_tarifar' => '',
                'denumire' => $articol['denumire'] . ' (' . $articol['articol'] . ', marfă gratuită)',
                'cantitate' => $articol['cantitate'],
                'um' => 'H87',
                // ANAF cere greutatea brută pozitivă pe fiecare linie.
                'greutate_neta' => $n > 0 ? max(0.01, $n) : null,
                'greutate_bruta' => max(0.01, $b),
                'valoare' => 0,
                'valoare_lei' => 0,
                'tara_origine' => null,
                'document' => null,
                'scop_operatiune' => self::SCOP_GRATUITATI,
            ];
        }

        return $linii;
    }

    /**
     * Greutatea netă și brută a coletului, de pe rândul de sub „KG   KG".
     *
     * @param array<int, string> $randuri
     * @return array{0: float, 1: float}
     */
    protected function greutatile(array $randuri): array
    {
        foreach ($randuri as $i => $rand) {
            if (!preg_match('/^\s*KG\s+KG\s*$/', $rand)) {
                continue;
            }

            for ($j = $i + 1; $j < min(count($randuri), $i + 3); $j++) {
                if (preg_match('/^\s*([\d.]+,\d+)\s+([\d.]+,\d+)\s*$/', $randuri[$j], $m)) {
                    $numar = function ($text) {
                        return (float) str_replace(',', '.', str_replace('.', '', $text));
                    };

                    return [$numar($m[1]), $numar($m[2])];
                }
            }
        }

        return [0.0, 0.0];
    }
}
