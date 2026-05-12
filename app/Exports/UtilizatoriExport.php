<?php
namespace App\Exports;

use App\Models\Utilizatori;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;

class UtilizatoriExport implements FromQuery, WithHeadings
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
            
        "utilizator",
        "parola",
        "drept de acces",
        "nume complet",
        "gestiune",
        "gestiune2",
        "nr telefon",
        "departament",
        "email",
        "compartiment",
        "data operarii",
        "data parola",
        "functia",
        ];
    }
    public function query()
    {
        return Utilizatori::query()->select(
        "utilizator",
        "parola",
        "drept_de_acces",
        "nume_complet",
        "gestiune",
        "gestiune2",
        "nr_telefon",
        "departament",
        "email",
        "compartiment",
        "data_operarii",
        "data_parola",
        "functia",)->where("company_id",$this->company_id);
    }
}
