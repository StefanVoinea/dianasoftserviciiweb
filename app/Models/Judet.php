<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Judet extends Model
{
    use RecordsActivity;
    protected $table ="judete";
    protected $fillable = ["cod","denumire","auto","codjud","oasp","denjud","cfsom","cfcjas","denofa","cjnum"];
}