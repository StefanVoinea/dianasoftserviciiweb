<?php
namespace App\Exports;

use App\Models\Ipautorizat;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;

class IpautorizatExport implements FromQuery, WithHeadings
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
            
        "ip",
        "utilizator",
        ];
    }
    public function query()
    {
        return Ipautorizat::query()->select(
        "ip",
        "utilizator",)->where("company_id",$this->company_id);
    }
}
