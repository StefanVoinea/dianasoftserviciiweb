<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Interogareanaf extends Model
{
    use RecordsActivity;
    protected $table ="interogari_anaf";
    protected $fillable = ["company_id","user_id","data","cui","raspuns",];

    public function company() {
             return $this->belongsTo('App\Company');
    }
    public function user() {
             return $this->belongsTo('App\User');
    }
}