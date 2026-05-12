<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Valute extends Model
{
    use RecordsActivity;
    protected $table ="valute";
    protected $fillable = ["company_id","data","simbol","denumire","paritate","curs",];
    protected $casts = [
    ];
    public  $groupByXLS=[
                            // ["col"=>"denumire_partener","denumire"=>"Partener","type"=>"","align"=>"center","width"=>"100%"],
                            // ["col"=>"contd","denumire"=>"Debit","type"=>"","align"=>"center","width"=>"100%"],
                           ];   
   public $totalByXLS=[];
   public $titluRaportXLS="Valute";
   public $columnFormatXLS=[
                                 // "D" => NumberFormat::FORMAT_DATE_DDMMYYYY,
                                 // "I" => NumberFormat::FORMAT_CURRENCY_EUR_SIMPLE
                               ];
    public  $antetTabelXLS=[
                      //["col"=>"test","denumire"=>"Test","type"=>""],
                      //["col"=>"testdata","denumire"=>"TestData","type"=>"Date"],
    ["col"=>"data","denumire"=>"data","type"=>""],["col"=>"simbol","denumire"=>"simbol","type"=>""],["col"=>"denumire","denumire"=>"denumire","type"=>""],["col"=>"paritate","denumire"=>"paritate","type"=>""],["col"=>"curs","denumire"=>"curs","type"=>""],
                      
                      
 ];
}