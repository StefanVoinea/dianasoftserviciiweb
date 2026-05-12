<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Societate extends Model
{
    //
     use RecordsActivity;
    protected $table ="societati";
    protected $fillable=['user_id','uuid','denumire','cui','regcom','adresa','localitate','judet','telefon','email',
            'capital_social','banca','cont','gps_position','plan_tarifar'];
}
