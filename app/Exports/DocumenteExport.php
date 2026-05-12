<?php
namespace App\Exports;

use App\Models\Documente;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;

class DocumenteExport implements FromQuery, WithHeadings
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
            
        "agentia",
        "denumire doc",
        "tip doc",
        "aplicatie",
        "continut",
        "data",
        "utilizator",
        "data operarii",
        "printabil",
        "exportabil",
        ];
    }
    public function query()
    {
        return Documente::query()->select(
        "agentia",
        "denumire_doc",
        "tip_doc",
        "aplicatie",
        "continut",
        "data",
        "utilizator",
        "data_operarii",
        "printabil",
        "exportabil",)->where("company_id",$this->company_id);
    }
}
