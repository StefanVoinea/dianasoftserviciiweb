<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ipautorizat extends Model
{
    use RecordsActivity;
    protected $table ="ipautorizat";
    protected $fillable = ["company_id","ip","utilizator",];
    protected $casts = [
    ];
    public  $groupByXLS=[
                            // ["col"=>"denumire_partener","denumire"=>"Partener","type"=>"","align"=>"center","width"=>"100%"],
                            // ["col"=>"contd","denumire"=>"Debit","type"=>"","align"=>"center","width"=>"100%"],
                           ];   
   public $totalByXLS=[];
   public $titluRaportXLS="IP-uri autorizate";
   public $columnFormatXLS=[
                                 // "D" => NumberFormat::FORMAT_DATE_DDMMYYYY,
                                 // "I" => NumberFormat::FORMAT_CURRENCY_EUR_SIMPLE
                               ];
    public  $antetTabelXLS=[
                      //["col"=>"test","denumire"=>"Test","type"=>""],
                      //["col"=>"testdata","denumire"=>"TestData","type"=>"Date"],
    ["col"=>"ip","denumire"=>"ip","type"=>""],["col"=>"utilizator","denumire"=>"utilizator","type"=>""],
                      
                      
 ];
}