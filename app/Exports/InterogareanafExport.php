<?php
namespace App\Exports;

use App\Interogareanaf;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;

class InterogareanafExport implements FromQuery, WithHeadings
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
            
        "company id",
        "user id",
        "data",
        "cod fiscal",
        "raspuns",
        ];
    }
    public function query()
    {
        return Interogareanaf::query()->select(
        "company_id",
        "user_id",
        "data",
        "cui",
        "raspuns")->where("company_id",$this->company_id);
    }
}
