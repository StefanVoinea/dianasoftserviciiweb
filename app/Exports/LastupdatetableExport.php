<?php
namespace App\Exports;

use App\Models\Lastupdatetable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;

class LastupdatetableExport implements FromQuery, WithHeadings
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
            
        "tabel",
        "data",
        ];
    }
    public function query()
    {
        return Lastupdatetable::query()->select(
        "tabel",
        "data",)->where("company_id",$this->company_id);
    }
}
