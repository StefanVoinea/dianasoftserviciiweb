<?php
namespace App\Exports;

use App\Models\Litigiisedinte;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;

class LitigiisedinteExport implements FromQuery, WithHeadings
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
            
        "idlitigiu",
        "complet",
        "data sedinta",
        "ora sedinta",
        "solutie",
        "solutie sumar",
        "data pronuntare",
        "document sedinta",
        "numar document",
        "data document",
        ];
    }
    public function query()
    {
        return Litigiisedinte::query()->select(
        "litigiu_id",
        "complet",
        "data_sedinta",
        "ora_sedinta",
        "solutie",
        "solutie_sumar",
        "data_pronuntare",
        "document_sedinta",
        "numar_document",
        "data_document",)->where("company_id",$this->company_id);
    }
}
