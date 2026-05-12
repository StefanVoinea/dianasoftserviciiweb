<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bank extends Model
{
    //
    protected $table ="banks";
     public function accounts() {
             return $this->hasMany('App\BankAccount','bank_id','bank_id');
			}
}
