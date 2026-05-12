<?php
namespace App\Exports;

use App\Alerteauto;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;

class AlerteautoExport implements FromQuery, WithHeadings
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
            
        "companyid",
        "parcautoid",
        "tip alerta",
        "data intocmire",
        "data expirare",
        "observatii",
        ];
    }
    public function query()
    {
        return Alerteauto::query()->select(
        "company_id",
        "parcauto_id",
        "tip_alerta",
        "data_intocmire",
        "data_expirare",
        "obs")->where("company_id",$this->company_id);
    }
}
