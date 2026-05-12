<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Litigiisedinte extends Model
{
    use RecordsActivity;
    protected $table ="litigiisedinte";
    protected $fillable = ["company_id","litigiu_id","complet","data_sedinta","ora_sedinta","solutie","solutie_sumar","data_pronuntare","document_sedinta","numar_document","data_document",];
    protected $casts = [
        
    ];
    public  $groupByXLS=[
                            // ["col"=>"denumire_partener","denumire"=>"Partener","type"=>"","align"=>"center","width"=>"100%"],
                            // ["col"=>"contd","denumire"=>"Debit","type"=>"","align"=>"center","width"=>"100%"],
                           ];   
   public $totalByXLS=[];
   public $titluRaportXLS="Sedinte litigii";
   public $columnFormatXLS=[
                                 // "D" => NumberFormat::FORMAT_DATE_DDMMYYYY,
                                 // "I" => NumberFormat::FORMAT_CURRENCY_EUR_SIMPLE
                               ];
    public  $antetTabelXLS=[
                      //["col"=>"test","denumire"=>"Test","type"=>""],
                      //["col"=>"testdata","denumire"=>"TestData","type"=>"Date"],
    ["col"=>"litigiu_id","denumire"=>"idlitigiu","type"=>""],["col"=>"complet","denumire"=>"complet","type"=>""],["col"=>"data_sedinta","denumire"=>"data sedinta","type"=>""],["col"=>"ora_sedinta","denumire"=>"ora sedinta","type"=>""],["col"=>"solutie","denumire"=>"solutie","type"=>""],["col"=>"solutie_sumar","denumire"=>"solutie sumar","type"=>""],["col"=>"data_pronuntare","denumire"=>"data pronuntare","type"=>""],["col"=>"document_sedinta","denumire"=>"document sedinta","type"=>""],["col"=>"numar_document","denumire"=>"numar document","type"=>""],["col"=>"data_document","denumire"=>"data document","type"=>""],
                      
                      
 ];
}