<?php

namespace App\Http\Controllers\Api;

use App\BankAccount;
use App\Http\Controllers\Controller;
use App\Societate;
use Illuminate\Http\Request;

class BankAccountsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $bankAccounts=BankAccount::where('user_id',auth()->user()->id)
                                  ->get();

         return $bankAccounts->load('bank');
    }

    public function societateAccounts(Societate $societate)
    {
        $bankAccounts=BankAccount::where('societate_id',$societate->id)
                                  ->get();

         return $bankAccounts->load('bank');
    }
    public function currentAccount(BankAccount $bankAccount)
    {
        $bankAccounts=BankAccount::where('user_id',auth()->user()->id)
                                   ->where('id',$bankAccount->id)             
                                  ->get()
                                  ->first();

         return $bankAccounts->load('bank');
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
     * @param  \App\BankAccount  $bankAccount
     * @return \Illuminate\Http\Response
     */
    public function show(BankAccount $bankAccount)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\BankAccount  $bankAccount
     * @return \Illuminate\Http\Response
     */
    public function edit(BankAccount $bankAccount)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\BankAccount  $bankAccount
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, BankAccount $bankAccount)
    {

        $soc_id=Societate::where('denumire',$request->societate_id)->get()->first();
       
        $bankAccount->update([
                'societate_id'=>$soc_id->id,
                'name'=>$request->name,
                'details'=>$request->details
        ]);
        return $bankAccount; 
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\BankAccount  $bankAccount
     * @return \Illuminate\Http\Response
     */
    public function destroy(BankAccount $bankAccount)
    {
        //
    }
}
