<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Gestiune extends Model
{
    // use RecordsActivity;

    protected $table ="gestiuni";
    protected $fillable = ["company_id","denumire","adresa","localitate","judet","telefon","email","tip_gestiune","cui","gestionar","cont_stoc","cont_venit","cont_cheltuiala","cont_adaos","cont_tva_neexigibil","cont_4426","cont_4427","cont_nesosite","cont_casierie","sold_initial","casieria","interior","data_sold","cod","sef_gestiune","economist","prescurtare","casier","nr_ordine","verificator","denumire_aplicaacum","consilier_juridic","opc","fax","telefon_fix"];
    
     public function getAllAttributes()
    {
        $columns = $this->getFillable();
        return $columns;
    } 
    public function notecontabile() {
             return $this->hasMany('App\Notacontabila');
    }
    public function company() {
        return $this->belongsTo("App\Company","id","company_id");
    }

     public function users() {
             return $this->belongsToMany(User::class,'gestiune_user')->withTimestamps();
    }
    public function plajenumere() {
             return $this->hasMany(Plajenumere::class);
    }
    public function nrdocumente() {
             return $this->hasMany(Nrdocumente::class);
    }
    public function articole() {
             return $this->hasMany(Articol::class);
    }
}