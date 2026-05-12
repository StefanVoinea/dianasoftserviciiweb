<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Versiuni extends Model
{
    use RecordsActivity;
    protected $table ="versiuni";
    protected $fillable = ["company_id","versiunea","agentia","data",];
    protected $casts = [
    ];
    public  $groupByXLS=[
                            // ["col"=>"denumire_partener","denumire"=>"Partener","type"=>"","align"=>"center","width"=>"100%"],
                            // ["col"=>"contd","denumire"=>"Debit","type"=>"","align"=>"center","width"=>"100%"],
                           ];   
   public $totalByXLS=[];
   public $titluRaportXLS="Versiuni";
   public $columnFormatXLS=[
                                 // "D" => NumberFormat::FORMAT_DATE_DDMMYYYY,
                                 // "I" => NumberFormat::FORMAT_CURRENCY_EUR_SIMPLE
                               ];
    public  $antetTabelXLS=[
                      //["col"=>"test","denumire"=>"Test","type"=>""],
                      //["col"=>"testdata","denumire"=>"TestData","type"=>"Date"],
    ["col"=>"versiunea","denumire"=>"versiunea","type"=>""],["col"=>"agentia","denumire"=>"agentia","type"=>""],["col"=>"data","denumire"=>"data","type"=>""],
                      
                      
 ];
}