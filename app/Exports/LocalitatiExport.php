<?php
namespace App\Exports;

use App\Localitati;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;

class LocalitatiExport implements FromQuery, WithHeadings
{
  use Exportable;
  public function forCompany(int $company_id)
    {
        
        return $this;
    }
    public function headings(): array
    {
        return [
            
        "denumire",
        "judet",
        ];
    }
    public function query()
    {
        return Localitati::query()->select(
        "denumire",
        "judet");
    }
}
