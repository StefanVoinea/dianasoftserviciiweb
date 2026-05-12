<?php
namespace App\Exports;

use App\Models\Task;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;

class TaskExport implements FromQuery, WithHeadings
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
            
        "assigned by",
        "assigned to",
        "titlu",
        "descriere",
        "termen executare",
        "tags",
        "data executarii",
        "executat",
        "sters",
        "important",
        "executat de catre",
        ];
    }
    public function query()
    {
        return Task::query()->select(
        "assignedby_id",
        "assignedto_id",
        "title",
        "description",
        "duedate",
        "tags",
        "completed_at",
        "iscompleted",
        "isdeleted",
        "isimportant",
        "completedby_id",)->where("company_id",$this->company_id);
    }
}
