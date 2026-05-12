<?php
namespace App\Exports;

use App\sarbatorilegale;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;

class sarbatorilegaleExport implements FromQuery, WithHeadings
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
        ];
    }
    public function query()
    {
        return sarbatorilegale::query()->select(
        "data",)->where("company_id",$this->company_id);
    }
}
