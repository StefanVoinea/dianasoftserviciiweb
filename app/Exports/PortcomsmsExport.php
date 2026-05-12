<?php
namespace App\Exports;

use App\Models\Portcomsms;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;

class PortcomsmsExport implements FromQuery, WithHeadings
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
            
        "portcom",
        ];
    }
    public function query()
    {
        return Portcomsms::query()->select(
        "portcom",)->where("company_id",$this->company_id);
    }
}
