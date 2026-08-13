<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

/**
 * Vectorul fiscal al unei luni, in Excel: aceleasi randuri si coloane ca pe
 * hartie — entitatile pe randuri, declaratiile pe coloane, in casuta recipisa
 * sau periodicitatea cu atentionare.
 *
 * Primeste raportul gata intocmit de RaportVectorLunar, nu o interogare:
 * PDF-ul si Excelul poarta astfel exact aceleasi cifre.
 */
class VectorLunarExport implements FromCollection, WithHeadings, WithTitle, WithStyles, ShouldAutoSize
{
    use Exportable;

    /** Albastrul institutional al modulului SPV Curier. */
    protected const NAVY = 'FF22406F';

    /** @var array */
    protected $raport;

    public function __construct(array $raport)
    {
        $this->raport = $raport;
    }

    public function title(): string
    {
        return sprintf('Vector %02d.%d', $this->raport['luna'], $this->raport['anul']);
    }

    public function headings(): array
    {
        return array_merge(
            ['Nr crt', 'CUI', 'Denumire'],
            $this->raport['tipuri'],
            ['Nedepuse']
        );
    }

    public function collection()
    {
        $randuri = collect($this->raport['randuri'])->map(function ($rand) {
            $celule = [];

            foreach ($this->raport['tipuri'] as $tip) {
                $celule[] = $this->textCelula($rand['celule'][$tip] ?? null);
            }

            return array_merge(
                [$rand['nr'], $rand['cui'], $rand['denumire']],
                $celule,
                [$rand['lipsa'] ?: '']
            );
        });

        // Randul TOTAL, ca pe hartie: cate depuse din cate datorate, pe coloana.
        $total = ['', '', 'TOTAL depuse / datorate'];

        foreach ($this->raport['tipuri'] as $tip) {
            $total[] = $this->raport['total'][$tip]['depuse'] . '/' . $this->raport['total'][$tip]['datorate'];
        }

        $total[] = '';

        return $randuri->push($total);
    }

    /**
     * Ce se scrie in casuta: recipisa cu momentul depunerii, sau
     * periodicitatea, cu atentionare cand declaratia era a lunii si lipseste.
     */
    protected function textCelula(?array $celula): string
    {
        if ($celula === null) {
            return '';
        }

        if ($celula['depusa']) {
            $text = $celula['index_recipisa'] . "\n" . trim($celula['data_depunere'] . ' ' . $celula['ora_depunere']);

            return $celula['rectificativa'] ? $text . "\nrectificativă" : $text;
        }

        $text = $celula['periodicitate'] ?: 'periodicitate necunoscută';

        return $celula['atentionare'] ? $text . "\n! NEDEPUSĂ" : $text;
    }

    public function styles(Worksheet $sheet)
    {
        $ultimulRand = count($this->raport['randuri']) + 2; // antet + randuri + TOTAL

        // Antetul poarta albastrul SPV Curier, ca PDF-ul si ca modulul.
        $sheet->getStyle('1:1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
            'fill' => ['fillType' => 'solid', 'startColor' => ['argb' => self::NAVY]],
        ]);

        $sheet->getStyle($ultimulRand . ':' . $ultimulRand)->applyFromArray([
            'font' => ['bold' => true, 'color' => ['argb' => self::NAVY]],
        ]);

        // Casutele au randuri multiple (index + moment); fara wrap s-ar lati urat.
        $sheet->getStyle('A1:' . $sheet->getHighestColumn() . $ultimulRand)
            ->getAlignment()
            ->setWrapText(true)
            ->setVertical(Alignment::VERTICAL_TOP);

        return [];
    }
}
