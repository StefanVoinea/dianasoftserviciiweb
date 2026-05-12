<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BankAccount extends Model
{
    //
    protected $fillable=['last_balance_check','balance','last_transaction_check','consent_id','societate_id','name',
                            'details'		];
    				
    public function transactions() {
             return $this->hasMany('App\BCRAccountTransaction','iban','iban');
			}
    public function payments() {
             return $this->hasMany('App\BCRAccountPayment','iban','iban');
            }
	public function bank() {
             return $this->belongsTo('App\Bank','bank_id','bank_id');
			}
}
