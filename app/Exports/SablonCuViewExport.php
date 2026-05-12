<?php
namespace App\Exports;

use App\Company;

use App\Exports\Sheets\SablonCuView;
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

class SablonCuViewExport implements WithMultipleSheets,Responsable
{
  use Exportable;
  public function forCompany(string $titluSheet,$numeView,$varsView)
    {
       
        $this->titluSheet=$titluSheet;
        $this->numeView=$numeView;
        $this->varsView=$varsView;
        return $this;
    }

   public function sheets(): array
    {       $sheets = [];
            $sheets[$this->titluSheet] = (new SablonCuView)->forCompany($this->titluSheet,$this->numeView,$this->varsView);
         return $sheets;
    }
}
