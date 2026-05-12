<?php
namespace App\Exports;

use App\Cursbnr;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;

class CursbnrExport implements FromQuery, WithHeadings
{
  use Exportable;
  public function forCompany(int $company_id)
    {
        $this->company_id = $company_id;
        return $this;
    }
    public function headings(): array
    {
        return [
            
        "data",
        "data comunicarii",
        "tip valuta",
        "curs",
        ];
    }
    public function query()
    {
        return Cursbnr::query()->select(
        "data",
        "data_comunicarii",
        "tip_valuta",
        "curs");
    }
}
