<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Coduricaenrev2 extends Model
{
    use RecordsActivity;
    protected $table ="coduricaenrev2";
    protected $fillable = ["company_id","cod_caen","descriere","procent",];
    protected $casts = [
    ];
    public  $groupByXLS=[
                            // ["col"=>"denumire_partener","denumire"=>"Partener","type"=>"","align"=>"center","width"=>"100%"],
                            // ["col"=>"contd","denumire"=>"Debit","type"=>"","align"=>"center","width"=>"100%"],
                           ];   
   public $totalByXLS=[];
   public $titluRaportXLS="Coduri CAEN rev2";
   public $columnFormatXLS=[
                                 // "D" => NumberFormat::FORMAT_DATE_DDMMYYYY,
                                 // "I" => NumberFormat::FORMAT_CURRENCY_EUR_SIMPLE
                               ];
    public  $antetTabelXLS=[
                      //["col"=>"test","denumire"=>"Test","type"=>""],
                      //["col"=>"testdata","denumire"=>"TestData","type"=>"Date"],
    ["col"=>"cod_caen","denumire"=>"cod caen","type"=>""],["col"=>"descriere","denumire"=>"descriere","type"=>""],["col"=>"procent","denumire"=>"procent","type"=>""],
                      
                      
 ];
}