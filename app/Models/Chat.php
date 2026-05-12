<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Chat extends Model
{
    // use RecordsActivity;
    protected $table ="chat";
    protected $fillable = ["company_id","user_id","textContent","link_fisier","isSeen","isDeleted","catre_id","tip_catre","isArchived","taguri",];

    
}