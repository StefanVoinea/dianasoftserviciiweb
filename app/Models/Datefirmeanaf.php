<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Datefirmeanaf extends Model
{
    // use RecordsActivity;
    protected $table ="datefirmeanaf";
    protected $fillable = ["cui","adresa","telefon","iban","cod_postal",];
}