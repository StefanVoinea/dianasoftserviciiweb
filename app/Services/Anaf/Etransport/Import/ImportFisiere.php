<?php

namespace App\Services\Anaf\Etransport\Import;

use App\Models\EtransportCodVamal;
use App\Services\Anaf\Etransport\EtransportException;

/**
 * Liniile declarației e-Transport, culese din fișierele furnizorului.
 *
 * Furnizorii trimit datele în forme diferite — un Excel cu detaliile facturii,
 * un raport text pe lângă factura de la alt program — dar toate spun același
 * lucru: ce marfă pleacă, cât cântărește și cât costă. De aici iese o singură
 * listă de linii, indiferent câte fișiere și în ce forme au venit.
 *
 * O linie are: cod_tarifar, denumire, cantitate, um, greutate_neta,
 * greutate_bruta, valoare (în valuta fișierului), tara_origine, document.
 */
class ImportFisiere
{
    /**
     * @param array<int, array{nume: string, cale: string}> $fisiere
     * @return array{linii: array, antet: array, avertismente: array<int, string>}
     */
    public function importa(array $fisiere, bool $grupate = true): array
    {
        $linii = [];
        $antet = [];
        $avertismente = [];

        foreach ($fisiere as $fisier) {
            $rezultat = $this->parserPentru($fisier)->citeste($fisier['cale']);

            if ($rezultat['linii'] === []) {
                $avertismente[] = 'Din „' . $fisier['nume'] . '" nu s-a putut citi nicio linie.';

                continue;
            }

            $linii = array_merge($linii, $rezultat['linii']);

            // Primul fisier care aduce un antet il da; restul doar completeaza golurile.
            $antet = array_merge(array_filter($rezultat['antet']), $antet);
        }

        if ($grupate) {
            $linii = $this->grupeaza($linii);
        }

        $linii = $this->completeazaDenumirile($linii);

        return ['linii' => array_values($linii), 'antet' => $antet, 'avertismente' => $avertismente];
    }

    protected function parserPentru(array $fisier): ParserFisier
    {
        $extensie = strtolower(pathinfo($fisier['nume'], PATHINFO_EXTENSION));

        if (in_array($extensie, ['xlsx', 'xls', 'ods'], true)) {
            return new ImportExcelDetalii();
        }

        if (in_array($extensie, ['txt', 'text', 'prn'], true)) {
            return new ImportRaportText();
        }

        throw new EtransportException(
            'Fișierul „' . $fisier['nume'] . '" nu e într-un format cunoscut (se acceptă Excel sau text).'
        );
    }

    /**
     * Adună liniile cu același cod vamal, cum face și raportul furnizorului:
     * declarația nu are nevoie de fiecare articol, ci de fiecare fel de marfă.
     */
    protected function grupeaza(array $linii): array
    {
        $grupate = [];

        foreach ($linii as $linie) {
            $cheie = $linie['cod_tarifar'] ?: ('fara-cod-' . count($grupate));

            if (!isset($grupate[$cheie])) {
                $grupate[$cheie] = $linie;

                continue;
            }

            foreach (['cantitate', 'greutate_neta', 'greutate_bruta', 'valoare'] as $camp) {
                if ($linie[$camp] !== null) {
                    $grupate[$cheie][$camp] = round(($grupate[$cheie][$camp] ?? 0) + $linie[$camp], 3);
                }
            }
        }

        return $grupate;
    }

    /**
     * Codul vamal își aduce denumirea din nomenclator: descrierea din fișier e
     * pe limba furnizorului și, la liniile grupate, doar a primului articol.
     */
    protected function completeazaDenumirile(array $linii): array
    {
        $coduri = array_filter(array_column($linii, 'cod_tarifar'));

        if ($coduri === []) {
            return $linii;
        }

        $denumiri = EtransportCodVamal::whereIn('cod', $coduri)->pluck('denumire', 'cod');

        foreach ($linii as &$linie) {
            $dinNomenclator = $linie['cod_tarifar'] ? ($denumiri[$linie['cod_tarifar']] ?? null) : null;

            if ($dinNomenclator) {
                $linie['denumire'] = $this->scurteaza($dinNomenclator);
            }
        }

        return $linii;
    }

    /**
     * Denumirea din nomenclator, adusă la cele 200 de caractere permise de ANAF.
     *
     * Denumirea plină e lanțul ierarhic („Taioare, ansambluri, ... — Pantaloni
     * — Din bumbac"). Când nu încape, tăiat de la coadă ar pieri tocmai partea
     * care deosebește codul; se păstrează începutul (poziția) și sfârșitul
     * (partea distinctivă), cu mijlocul scos.
     */
    protected function scurteaza(string $denumire): string
    {
        if (mb_strlen($denumire) <= 200) {
            return $denumire;
        }

        $parti = explode(' — ', $denumire);
        $coada = count($parti) > 1 ? array_pop($parti) : '';
        $cap = $parti[0];

        $loc = 200 - mb_strlen($coada) - mb_strlen(' … — ');

        return mb_substr(mb_substr($cap, 0, max($loc, 50)) . ' … — ' . $coada, 0, 200);
    }
}
