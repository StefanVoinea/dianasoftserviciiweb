<?php
namespace App\Exports;

use App\Models\Societati;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;

class SocietatiExport implements FromQuery, WithHeadings
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
            
        "denumire",
        "cod firma",
        ];
    }
    public function query()
    {
        return Societati::query()->select(
        "denumire",
        "cod_firma",)->where("company_id",$this->company_id);
    }
}
