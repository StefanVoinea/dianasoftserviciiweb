<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Alertetransmise extends Model
{
    use RecordsActivity;
    protected $table ="alertetransmise";
    protected $fillable = ["company_id","alerta","data","ora",];
    protected $casts = [
    ];
    public  $groupByXLS=[
                            // ["col"=>"denumire_partener","denumire"=>"Partener","type"=>"","align"=>"center","width"=>"100%"],
                            // ["col"=>"contd","denumire"=>"Debit","type"=>"","align"=>"center","width"=>"100%"],
                           ];   
   public $totalByXLS=[];
   public $titluRaportXLS="Alerte transmise";
   public $columnFormatXLS=[
                                 // "D" => NumberFormat::FORMAT_DATE_DDMMYYYY,
                                 // "I" => NumberFormat::FORMAT_CURRENCY_EUR_SIMPLE
                               ];
    public  $antetTabelXLS=[
                      //["col"=>"test","denumire"=>"Test","type"=>""],
                      //["col"=>"testdata","denumire"=>"TestData","type"=>"Date"],
    ["col"=>"alerta","denumire"=>"alerta","type"=>""],["col"=>"data","denumire"=>"data","type"=>""],["col"=>"ora","denumire"=>"ora","type"=>""],
                      
                      
 ];
}