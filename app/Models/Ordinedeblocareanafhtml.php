<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ordinedeblocareanafhtml extends Model
{
    //use RecordsActivity;
    protected $table ="ordinedeblocareanafhtml";
    protected $fillable = ["continut"];
    protected $casts = [
    ];
    public  $groupByXLS=[
                            // ["col"=>"denumire_partener","denumire"=>"Partener","type"=>"","align"=>"center","width"=>"100%"],
                            // ["col"=>"contd","denumire"=>"Debit","type"=>"","align"=>"center","width"=>"100%"],
                           ];   
   public $totalByXLS=[];
   public $titluRaportXLS="Ordinedeblocareanafhtml";
   public $columnFormatXLS=[
                                 // "D" => NumberFormat::FORMAT_DATE_DDMMYYYY,
                                 // "I" => NumberFormat::FORMAT_CURRENCY_EUR_SIMPLE
                               ];
    public  $antetTabelXLS=[
                      //["col"=>"test","denumire"=>"Test","type"=>""],
                      //["col"=>"testdata","denumire"=>"TestData","type"=>"Date"],
    // ["col"=>"nr_ordin","denumire"=>"nr ordin","type"=>""],["col"=>"data_ordin","denumire"=>"data ordin","type"=>""],["col"=>"suspect","denumire"=>"suspect","type"=>""],["col"=>"date_de_identificare","denumire"=>"date de identificare","type"=>""],["col"=>"bunuri_blocate","denumire"=>"bunuri blocate","type"=>""],["col"=>"ordin_de_revocare","denumire"=>"ordin de revocare","type"=>""],["col"=>"data_revocarii","denumire"=>"data revocarii","type"=>""],["col"=>"institutia","denumire"=>"institutia","type"=>""],
                      
                      
 ];
}