<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Jurnalsms extends Model
{
    // use RecordsActivity;
    protected $table ="jurnalsms";
    protected $fillable = ["company_id","nr_contract","telefon","mesaj","status","utilizator","data_operare","data_transmitere","catre","id_site","data_verificare_status",];
   protected $casts = [
        'data_operare' => 'datetime',
        'data_transmitere' => 'datetime',
        'data_verificare_status' => 'datetime'
    ];
    public  $groupByXLS=[
                            // ["col"=>"denumire_partener","denumire"=>"Partener","type"=>"","align"=>"center","width"=>"100%"],
                            // ["col"=>"contd","denumire"=>"Debit","type"=>"","align"=>"center","width"=>"100%"],
                           ];   
   public $totalByXLS=[];
   public $titluRaportXLS="Jurnal SMS";
   public $columnFormatXLS=[
                                 // "D" => NumberFormat::FORMAT_DATE_DDMMYYYY,
                                 // "I" => NumberFormat::FORMAT_CURRENCY_EUR_SIMPLE
                               ];
    public  $antetTabelXLS=[
                      //["col"=>"test","denumire"=>"Test","type"=>""],
                      //["col"=>"testdata","denumire"=>"TestData","type"=>"Date"],
    ["col"=>"nr_contract","denumire"=>"nr contract","type"=>""],["col"=>"telefon","denumire"=>"telefon","type"=>""],["col"=>"mesaj","denumire"=>"mesaj","type"=>""],["col"=>"status","denumire"=>"status","type"=>""],["col"=>"utilizator","denumire"=>"utilizator","type"=>""],["col"=>"data_operare","denumire"=>"data operarii","type"=>""],["col"=>"data_transmitere","denumire"=>"data transmitere","type"=>""],["col"=>"catre","denumire"=>"catre","type"=>""],["col"=>"id_site","denumire"=>"id site","type"=>""],["col"=>"data_verificare_status","denumire"=>"data verificare status","type"=>""],
                      
                      
 ];
 public function contract() {
             return $this->belongsTo('App\Models\Contract','nr_contract','nr_contract');
    }
}