<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Calendarevent extends Model
{
   // use RecordsActivity;
    protected $table ="calendarevent";
    protected $fillable = ["company_id","createdby_id","title","url","start","end","allday","calendar","guests","location","description","participating_users"];
    protected $casts = ["allday" => "boolean",'guests'=>'array','start'=>'datetime','end'=>'datetime','participating_users'=>'array'];
     
    public  $groupByXLS=[
                            // ["col"=>"denumire_partener","denumire"=>"Partener","type"=>"","align"=>"center","width"=>"100%"],
                            // ["col"=>"contd","denumire"=>"Debit","type"=>"","align"=>"center","width"=>"100%"],
                           ];   
   public $totalByXLS=[];
   public $titluRaportXLS="Calendar";
   public $columnFormatXLS=[
                                 // "D" => NumberFormat::FORMAT_DATE_DDMMYYYY,
                                 // "I" => NumberFormat::FORMAT_CURRENCY_EUR_SIMPLE
                               ];
    public  $antetTabelXLS=[
                      //["col"=>"test","denumire"=>"Test","type"=>""],
                      //["col"=>"testdata","denumire"=>"TestData","type"=>"Date"],
    ["col"=>"createdby.name","denumire"=>"createdby","type"=>""],["col"=>"title","denumire"=>"titlu","type"=>""],["col"=>"url","denumire"=>"url","type"=>""],["col"=>"start","denumire"=>"start","type"=>""],["col"=>"end","denumire"=>"end","type"=>""],["col"=>"allday","denumire"=>"allday","type"=>""],["col"=>"calendar","denumire"=>"calendar","type"=>""],["col"=>"guests","denumire"=>"invitati","type"=>""],["col"=>"location","denumire"=>"locatie","type"=>""],["col"=>"description","denumire"=>"descriere","type"=>""],
                      
                      
 ];
 public function createdby() {
         return $this->belongsTo('App\Models\User','createdby_id','id');
  }
}