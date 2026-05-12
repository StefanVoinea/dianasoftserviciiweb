<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Adreseemailalerte extends Model
{
    use RecordsActivity;
    protected $table ="adreseemailalerte";
    protected $fillable = ["company_id","alerta","adresa_email",];
    protected $casts = [
    ];
    public  $groupByXLS=[
                            // ["col"=>"denumire_partener","denumire"=>"Partener","type"=>"","align"=>"center","width"=>"100%"],
                            // ["col"=>"contd","denumire"=>"Debit","type"=>"","align"=>"center","width"=>"100%"],
                           ];   
   public $totalByXLS=[];
   public $titluRaportXLS="Adrese email alerte";
   public $columnFormatXLS=[
                                 // "D" => NumberFormat::FORMAT_DATE_DDMMYYYY,
                                 // "I" => NumberFormat::FORMAT_CURRENCY_EUR_SIMPLE
                               ];
    public  $antetTabelXLS=[
                      //["col"=>"test","denumire"=>"Test","type"=>""],
                      //["col"=>"testdata","denumire"=>"TestData","type"=>"Date"],
    ["col"=>"alerta","denumire"=>"alerta","type"=>""],["col"=>"adresa_email","denumire"=>"adresa email","type"=>""],
                      
                      
 ];
}