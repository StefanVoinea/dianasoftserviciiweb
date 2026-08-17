<?php

namespace App\Services\Anaf\Etransport\Import;

use App\Services\Anaf\Etransport\EtransportException;
use PhpOffice\PhpSpreadsheet\IOFactory;

/**
 * Excelul cu detaliile facturii, cum trimite Store-Lab („details Invoice").
 *
 * Coloanele sunt numite pe italiană și pot veni în altă ordine, așa că se caută
 * după nume, nu după poziție: Articolo, Quantità, Descr.Articolo, ID dogan.
 * (codul vamal), M.In (țara de origine), Tot.Kg, Tot.Netto. Unele fișiere au și
 * coloana „fattura", când un singur Excel acoperă mai multe facturi.
 *
 * Greutatea brută nu există în aceste fișiere: rămâne de completat în formular,
 * unde greutatea totală de pe DDT/CMR se împarte pe linii.
 */
class ImportExcelDetalii implements ParserFisier
{
    /** Cum se recunosc coloanele: numele din antet, pe variante. */
    protected const COLOANE = [
        'cod_tarifar' => ['id dogan.', 'id dogan', 'id doganale', 'taric', 'cod vamal'],
        'denumire' => ['descr.articolo', 'descrizione', 'descriere', 'denumire'],
        'cantitate' => ['quantità', 'quantita', 'cantitate', 'qty'],
        'greutate_neta' => ['tot.kg', 'tot kg', 'kg tot', 'greutate'],
        'valoare' => ['tot.netto', 'tot netto', 'valoare', 'total'],
        'tara_origine' => ['m.in', 'made in', 'origine'],
        'document' => ['fattura', 'factura', 'invoice'],
    ];

    public function citeste(string $cale): array
    {
        $reader = IOFactory::createReaderForFile($cale);
        $reader->setReadDataOnly(true);
        $sheet = $reader->load($cale)->getActiveSheet();

        $randuri = $sheet->toArray(null, true, false);

        [$pozitii, $inceput] = $this->gasesteAntetul($randuri);

        $linii = [];

        foreach (array_slice($randuri, $inceput) as $rand) {
            $cod = preg_replace('/\D/', '', (string) ($rand[$pozitii['cod_tarifar']] ?? ''));

            // Randurile de total si cele goale nu au cod vamal.
            if (!in_array(strlen($cod), [4, 6, 8], true)) {
                continue;
            }

            $linii[] = [
                'cod_tarifar' => str_pad($cod, 8, '0', STR_PAD_LEFT),
                'denumire' => trim((string) $this->valoare($rand, $pozitii, 'denumire')),
                'cantitate' => $this->numar($this->valoare($rand, $pozitii, 'cantitate')),
                'um' => 'H87',
                'greutate_neta' => $this->numar($this->valoare($rand, $pozitii, 'greutate_neta')),
                'greutate_bruta' => null,
                'valoare' => $this->numar($this->valoare($rand, $pozitii, 'valoare')),
                'tara_origine' => trim((string) $this->valoare($rand, $pozitii, 'tara_origine')) ?: null,
                'document' => trim((string) $this->valoare($rand, $pozitii, 'document')) ?: null,
            ];
        }

        return ['linii' => $linii, 'antet' => ['valuta' => 'EUR']];
    }

    /** @return array{0: array<string, int>, 1: int} pozitiile coloanelor si primul rand cu date */
    protected function gasesteAntetul(array $randuri): array
    {
        foreach (array_slice($randuri, 0, 10, true) as $index => $rand) {
            $pozitii = [];

            foreach ($rand as $coloana => $celula) {
                $nume = mb_strtolower(trim((string) $celula));

                foreach (self::COLOANE as $camp => $variante) {
                    if (!isset($pozitii[$camp]) && in_array($nume, $variante, true)) {
                        $pozitii[$camp] = $coloana;
                    }
                }
            }

            if (isset($pozitii['cod_tarifar'], $pozitii['cantitate'])) {
                return [$pozitii, $index + 1];
            }
        }

        throw new EtransportException(
            'Excelul nu are antetul așteptat: lipsesc coloanele cu codul vamal („ID dogan.") și cantitatea.'
        );
    }

    protected function valoare(array $rand, array $pozitii, string $camp)
    {
        return isset($pozitii[$camp]) ? ($rand[$pozitii[$camp]] ?? null) : null;
    }

    protected function numar($valoare): ?float
    {
        if ($valoare === null || $valoare === '') {
            return null;
        }

        if (is_numeric($valoare)) {
            return (float) $valoare;
        }

        // Numar scris ca text, eventual in format italian: 1.595,96
        $text = str_replace(' ', '', (string) $valoare);

        if (preg_match('/,\d{1,3}$/', $text)) {
            $text = str_replace('.', '', $text);
            $text = str_replace(',', '.', $text);
        } else {
            $text = str_replace(',', '', $text);
        }

        return is_numeric($text) ? (float) $text : null;
    }
}
