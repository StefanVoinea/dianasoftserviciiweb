<?php

namespace App\Http\Controllers\Api;

use App\BCRAccountTransaction;
use App\BankAccount;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BCRAccountTransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(BankAccount $bankAccount)
    {
        $bankTransactions=BCRAccountTransaction::where('iban',$bankAccount->iban)
                                                ->orderBy('id','DESC')
                                                 ->get()  ;

        return $bankTransactions;
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
     * @param  \App\BCRAccountTransaction  $bCRAccountTransaction
     * @return \Illuminate\Http\Response
     */
    public function show(BCRAccountTransaction $bCRAccountTransaction)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\BCRAccountTransaction  $bCRAccountTransaction
     * @return \Illuminate\Http\Response
     */
    public function edit(BCRAccountTransaction $bCRAccountTransaction)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\BCRAccountTransaction  $bCRAccountTransaction
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, BCRAccountTransaction $bCRAccountTransaction)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\BCRAccountTransaction  $bCRAccountTransaction
     * @return \Illuminate\Http\Response
     */
    public function destroy(BCRAccountTransaction $bCRAccountTransaction)
    {
        //
    }
}
