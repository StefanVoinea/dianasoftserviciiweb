<?php

namespace App\Services\Anaf\Etransport\Import;

/**
 * Lista pe articole care însoțește nota de credit, cum trimite Teddy S.p.A.
 * (fișierele T01_*). Pentru Emporio, la retururi.
 *
 * Același raport la imprimantă ca recapitulația T02, cu același antet (Sender,
 * Customer, Vat N), dar cu câte un rând pe articol și lot, nu pe cod vamal:
 *
 *   SAB0066865001   1 Pants   BD   Bangladesh   61046200  Pantaloni,tute...   5,282   38 EUR   2,44   92,54
 *
 * Coloanele: articol, lot, descriere scurtă, țara de origine (cod și nume),
 * codul vamal, descrierea codului, greutatea netă, cantitatea, valuta, prețul
 * unitar și valoarea liniei. Gruparea pe cod vamal, cum o face T02, se face
 * apoi în ImportFisiere, ca la orice fișier.
 *
 * Raportul n-are greutate brută pe linie, doar totalul la sfârșit („Total
 * gross weight.: KG 135,107"). Declarația însă cere brutul pe fiecare linie,
 * așa că totalul se împarte pe linii proporțional cu netul, cu rotunjirea
 * potrivită ca suma să dea exact totalul.
 */
class ImportRaportArticole extends ImportRaportText
{
    /** Antetul de coloane, dupa care se recunoaste raportul. */
    public const SEMN = '/^\s*Item_+\s+Lot\s+Description/mi';

    protected const LINIE_ARTICOL = '/^\s+(\S+)\s+(\d+)\s+(.+?)\s{2,}([A-Z]{2})\s+\S.*?\s{2,}(\d{4,8})\s+(.+?)\s{2,}([\d.,]+)\s+([\d.,]+)\s+([A-Z]{3})\s+([\d.,]+)\s+([\d.,]+)\s*$/u';

    /** Este acesta un raport pe articole (T01), nu recapitulatia (T02)? */
    public static function recunoaste(string $continut): bool
    {
        return (bool) preg_match(self::SEMN, $continut);
    }

    public function citeste(string $cale): array
    {
        $continut = file_get_contents($cale);

        $linii = [];
        $antet = ['valuta' => null];
        $brutTotal = null;

        foreach (preg_split('/\r\n|\r|\n/', $continut) as $rand) {
            if (preg_match(self::LINIE_ARTICOL, $rand, $gasit)) {
                $linii[] = [
                    'cod_tarifar' => str_pad($gasit[5], 8, '0', STR_PAD_LEFT),
                    'denumire' => trim($gasit[6]),
                    'cantitate' => $this->numar($gasit[8]),
                    'um' => 'H87',
                    'greutate_neta' => $this->numar($gasit[7]),
                    'greutate_bruta' => null,
                    'valoare' => $this->numar($gasit[11]),
                    'tara_origine' => $gasit[4],
                    'document' => null,
                ];

                $antet['valuta'] = $antet['valuta'] ?: $gasit[9];

                continue;
            }

            if (preg_match('/^\s*Total gross weight\.*\s*:\s*KG\s+([\d.,]+)/i', $rand, $gasit)) {
                $brutTotal = $this->numar($gasit[1]);

                continue;
            }

            $this->citesteAntetul($rand, $antet);
        }

        return ['linii' => $this->imparteBrutul($linii, $brutTotal), 'antet' => $antet];
    }

    /**
     * Pe langa antetul comun, documentul sta pe randul
     * „Documents..........:  10074615 of 02.09.2026".
     */
    protected function citesteAntetul(string $rand, array &$antet): void
    {
        parent::citesteAntetul($rand, $antet);

        if (preg_match('/^\s*Documents\.*\s*:\s*(\S+)\s+of\s+(\d{2}\.\d{2}\.\d{4})/i', $rand, $gasit)) {
            $antet['document_numar'] = $gasit[1];
            $antet['document_data'] = \Carbon\Carbon::createFromFormat('d.m.Y', $gasit[2])->format('Y-m-d');
        }
    }

    /**
     * Brutul total, împărțit pe linii după greutatea netă a fiecăreia.
     *
     * Rotunjirea la trei zecimale lasă o diferență de câteva grame; ea se
     * pune pe ultima linie, ca suma bruturilor să fie exact totalul din raport.
     */
    protected function imparteBrutul(array $linii, ?float $brutTotal): array
    {
        $netTotal = array_sum(array_column($linii, 'greutate_neta'));

        if ($linii === [] || $brutTotal === null || $brutTotal <= 0 || $netTotal <= 0) {
            return $linii;
        }

        $impartit = 0.0;
        $ultima = count($linii) - 1;

        foreach ($linii as $i => &$linie) {
            if ($i === $ultima) {
                $linie['greutate_bruta'] = round($brutTotal - $impartit, 3);

                break;
            }

            $linie['greutate_bruta'] = round($brutTotal * ($linie['greutate_neta'] ?? 0) / $netTotal, 3);
            $impartit += $linie['greutate_bruta'];
        }

        return $linii;
    }
}
