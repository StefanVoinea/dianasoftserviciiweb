<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BCRAccountTransaction extends Model
{
    protected $table ="bcr_account_transactions";

    public function account() {
             return $this->belongsTo('App\BankAccount','iban','iban');
			}
}
