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

    /**
     * Tarile cum le scrie raportul — pe englezeste in cele noi, pe italiana in
     * cele vechi — aduse la codul din declaratie. Grecia e „EL" la ANAF.
     */
    protected const TARI = [
        'italy' => 'IT', 'germany' => 'DE', 'france' => 'FR', 'spain' => 'ES',
        'austria' => 'AT', 'hungary' => 'HU', 'poland' => 'PL', 'netherlands' => 'NL',
        'belgium' => 'BE', 'greece' => 'EL', 'bulgaria' => 'BG', 'portugal' => 'PT',
        'czech republic' => 'CZ', 'czechia' => 'CZ', 'slovakia' => 'SK', 'slovenia' => 'SI',
        'croatia' => 'HR', 'denmark' => 'DK', 'sweden' => 'SE', 'finland' => 'FI',
        'ireland' => 'IE', 'lithuania' => 'LT', 'latvia' => 'LV', 'estonia' => 'EE',
        'luxembourg' => 'LU', 'malta' => 'MT', 'cyprus' => 'CY', 'romania' => 'RO',
        'italia' => 'IT', 'germania' => 'DE', 'francia' => 'FR', 'spagna' => 'ES',
        'ungheria' => 'HU', 'polonia' => 'PL', 'paesi bassi' => 'NL', 'belgio' => 'BE',
        'grecia' => 'EL', 'portogallo' => 'PT', 'repubblica ceca' => 'CZ',
        'slovacchia' => 'SK', 'croazia' => 'HR',
    ];

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

    /**
     * Rapoartele vechi (2024) au antetul pe italiana — Mittente, P.IVA,
     * Numero documento ... del —, cele noi pe engleza. Se citesc amandoua.
     */
    protected function citesteAntetul(string $rand, array &$antet): void
    {
        if (preg_match('/^\s*(?:Sender|Mittente)\.*\s*:\s*(.+?)\s*$/i', $rand, $gasit)) {
            $antet['partener_denumire'] = $gasit[1];
        }

        if (preg_match('/(?:Vat\s*N|P\.?\s?IVA)\s*:\s*(\S+)/i', $rand, $gasit)) {
            $antet['partener_cod'] = $gasit[1];
        }

        /*
         * Tara partenerului sta pe acelasi rand cu codul lui de TVA:
         * „Italy    Vat N: 00953910403" sau „Italia   P.IVA: ...". Doar randul
         * acesta se citeste — blocul destinatarului (Romania) n-are codul.
         */
        if (preg_match('/^\s*([A-Za-z][A-Za-z ]+?)\s{2,}(?:Vat\s*N|P\.?\s?IVA)\s*:/i', $rand, $gasit)) {
            $tara = self::TARI[strtolower(trim($gasit[1]))] ?? null;

            if ($tara !== null) {
                $antet['partener_tara'] = $tara;
            }
        }

        if (preg_match('/^\s*(?:Doc number|Numero documento)\.*\s*:\s*(\S+)\s+(?:of|del)\s+(\d{2}\.\d{2}\.\d{4})/i', $rand, $gasit)) {
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
