<?php
namespace App\Exports\Sheets;

use App\Models\Company;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\WithDrawings;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

class SablonCuView implements FromView, WithHeadings,WithTitle,ShouldAutoSize,WithEvents,WithColumnFormatting//,WithDrawings
{
  use Exportable;
  public function forCompany($titluSheet,$numeView,$varsView)
    {
       
        $this->titluSheet=$titluSheet;
        $this->numeView=$numeView;
        $this->varsView=$varsView;
        return $this;
    }
  
    public function headings(): array
    {
        
        // $head=$this->centralizator->keys()->all();
        // return $head;
    }

    public function columnFormats(): array
    {
        return [];
    }
   public function title(): string
    {
        return $this->titluSheet;
    }
    public function registerEvents(): array
    {
        
      return [];
    }
    /*
    public function drawings()
    {
        $drawing = new Drawing();
        $drawing->setName('Antet');
        $drawing->setDescription('Antet firma');
        
        $drawing->setPath(public_path('/images/logo/fortuna/antetxls.jpg'));
       
        $drawing->setCoordinates('A1');
       $drawing->setHeight(80);
        $drawing->setResizeProportional(true);
        // $drawing->setCoordinates('A1');

        return $drawing;
    }
  */
    public function view():View
    {
         return view($this->numeView, $this->varsView);
    }
}