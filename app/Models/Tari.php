<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tari extends Model
{
    use RecordsActivity;
    protected $table ="tari";
    protected $fillable = ["cod","denumire","capitala","forma_guvernare","cod_tara_fiscal","cod_bnr","valuta","cod_sm","ue",];
}