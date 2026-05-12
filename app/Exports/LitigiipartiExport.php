<?php
namespace App\Exports;

use App\Models\Litigiiparti;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;

class LitigiipartiExport implements FromQuery, WithHeadings
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
        "nume",
        "calitate",
        ];
    }
    public function query()
    {
        return Litigiiparti::query()->select(
        "litigiu_id",
        "nume",
        "calitate",)->where("company_id",$this->company_id);
    }
}
