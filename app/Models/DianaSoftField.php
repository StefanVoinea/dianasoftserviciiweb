<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DianaSoftField extends Model
{
    protected $table ="dianasoftfield";
    protected $fillable = ["dianasoftmodel_id","name","type","length","nullable","default","fillable","required","indexed","frontendvalidation","backendvalidation","faker","display_name","input_type","input_source","input_source_type",];
  protected $casts = [
        'nullable' => 'boolean',
        'fillable' => 'boolean',
        'required' => 'boolean',
        'indexed' => 'boolean',
    ];
    public function dianasoftmodel() {
        return $this->belongsTo("App\Models\DianaSoftModel","dianasoftmodel_id","id");
    }
}