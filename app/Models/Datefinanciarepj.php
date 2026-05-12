<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Datefinanciarepj extends Model
{
    use RecordsActivity;
    protected $table ="datefinanciarepj";
    protected $fillable = ["company_id","cui","an","indicator","val_indicator","val_den_indicator",];
    protected $casts = [
    ];
    public  $groupByXLS=[
                            // ["col"=>"denumire_partener","denumire"=>"Partener","type"=>"","align"=>"center","width"=>"100%"],
                            // ["col"=>"contd","denumire"=>"Debit","type"=>"","align"=>"center","width"=>"100%"],
                           ];   
   public $totalByXLS=[];
   public $titluRaportXLS="Date financiare persoane juridice";
   public $columnFormatXLS=[
                                 // "D" => NumberFormat::FORMAT_DATE_DDMMYYYY,
                                 // "I" => NumberFormat::FORMAT_CURRENCY_EUR_SIMPLE
                               ];
    public  $antetTabelXLS=[
                      //["col"=>"test","denumire"=>"Test","type"=>""],
                      //["col"=>"testdata","denumire"=>"TestData","type"=>"Date"],
    ["col"=>"cui","denumire"=>"cui","type"=>""],["col"=>"an","denumire"=>"anul","type"=>""],["col"=>"indicator","denumire"=>"indicator","type"=>""],["col"=>"val_indicator","denumire"=>"valoare indicator","type"=>""],["col"=>"val_den_indicator","denumire"=>"denumire indicator","type"=>""],
                      
                      
 ];
}