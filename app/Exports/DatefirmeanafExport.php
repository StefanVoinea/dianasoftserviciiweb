<?php
namespace App\Exports;

use App\Datefirmeanaf;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;

class DatefirmeanafExport implements FromQuery, WithHeadings
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
        "adresa",
        "telefon",
        "iban",
        "cod postal",
        ];
    }
    public function query()
    {
        return Datefirmeanaf::query()->select(
        "cui",
        "adresa",
        "telefon",
        "iban",
        "cod_postal",)->where("company_id",$this->company_id);
    }
}
