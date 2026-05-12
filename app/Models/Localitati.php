<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Localitati extends Model
{
    use RecordsActivity;
    protected $table ="localitati";
    protected $fillable = ["denumire","judet",];
}