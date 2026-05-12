<?php
namespace App\Exports;

use App\Models\Notificationlog;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;

class NotificationlogExport implements FromQuery, WithHeadings
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
        "de la",
        "user",
        "canal de comunicare",
        "email",
        "telefon",
        "titlu",
        "descriere",
        "tip",
        "icon",
        "avatar",
        "link",
        "categoria",
        "citit la",
        ];
    }
    public function query()
    {
        return Notificationlog::query()->select(
        "notificationtype_id",
        "from",
        "user_id",
        "channel",
        "email",
        "telefon",
        "title",
        "subtitle",
        "type",
        "icon",
        "avatar",
        "link",
        "category",
        "read_at",)->where("company_id",$this->company_id);
    }
}
