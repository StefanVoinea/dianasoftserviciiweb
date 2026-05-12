<?php
namespace App\Exports;

use App\Optiunidropdown;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;

class OptiunidropdownExport implements FromQuery, WithHeadings
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
        "field name",
        "field option",
        ];
    }
    public function query()
    {
        return Optiunidropdown::query()->select(
        "comapny_id",
        "field_name",
        "field_option",)->where("company_id",$this->company_id);
    }
}
