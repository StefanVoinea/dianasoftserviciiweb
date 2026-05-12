<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BCRAccountPayment extends Model
{
     
     protected $table ="bcr_account_payments";
   protected $fillable=[
   			'bank_id',
            'user_id',
            'iban',
            'endToEndIdentification',
            'currency',
            'amount',
            'creditorIban',
            'creditorAgent',
            'creditorName',
            'creditorBuildingNumber',
            'creditorCity',
            'creditorCountry',
            'creditorPostalCode',
            'creditorStreet',
            'remittanceInformationUnstructured',
            'paymentId',
            'transactionStatus',
            'scaStatus',
            'link_scaRedirect',
            'link_self',
            'link_status',
            'link_scaStatus'
            ];
    public function account() {
             return $this->belongsTo('App\BankAccount','iban','iban');
			}
}
