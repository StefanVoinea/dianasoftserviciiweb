<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tokenuri extends Model
{
    use RecordsActivity;
    protected $table ="tokenuri";
    protected $fillable = ["company_id","access_token","refresh_token","data_expirarii","data_obtinere",];
    protected $casts = [
    ];
    public  $groupByXLS=[
                            // ["col"=>"denumire_partener","denumire"=>"Partener","type"=>"","align"=>"center","width"=>"100%"],
                            // ["col"=>"contd","denumire"=>"Debit","type"=>"","align"=>"center","width"=>"100%"],
                           ];   
   public $totalByXLS=[];
   public $titluRaportXLS="Tokenuri";
   public $columnFormatXLS=[
                                 // "D" => NumberFormat::FORMAT_DATE_DDMMYYYY,
                                 // "I" => NumberFormat::FORMAT_CURRENCY_EUR_SIMPLE
                               ];
    public  $antetTabelXLS=[
                      //["col"=>"test","denumire"=>"Test","type"=>""],
                      //["col"=>"testdata","denumire"=>"TestData","type"=>"Date"],
    ["col"=>"access_token","denumire"=>"access token","type"=>""],["col"=>"refresh_token","denumire"=>"refresh token","type"=>""],["col"=>"data_expirarii","denumire"=>"data expirarii","type"=>""],["col"=>"data_obtinere","denumire"=>"data obtinerii","type"=>""],
                      
                      
 ];
}