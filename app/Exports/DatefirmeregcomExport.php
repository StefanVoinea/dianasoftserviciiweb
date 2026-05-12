<?php
namespace App\Exports;

use App\Datefirmeregcom;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;

class DatefirmeregcomExport implements FromQuery, WithHeadings
{
  use Exportable;
  public function forCompany()
    {
      
        return $this;
    }
    public function headings(): array
    {
        return [
            
        "denumire",
        "cod fiscal",
        "reg com",
        "adresa",
        "localitate",
        "judet",
        "telefon",
        "email",
        ];
    }
    public function query()
    {
        return Datefirmeregcom::query()->select(
        "denumire",
        "cui",
        "regcom",
        "adresa",
        "localitate",
        "judet",
        "telefon",
        "email");
    }
}
