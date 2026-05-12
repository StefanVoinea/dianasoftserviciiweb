<?php
namespace App\Exports;

use App\Models\Notificationuser;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;

class NotificationuserExport implements FromQuery, WithHeadings
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
            
        "tip notificare",
        "user",
        "canal de comunicare",
        ];
    }
    public function query()
    {
        return Notificationuser::query()->select(
        "notificationtype_id",
        "user_id",
        "channel",)->where("company_id",$this->company_id);
    }
}
