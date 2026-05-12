<?php
namespace App\Exports;

use App\Models\Monitorizaredocumente;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;

class MonitorizaredocumenteExport implements FromQuery, WithHeadings
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
            
        "gestiuneid",
        "contractid",
        "userid",
        "tip document",
        "fisier",
        ];
    }
    public function query()
    {
        return Monitorizaredocumente::query()->select(
        "gestiune_id",
        "contract_id",
        "user_id",
        "tip_document",
        "fisier",)->where("company_id",$this->company_id);
    }
}
