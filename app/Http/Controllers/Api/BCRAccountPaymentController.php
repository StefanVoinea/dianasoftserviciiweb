<?php

namespace App\Http\Controllers\Api;

use App\BCRAccountPayment;
use App\BankAccount;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BCRAccountPaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }
     public function incasariUser()
    {
        $conturiUser=BankAccount::where('user_id',auth()->user()->id)->get()->pluck('iban');
        $incasari=BCRAccountPayment::whereIn('iban',$conturiUser)
                                    ->orderBy('created_at','desc')
                                    ->get();
        return $incasari;
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
     * @param  \App\BCRAccountPayment  $bCRAccountPayment
     * @return \Illuminate\Http\Response
     */
    public function show(BCRAccountPayment $bCRAccountPayment)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\BCRAccountPayment  $bCRAccountPayment
     * @return \Illuminate\Http\Response
     */
    public function edit(BCRAccountPayment $bCRAccountPayment)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\BCRAccountPayment  $bCRAccountPayment
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, BCRAccountPayment $bCRAccountPayment)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\BCRAccountPayment  $bCRAccountPayment
     * @return \Illuminate\Http\Response
     */
    public function destroy(BCRAccountPayment $bCRAccountPayment)
    {
        //
    }
}
