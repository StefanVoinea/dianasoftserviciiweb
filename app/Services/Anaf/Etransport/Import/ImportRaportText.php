<?php

namespace App\Services\Anaf\Etransport\Import;

/**
 * Raportul text care însoțește factura, cum trimite Teddy S.p.A.
 *
 * Un raport la imprimantă, cu antet (Sender, Receiver, Doc number) și linii
 * grupate pe țara de origine și codul vamal:
 *
 *   BD   Bangladesh   61046200  Pantaloni,tute con bretelle,...   20,307   22,515   133  EUR   985,23
 *
 * Numerele sunt în format italian (punct la mii, virgulă la zecimale), iar
 * rândurile de total („Total Made IN...", „Total ...") nu încep cu un cod de
 * țară, așa că nu se potrivesc tiparului liniilor și rămân pe dinafară.
 * Raportul are, spre deosebire de Excel, și greutatea brută pe linie.
 */
class ImportRaportText implements ParserFisier
{
    protected const LINIE = '/^\s+([A-Z]{2})\s+\S.*?\s{2,}(\d{4,8})\s+(.+?)\s{2,}([\d.,]+)\s+([\d.,]+)\s+([\d.,]+)\s+([A-Z]{3})\s+([\d.,]+)\s*$/u';

    public function citeste(string $cale): array
    {
        $continut = file_get_contents($cale);

        $linii = [];
        $antet = ['valuta' => null];

        foreach (preg_split('/\r\n|\r|\n/', $continut) as $rand) {
            if (preg_match(self::LINIE, $rand, $gasit)) {
                $cod = $gasit[2];

                $linii[] = [
                    'cod_tarifar' => str_pad($cod, 8, '0', STR_PAD_LEFT),
                    'denumire' => trim($gasit[3]),
                    'cantitate' => $this->numar($gasit[6]),
                    'um' => 'H87',
                    'greutate_neta' => $this->numar($gasit[4]),
                    'greutate_bruta' => $this->numar($gasit[5]),
                    'valoare' => $this->numar($gasit[8]),
                    'tara_origine' => $gasit[1],
                    'document' => null,
                ];

                $antet['valuta'] = $antet['valuta'] ?: $gasit[7];

                continue;
            }

            $this->citesteAntetul($rand, $antet);
        }

        return ['linii' => $linii, 'antet' => $antet];
    }

    protected function citesteAntetul(string $rand, array &$antet): void
    {
        if (preg_match('/^\s*Sender\.*\s*:\s*(.+?)\s*$/i', $rand, $gasit)) {
            $antet['partener_denumire'] = $gasit[1];
        }

        if (preg_match('/Vat\s*N\s*:\s*(\S+)/i', $rand, $gasit)) {
            $antet['partener_cod'] = $gasit[1];
        }

        if (preg_match('/^\s*Doc number\.*\s*:\s*(\S+)\s+of\s+(\d{2}\.\d{2}\.\d{4})/i', $rand, $gasit)) {
            $antet['document_numar'] = $gasit[1];
            $antet['document_data'] = \Carbon\Carbon::createFromFormat('d.m.Y', $gasit[2])->format('Y-m-d');
        }
    }

    /** Numar in format italian: punctul desparte miile, virgula zecimalele. */
    protected function numar(string $text): ?float
    {
        $text = str_replace('.', '', trim($text));
        $text = str_replace(',', '.', $text);

        return is_numeric($text) ? (float) $text : null;
    }
}
