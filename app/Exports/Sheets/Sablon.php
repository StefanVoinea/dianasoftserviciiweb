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

class Sablon implements FromView, WithHeadings,WithTitle,ShouldAutoSize,WithEvents,WithColumnFormatting//,WithDrawings
{
  use Exportable;
  public function forCompany(int $company_id,$titluSheet,$tabel,$antetTabel,$totalBy,$groupBy,$columnFormat,$titluRaport)
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
    public function headings(): array
    {
        // $head=$this->centralizator->keys()->all();
        // return $head;
    }
    /*public function drawings()
    {
        $drawing = new Drawing();
        $drawing->setName('Antet');
        $drawing->setDescription('Antet firma');
        $drawing->setPath(public_path('/images/logo/dianasoft/antetxls.png'));
       
        $drawing->setCoordinates('A1');
        $drawing->setHeight(40);
      
        

        return $drawing;
    }*/
    public function columnFormats(): array
    {
        return $this->columnFormat;
    }
   public function title(): string
    {
        return $this->titluSheet;
    }
    public function registerEvents(): array
    {
        
        return [
             AfterSheet::class    => function(AfterSheet $event) {
                $cellRange = 'A1:W1000'; // All headers
                $event->sheet->getDelegate()->getStyle($cellRange)->getAlignment()->setWrapText(true);
               } 
            /*
            AfterSheet::class    => function(AfterSheet $event) {
                $cellRange = 'A5:W7'; // All headers
                $event->sheet->getDelegate()->getStyle($cellRange)->getFont()->setSize(14);
                $i=7; 
                foreach($this->tabel as $rand){
                 // if(($rand instanceof stdClass))
                 if((count($this->groupBy)==0))
                 {
                    $i=$i+1;
                    // Log::info("RAND NORMAL ".$i);
                  }   
                  else{  
                        $i=$i+1; 
                        // Log::info("INCEPUT GRUP 1 ".$i);
                        // Log::info($rand);
                        $cellRange = 'A'.$i.':W'.$i;
                        $event->sheet->getDelegate()->getStyle($cellRange)->getFont()->setSize(14);
                     foreach($rand as $randGrup){
                         // if($randGrup instanceof stdClass)
                        if((count($this->groupBy)==1))
                         {

                            $i=$i+1;
                            // Log::info("RAND NORMAL GRUP1 ".$i);
                         }   
                         else{  
                                 $i=$i+1;

                        // Log::info("INCEPUT GRUP 2 ".$i);
                        // Log::info($randGrup);
                        $cellRange = 'A'.$i.':W'.$i;
                        $event->sheet->getDelegate()->getStyle($cellRange)->getFont()->setSize(14);
                     foreach($randGrup as $randGrup1){

                         // if($randGrup1 instanceof stdClass)
                        if((count($this->groupBy)==2))
                         {

                            $i=$i+1;
                            // Log::info("RAND NORMAL GRUP2 ".$i);
                         }   
                         else{  
                                 $i=$i+1; 
                        // Log::info("INCEPUT GRUP 3 ".$i);
                        // Log::info($randGrup1);
                        $cellRange = 'A'.$i.':W'.$i;
                        $event->sheet->getDelegate()->getStyle($cellRange)->getFont()->setSize(14);
                     foreach($randGrup1 as $randGrup2){
                         // if($randGrup2 instanceof stdClass)
                        if((count($this->groupBy)==3))
                         {

                            $i=$i+1;
                            // Log::info("RAND NORMAL GRUP3 ".$i);
                         }   
                         else{  
                                 $i=$i+1;
                                 $cellRange = 'A'.$i.':W'.$i;
                                 $event->sheet->getDelegate()->getStyle($cellRange)->getFont()->setSize(14);
                                 // Log::info("INCEPUT GRUP4 ".$i);
                                 $i=$i+count($randGrup2)+1;
                                 $cellRange = 'A'.$i.':W'.$i;
                                 $event->sheet->getDelegate()->getStyle($cellRange)->getFont()->setSize(14);
                                 // Log::info("SFARSIT GRUP4 ".$i);
                         }
                     }
                         $i=$i+1;
                         $cellRange = 'A'.$i.':W'.$i;
                         $event->sheet->getDelegate()->getStyle($cellRange)->getFont()->setSize(14);
                         // Log::info("SFARSIT GRUP3 ".$i);
                         }
                     }
                         $i=$i+1;
                         $cellRange = 'A'.$i.':W'.$i;
                         $event->sheet->getDelegate()->getStyle($cellRange)->getFont()->setSize(14);
                         // Log::info("SFARSIT GRUP2 ".$i);
                            
                         }
                     }
                         $i=$i+1;
                         $cellRange = 'A'.$i.':W'.$i;
                         $event->sheet->getDelegate()->getStyle($cellRange)->getFont()->setSize(14);
                         // Log::info("SFARSIT GRUP1 ".$i);
                       
                  }
                 
                 }
                 $i=$i+1;
                $cellRange = 'A'.$i.':W'.$i; // All headers
                $event->sheet->getDelegate()->getStyle($cellRange)->getFont()->setSize(14);
            },
            */
        ];
    }

    public function view():View
    {
         
         $i=1; 
       

         return view('excel.sablon', [
            'tabel' => $this->tabel,
            'antetTabel'=>$this->antetTabel,
            'totalBy'=>$this->totalBy,
            'groupBy'=>$this->groupBy,
            'titluRaport'=>$this->titluRaport,
            // 'company'=>$this->company,
            'i'=>$i
        ]);
    }
}