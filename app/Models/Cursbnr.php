<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cursbnr extends Model
{
    
    protected $table ="cursbnr";
    protected $fillable = ["data","data_comunicarii","tip_valuta","curs",];
}