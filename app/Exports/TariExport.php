<?php
namespace App\Exports;

use App\Tari;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;

class TariExport implements FromQuery, WithHeadings
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
            
        "cod",
        "denumire",
        "capitala",
        "forma de guvernare",
        "cod tara fiscal",
        "cod bnr",
        "valuta",
        "cod sm",
        "ue",
        ];
    }
    public function query()
    {
        return Tari::query()->select(
        "cod",
        "denumire",
        "capitala",
        "forma_guvernare",
        "cod_tara_fiscal",
        "cod_bnr",
        "valuta",
        "cod_sm",
        "ue")->where("company_id",$this->company_id);
    }
}
