<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
	use RecordsActivity;
	
    protected $table ="companies";
    // protected $with ="users";
    protected $fillable = ["denumire","cui","regcom","adresa","localitate","judet","telefon","email","capital_social","banca","cont","plan_tarifar","cod_caen","email_factura","email_restante","slug","operator_gdpr","nrautorizatie","cerber_url","director_general","contabil_sef","banca_valuta","cont_valuta"];
    
    public function users() {
             return $this->belongsToMany(User::class,'company_user', 'company_id', 'user_id')->withTimestamps();
    }
     public function gestiuni() {
             return $this->hasMany('App\Models\Gestiune');
    }
     public function parteneri() {
             return $this->hasMany('App\Models\Partener');
    }
}