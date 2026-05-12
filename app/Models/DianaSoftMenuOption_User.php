<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DianaSoftMenuOption_User extends Model
{
    protected $table ="dianasoftmenuoption_user";
    protected $fillable = ["dianasoftmenuoption_id","user_id","isactive","company_id"];
    protected $casts = [
        'isactive' => 'boolean',
        
    ];
}