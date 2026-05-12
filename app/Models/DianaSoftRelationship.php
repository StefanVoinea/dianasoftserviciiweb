<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DianaSoftRelationship extends Model
{
    protected $table ="dianasoftrelationship";
    protected $fillable = ["dianasoftmodel_id","name","type","model_name","foreign_key","local_key",];

    public function dianasoftmodel() {
        return $this->belongsTo("App\Models\DianaSoftModel","dianasoftmodel_id","id");
    }
}