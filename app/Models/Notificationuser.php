<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notificationuser extends Model
{
  //  use RecordsActivity;
    protected $table ="notificationuser";
    protected $fillable = ["company_id","notificationtype_id","user_id","channel",];
    protected $casts = [
    ];
    public  $groupByXLS=[
                            // ["col"=>"denumire_partener","denumire"=>"Partener","type"=>"","align"=>"center","width"=>"100%"],
                            // ["col"=>"contd","denumire"=>"Debit","type"=>"","align"=>"center","width"=>"100%"],
                           ];   
   public $totalByXLS=[];
   public $titluRaportXLS="Utilizatori notificari";
   public $columnFormatXLS=[
                                 // "D" => NumberFormat::FORMAT_DATE_DDMMYYYY,
                                 // "I" => NumberFormat::FORMAT_CURRENCY_EUR_SIMPLE
                               ];
    public  $antetTabelXLS=[
                      //["col"=>"test","denumire"=>"Test","type"=>""],
                      //["col"=>"testdata","denumire"=>"TestData","type"=>"Date"],
    ["col"=>"notificationtype_id","denumire"=>"tip notificare","type"=>""],["col"=>"user_id","denumire"=>"user","type"=>""],["col"=>"channel","denumire"=>"canal de comunicare","type"=>""],
                      
                      
 ];

  public $with=["user"];

 public function user() {
         return $this->belongsTo('App\Models\User');
  }
}