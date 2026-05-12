<?php
namespace App\Exports;

use App\Models\Tokenuri;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;

class TokenuriExport implements FromQuery, WithHeadings
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
            
        "access token",
        "refresh token",
        "data expirarii",
        "data obtinerii",
        ];
    }
    public function query()
    {
        return Tokenuri::query()->select(
        "access_token",
        "refresh_token",
        "data_expirarii",
        "data_obtinere",)->where("company_id",$this->company_id);
    }
}
