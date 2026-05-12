<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Chatcontactegrup extends Model
{
    use RecordsActivity;
    protected $table ="chatcontactegrup";
    protected $fillable = ["grup_id","user_id",];
}