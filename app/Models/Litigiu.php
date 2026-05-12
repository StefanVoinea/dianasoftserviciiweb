<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Litigiu extends Model
{
    use RecordsActivity;
    protected $table ="litigii";
    protected $fillable = ["company_id","numar_dosar","numar_vechi","data_dosar","institutie","departament","categorie_caz","stadiu_procesual","avocatul_apararii","avocatul_acuzarii","observatii","status","taxa_de_timbru","cheltuieli_de_judecata","parti","obiect","data_modificare","categorie_caz_nume","stadiu_procesual_nume","email_alerte","telefon_alerte","data_ultimei_verificari"];
    protected $casts = ["data_modificare"=>"datetime",
                        "data_ultimei_verificari"=>"datetime"
                        ];
    
    protected $with=["litigiiparti","litigiicaleatac","litigiisedinte"];

    public  $groupByXLS=[
                            // ["col"=>"denumire_partener","denumire"=>"Partener","type"=>"","align"=>"center","width"=>"100%"],
                            // ["col"=>"contd","denumire"=>"Debit","type"=>"","align"=>"center","width"=>"100%"],
                           ];   
   public $totalByXLS=[];
   public $titluRaportXLS="Litigii";
   public $columnFormatXLS=[
                                 // "D" => NumberFormat::FORMAT_DATE_DDMMYYYY,
                                 // "I" => NumberFormat::FORMAT_CURRENCY_EUR_SIMPLE
                               ];
    public  $antetTabelXLS=[
                      //["col"=>"test","denumire"=>"Test","type"=>""],

                      //["col"=>"testdata","denumire"=>"TestData","type"=>"Date"],
    ["col"=>"numar_dosar","denumire"=>"numar dosar","type"=>""],
    ["col"=>"numar_vechi","denumire"=>"numar vechi","type"=>""],
    ["col"=>"data_dosar","denumire"=>"data dosar","type"=>""],
    ["col"=>"institutie","denumire"=>"institutia","type"=>""],
    ["col"=>"departament","denumire"=>"departament","type"=>""],
    ["col"=>"categorie_caz","denumire"=>"categorie caz","type"=>""],
    ["col"=>"stadiu_procesual","denumire"=>"stadiu procesual","type"=>""],
    ["col"=>"avocatul_apararii","denumire"=>"avocatul apararii","type"=>""],
    ["col"=>"avocatul_acuzarii","denumire"=>"avocatul acuzarii","type"=>""],
    ["col"=>"observatii","denumire"=>"observatii","type"=>""],
    ["col"=>"status","denumire"=>"status","type"=>""],
    ["col"=>"taxa_de_timbru","denumire"=>"taxa de timbru","type"=>""],
    ["col"=>"cheltuieli_de_judecata","denumire"=>"cheltuieli de judecata","type"=>""],
    ["col"=>"parti","denumire"=>"parti","type"=>""],
    ["col"=>"obiect","denumire"=>"obiect","type"=>""],
    ["col"=>"data_modificare","denumire"=>"data_modificare","type"=>""],
    ["col"=>"categorie_caz_nume","denumire"=>"categorie_caz_nume","type"=>""],
    ["col"=>"stadiu_procesual_nume","denumire"=>"stadiu_procesual_nume","type"=>""],
    ["col"=>"email_alerte","denumire"=>"email_alerte","type"=>""],
    ["col"=>"telefon_alerte","denumire"=>"telefon_alerte","type"=>""],
    ["col"=>"data_ultimei_verificari","denumire"=>"data_ultimei_verificari","type"=>""],

                      
                      
 ];
  public function litigiiparti() {
             return $this->hasMany('App\Models\Litigiiparti');
  }
  public function litigiicaleatac() {
             return $this->hasMany('App\Models\Litigiicaleatac');
  }
  public function litigiisedinte() {
             return $this->hasMany('App\Models\Litigiisedinte');
  }
}