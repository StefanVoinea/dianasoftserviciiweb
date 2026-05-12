<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class DianaSoftMenuOption extends Model
{
	
    protected $table ="dianasoftmenuoptions";
    protected $fillable = ["name","url","slug","icon","tag","tagcolor","i18n","dropdown","parent","position1","position2","isdisabled","company_id"];
      protected $casts = [
        'isdisabled' => 'boolean'
    ];
     public function users() {
             return $this->belongsToMany(User::class,'dianasoftmenuoption_user','dianasoftmenuoption_id','user_id')->withTimestamps();
    }
}