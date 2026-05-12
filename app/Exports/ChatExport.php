<?php
namespace App\Exports;

use App\Chat;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ChatExport implements FromQuery, WithHeadings
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
        "userid",
        "mesaj",
        "linkfisier",
        "vazut",
        "sters",
        "catreid",
        "tipcatre",
        "arhivat",
        "taguri",
        ];
    }
    public function query()
    {
        return Chat::query()->select(
        "company_id",
        "user_id",
        "mesaj",
        "link_fisier",
        "vazut",
        "sters",
        "catre_id",
        "tip_catre",
        "arhivat",
        "taguri",)->where("company_id",$this->company_id);
    }
}
