<?php
namespace App\Exports;

use App\Emailalerte;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;

class EmailalerteExport implements FromQuery, WithHeadings
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
            
        "companyid",
        "tip alerta",
        "email",
        "cc",
        ];
    }
    public function query()
    {
        return Emailalerte::query()->select(
        "company_id",
        "tip_alerta",
        "email",
        "cc",)->where("company_id",$this->company_id);
    }
}
