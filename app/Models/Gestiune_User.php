<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Gestiune_User extends Model
{
    //use RecordsActivity;
    protected $table ="gestiune_user";
    protected $fillable = ["gestiune_id","user_id","company_id","isactive"];
    protected $casts = [
        'isactive' => 'boolean',
        
    ];
}