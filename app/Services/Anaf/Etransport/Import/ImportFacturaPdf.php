<?php

namespace App\Services\Anaf\Etransport\Import;

/**
 * [2026-10-14] Factura PDF a furnizorului, venită fără rapoartele text.
 *
 * Marfa trimisă prin curier (DHL) n-are T02 și D01: vine doar factura, ca PDF
 * („INVOICE FTI MV"). Din textul ei se scot numărul și data, magazinul (codul
 * NEG*), articolele cu cantitatea și valoarea, greutatea coletului și valuta.
 *
 * Factura nu are coduri vamale: rămân de completat de om. Greutatea e a
 * coletului întreg, așa că se împarte pe articole după valoare. Serviciile —
 * garanția, de pildă — nu sunt marfă și nu intră în linii.
 */
class ImportFacturaPdf
{
    /** E o factură a furnizorului, după ce scrie în ea? */
    public static function recunoaste(string $text): bool
    {
        $text = self::curat($text);

        return preg_match('/\bINVOICE\b/', $text) === 1
            && preg_match('/TEDDY/i', $text) === 1
            && preg_match('/^\s*[A-Z]{2,4}\s+\d{5,8}\s+\d{3}\s/m', $text) === 1;
    }

    /**
     * @return array{numar: ?string, data: ?string, magazin_cod: ?string, valuta: string, linii: array, servicii: array<int, string>}
     */
    public function citeste(string $text): array
    {
        $text = self::curat($text);
        $randuri = preg_split('/\r\n|\r|\n/', $text);
        [$numar, $data] = $this->factura($text);
        [$articole, $servicii] = $this->articolele($randuri);

        return [
            'numar' => $numar,
            'data' => $data,
            'magazin_cod' => preg_match('/\b(NEG\d{4,})\b/', $text, $m) ? mb_strtoupper($m[1]) : null,
            'valuta' => preg_match('/^\s*(EUR|USD|RON|GBP)\s*$/m', $text, $m) ? $m[1] : 'EUR',
            'linii' => $this->linii($articole, $this->greutatile($randuri)),
            'servicii' => $servicii,
        ];
    }

    /** „INVOICE   FTI MV" și, pe rândul de sub el, „10076871     08.09.2026". */
    protected function factura(string $text): array
    {
        if (preg_match('/\bINVOICE\b[^\n]*\n\s*(\d{6,})\s+(\d{2})\.(\d{2})\.(\d{4})/', $text, $m)) {
            return [$m[1], $m[4] . '-' . $m[3] . '-' . $m[2]];
        }

        return [null, null];
    }

    /**
     * Rândurile de articole, adunate pe articol; serviciile, deoparte.
     *
     * „ ITC 0000917 001 1     KIT PATHFINDER 6140/BATTERIA  NR    1,000    1.700,00    1.700,00"
     *
     * @param array<int, string> $randuri
     * @return array{0: array<string, array>, 1: array<int, string>}
     */
    protected function articolele(array $randuri): array
    {
        $tipar = '/^\s*([A-Z]{2,4})\s+(\d{5,8})\s+\d{3}((?:\s\S{1,2})*)\s{2,}(.+?)\s{2,}(\S{1,3})\s+([\d.]+,\d+)\s+([\d.]+,\d+)(?:\s+[\d.]+,\d+-)?\s+([\d.]+,\d+)\s*$/';
        $articole = [];
        $servicii = [];

        foreach ($randuri as $rand) {
            if (!preg_match($tipar, $rand, $m)) {
                continue;
            }

            $cod = $m[1] . ' ' . $m[2];
            $denumire = trim(preg_replace('/\s+/', ' ', $m[4]));

            // Garantia si celelalte servicii nu sunt marfa: n-au ce cauta in transport.
            if (preg_match('/SERVIZIO|GARANZIA|SERVICE|WARRANTY/i', $denumire)) {
                $servicii[] = $cod . ' ' . $denumire . ' (' . $m[8] . ')';

                continue;
            }

            if (!isset($articole[$cod])) {
                $articole[$cod] = ['cod' => $cod, 'denumire' => $denumire, 'cantitate' => 0.0, 'valoare' => 0.0];
            }

            $articole[$cod]['cantitate'] += $this->numar($m[6]);
            $articole[$cod]['valoare'] += $this->numar($m[8]);
        }

        return [$articole, array_values(array_unique($servicii))];
    }

    /**
     * Liniile declarației, cu greutatea coletului împărțită după valoare.
     *
     * @param array{0: float, 1: float} $greutati
     */
    protected function linii(array $articole, array $greutati): array
    {
        [$neta, $bruta] = $greutati;
        $articole = array_values($articole);
        $total = array_sum(array_column($articole, 'valoare'));
        $netaRamasa = $neta;
        $brutaRamasa = $bruta;
        $linii = [];

        foreach ($articole as $i => $articol) {
            $ultimul = $i === count($articole) - 1;
            $parte = $total > 0 ? $articol['valoare'] / $total : 1 / max(1, count($articole));

            // Ultimul ia restul, ca suma liniilor sa fie exact greutatea coletului.
            $n = $ultimul ? $netaRamasa : round($neta * $parte, 3);
            $b = $ultimul ? $brutaRamasa : round($bruta * $parte, 3);
            $netaRamasa = round($netaRamasa - $n, 3);
            $brutaRamasa = round($brutaRamasa - $b, 3);

            $linii[] = [
                'cod_tarifar' => '',
                'denumire' => $articol['denumire'] . ' (' . $articol['cod'] . ')',
                'cantitate' => round($articol['cantitate'], 3),
                'um' => 'H87',
                // ANAF si Intrastat cer greutatea pozitiva pe fiecare linie.
                'greutate_neta' => $n > 0 ? max(0.01, $n) : null,
                'greutate_bruta' => max(0.01, $b),
                'valoare' => round($articol['valoare'], 2),
                'valoare_lei' => null,
                'tara_origine' => null,
                'document' => null,
                'scop_operatiune' => 101,
            ];
        }

        return $linii;
    }

    /**
     * Greutatea netă și brută a coletului, de pe rândul de sub „Kg   Kg".
     *
     * @param array<int, string> $randuri
     * @return array{0: float, 1: float}
     */
    protected function greutatile(array $randuri): array
    {
        foreach ($randuri as $i => $rand) {
            if (!preg_match('/^\s*Kg\s+Kg\s*$/i', $rand)) {
                continue;
            }

            for ($j = $i + 1; $j < min(count($randuri), $i + 3); $j++) {
                if (preg_match('/^\s*([\d.]+,\d+)\s+([\d.]+,\d+)\s*$/', $randuri[$j], $m)) {
                    return [$this->numar($m[1]), $this->numar($m[2])];
                }
            }
        }

        return [0.0, 0.0];
    }

    /**
     * Spatiile din PDF sunt „non-breaking" (U+00A0), nu spatii obisnuite: asa
     * nu se potrivea niciun rand. Se aduc la spatiu simplu inainte de citire.
     */
    protected static function curat(string $text): string
    {
        return str_replace(["\u{00A0}", "\t"], ' ', $text);
    }

    /** „1.700,00" → 1700.0 */
    protected function numar(string $text): float
    {
        return (float) str_replace(',', '.', str_replace('.', '', $text));
    }
}
