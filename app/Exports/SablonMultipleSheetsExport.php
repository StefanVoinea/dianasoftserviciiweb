<?php
namespace App\Exports;

use App\Company;

use App\Exports\Sheets\Sablon;
use Carbon\Carbon;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class SablonMultipleSheetsExport implements WithMultipleSheets,Responsable
{
  use Exportable;
  public function forCompany(int $company_id, $sheeturi,$titluRaport)
    {
        $this->company_id = $company_id;
        $this->sheeturi=$sheeturi;
 
        $this->titluRaport=$titluRaport;
        return $this;
    }

   public function sheets(): array
    {       $sheets = [];
            foreach($this->sheeturi as $sheet){
            $sheets[$sheet->titluSheet] = (new Sablon)->forCompany($this->company_id,$sheet->titluSheet,$sheet->tabel,
              $sheet->antetTabel,$sheet->totalBy,$sheet->groupBy,$sheet->columnFormat,$this->titluRaport);
            }
         return $sheets;
    }
}