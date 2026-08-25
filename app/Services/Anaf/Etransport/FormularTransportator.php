<?php

namespace App\Services\Anaf\Etransport;

use App\Models\EtransportDeclaratie;
use App\Models\EtransportGestiune;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

/**
 * Formularul cu codurile UIT pentru transportator.
 *
 * După ce declarațiile unei zile de transport și-au primit UIT-urile, șoferul
 * trebuie să le aibă scrise pe CMR-uri. Transportatorul primea până acum un
 * fișier întocmit de mână, cu câte o foaie pe magazin; de aici iese același
 * fișier, gata completat: punctul de trecere, vehiculul, transportatorul,
 * locul de descărcare și codul UIT al fiecărei facturi.
 */
class FormularTransportator
{
    /** Gestiunile companiei, pe codul furnizorului; se încarcă o dată. */
    protected $gestiuni;

    /**
     * @return array{nume: string, continut: string, foi: int}
     */
    public function genereaza(array $iduri): array
    {
        $declaratii = EtransportDeclaratie::whereIn('id', $iduri)
            ->whereNotNull('uit')
            ->orderBy('id')
            ->get();

        if ($declaratii->isEmpty()) {
            throw new EtransportException('Niciuna dintre declarațiile alese nu are cod UIT.');
        }

        $registru = new Spreadsheet();
        $registru->removeSheetByIndex(0);

        $numeFolosite = [];

        foreach ($declaratii as $declaratie) {
            $this->foaia($registru, $declaratie, $numeFolosite);
        }

        $scriitor = new Xlsx($registru);

        ob_start();
        $scriitor->save('php://output');
        $continut = ob_get_clean();

        $prima = $declaratii->first();

        return [
            'nume' => 'FORM_ROMANIA_'
                . preg_replace('/[^A-Za-z0-9]+/', '_', (string) ($prima->partener_denumire ?: 'TRANSPORT'))
                . '_' . ($prima->data_transport ? $prima->data_transport->format('d.m.Y') : now()->format('d.m.Y'))
                . '.xlsx',
            'continut' => $continut,
            'foi' => $declaratii->count(),
        ];
    }

    protected function foaia(Spreadsheet $registru, EtransportDeclaratie $d, array &$numeFolosite): void
    {
        $magazin = $d->loc_final['magazin_denumire'] ?? null;

        // Titlul foii, ca in fisierul facut de mana: prescurtarea gestiunii.
        if ($this->gestiuni === null) {
            $this->gestiuni = EtransportGestiune::peCodFurnizor();
        }

        $gestiune = isset($d->loc_final['magazin_cod'])
            ? ($this->gestiuni[mb_strtoupper($d->loc_final['magazin_cod'])] ?? null)
            : null;

        if ($gestiune !== null) {
            $magazin = $gestiune->prescurtare ?: $gestiune->denumire;
        }

        $titlu = $magazin ?: ($d->loc_final['localitate'] ?? ('Factura ' . ($d->documente[0]['numar'] ?? $d->id)));

        // Numele foii: fara caracterele oprite de Excel, cel mult 31, unic.
        $nume = mb_substr(trim(str_replace(['\\', '/', ':', '*', '?', '[', ']', "'"], ' ', $titlu)), 0, 28) ?: 'Foaie';

        $unic = $nume;
        $al = 2;

        while (in_array($unic, $numeFolosite, true)) {
            $unic = $nume . ' ' . $al++;
        }

        $numeFolosite[] = $unic;

        $foaie = $registru->createSheet();
        $foaie->setTitle($unic);
        $foaie->getColumnDimension('A')->setWidth(2);
        $foaie->getColumnDimension('B')->setWidth(70);
        $foaie->getColumnDimension('C')->setWidth(30);

        $foaie->setCellValue('B2', mb_strtoupper($titlu));
        $foaie->getStyle('B2')->getFont()->setBold(true)->setSize(14);

        $descarcare = trim(implode(', ', array_filter([
            $d->loc_final['strada'] ?? null,
            $d->loc_final['localitate'] ?? null,
        ])));

        $randuri = [
            ['DATI RICHIESTI', $this->ptf($d)],
            ['-Vehicle registration number (plate number for tractor head/unit)', $d->nr_vehicul],
            ['-Trailer registration number1 (plate number for car trailer1)', $d->nr_remorca1],
            ['-Trailer registration number2 (plate number for car trailer2)', $d->nr_remorca2],
            ['-Country of carrier', $d->transportator_tara === 'RO' ? 'ROMANIA' : $d->transportator_tara],
            ['-Name of carrier', $d->transportator_denumire],
            ['-CUI carrier', $d->transportator_cod],
            ['-Date of start the transport', $d->data_transport ? $d->data_transport->format('d.m.Y') : ''],
            ['-Place of load', $this->taraIncarcarii($d)],
            ['-Place of unload', $descarcare],
            ['-Transport documents :', ''],
        ];

        $rand = 4;

        foreach ($randuri as [$eticheta, $valoare]) {
            $foaie->setCellValue('B' . $rand, $eticheta);
            $foaie->setCellValueExplicit(
                'C' . $rand,
                (string) $valoare,
                \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING
            );
            $rand++;
        }

        $foaie->getStyle('B4:C4')->getFont()->setBold(true);

        foreach ($d->documente ?: [] as $document) {
            $fel = ((int) ($document['tip'] ?? 0)) === 10 ? 'CMR number' : 'Invoice number';
            $cand = !empty($document['data'])
                ? \Carbon\Carbon::parse($document['data'])->format('d.m.Y')
                : '';

            $foaie->setCellValue(
                'B' . $rand,
                '     -COD UIT for ' . $fel . ' ' . ($document['numar'] ?? '') . ($cand ? ' / ' . $cand : '')
            );
            $foaie->setCellValueExplicit(
                'C' . $rand,
                (string) $d->uit,
                \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING
            );
            $foaie->getStyle('C' . $rand)->getFont()->setBold(true);
            $rand++;
        }

        $foaie->getStyle('B2:C' . $rand)->getAlignment()->setVertical(Alignment::VERTICAL_TOP);
    }

    protected function ptf(EtransportDeclaratie $d): string
    {
        $cod = $d->loc_start['cod_ptf'] ?? null;

        if ($cod === null) {
            return '';
        }

        // "Borș 2 - A3 (HU)" -> "BORS 2 - A3", cum il scrie transportatorul:
        // fara tara din paranteza si fara diacritice.
        $nume = Nomenclatoare::PTF[(int) $cod] ?? (string) $cod;
        $nume = trim(preg_replace('/\s*\([A-Z]{2}\)\s*$/', '', $nume));
        $nume = strtr($nume, ['ă' => 'a', 'â' => 'a', 'î' => 'i', 'ș' => 's', 'ț' => 't', 'Ă' => 'A', 'Â' => 'A', 'Î' => 'I', 'Ș' => 'S', 'Ț' => 'T']);

        return mb_strtoupper($nume);
    }

    protected function taraIncarcarii(EtransportDeclaratie $d): string
    {
        $tari = ['IT' => 'ITALIA', 'DE' => 'GERMANIA', 'HU' => 'UNGARIA', 'FR' => 'FRANTA', 'ES' => 'SPANIA'];

        return $tari[$d->partener_tara] ?? (string) $d->partener_tara;
    }
}
