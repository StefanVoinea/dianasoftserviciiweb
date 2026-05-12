<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    //use RecordsActivity;
    protected $table ="task";
    protected $fillable = ["company_id","assignedby_id","assignedto_id","title","description","duedate","tags","completed_at","iscompleted","isdeleted","isimportant","completedby_id",];
    protected $casts = [
            "iscompleted" => "boolean","isdeleted" => "boolean","isimportant" => "boolean",
           'completed_at' => 'datetime','duedate' => 'datetime',
           'tags'=>'array'
    
    ];
    public  $groupByXLS=[
                            // ["col"=>"denumire_partener","denumire"=>"Partener","type"=>"","align"=>"center","width"=>"100%"],
                            // ["col"=>"contd","denumire"=>"Debit","type"=>"","align"=>"center","width"=>"100%"],
                           ];   
   public $totalByXLS=[];
   public $titluRaportXLS="Task";
   public $columnFormatXLS=[
                                 // "D" => NumberFormat::FORMAT_DATE_DDMMYYYY,
                                 // "I" => NumberFormat::FORMAT_CURRENCY_EUR_SIMPLE
                               ];
    public  $antetTabelXLS=[
                      //["col"=>"test","denumire"=>"Test","type"=>""],
                      //["col"=>"testdata","denumire"=>"TestData","type"=>"Date"],
    ["col"=>"assignedby.name","denumire"=>"assigned by","type"=>""],["col"=>"assignedto.name","denumire"=>"assigned to","type"=>""],["col"=>"title","denumire"=>"titlu","type"=>""],["col"=>"description","denumire"=>"descriere","type"=>""],["col"=>"duedate","denumire"=>"termen executare","type"=>""],["col"=>"tags","denumire"=>"tags","type"=>""],["col"=>"completed_at","denumire"=>"data executarii","type"=>""],["col"=>"iscompleted","denumire"=>"executat","type"=>""],["col"=>"isdeleted","denumire"=>"sters","type"=>""],["col"=>"isimportant","denumire"=>"important","type"=>""],["col"=>"completedby.name","denumire"=>"executat de catre","type"=>""],
                      
                      
 ];

  public function assignedby() {
         return $this->belongsTo('App\Models\User','assignedby_id','id');
  }
  public function assignedto() {
         return $this->belongsTo('App\Models\User','assignedto_id','id');
  }
  public function completedby() {
         return $this->belongsTo('App\Models\User','completedby_id','id');
  }


}