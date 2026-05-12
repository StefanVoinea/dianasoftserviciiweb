<?php

namespace App\Http\Controllers\Api;

use App\Models\Company;
use App\Models\CompanyUser;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
     public function indexPaginat(Request $request)
    {
          $records= Company::select('*')   ;
          
          $records=filterRequest($records,$request->searchModel,$request->cautareDupa,$request->sortModel,$request->filterModel);
          $records= $records->orderBy('id','desc')
                                          ->paginate($request->pageLength,
                                                      ['page'=>$request->page]);
          
                                //::where("user_id",auth()->user()->id)
                                  
          return json_encode($records);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $company= Company::with('users')->get();
        return json_encode($company);
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
        
        // $token = str_replace("Bearer ","",$request->header('Authorization')); 
        // $currentuser=userfromPassportToken($token);
         $currentuser=$request->user();
         $request->validate( [
    	  "denumire"=>["required"],
    	  "cui"=>["required"],]);


        $company= Company::create([
    	  "denumire"=>$request->denumire,
    	  "cui"=>$request->cui,
    	  "regcom"=>$request->regcom,
    	  "adresa"=>$request->adresa,
    	  "localitate"=>$request->localitate,
    	  "judet"=>$request->judet,
    	  "telefon"=>$request->telefon,
    	  "email"=>$request->email,
    	  "capital_social"=>$request->capital_social,
    	  "banca"=>$request->banca,
    	  "cont"=>$request->cont,
    	  "plan_tarifar"=>$request->plan_tarifar,
    	  "cod_caen"=>$request->cod_caen,
    	  "email_factura"=>$request->email_factura,
    	  "email_restante"=>$request->email_restante,
    	  "slug"=>$request->slug,
    	  "operator_gdpr"=>$request->operator_gdpr,
    	  "nrautorizatie"=>$request->nrautorizatie,
    	  "cerber_url"=>$request->cerber_url,           
        ]);
        
        $company->users()->attach($currentuser->id);
        // CompanyUser::create([
        //         "company_id"=>$company->id,
        //         "user_id"=>$currentuser->id,
        // ]);
        
        return  $company;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Company  $company
     * @return \Illuminate\Http\Response
     */
    public function show(Company $company)
    {
        $resp= Company::where("id",$company->id)
        											->get()->first();
                                
        return json_encode($resp);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Company  $company
     * @return \Illuminate\Http\Response
     */
    public function edit(Company $company)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Company  $company
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Company $company)
    {
        $company->update([
    	  "denumire"=>$request->denumire,
    	  "cui"=>$request->cui,
    	  "regcom"=>$request->regcom,
    	  "adresa"=>$request->adresa,
    	  "localitate"=>$request->localitate,
    	  "judet"=>$request->judet,
    	  "telefon"=>$request->telefon,
    	  "email"=>$request->email,
    	  "capital_social"=>$request->capital_social,
    	  "banca"=>$request->banca,
    	  "cont"=>$request->cont,
    	  "plan_tarifar"=>$request->plan_tarifar,
    	  "cod_caen"=>$request->cod_caen,
    	  "email_factura"=>$request->email_factura,
    	  "email_restante"=>$request->email_restante,
    	  "slug"=>$request->slug,
    	  "operator_gdpr"=>$request->operator_gdpr,
    	  "nrautorizatie"=>$request->nrautorizatie,
    	  "cerber_url"=>$request->cerber_url,
        ]);
        return $company;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Company  $company
     * @return \Illuminate\Http\Response
     */
    public function destroy(Company $company)
    {
        foreach ($company->users as $user) {
           $company->users()->detach($user->id);
        }
        $company->delete();
        

    }
}