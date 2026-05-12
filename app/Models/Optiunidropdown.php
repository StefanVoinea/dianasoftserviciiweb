<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Optiunidropdown extends Model
{
    use RecordsActivity;
    protected $table ="optiunidropdown";
    protected $fillable = ["company_id","field_name","field_option",];
}