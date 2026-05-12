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

class SablonExport implements WithMultipleSheets,Responsable
{
  use Exportable;
  public function forCompany(int $company_id,string $titluSheet, $tabel,$antetTabel,$groupBy,$totalBy,$columnFormat,$titluRaport)
    {
        $this->company_id = $company_id;
        $this->titluSheet=$titluSheet;
        $this->tabel=$tabel;
        $this->antetTabel=$antetTabel;
        $this->totalBy=$totalBy;
        $this->groupBy=$groupBy;
        $this->columnFormat=$columnFormat;
        $this->titluRaport=$titluRaport;
        return $this;
    }

   public function sheets(): array
    {       $sheets = [];
            
            $sheets[$this->titluSheet] = (new Sablon)->forCompany($this->company_id,$this->titluSheet,$this->tabel,
              $this->antetTabel,$this->totalBy,$this->groupBy,$this->columnFormat,$this->titluRaport);
         return $sheets;
    }
}
