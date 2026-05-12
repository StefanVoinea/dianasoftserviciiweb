<?php

namespace App\Http\Controllers\Api;

use App\BCRAccountBalance;
use App\BankAccount;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BCRAccountBalanceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(BankAccount $bankAccount)
    {
         $accountBalances=BCRAccountBalance::where('iban',$bankAccount->iban)
                                      ->get();
        return $accountBalances;
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
     * @param  \App\BCRAccountBalance  $bCRAccountBalance
     * @return \Illuminate\Http\Response
     */
    public function show(BCRAccountBalance $bCRAccountBalance)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\BCRAccountBalance  $bCRAccountBalance
     * @return \Illuminate\Http\Response
     */
    public function edit(BCRAccountBalance $bCRAccountBalance)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\BCRAccountBalance  $bCRAccountBalance
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, BCRAccountBalance $bCRAccountBalance)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\BCRAccountBalance  $bCRAccountBalance
     * @return \Illuminate\Http\Response
     */
    public function destroy(BCRAccountBalance $bCRAccountBalance)
    {
        //
    }
}
