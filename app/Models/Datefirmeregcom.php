<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Datefirmeregcom extends Model
{

    protected $table ="datefirmeregcom";
    protected $fillable = ["denumire","cui","regcom","adresa","localitate","judet","telefon","email","iban","cod_caen","activitate","adresa_anaf","persoana_de_contact","website","cod_postal"];
}