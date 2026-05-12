<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Documente extends Model
{
    use RecordsActivity;
    protected $table ="documente";
    protected $fillable = ["company_id","agentia","denumire_doc","tip_doc","aplicatie","continut","data","utilizator","data_operarii","printabil","exportabil",];
    protected $casts = [
    ];
    public  $groupByXLS=[
                            // ["col"=>"denumire_partener","denumire"=>"Partener","type"=>"","align"=>"center","width"=>"100%"],
                            // ["col"=>"contd","denumire"=>"Debit","type"=>"","align"=>"center","width"=>"100%"],
                           ];   
   public $totalByXLS=[];
   public $titluRaportXLS="Documente";
   public $columnFormatXLS=[
                                 // "D" => NumberFormat::FORMAT_DATE_DDMMYYYY,
                                 // "I" => NumberFormat::FORMAT_CURRENCY_EUR_SIMPLE
                               ];
    public  $antetTabelXLS=[
                      //["col"=>"test","denumire"=>"Test","type"=>""],
                      //["col"=>"testdata","denumire"=>"TestData","type"=>"Date"],
    ["col"=>"agentia","denumire"=>"agentia","type"=>""],["col"=>"denumire_doc","denumire"=>"denumire doc","type"=>""],["col"=>"tip_doc","denumire"=>"tip doc","type"=>""],["col"=>"aplicatie","denumire"=>"aplicatie","type"=>""],["col"=>"continut","denumire"=>"continut","type"=>""],["col"=>"data","denumire"=>"data","type"=>""],["col"=>"utilizator","denumire"=>"utilizator","type"=>""],["col"=>"data_operarii","denumire"=>"data operarii","type"=>""],["col"=>"printabil","denumire"=>"printabil","type"=>""],["col"=>"exportabil","denumire"=>"exportabil","type"=>""],
                      
                      
 ];
}