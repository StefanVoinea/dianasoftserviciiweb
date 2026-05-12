<?php

namespace App\Http\Controllers\Api;

use App\AccessToken;
use App\BCRAccountBalance;
use App\BCRAccountPayment;
use App\BCRAccountTransaction;
use App\Bank;
use App\BankAccount;
use App\Events\IncasareNoua;
use App\Events\UserGivedConsent;
use App\Http\Controllers\Controller;
use App\RefreshToken;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BCRBankController extends Controller
{
    

    // public function requestCode()               // SE FACE IN FRONT END ACUM
    // {
        
    //     try{
    //           $bank =  new Bank;
    //           $bank=Bank::where("bank_id","erste_bcr")->get()->first();
    //           $state=auth()->user()->token();
              
    //           return redirect($bank->idp_sandbox . '/auth?redirect_uri='.$bank->redirect_url.'&client_id='.
    //             $bank->client_id.'&response_type=code&access_type=offline&state='.$state);
    //         }
    //     catch(\Exception $err){
                
    //             info($err->getMessage());

    //             return $err->getMessage();
    //         }
    // }
   
    public function getPaymentStatus(Request $request)
    {   
         // Log::info("State:".$request->state);
         $code=$request->code; 
         $paymentId=str_replace("auth_payment","",$request->state);
         $payment=BCRAccountPayment::where('paymentId',$paymentId)->get()->first();
         
      //    $accessToken=retrieveValidAccessToken($userAuth,$bank);

         $payStatus="1";
         
      //    if($payment) //User::tokens()->where('id',$state)->where('revoked',false))
      //    {
      //    $bank =  new Bank;
      //    $bank=Bank::where("bank_id","erste_bcr")->get()->first();
      //    $client = new \GuzzleHttp\Client(['headers' => [
      //                                                       'Accept'=> '*/*',
      //                                                       'Authorization'=> 'Bearer '.$accessToken->access_token,
      //                                                       'Cache-Control' => 'no-cache',
      //                                                       'Connection' => 'keep-alive',
      //                                                       'Host'=>  'webapi.developers.erstegroup.com',
      //                                                       'Postman-Token'=>'sdsdfasd-9642-4fec-b361-1f1039f95f76-58da4ae3-bea3-40cb',
      //                                                       'User-Agent'=>'PostmanRuntime/7.13.0',
      //                                                       'accept-encoding'=>'gzip, deflate',
      //                                                       'cache-control' => 'no-cache',
      //                                                       'web-api-key'=>$bank->api_key,
      //                                                       'x-request-id' => '3asdfasdf8c2e-11e9-b683-526af7764f64'
      //                                                    ]]);

         
      //    $req = $client->get($bank->pisp_api_sandbox . $payment->link_status);
      //    $resp = json_decode($req->getBody());
      //    $payment->update(['transactionStatus'=>$resp->transactionStatus]);

      //    if($resp->transactionStatus=="ACSC"){
      //       $payStatus="1";   // PLATA EFECTUATA CU SUCCES 
      //    }else
      //    {
      //       $payStatus="0";   // PLATA NEINCHEIATA
      //    }
      // }
      event(new IncasareNoua());
      return redirect()->to('/payments/'.$payStatus);
    }
    public function getRefreshToken(Request $request)
    {   

         $code=$request->code; 
         $state=str_replace("auth_token","",$request->state);
         $userfromtoken=userfromPassportToken($state);
         $userAuth=User::where('id',$userfromtoken->id)->get()->first();

         if($userAuth) //User::tokens()->where('id',$state)->where('revoked',false))
         {
         $bank =  new Bank;
         $bank=Bank::where("bank_id","erste_bcr")->get()->first();
         $client = new \GuzzleHttp\Client(['headers' => [
                                                          'Content-Type' => 'application/x-www-form-urlencoded',
                                                          'cache-control' => 'no-cache',
                                                          'Postman-Token'=>'f57f9d62-2c8b-4278-891f-9975d8c85805'
                                                         ]]);

         
         $req = $client->get($bank->idp_sandbox . '/token?client_id='.$bank->client_id.'&code='.$code.
                                        '&grant_type=authorization_code&redirect_uri='.$bank->redirect_url.'/bcr_auth_token'.
                                        '&client_secret='.$bank->client_secret );
         $resp = json_decode($req->getBody());
         
         // $accessToken = new AccessToken();
         // $accessToken->access_token=$resp->access_token;
         // $accessToken->token_type=$resp->token_type;
         // $accessToken->expires_in=$bank->access_token_expire;
         // $accessToken->scope=$resp->scope;
         // $accessToken->user_id = $userAuth->id;                                 // DE PUS USER 
         // $accessToken->bank_id=$bank->bank_id;
         // $accessToken->save();

         $refreshTokens = new RefreshToken();
         $refreshTokens->access_token=$resp->access_token;
         $refreshTokens->token_type=$resp->token_type;
         $refreshTokens->access_expires_in=$bank->access_token_expire;
         $refreshTokens->refresh_expires_in=$resp->expires_in;
         $refreshTokens->scope=$resp->scope;
         $refreshTokens->refresh_token=$resp->refresh_token;
         $refreshTokens->user_id = $userAuth->id;                                 // DE PUS USER 
         $refreshTokens->bank_id=$bank->bank_id;
         $refreshTokens->consent_id=$code;
         $refreshTokens->save();
      }
      
      $bankAccounts=$this->retrieveAccounts($request);
      foreach ($bankAccounts as $bankAccount) {
          $this->retrieveAccountBalances($bankAccount);
          $this->retrieveAccountTransactions($bankAccount);

      };

      event(new UserGivedConsent());

      return redirect()->to('/');
    }

    // public function refreshAccessToken()
    // {
    //     $bank =  new Bank;
    //     $bank=Bank::where("bank_id","erste_bcr")->get()->first();
    //     $refreshToken=retrieveValidRefreshToken($user,$bank);
    //     $client = new \GuzzleHttp\Client(['headers' => [
    //                                                       'Content-Type' => 'application/x-www-form-urlencoded',
    //                                                       'cache-control' => 'no-cache',
    //                                                       'Postman-Token'=>'f57f9d62-2c8b-4278-891f-9975d8c85805'
    //                                                      ]]);

    //     $req = $client->get($bank->idp_sandbox . '/token?grant_type=refresh_token&refresh_token='.$refreshToken->refresh_token.'&client_id='.
    //         $bank->client_id.'&client_secret='.$bank->client_secret);
    //      $resp = json_decode($req->getBody());
    //      $accessToken = new AccessToken();
    //      $accessToken->access_token=$resp->access_token;
    //      $accessToken->token_type=$resp->token_type;
    //      $accessToken->expires_in=$resp->expires_in;
    //      $accessToken->scope=$resp->scope;
    //      $accessToken->user_id = auth()->user()->id;                                
    //      $accessToken->bank_id=$bank->bank_id;
    //      $accessToken->save();
    //      return $accessToken;
    // }
   // public function retrieveValidRefreshToken()
   //  {

   //     $refreshToken=RefreshToken::where('user_id',auth()->user()->id)
   //                   ->where('bank_id','erste_bcr')
   //                   ->where(DB::raw('DATE_ADD(created_at, INTERVAL expires_in second)'),'>', Carbon::now())
   //                   ->get()
   //                   ->first();
        
   //      return $refreshToken;
   //  }

    // public function retrieveValidAccessToken()
    // {
    //    $accessToken=AccessToken::where('user_id',auth()->user()->id)
    //                  ->where('bank_id','erste_bcr')
    //                  ->where(DB::raw('DATE_ADD(created_at, INTERVAL expires_in second)'),'>', Carbon::now())
    //                  ->get()
    //                  ->first();
       
    //     if(!$accessToken){
    //        $accessToken = $this->refreshAccessToken();
    //     }
    //     return $accessToken;
    // }

    public function retrieveAccounts(Request $request)
    {
        $code=$request->code; 
        $state=str_replace("auth_token","",$request->state);
        $userfromtoken=userfromPassportToken($state);
        $userAuth=User::where('id',$userfromtoken->id)->get()->first();
        $bank =  new Bank;
        $bank=Bank::where("bank_id","erste_bcr")->get()->first();
        $accessToken=retrieveValidAccessToken($userAuth,$bank);

        $client = new \GuzzleHttp\Client(['headers' => 
                                            [
                                            'Accept'=> '*/*',
                                            'Authorization'=> 'Bearer '.$accessToken->access_token,
                                            'Cache-Control' => 'no-cache',
                                            'Connection' => 'keep-alive',
                                            'Host'=>  'webapi.developers.erstegroup.com',
                                            'Postman-Token'=>'sdsdfasd-9642-4fec-b361-1f1039f95f76-58da4ae3-bea3-40cb',
                                            'User-Agent'=>'PostmanRuntime/7.13.0',
                                            'accept-encoding'=>'gzip, deflate',
                                            'cache-control' => 'no-cache',
                                            'web-api-key'=>$bank->api_key,
                                            'x-request-id' => '3asdfasdf8c2e-11e9-b683-526af7764f64'
                                            ],

                                    'cert' => base_path().'/certificates/public-key-2c01c1389810512044d0724cc9ffed61e3a35c656852ac448d3a471360a39d96.pem',
                                    'ssl_key' => base_path().'/certificates/private-key-2c01c1389810512044d0724cc9ffed61e3a35c656852ac448d3a471360a39d96.key',
                                                 
                                                  // 'debug' => true,
                                                 ]);

        
         $req = $client->get($bank->aisp_api_sandbox . '/v1/accounts');
         $resp = json_decode($req->getBody());

         BankAccount::where('user_id',$userAuth->id)
                      ->where('bank_id',$bank->bank_id)
                      ->delete();

         foreach($resp->accounts as $account)
         {
           // dd($account);
            $bankAccount=new BankAccount();
            $bankAccount->iban=$account->iban;
            $bankAccount->name=$account->name;
            $bankAccount->cashAccountType=$account->cashAccountType;
            $bankAccount->status=$account->status;
            $bankAccount->bic=$account->bic;
            $bankAccount->resourceId=$account->resourceId;
            $bankAccount->usage=$account->usage;
            $bankAccount->details=$account->details;
            $bankAccount->currency=$account->currency;
            $bankAccount->link_self=$account->_links->self->href;
            $bankAccount->link_balances=$account->_links->balances->href;
            $bankAccount->link_transactions=$account->_links->transactions->href;
            $bankAccount->user_id = $userAuth->id;                                 // DE PUS USER 
            $bankAccount->bank_id=$bank->bank_id;
            $bankAccount->consent_id=$accessToken->consent_id;
            $bankAccount->save();
         }
         $bankAccounts=BankAccount::where('user_id',$userAuth->id)
                      ->where('bank_id',$bank->bank_id)
                      ->get();
         return $bankAccounts->load('bank');
    
    }
    public function retrieveAccountDetails(BankAccount $bankAccount)
    {
        $bank =  new Bank;
        $bank=Bank::where("bank_id",$bankAccount->bank_id)->get()->first();
        $user=User::where("id",$bankAccount->user_id)->get()->first();
        $accessToken=retrieveValidAccessToken($user,$bank);
        $client = new \GuzzleHttp\Client(['headers' => 
                                            [
                                            'Accept'=> '*/*',
                                            'Authorization'=> 'Bearer '.$accessToken->access_token,
                                            'web-api-key'=>$bank->api_key,
                                            'x-request-id' => '30654as65fb2676-8c2e-11e9-b683-526af7764f64'
                                            ],

                                    'cert' => base_path().'/certificates/public-key-2c01c1389810512044d0724cc9ffed61e3a35c656852ac448d3a471360a39d96.pem',
                                    'ssl_key' => base_path().'/certificates/private-key-2c01c1389810512044d0724cc9ffed61e3a35c656852ac448d3a471360a39d96.key',
                                                 
                                                  // 'debug' => true,
                                                 ]);
         ///// ACCOUNT DETAILS
         $req = $client->get($bank->aisp_api_sandbox . $bankAccount->link_self);
         $resp = json_decode($req->getBody());
         dd($resp);
         // return $req;
         // $resp = json_decode($req->getBody());
    
    }
    

    public function retrieveAccountTransactions(BankAccount $bankAccount)
    {
        $bank =  new Bank;
        $bank=Bank::where("bank_id",$bankAccount->bank_id)->get()->first();
        $user=User::where("id",$bankAccount->user_id)->get()->first();
        $accessToken=retrieveValidAccessToken($user,$bank);
        $client = new \GuzzleHttp\Client(['headers' => 
                                            [
                                            'Accept'=> '*/*',
                                            'Authorization'=> 'Bearer '.$accessToken->access_token,
                                            'web-api-key'=>$bank->api_key,
                                            'x-request-id' => '30654as65fb2676-8c2e-11e9-b683-526af7764f64'
                                            ],

                                    'cert' => base_path().'/certificates/public-key-2c01c1389810512044d0724cc9ffed61e3a35c656852ac448d3a471360a39d96.pem',
                                    'ssl_key' => base_path().'/certificates/private-key-2c01c1389810512044d0724cc9ffed61e3a35c656852ac448d3a471360a39d96.key',
                                                 
                                                  // 'debug' => true,
                                                 ]);
         ///// TRANZACTII
          // $req = $client->get("https://webapi.developers.erstegroup.com/api/bcr/sandbox/v1/aisp/v1/accounts/11e07590-27d4-409e-914b-c248fb945b11/transactions");
         $req = $client->get($bank->aisp_api_sandbox . $bankAccount->link_transactions);
         $resp = json_decode($req->getBody());
         // TINE CONT DE KEY next ca sa iei si alte pagini daca exista

         BCRAccountTransaction::where('iban',$bankAccount->iban)
                                 ->delete();

          foreach($resp->transactions->booked as $transaction)
         {
           // dd($transaction);
            $transArr=json_decode(json_encode($transaction), true);

            $banktransaction=new BCRAccountTransaction();
            $banktransaction->iban=$bankAccount->iban;
            $banktransaction->currency=$bankAccount->currency;
            $banktransaction->transactionId=$transaction->transactionId;
            if(array_key_exists('endToEndId', $transArr)){
             $banktransaction->endToEndId=$transaction->endToEndId;
            }
            $banktransaction->bookingDate=str_replace("Z"," ",str_replace("T"," ",$transaction->bookingDate));
            $banktransaction->valueDate=str_replace("Z"," ",str_replace("T"," ",$transaction->valueDate));
            $banktransaction->transactionAmount=$transaction->transactionAmount->amount;
            $banktransaction->transactioncurrency=$transaction->transactionAmount->currency;
            $banktransaction->creditorName=$transaction->creditorName;
            $banktransaction->creditorAccount=$transaction->creditorAccount->iban;
            if(array_key_exists('debtorName', $transArr)){
               $banktransaction->debtorName=$transaction->debtorName;
            }
            $banktransaction->debtorAccount=$transaction->debtorAccount->iban;
            if(array_key_exists('remittanceInformationUnstructured', $transArr)){
            $banktransaction->remittanceInfoUnstructured=$transaction->remittanceInformationUnstructured;
             }
                       
            
            

            if(array_key_exists('currencyExchange', $transArr)){
                // dd($transArr,array_key_exists('currencyExchange', $transArr)) ;
                $banktransaction->sourceCurrency=$transaction->currencyExchange[0]->sourceCurrency;
                $banktransaction->exchangeRate=$transaction->currencyExchange[0]->exchangeRate;
                $banktransaction->unitCurrency=$transaction->currencyExchange[0]->unitCurrency;
                $banktransaction->targetCurrency=$transaction->currencyExchange[0]->targetCurrency;
                $banktransaction->quotationDate=$transaction->currencyExchange[0]->quotationDate;
            }

            $banktransaction->save();
         }
         
         $bankAccount->update([
                               'last_transaction_check'=>Carbon::now()
                            ]);
         $bankTransactions=BCRAccountTransaction::where('iban',$bankAccount->iban)
                                      ->get();
         
         return $bankTransactions;
       
    }
    public function retrieveAccountBalances(BankAccount $bankAccount)
    {
        $bank =  new Bank;
        $bank=Bank::where("bank_id",$bankAccount->bank_id)->get()->first();
        $user=User::where("id",$bankAccount->user_id)->get()->first();

        $accessToken=retrieveValidAccessToken($user,$bank);

        $client = new \GuzzleHttp\Client(['headers' => 
                                            [
                                            'Accept'=> '*/*',
                                            'Authorization'=> 'Bearer '.$accessToken->access_token,
                                            'web-api-key'=>$bank->api_key,
                                            'x-request-id' => '30654as65fb2676-8c2e-11e9-b683-526af7764f64'
                                            ],

                                    'cert' => base_path().'/certificates/public-key-2c01c1389810512044d0724cc9ffed61e3a35c656852ac448d3a471360a39d96.pem',
                                    'ssl_key' => base_path().'/certificates/private-key-2c01c1389810512044d0724cc9ffed61e3a35c656852ac448d3a471360a39d96.key',
                                                 
                                                  // 'debug' => true,
                                                 ]);
         ///// BALANCES
          // $req = $client->get("https://webapi.developers.erstegroup.com/api/bcr/sandbox/v1/aisp/v1/accounts/11e07590-27d4-409e-914b-c248fb945b11/transactions");
        $req = $client->get($bank->aisp_api_sandbox . $bankAccount->link_balances);
        $resp = json_decode($req->getBody());

        $balAccount=$bankAccount->balance;
        BCRAccountBalance::where('iban',$bankAccount->iban)
                                 ->delete();

          foreach($resp->balances as $balance)
         {
           // dd($balance);
            $transArr=json_decode(json_encode($balance), true);

            $bankbalance=new BCRAccountBalance();
            $bankbalance->iban=$bankAccount->iban;
            $bankbalance->referenceDate=$balance->referenceDate;
            $bankbalance->balanceAmount=$balance->balanceAmount->amount;
            $bankbalance->balanceCurrency=$balance->balanceAmount->currency;
            $bankbalance->balanceType=$balance->balanceType;
            if(array_key_exists('lastChangeDateTime', $transArr)){
             $bankbalance->lastChangeDateTime=str_replace("Z"," ",str_replace("T"," ",$balance->lastChangeDateTime));
             $bankbalance->lastCommittedTransaction=$balance->lastCommittedTransaction;
             $balAccount=$balance->balanceAmount->amount;
            }
            

            $bankbalance->save();
         }
         $bankAccount->update([
                               'last_balance_check'=>Carbon::now(),
                               'balance'=>$balAccount
                            ]); 
         $bankAccounts=BankAccount::where('user_id',$user->id)
                                  ->get();
         
         return $bankAccounts->load('bank');
    }

    public function makeOnePayment(Request $request)
    {
        
       
        $bankAccount=BankAccount::where('iban',$request->iban)->get()->first();
        $bank=Bank::where("bank_id",$bankAccount->bank_id)->get()->first();
        $userAuth=User::where('id',$bankAccount->user_id)->get()->first();
        $accessToken=retrieveValidAccessToken($userAuth,$bank);
        // $myBody='{"endToEndIdentification": "e2eid","debtorAccount": {"iban":"'.$bankAccount->iban.'"},"instructedAmount": {"currency": "'.$bankAccount->currency.'","amount": "'.$request->paymentAmount.'"},"creditorAccount":{"iban": "'.$request->paymentAmount.'"},"creditorAgent": "","creditorName":"'.$request->creditorName.'","creditorAddress": {"buildingNumber": "","city": "","country": "","postalCode":"","street": ""},"remittanceInformationUnstructured": "'.$request->paymentDetails.'"}';
        //   // dd($myBody);
        $myBody='{
  "endToEndIdentification": "e2eid",
  "debtorAccount": {
    "iban": "'.$bankAccount->iban.'"
  },
  "instructedAmount": {
    "currency": "'.$bankAccount->currency.'",
    "amount": "'.$request->paymentAmount.'"
  },
  "creditorAccount": {
    "iban": "'.$request->creditorAccount.'"
  },
  "creditorAgent": "DEUTDEF0XXX",
  "creditorName": "'.$request->creditorName.'",
  "creditorAddress": {
    "buildingNumber": "56",
    "city": "Bucuresti",
    "country": "RO",
    "postalCode": "90543",
    "street": "Bridge"
  },
  "remittanceInformationUnstructured": "'.$request->paymentDetails.'"
}';
          $client = new \GuzzleHttp\Client([
                                            'headers' => 
                                            [
                                            'Accept'=>'*/*',
                                            'Authorization'=> 'Bearer '.$accessToken->access_token,
                                            'Cache-Control'=>'no-cache',
                                            'Connection'=>'keep-alive',
                                            'Content-Type'=>'text/plain',
                                            'Host'=>'webapi.developers.erstegroup.com',
                                            'PSU-IP-Address'=>'10.192.1.1',
                                            'Postman-Token'=>'c7699500-b09e-49c8-a644-f54a1e488eac,c017bdc1-c9d9-48ae-adf2-da93c3f4b3a0',
                                            'TPP-IP-Address'=>'10.1.1.1',
                                            'psu-involved: true',
                                            'web-api-key'=>$bank->api_key,
                                            'x-request-id' => '3asdfasdf8c2e-11e9-b683-526af7764f64'
                                            ],
                                    'cert' => base_path().'/certificates/public-key-2c01c1389810512044d0724cc9ffed61e3a35c656852ac448d3a471360a39d96.pem',
                                    'ssl_key' => base_path().'/certificates/private-key-2c01c1389810512044d0724cc9ffed61e3a35c656852ac448d3a471360a39d96.key',
                                    
                                    
                                                 
                                                   // 'debug' => true,
                                                
                                    ]);
         $req = $client->post($bank->pisp_api_sandbox . '/v1/payments/sepa-credit-transfers',['body'=>$myBody]);
         $resp = json_decode($req->getBody());
         // dd($resp);
        // dd($resp->_links->scaRedirect->href);
         $payment= new BCRAccountPayment([
            'bank_id'=>'erste_bcr',
            'user_id'=>auth()->user()->id,
            'iban'=>$bankAccount->iban,
            'endToEndIdentification'=>'e2eid',
            'currency'=>$bankAccount->currency,
            'amount'=>$request->paymentAmount,
            'creditorIban'=>$request->creditorAccount,
            'creditorAgent'=>$request->creditorAgent,
            'creditorName'=>$request->creditorName,
            'creditorBuildingNumber'=>'',
            'creditorCity'=>'',
            'creditorCountry'=>'',
            'creditorPostalCode'=>'',
            'creditorStreet'=>'',
            'remittanceInformationUnstructured'=>$request->paymentDetails,
            'paymentId'=>$resp->paymentId,
            'transactionStatus'=>$resp->transactionStatus,
            'scaStatus'=>'',
            'link_scaRedirect'=>$resp->_links->scaRedirect->href,
            'link_self'=>$resp->_links->self->href,
            'link_status'=>$resp->_links->status->href,
            'link_scaStatus'=>$resp->_links->scaStatus->href

         ]) ;
         $payment->save();

         return json_encode($resp->_links->scaRedirect->href . '?redirect_uri='.$bank->redirect_url.'/bcr_auth_payment'.'&client_id='.
            $bank->client_id.'&state=auth_payment'.$resp->paymentId);
    
    }

    public function getTokenAndSoOn(Request $request)
    {

         $code=$request->code; 
         $state=$request->state;
         if($state)
         $bank =  new Bank;
         $bank=Bank::where("bank_id","erste_bcr")->get()->first();
         $client = new \GuzzleHttp\Client(['headers' => [
                                                          'Content-Type' => 'application/x-www-form-urlencoded',
                                                          'cache-control' => 'no-cache',
                                                          'Postman-Token'=>'f57f9d62-2c8b-4278-891f-9975d8c85805'
                                                         ]]);

         // dd($bank->idp_sandbox . '/token?oci_set_client_identifier(connection, client_identifier)='.$bank->client_id.'&code='.$code.
         //                                '&grant_type=authorization_code&redirect_uri='.$bank->redirect_url.
         //                                '&client_secret='.$bank->client_secret);
          //grant_type - Allowed values are authorization_code and refresh_token. Use authorization_code whnen you want to exchange code for acess token and refresh token. Use refresh_token when you want new acess token and you allready have a refresh token
         $req = $client->get($bank->idp_sandbox . '/token?client_id='.$bank->client_id.'&code='.$code.
                                        '&grant_type=authorization_code&redirect_uri='.$bank->redirect_url.
                                        '&client_secret='.$bank->client_secret );
         $resp = json_decode($req->getBody());
         // dd($resp->access_token);
         
         $accessToken = new AccessToken();
         $accessToken->access_token=$resp->access_token;
         $accessToken->token_type=$resp->token_type;
         $accessToken->expires_in=$resp->expires_in;
         $accessToken->scope=$resp->scope;
         $accessToken->refresh_token=$resp->refresh_token;
         $accessToken->user_id = auth()->user()->id;                                 // DE PUS USER 
         $accessToken->bank_id=$bank->bank_id;

         $accessToken->save();
         $client = new \GuzzleHttp\Client(['headers' => [
                                                          'Content-Type' => 'application/x-www-form-urlencoded',
                                                          'cache-control' => 'no-cache',
                                                          'Postman-Token'=>'f57f9d62-2c8b-4278-891f-9975d8c85805'
                                                         ]]);

         // dd($bank->idp_sandbox . '/token?oci_set_client_identifier(connection, client_identifier)='.$bank->client_id.'&code='.$code.
         //                                '&grant_type=authorization_code&redirect_uri='.$bank->redirect_url.
         //                                '&client_secret='.$bank->client_secret);
          //grant_type - Allowed values are authorization_code and refresh_token. Use authorization_code whnen you want to exchange code for acess token and refresh token. Use refresh_token when you want new acess token and you allready have a refresh token
         $req = $client->get($bank->idp_sandbox . '/token?grant_type=refresh_token&refresh_token='.$accessToken->refresh_token.'&client_id='.
            $bank->client_id.'&client_secret='.$bank->client_secret);
         $resp = json_decode($req->getBody());
         $accessToken = new AccessToken();
         $accessToken->access_token=$resp->access_token;
         $accessToken->token_type=$resp->token_type;
         $accessToken->expires_in=$resp->expires_in;
         $accessToken->scope=$resp->scope;
       //$accessToken->refresh_token=$resp->refresh_token;
         $accessToken->user_id = auth()->user()->id;                                 // DE PUS USER 
         $accessToken->bank_id=$bank->bank_id;

         $accessToken->save();
         $client = new \GuzzleHttp\Client(['headers' => 
                                            [
                                            'Accept'=> '*/*',
                                            'Authorization'=> 'Bearer '.$accessToken->access_token,
                                            'Cache-Control' => 'no-cache',
                                            'Connection' => 'keep-alive',
                                            'Host'=>  'webapi.developers.erstegroup.com',
                                            'Postman-Token'=>'sdsdfasd-9642-4fec-b361-1f1039f95f76-58da4ae3-bea3-40cb',
                                            'User-Agent'=>'PostmanRuntime/7.13.0',
                                            'accept-encoding'=>'gzip, deflate',
                                            'cache-control' => 'no-cache',
                                            'web-api-key'=>$bank->api_key,
                                            'x-request-id' => '3asdfasdf8c2e-11e9-b683-526af7764f64'
                                            ],

                                    'cert' => base_path().'/certificates/public-key-2c01c1389810512044d0724cc9ffed61e3a35c656852ac448d3a471360a39d96.pem',
                                    'ssl_key' => base_path().'/certificates/private-key-2c01c1389810512044d0724cc9ffed61e3a35c656852ac448d3a471360a39d96.key',
                                                 
                                                  // 'debug' => true,
                                                 ]);

        
         $req = $client->get($bank->aisp_api_sandbox . '/v1/accounts');
         $resp = json_decode($req->getBody());
         foreach($resp->accounts as $account)
         {
           // dd($account);
            $bankAccount=new BankAccount();
            $bankAccount->iban=$account->iban;
            $bankAccount->name=$account->name;
            $bankAccount->cashAccountType=$account->cashAccountType;
            $bankAccount->status=$account->status;
            $bankAccount->bic=$account->bic;
            $bankAccount->resourceId=$account->resourceId;
            $bankAccount->usage=$account->usage;
            $bankAccount->details=$account->details;
            $bankAccount->currency=$account->currency;
            $bankAccount->link_self=$account->_links->self->href;
            $bankAccount->link_balances=$account->_links->balances->href;
            $bankAccount->link_transactions=$account->_links->transactions->href;
            $bankAccount->user_id = auth()->user()->id;                                 // DE PUS USER 
            $bankAccount->bank_id=$bank->bank_id;
            $bankAccount->save();
         }

         $client = new \GuzzleHttp\Client(['headers' => 
                                            [
                                            'Accept'=> '*/*',
                                            'Authorization'=> 'Bearer '.$accessToken->access_token,
                                            'web-api-key'=>$bank->api_key,
                                            'x-request-id' => '3asdfasdf8c2e-11e9-b683-526af7764f64'
                                            ],

                                    'cert' => base_path().'/certificates/public-key-2c01c1389810512044d0724cc9ffed61e3a35c656852ac448d3a471360a39d96.pem',
                                    'ssl_key' => base_path().'/certificates/private-key-2c01c1389810512044d0724cc9ffed61e3a35c656852ac448d3a471360a39d96.key',
                                                 
                                                  // 'debug' => true,
                                                 ]);
         ///// TRANZACTII
         $req = $client->get($bank->aisp_api_sandbox . '/v1/accounts/'.$bankAccount->resourceId.'/transactions');
         $resp = json_decode($req->getBody());

         ////// SOLD
         $req = $client->get($bank->aisp_api_sandbox . '/v1/accounts/'.$bankAccount->resourceId.'/balances');
         $resp = json_decode($req->getBody());
         //////////         PLATA
         $myBody='{"endToEndIdentification": "e2eid","debtorAccount": {"iban":"'.$bankAccount->iban.'"},"instructedAmount": {"currency": "'.$bankAccount->currency.'","amount": "44.00"},"creditorAccount":{"iban": "DE15500105172293339744"},"creditorAgent": "DEUTDEF0XXX","creditorName":"Aurel","creditorAddress": {"buildingNumber": "56","city": "Bucuresti","country": "RO","postalCode":"90543","street": "Bridge"},"remittanceInformationUnstructured": "Ref. Number 1 Aurel"}';
          // dd($myBody);
          $client = new \GuzzleHttp\Client([
                                            'headers' => 
                                            [
                                            'Accept'=>'*/*',
                                            'Authorization'=> 'Bearer '.$accessToken->access_token,
                                            'Cache-Control'=>'no-cache',
                                            'Connection'=>'keep-alive',
                                            'Content-Type'=>'text/plain',
                                            'Host'=>'webapi.developers.erstegroup.com',
                                            'PSU-IP-Address'=>'10.192.1.1',
                                            'Postman-Token'=>'c7699500-b09e-49c8-a644-f54a1e488eac,c017bdc1-c9d9-48ae-adf2-da93c3f4b3a0',
                                            'TPP-IP-Address'=>'10.1.1.1',
                                            'psu-involved: true',
                                            'web-api-key'=>$bank->api_key,
                                            'x-request-id' => '3asdfasdf8c2e-11e9-b683-526af7764f64'
                                            ],
                                    'cert' => base_path().'/certificates/public-key-2c01c1389810512044d0724cc9ffed61e3a35c656852ac448d3a471360a39d96.pem',
                                    'ssl_key' => base_path().'/certificates/private-key-2c01c1389810512044d0724cc9ffed61e3a35c656852ac448d3a471360a39d96.key',
                                    
                                    
                                                 
                                                   // 'debug' => true,
                                                
                                    ]);
         $req = $client->post($bank->pisp_api_sandbox . '/v1/payments/sepa-credit-transfers',['body'=>$myBody]);
         $resp = json_decode($req->getBody());
         dd($resp);
        // dd($resp->_links->scaRedirect->href);
         return redirect($resp->_links->scaRedirect->href . '?redirect_uri='.$bank->redirect_url.'&client_id='.
            $bank->client_id.'&state=your_state');
        // dd($resp);


    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Bank  $Bank
     * @return \Illuminate\Http\Response
     */
    public function show(Bank $Bank)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Bank  $Bank
     * @return \Illuminate\Http\Response
     */
    public function edit(Bank $Bank)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Bank  $Bank
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Bank $Bank)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Bank  $Bank
     * @return \Illuminate\Http\Response
     */
    public function destroy(Bank $Bank)
    {
        //
    }
}
