<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Documentepdf extends Model
{
    use RecordsActivity;
    protected $table ="documentepdf";
    protected $fillable = ["company_id","grupa","denumire","descriere","fisier","data","acces","status"];
    protected $casts = [
    ];
    public  $groupByXLS=[
                            // ["col"=>"denumire_partener","denumire"=>"Partener","type"=>"","align"=>"center","width"=>"100%"],
                            // ["col"=>"contd","denumire"=>"Debit","type"=>"","align"=>"center","width"=>"100%"],
                           ];   
   public $totalByXLS=[];
   public $titluRaportXLS="Documentepdf";
   public $columnFormatXLS=[
                                 // "D" => NumberFormat::FORMAT_DATE_DDMMYYYY,
                                 // "I" => NumberFormat::FORMAT_CURRENCY_EUR_SIMPLE
                               ];
    public  $antetTabelXLS=[
                      //["col"=>"test","denumire"=>"Test","type"=>""],
                      //["col"=>"testdata","denumire"=>"TestData","type"=>"Date"],
    ["col"=>"grupa","denumire"=>"grupa","type"=>""],
    ["col"=>"denumire","denumire"=>"denumire","type"=>""],
    ["col"=>"descriere","denumire"=>"descriere","type"=>""],
    ["col"=>"fisier","denumire"=>"fisier","type"=>""],
    ["col"=>"data","denumire"=>"data","type"=>""],
    ["col"=>"acces","denumire"=>"acces","type"=>""],
    ["col"=>"status","denumire"=>"status","type"=>""],
                      
 ];
}