<?php
namespace App\Exports;

use App\Models\Versiuni;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;

class VersiuniExport implements FromQuery, WithHeadings
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
            
        "versiunea",
        "agentia",
        "data",
        ];
    }
    public function query()
    {
        return Versiuni::query()->select(
        "versiunea",
        "agentia",
        "data",)->where("company_id",$this->company_id);
    }
}
