<?php
namespace App\Exports;

use App\Models\Datefinanciarepj;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;

class DatefinanciarepjExport implements FromQuery, WithHeadings
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
            
        "cui",
        "anul",
        "indicator",
        "valoare indicator",
        "denumire indicator",
        ];
    }
    public function query()
    {
        return Datefinanciarepj::query()->select(
        "cui",
        "an",
        "indicator",
        "val_indicator",
        "val_den_indicator",)->where("company_id",$this->company_id);
    }
}
