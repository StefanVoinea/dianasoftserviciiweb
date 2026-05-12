<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Litigiicaleatac extends Model
{
    use RecordsActivity;
    protected $table ="litigiicaleatac";
    protected $fillable = ["company_id","litigiu_id","data_declarare","parte_declaratoare","tip_cale_atac",];
    protected $casts = [
    ];
    public  $groupByXLS=[
                            // ["col"=>"denumire_partener","denumire"=>"Partener","type"=>"","align"=>"center","width"=>"100%"],
                            // ["col"=>"contd","denumire"=>"Debit","type"=>"","align"=>"center","width"=>"100%"],
                           ];   
   public $totalByXLS=[];
   public $titluRaportXLS="Cale atac";
   public $columnFormatXLS=[
                                 // "D" => NumberFormat::FORMAT_DATE_DDMMYYYY,
                                 // "I" => NumberFormat::FORMAT_CURRENCY_EUR_SIMPLE
                               ];
    public  $antetTabelXLS=[
                      //["col"=>"test","denumire"=>"Test","type"=>""],
                      //["col"=>"testdata","denumire"=>"TestData","type"=>"Date"],
    ["col"=>"litigiu_id","denumire"=>"idlitigiu","type"=>""],["col"=>"data_declarare","denumire"=>"data declarare","type"=>""],["col"=>"parte_declaratoare","denumire"=>"parte declaratoare","type"=>""],["col"=>"tip_cale_atac","denumire"=>"tip cale atac","type"=>""],
                      
                      
 ];
}