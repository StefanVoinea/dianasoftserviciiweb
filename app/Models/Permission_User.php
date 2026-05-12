<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;

class Permission_User extends Model
{
    protected $table ="permission_user";
    protected $fillable = ["permission_id","user_id","isactive","company_id"];
    protected $casts = [
        'isactive' => 'boolean',
        
    ];
      public function permission() {
             return $this->belongsTo("App\Models\Permission");
    }
}