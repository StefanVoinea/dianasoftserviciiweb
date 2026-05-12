<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Filemanager extends Model
{
    use RecordsActivity;
    protected $table ="filemanager";
    protected $fillable = ["company_id","gestiune_id","grupa","denumire","data_ultimei_revizii","status","obs","fisier","fisier_original","tip_fisier","data_inceput","data_sfarsit",];
    protected $casts = [
    ];
    public  $groupByXLS=[
                            // ["col"=>"denumire_partener","denumire"=>"Partener","type"=>"","align"=>"center","width"=>"100%"],
                            // ["col"=>"contd","denumire"=>"Debit","type"=>"","align"=>"center","width"=>"100%"],
                           ];   
   public $totalByXLS=[];
   public $titluRaportXLS="File manager";
   public $columnFormatXLS=[
                                 // "D" => NumberFormat::FORMAT_DATE_DDMMYYYY,
                                 // "I" => NumberFormat::FORMAT_CURRENCY_EUR_SIMPLE
                               ];
    public  $antetTabelXLS=[
                      //["col"=>"test","denumire"=>"Test","type"=>""],
                      //["col"=>"testdata","denumire"=>"TestData","type"=>"Date"],
    ["col"=>"gestiune_id","denumire"=>"gestiuneid","type"=>""],["col"=>"grupa","denumire"=>"grupa","type"=>""],["col"=>"denumire","denumire"=>"denumire","type"=>""],["col"=>"data_ultimei_revizii","denumire"=>"data ultimei revizii","type"=>""],["col"=>"status","denumire"=>"status","type"=>""],["col"=>"obs","denumire"=>"obs","type"=>""],["col"=>"fisier","denumire"=>"fisier","type"=>""],["col"=>"fisier_original","denumire"=>"fisier original","type"=>""],["col"=>"tip_fisier","denumire"=>"tip fisier","type"=>""],["col"=>"data_inceput","denumire"=>"data inceput valabilitate","type"=>""],["col"=>"data_sfarsit","denumire"=>"data sfarsit valabilitate","type"=>""],
                      
                      
 ];
}