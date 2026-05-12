<?php

namespace App\Http\Controllers\Api;

use App\Efacturaparams;
use App\Efacturatokens;
use App\Exports\EfacturatokensExport;
use App\Http\Controllers\Controller;
use App\Imports\EfacturatokensImport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
class EfacturatokensController extends Controller
{
    public function callback(Request $request){
        Log::info("A fost apelat callback");
        Log::info($request);
        Log::info($request->ip());
        $codeAnaf=$request->code;
        
        $cui=Efacturatokens::where("access_token",null)->get()->first();
        Log::info($cui);
        getTokenAnaf($request->code,$cui->cui);
        ob_end_clean(); 
        ob_start(); 
        return json_encode("Autorizare efectuata cu succes!!!");
    }
    public function gettoken(Request $request)
    {
        Log::info("Get token pentru ".$request->cui);
        $cui=$request->cui;
        //1. Verific daca am token valabil pentru cui si returnez token
        $token=Efacturatokens::where("cui",$cui)
                               ->whereDate("data_obtinerii","<=",Carbon::today()) 
                               ->whereDate("data_expirare",">",Carbon::today())  
                               ->get()->first();
        Log::info("Get token Pas 1 ");                       
        if($token) {      // NU AM TOKEN VALABIL
            Log::info("Get token Pas 2 ");                                              
            return json_encode($token); 
        }else{ 
            Log::info("Get token Pas 3 ");
            $efacturaparams=Efacturaparams::get()->first();
            $token=Efacturatokens::where("cui",$cui)->get()->first();
            Log::info("Get token Pas 4");
            if($token) {      // AM TOKEN DAR NU ESTE VALABIL SI FAC REFRESH LA TOKEN
                Log::info("Get token Pas 5");
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL,$efacturaparams->link_token);
                curl_setopt($ch, CURLOPT_POST, TRUE);
                curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
                        'client_id'     => urlencode(env("CLIENT_ANAF_ID")),
                        'client_secret' => urlencode(env("CLIENT_ANAF_SECRET")),
                        'refresh_token'  => urlencode($token->refresh_token),
                        'grant_type'    => 'refresh_token',
                    ]));
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
               if (curl_errno($ch)){

                    // moving to display page to display curl errors
                    Log::info("EROARE LA REFRESH TOKEN PTR CUI: ". $cui ." ".curl_errno($ch));
                }else{
                    //getting response from server
                    Log::info("Get token Pas 6");
                    $response = curl_exec($ch);
                    curl_close ($ch);
                    $raspuns=json_decode($response);
                    Log::info("Get token Pas 7");                    
                    Log::info($response);                    
                    Log::info($raspuns->expires_in);
                    Log::info($raspuns->expires_in/86400);
                    Log::info(Carbon::today()->addDays($raspuns->expires_in/86400));
                    $token=Efacturatokens::create(["cui"=>$cui,
                                            "access_token"=>$raspuns->access_token,
                                            "refresh_token"=>$token->refresh_token,
                                            "data_obtinerii"=>Carbon::today(),
                                            "data_expirare"=>Carbon::today()->addDays($raspuns->expires_in/86400)
                                            ]);

                    return json_encode($token); 
                }
            }else{  // NU AM NICI TOKEN EXPIRAT TREBUIE SA OBTIN PRIMUL TOKEN
                Log::info("Get token Pas 8");                    
                // $authUrl = $efacturaparams->link_authorization.'?'.http_build_query([
                //     'response_type'          => 'code',
                //     'client_id'     => urlencode(env("CLIENT_ANAF_ID")),
                //     'redirect_uri'           => "https://maya.bancomatic.ro/api/callback",              
                //     'scope'                  => "",
                   
                // ]);
                // Log::info($authUrl);
                // $ch = curl_init();
                // curl_setopt($ch, CURLOPT_URL,$authUrl);
                // //curl_setopt($ch, CURLOPT_POST, TRUE); 
                // curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                // curl_setopt($ch, CURLOPT_TIMEOUT, 3);
                // curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
                // $response = curl_exec($ch);
                // curl_close ($ch);
                // Log::info("Get token Pas 9");                    
                // Log::info($response);                   

                $token=Efacturatokens::create(["cui"=>$cui]);
                ob_end_clean(); 
                ob_start(); 
                return json_encode("OK");
                
  


                // $codeAnaf=session("codeAnaf");
                // Log::info("Get token Pas 10");                     
                // Log::info($codeAnaf);                    
                // $ch = curl_init();
                // curl_setopt($ch, CURLOPT_URL,$efacturaparams->link_token);
                // curl_setopt($ch, CURLOPT_POST, TRUE);
                // curl_setopt($ch, CURLOPT_HTTPHEADER, [ 'Content-Type: application/x-www-form-urlencoded']);
                // curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
                // curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
                //     'code'          => $codeAnaf,
                //     'client_id'     =>urlencode(env("CLIENT_ANAF_ID")),
                //     'client_secret' =>urlencode(env("CLIENT_ANAF_SECRET")),
                //     'redirect_uri'  => "https://maya.bancomatic.ro/api/callback",
                //     'grant_type'    => 'authorization_code',
                // ]));
                // curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                // $response = curl_exec($ch);
                // curl_close ($ch);
                // Log::info("Get token Pas 11");                    
                // Log::info($response);                    
                // $raspuns=json_decode($response);
                // $token=Efacturatokens::create(["cui"=>$cui,
                //                             "access_token"=>$raspuns->access_token,
                //                             "refresh_token"=>$raspuns->refresh_token,
                //                             "data_obtinerii"=>Carbon::today(),
                //                             "data_expirarii"=>$raspuns->expires_in/86400
                //                             ]);

                // return json_encode($token); 


            }
        }                       
        
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function indexPaginat(Request $request)
    {
        $efacturatokens= Efacturatokens::select('*')->where("company_id",session("company_id"));
        $efacturatokens=filterRequest($efacturatokens,$request->searchModel,$request->cautareDupa,$request->sortModel,$request->filterModel);
        $efacturatokens=  $efacturatokens->paginate(50);
        
                              //::where("user_id",auth()->user()->id)
                                
        return json_encode($efacturatokens);
    }
     public function index()
    {
          $efacturatokens= Efacturatokens::where("company_id",session("company_id"))->get();
          return json_encode($efacturatokens);
    }
    public function export() 
        {
             
            ob_end_clean(); 
            ob_start(); 
             $company_id=session("company_id");
            return Excel::download((new EfacturatokensExport)->forCompany($company_id),"efacturatokens.xls");


        }

    public function import(Request $request) 
        {
          $fileName = "efacturatokens_".time().".".$request->file->getClientOriginalExtension();
          $request->file->move(public_path("upload"), $fileName);
          
          Excel::import(new EfacturatokensImport, public_path("upload")."/".$fileName);

          
            $efacturatokens= Efacturatokens::where("company_id",session("company_id"))
                                                     ->paginate(50);
         
            return json_encode($efacturatokens);
             
        
           
         }
        
    public function storeExcel() 
        {
            ob_end_clean(); 
            ob_start(); 
            $company_id=session("company_id");
            
            Excel::store((new EfacturatokensExport)->forCompany($company_id), "efacturatokens.xls","public",null,[
        "visibility" => "private",
    ]);
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
         $request->validate( [
         
    	  "cui"=>["required"],]);

        // event(new EfacturatokensUpdated());
         $data_expirare= Efacturatokens::create([
    	  "cui"=>$request->cui,
    	  "access_token"=>$request->access_token,
    	  "refresh_token"=>$request->refresh_token,
    	  "data_obtinerii"=>$request->data_obtinerii,
    	  "data_expirare"=>$request->data_expirare,           
        ]);
        return $data_expirare->paginate(50);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App/Efacturatokens  $efacturatokens
     * @return \Illuminate\Http\Response
     */
    public function show(Efacturatokens $efacturatokens)
    {
        $resp= Efacturatokens::where("id",$efacturatokens->id)
                               ->where("company_id",session("company_id"))
        					   ->get()->first();
                                
        return json_encode($resp);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App/Efacturatokens  $efacturatokens
     * @return \Illuminate\Http\Response
     */
    public function edit(Efacturatokens $efacturatokens)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App/Efacturatokens  $efacturatokens
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Efacturatokens $efacturatokens)
    {
        $efacturatokens->update([
    	  "cui"=>$request->cui,
    	  "access_token"=>$request->access_token,
    	  "refresh_token"=>$request->refresh_token,
    	  "data_obtinerii"=>$request->data_obtinerii,
    	  "data_expirare"=>$request->data_expirare,
        ]);
       // event(new EfacturatokensUpdated());
        return $efacturatokens;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App/Efacturatokens  $efacturatokens
     * @return \Illuminate\Http\Response
     */
    public function destroy(Efacturatokens $efacturatokens)
    {
        $efacturatokens->delete();
      //  event(new EfacturatokensUpdated());

    }
}