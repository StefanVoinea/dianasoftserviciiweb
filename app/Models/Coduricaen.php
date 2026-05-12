<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Coduricaen extends Model
{
    use RecordsActivity;
    protected $table ="coduricaen";
    protected $fillable = ["company_id","cod","denumire",];
    protected $casts = [
    ];
    public  $groupByXLS=[
                            // ["col"=>"denumire_partener","denumire"=>"Partener","type"=>"","align"=>"center","width"=>"100%"],
                            // ["col"=>"contd","denumire"=>"Debit","type"=>"","align"=>"center","width"=>"100%"],
                           ];   
   public $totalByXLS=[];
   public $titluRaportXLS="Coduri CAEN";
   public $columnFormatXLS=[
                                 // "D" => NumberFormat::FORMAT_DATE_DDMMYYYY,
                                 // "I" => NumberFormat::FORMAT_CURRENCY_EUR_SIMPLE
                               ];
    public  $antetTabelXLS=[
                      //["col"=>"test","denumire"=>"Test","type"=>""],
                      //["col"=>"testdata","denumire"=>"TestData","type"=>"Date"],
    ["col"=>"cod","denumire"=>"cod","type"=>""],["col"=>"denumire","denumire"=>"denumire","type"=>""],
                      
                      
 ];
}