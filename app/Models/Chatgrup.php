<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Chatgrup extends Model
{
    use RecordsActivity;
    protected $table ="chatgrup";
    protected $fillable = ["company_id","nume"];
}