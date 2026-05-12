<?php

namespace App\Http\Controllers\Api;

use App\AccessToken;
use App\Bank;
use App\BankAccount;
use App\Http\Controllers\Controller;
use App\RefreshToken;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BanksController extends Controller
{
    

    

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        $token = str_replace("Bearer ","",$request->header('Authorization'));
        
        $banks=Bank::all();
        $bankstmp=[];
        $banktmp=[];
        foreach($banks as $bank)
        {
         
         $refreshToken=RefreshToken::where('bank_id',$bank->bank_id)
                       ->where('user_id',auth()->user()->id) 
                       ->where('created_at','>',Carbon::now()->subSeconds($bank->refresh_token_expire))
                       ->get()
                       ->first();
         if($refreshToken)
         {
            $token_expires_in= Carbon::createFromFormat('Y-m-d H:i:s',$refreshToken->created_at)->addSeconds($refreshToken->expires_in)->format('d.m.Y');
            $scope=$refreshToken->scope;
            $consent_id=$refreshToken->consent_id;

         }else
         {
            $token_expires_in="";
            $scope="";
            $consent_id="";
         };

         $banktmp=[
                    'id'=>$bank->id,
                    'bank_id'=>$bank->bank_id,
                    'group'=>$bank->group,
                    'name'=>$bank->name,
                    'logo'=>$bank->logo,
                    'country'=>$bank->country,
                    'token_expires_in'=>$token_expires_in,
                    'consent_id'=>$consent_id,
                    'scope'=>$scope,
                    'codeURL'=>$bank->idp_sandbox . '/auth?redirect_uri='.$bank->redirect_url.'/bcr_auth_token'.
                                        '&client_id='.
                $bank->client_id.'&response_type=code&access_type=offline&state=auth_token'. $token

                    ];
           array_push($bankstmp, $banktmp);
        }
        return json_encode($bankstmp);        

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
