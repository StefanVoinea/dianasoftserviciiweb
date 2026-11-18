<?php

namespace App\Http\Controllers\Api;

use App\Models\Efacturaparams;
use App\Models\Efacturatokens;
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
        // 0. Reinnoire ceruta inainte de expirare (fortat=1, cerut 18.11.2026): se cere la ANAF un token nou cu
        //    refresh token-ul celui mai recent token al CUI-ului. Tokenul existent ramane neatins; daca ANAF refuza,
        //    se intoarce eroarea si aplicatiile merg mai departe cu tokenul valabil.
        if ($request->boolean("fortat")) {
            return $this->reinnoiesteFortat($cui);
        }
        //1. Verific daca am token valabil pentru cui si returnez token (cel mai nou, ca dupa o reinnoire fortata
        //   sa se dea tokenul nou, nu cel vechi inca valabil)
        $token=Efacturatokens::where("cui",$cui)
                               ->whereDate("data_obtinerii","<=",Carbon::today())
                               ->whereDate("data_expirare",">",Carbon::today())
                               ->orderByDesc("id")
                               ->get()->first();
        Log::info("Get token Pas 1 ");                       
        if($token) {      // NU AM TOKEN VALABIL
            Log::info("Get token Pas 2 ");                                              
            return json_encode($token); 
        }else{ 
            Log::info("Get token Pas 3 ");
            // tokenul cel mai recent care are refresh token (corectat 18.11.2026: inainte se lua PRIMUL rand al CUI-ului -
            // refresh token-ul cel mai vechi, care expira la ANAF, sau randul gol lasat de o autorizare neterminata)
            $token=Efacturatokens::where("cui",$cui)->whereNotNull("access_token")->whereNotNull("refresh_token")->orderByDesc("id")->first();
            Log::info("Get token Pas 4");
            if($token) {      // AM TOKEN DAR NU ESTE VALABIL SI FAC REFRESH LA TOKEN
                Log::info("Get token Pas 5");
                // aceeasi cerere ca la reinnoirea ceruta inainte de expirare: token nou cu refresh token-ul celui mai
                // recent, pastrand refresh token-ul nou din raspuns; daca ANAF refuza se intoarce {"eroare": ...}
                // (inainte: eroare 500)
                return $this->reinnoiesteFortat($cui);
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

                // randul gol care asteapta autorizarea cu certificatul (callback-ul il completeaza): unul singur pe CUI
                if(!Efacturatokens::where("cui",$cui)->whereNull("access_token")->exists()) {
                    $token=Efacturatokens::create(["cui"=>$cui]);
                }
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
     * Token nou de la ANAF inainte de expirarea celui existent (gettoken?cui=...&fortat=1). Foloseste refresh token-ul
     * celui mai recent token al CUI-ului; raspunsul ANAF aduce si un refresh token nou, care se pastreaza. Tokenul
     * vechi nu se sterge. La esec intoarce {"eroare": "..."} si nu scrie nimic.
     */
    private function reinnoiesteFortat($cui)
    {
        Log::info("Get token fortat pentru ".$cui);
        $vechi=Efacturatokens::where("cui",$cui)->whereNotNull("access_token")->whereNotNull("refresh_token")->orderByDesc("id")->first();
        if(!$vechi) {
            return response()->json(["eroare"=>"Nu exista un token ANAF pentru acest CUI; este nevoie de autorizarea cu certificatul digital."]);
        }
        $efacturaparams=Efacturaparams::get()->first();
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$efacturaparams->link_token);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_TIMEOUT, 45);
        // aceiasi parametri ca la reinnoirea de dupa expirare (mai sus)
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
                'client_id'     => urlencode(env("CLIENT_ANAF_ID")),
                'client_secret' => urlencode(env("CLIENT_ANAF_SECRET")),
                'refresh_token'  => urlencode($vechi->refresh_token),
                'grant_type'    => 'refresh_token',
            ]));
        $response = curl_exec($ch);
        $eroareCurl = curl_errno($ch) ? curl_error($ch) : null;
        curl_close($ch);
        if($eroareCurl) {
            Log::info("EROARE LA REFRESH TOKEN FORTAT PTR CUI: ".$cui." ".$eroareCurl);
            return response()->json(["eroare"=>"ANAF nu a raspuns la cererea de token: ".$eroareCurl]);
        }
        $raspuns=json_decode($response);
        if(!$raspuns || empty($raspuns->access_token) || empty($raspuns->expires_in)) {
            Log::info("REFRESH TOKEN FORTAT REFUZAT PTR CUI: ".$cui." ".substr((string)$response,0,500));
            return response()->json(["eroare"=>"ANAF a refuzat reinnoirea tokenului: ".substr(strip_tags((string)$response),0,200)]);
        }
        $token=Efacturatokens::create(["cui"=>$cui,
                                "access_token"=>$raspuns->access_token,
                                "refresh_token"=>!empty($raspuns->refresh_token) ? $raspuns->refresh_token : $vechi->refresh_token,
                                "data_obtinerii"=>Carbon::today(),
                                "data_expirare"=>Carbon::today()->addDays($raspuns->expires_in/86400)
                                ]);

        return json_encode($token);
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