<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Efacturatokens extends Model
{
    //use RecordsActivity;
    protected $table ="efacturatokens";
    protected $fillable = ["cui","access_token","refresh_token","data_obtinerii","data_expirare",];
}