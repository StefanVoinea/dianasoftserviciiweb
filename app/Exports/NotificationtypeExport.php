<?php
namespace App\Exports;

use App\Models\Notificationtype;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;

class NotificationtypeExport implements FromQuery, WithHeadings
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
            
        "categoria",
        "denumire",
        ];
    }
    public function query()
    {
        return Notificationtype::query()->select(
        "categoria",
        "denumire",)->where("company_id",$this->company_id);
    }
}
