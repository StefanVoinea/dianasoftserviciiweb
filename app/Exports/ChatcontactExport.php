<?php
namespace App\Exports;

use App\Chatcontact;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ChatcontactExport implements FromQuery, WithHeadings
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
        "nume",
        "functia",
        "status",
        "email",
        "telefon",
        "linkpoza",
        "program",
        "tip contact",
        ];
    }
    public function query()
    {
        return Chatcontact::query()->select(
        "company_id",
        "nume",
        "functia",
        "status",
        "email",
        "telefon",
        "link_poza",
        "program",
        "tip_contact",)->where("company_id",$this->company_id);
    }
}
