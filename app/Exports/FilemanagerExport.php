<?php
namespace App\Exports;

use App\Models\Filemanager;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;

class FilemanagerExport implements FromQuery, WithHeadings
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
        "grupa",
        "denumire",
        "data ultimei revizii",
        "status",
        "obs",
        "fisier",
        "fisier original",
        "tip fisier",
        "data inceput valabilitate",
        "data sfarsit valabilitate",
        ];
    }
    public function query()
    {
        return Filemanager::query()->select(
        "gestiune_id",
        "grupa",
        "denumire",
        "data_ultimei_revizii",
        "status",
        "obs",
        "fisier",
        "fisier_original",
        "tip_fisier",
        "data_inceput",
        "data_sfarsit",)->where("company_id",$this->company_id);
    }
}
