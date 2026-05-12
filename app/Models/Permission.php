<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
	use RecordsActivity;
    protected $table ="permissions";
    protected $with="dianasoftmenuoption";
    protected $fillable = ["name","display_name","dianasoftmenuoption_id"];

    protected static $recordableEvents = ['created','updated', 'deleted'];

    public function dianasoftmenuoption() {
             return $this->belongsTo("App\Models\DianaSoftMenuOption");
    }
     public function users() {
             return $this->belongsToMany(User::class,'permission_user')->withTimestamps();
    
    }
}