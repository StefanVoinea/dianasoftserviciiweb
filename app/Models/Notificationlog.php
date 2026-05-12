<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notificationlog extends Model
{
   // use RecordsActivity;
    protected $table ="notificationlog";
    protected $fillable = ["company_id","notificationtype_id","from_id","user_id","channel","email","telefon","title","subtitle","type","icon","avatar","link","category","read_at","subject_type","subject_id"];
    protected $casts = [
        'read_at' => 'datetime'
    ];
    public  $groupByXLS=[
                            // ["col"=>"denumire_partener","denumire"=>"Partener","type"=>"","align"=>"center","width"=>"100%"],
                            // ["col"=>"contd","denumire"=>"Debit","type"=>"","align"=>"center","width"=>"100%"],
                           ];   
   public $totalByXLS=[];
   public $titluRaportXLS="Jurnal notificari";
   public $columnFormatXLS=[
                                 // "D" => NumberFormat::FORMAT_DATE_DDMMYYYY,
                                 // "I" => NumberFormat::FORMAT_CURRENCY_EUR_SIMPLE
                               ];
    public  $antetTabelXLS=[
                      //["col"=>"test","denumire"=>"Test","type"=>""],
                      //["col"=>"testdata","denumire"=>"TestData","type"=>"Date"],
    ["col"=>"notificationtype.denumire","denumire"=>"tip notificare","type"=>""],["col"=>"from.name","denumire"=>"de la","type"=>""],["col"=>"user.name","denumire"=>"catre","type"=>""],["col"=>"channel","denumire"=>"canal de comunicare","type"=>""],["col"=>"email","denumire"=>"email","type"=>""],["col"=>"telefon","denumire"=>"telefon","type"=>""],["col"=>"title","denumire"=>"titlu","type"=>""],["col"=>"subtitle","denumire"=>"descriere","type"=>""],["col"=>"type","denumire"=>"tip","type"=>""],["col"=>"icon","denumire"=>"icon","type"=>""],["col"=>"avatar","denumire"=>"avatar","type"=>""],["col"=>"link","denumire"=>"link","type"=>""],["col"=>"category","denumire"=>"categoria","type"=>""],["col"=>"read_at","denumire"=>"citit la","type"=>""],
                      
                      
 ];
  public function from() {
         return $this->belongsTo('App\Models\User','from_id','id');
  }
  public function user() {
         return $this->belongsTo('App\Models\User');
  }
  public function notificationtype() {
         return $this->belongsTo('App\Models\Notificationtype');
  }
}