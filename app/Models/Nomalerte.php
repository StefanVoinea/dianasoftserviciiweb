<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Nomalerte extends Model
{
    use RecordsActivity;
    protected $table ="nomalerte";
    protected $fillable = ["company_id","denumire",];
}