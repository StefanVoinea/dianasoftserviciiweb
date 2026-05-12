<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Societati extends Model
{
    use RecordsActivity;
    protected $table ="societati";
    protected $fillable = ["company_id","denumire","cod_firma",];
    protected $casts = [
    ];
    public  $groupByXLS=[
                            // ["col"=>"denumire_partener","denumire"=>"Partener","type"=>"","align"=>"center","width"=>"100%"],
                            // ["col"=>"contd","denumire"=>"Debit","type"=>"","align"=>"center","width"=>"100%"],
                           ];   
   public $totalByXLS=[];
   public $titluRaportXLS="Societati";
   public $columnFormatXLS=[
                                 // "D" => NumberFormat::FORMAT_DATE_DDMMYYYY,
                                 // "I" => NumberFormat::FORMAT_CURRENCY_EUR_SIMPLE
                               ];
    public  $antetTabelXLS=[
                      //["col"=>"test","denumire"=>"Test","type"=>""],
                      //["col"=>"testdata","denumire"=>"TestData","type"=>"Date"],
    ["col"=>"denumire","denumire"=>"denumire","type"=>""],["col"=>"cod_firma","denumire"=>"cod firma","type"=>""],
                      
                      
 ];
}