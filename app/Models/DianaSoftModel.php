<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DianaSoftModel extends Model
{
	 
    protected $table ="dianasoftmodel";
    protected $fillable = ["model_name","table_name","model_type","display_name","master_model_name","detail_model_name",];

    public function dianasoftfields() {
        return $this->hasMany("App\Models\DianaSoftField","dianasoftmodel_id","id");
    }

    public function dianasoftrelationships() {
        return $this->hasMany("App\Models\DianaSoftRelationship","dianasoftmodel_id","id");
    }
}