<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Utilizatori extends Model
{
    use RecordsActivity;
    protected $table ="utilizatori";
    protected $fillable = ["company_id","utilizator","parola","drept_de_acces","nume_complet","gestiune","gestiune2","nr_telefon","departament","email","compartiment","data_operarii","data_parola","functia",];
    protected $casts = [
    ];
    public  $groupByXLS=[
                            // ["col"=>"denumire_partener","denumire"=>"Partener","type"=>"","align"=>"center","width"=>"100%"],
                            // ["col"=>"contd","denumire"=>"Debit","type"=>"","align"=>"center","width"=>"100%"],
                           ];   
   public $totalByXLS=[];
   public $titluRaportXLS="Utilizatori";
   public $columnFormatXLS=[
                                 // "D" => NumberFormat::FORMAT_DATE_DDMMYYYY,
                                 // "I" => NumberFormat::FORMAT_CURRENCY_EUR_SIMPLE
                               ];
    public  $antetTabelXLS=[
                      //["col"=>"test","denumire"=>"Test","type"=>""],
                      //["col"=>"testdata","denumire"=>"TestData","type"=>"Date"],
    ["col"=>"utilizator","denumire"=>"utilizator","type"=>""],["col"=>"parola","denumire"=>"parola","type"=>""],["col"=>"drept_de_acces","denumire"=>"drept de acces","type"=>""],["col"=>"nume_complet","denumire"=>"nume complet","type"=>""],["col"=>"gestiune","denumire"=>"gestiune","type"=>""],["col"=>"gestiune2","denumire"=>"gestiune2","type"=>""],["col"=>"nr_telefon","denumire"=>"nr telefon","type"=>""],["col"=>"departament","denumire"=>"departament","type"=>""],["col"=>"email","denumire"=>"email","type"=>""],["col"=>"compartiment","denumire"=>"compartiment","type"=>""],["col"=>"data_operarii","denumire"=>"data operarii","type"=>""],["col"=>"data_parola","denumire"=>"data parola","type"=>""],["col"=>"functia","denumire"=>"functia","type"=>""],
                      
                      
 ];
}