<?php
namespace App\Exports;

use App\Models\Litigiu;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;

class LitigiuExport implements FromQuery, WithHeadings
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
            
        "numar dosar",
        "numar vechi",
        "data dosar",
        "institutia",
        "departament",
        "categorie caz",
        "stadiu procesual",
        "avocatul apararii",
        "avocatul acuzarii",
        "observatii",
        "status",
        "taxa de timbru",
        "cheltuieli de judecata",
        "parti",
        ];
    }
    public function query()
    {
        return Litigiu::query()->select(
        "numar_dosar",
        "numar_vechi",
        "data_dosar",
        "institutie",
        "departament",
        "categorie_caz",
        "stadiu_procesual",
        "avocatul_apararii",
        "avocatul_acuzarii",
        "observatii",
        "status",
        "taxa_de_timbru",
        "cheltuieli_de_judecata",
        "parti",)->where("company_id",$this->company_id);
    }
}
