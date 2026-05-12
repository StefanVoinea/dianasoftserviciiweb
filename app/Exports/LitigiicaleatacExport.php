<?php
namespace App\Exports;

use App\Models\Litigiicaleatac;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;

class LitigiicaleatacExport implements FromQuery, WithHeadings
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
            
        "idlitigiu",
        "data declarare",
        "parte declaratoare",
        "tip cale atac",
        ];
    }
    public function query()
    {
        return Litigiicaleatac::query()->select(
        "litigiu_id",
        "data_declarare",
        "parte_declaratoare",
        "tip_cale_atac",)->where("company_id",$this->company_id);
    }
}
