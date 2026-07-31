<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

/**
 * Jurnalul de activitate, scos in Excel exact cum se vede in tabel.
 *
 * Primeste randurile deja pregatite de controler, nu o interogare: asa fisierul
 * poarta chiar ce a filtrat omul pe ecran, cu aceleasi etichete si aceeasi
 * ordine, fara sa fie nevoie ca filtrele sa fie scrise a doua oara aici.
 */
class JurnalAnafExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    use Exportable;

    /** @var \Illuminate\Support\Collection */
    protected $randuri;

    public function __construct($randuri)
    {
        $this->randuri = collect($randuri);
    }

    public function headings(): array
    {
        return [
            'Când',
            'Utilizator',
            'Email',
            'Acțiune',
            'Descriere',
            'CIF',
            'IP',
            'Rezultat',
        ];
    }

    public function collection()
    {
        return $this->randuri->map(function ($intrare) {
            return [
                $intrare['cand'],
                $intrare['utilizator'],
                $intrare['email'],
                $intrare['actiune_eticheta'] ?: $intrare['actiune'],
                $intrare['descriere'],
                $intrare['cif'],
                $intrare['ip'],
                $intrare['reusit'] ? 'reușit' : 'eșuat',
            ];
        });
    }
}
