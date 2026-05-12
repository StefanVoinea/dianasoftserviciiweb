<?php
namespace App\Exports;

use App\Judet;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;

class JudetExport implements FromQuery, WithHeadings
{
  use Exportable;
  public function forCompany(int $company_id)
    {
        
        return $this;
    }
    public function headings(): array
    {
        return [
            
        "cod",
        "denumire",
        "auto",
        ];
    }
    public function query()
    {
        return Judet::query()->select(
        "cod",
        "denumire",
        "auto");
    }
}
