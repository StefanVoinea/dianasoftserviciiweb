<?php
namespace App\Exports;

use App\Models\Documentepdf;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;

class DocumentepdfExport implements FromQuery, WithHeadings
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
            
        "grupa",
        "denumire",
        "descriere",
        "fisier",
        "data",
        "acces",
        ];
    }
    public function query()
    {
        return Documentepdf::query()->select(
        "grupa",
        "denumire",
        "descriere",
        "fisier",
        "data",
        "acces",)->where("company_id",$this->company_id);
    }
}
