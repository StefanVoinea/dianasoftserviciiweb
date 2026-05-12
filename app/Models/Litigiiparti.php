<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Litigiiparti extends Model
{
    use RecordsActivity;
    protected $table ="litigiiparti";
    protected $fillable = ["company_id","litigiu_id","nume","calitate",];
    protected $casts = [
    ];
    public  $groupByXLS=[
                            // ["col"=>"denumire_partener","denumire"=>"Partener","type"=>"","align"=>"center","width"=>"100%"],
                            // ["col"=>"contd","denumire"=>"Debit","type"=>"","align"=>"center","width"=>"100%"],
                           ];   
   public $totalByXLS=[];
   public $titluRaportXLS="Parti litigii";
   public $columnFormatXLS=[
                                 // "D" => NumberFormat::FORMAT_DATE_DDMMYYYY,
                                 // "I" => NumberFormat::FORMAT_CURRENCY_EUR_SIMPLE
                               ];
    public  $antetTabelXLS=[
                      //["col"=>"test","denumire"=>"Test","type"=>""],
                      //["col"=>"testdata","denumire"=>"TestData","type"=>"Date"],
    ["col"=>"litigiu_id","denumire"=>"idlitigiu","type"=>""],["col"=>"nume","denumire"=>"nume","type"=>""],["col"=>"calitate","denumire"=>"calitate","type"=>""],
                      
                      
 ];
}