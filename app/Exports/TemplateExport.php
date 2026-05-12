<?php
namespace App\Exports;


use App\Models\Company;
use App\Exports\Sheets\TemplateSheetExportPJ;
use App\Models\Solicitare;
use Carbon\Carbon;
use DB;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Events\BeforeExport;
use Maatwebsite\Excel\Events\BeforeSheet;
use Maatwebsite\Excel\Events\BeforeWriting;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class TemplateExport implements WithEvents
{
    /**
    * Export data 
    * @author Matin Malek
    * @return Array
    */
     public function forCompany(int $company_id,$parametrii,$numetemplate)
    {
        $this->company_id = $company_id;
        $this->parametrii=$parametrii;
        $this->numetemplate=$numetemplate;
        return $this;
    }
    public function registerEvents(): array
    {
        $company=Company::where("id",session("company_id"))->get()->first();
        $parametrii=$this->parametrii;
        $numetemplate=$this->numetemplate;
      return [
         BeforeExport::class => function(BeforeExport $event )use($company,$parametrii,$numetemplate){
            $templateName=new \Maatwebsite\Excel\Files\LocalTemporaryFile(storage_path('app/public/'.$company->slug.'/template'.$numetemplate));
            $event->writer->reopen($templateName,\Maatwebsite\Excel\Excel::XLSX);

            $event->writer->getSheetByIndex(0);
            foreach($parametrii as $parametru){
               $event->getWriter()->getSheetByIndex(0)->setCellValue($parametru["celula"],$parametru["valoare"]);
            }
            return $event->getWriter()->getSheetByIndex(0);
         }
      ];
    }
}  