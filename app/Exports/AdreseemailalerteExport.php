<?php
namespace App\Exports;

use App\Models\Adreseemailalerte;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;

class AdreseemailalerteExport implements FromQuery, WithHeadings
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
            
        "alerta",
        "adresa email",
        ];
    }
    public function query()
    {
        return Adreseemailalerte::query()->select(
        "alerta",
        "adresa_email",)->where("company_id",$this->company_id);
    }
}
