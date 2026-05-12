<?php

namespace App\Http\Controllers\Api;

use App\Models\Societate;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SocietateController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $records=Societate::where('user_id',auth()->user()->id);
          $records=filterRequest($records,$request->searchModel,$request->cautareDupa,$request->sortModel,$request->filterModel);
          $records= $records->orderBy('id','desc')
                                          ->paginate($request->pageLength,
                                                      ['page'=>$request->page]);
          
                                //::where("user_id",auth()->user()->id)
                                  
          return json_encode($records);                         
        
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
            'denumire' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255'],
            
        ]);


         return Societate::create([
            'user_id' => auth()->user()->id,
            'uuid'=>str_random(32),
            'denumire'=>$request->denumire,
            'cui'=>$request->cui,
            'regcom'=>$request->regcom,
            'adresa'=>$request->adresa,
            'localitate'=>$request->localitate,
            'judet'=>$request->judet,
            'telefon'=>$request->telefon,
            'email' => $request->email,
            'capital_social'=>$request->capital_social,
            'banca'=>$request->banca,
            'cont'=>$request->cont,
            'gps_position'=>$request->gps_position,
            'plan_tarifar'=>$request->plan_tarifar,
            
        ]);
    }


    public function verificaCUI($cui)
    {    
            $cui=filter_var($cui, FILTER_SANITIZE_NUMBER_INT);
            $xmlDocument = file_get_contents("https://legacy.openapi.ro/api/companies/".$cui.".xml");
            
             $xml = new \SimpleXMLElement($xmlDocument);
             $json_string = json_encode($xml);    
             $result = json_decode($json_string, TRUE);
              
            
             $societate= new Societate;   
             $societate->cui = $result["cif"];
             $societate->denumire = $result["name"];
             $societate->adresa=$result["address"];
             $societate->localitate=$result["city"];
             $societate->judet=$result["state"];
             $societate->regcom=$result["registration-id"];
             $societate->telefon=$result["phone"];
             // if ($result["vat"]==1)
             // {
             //    $user->platitor_tva="Platitor de TVA Romania";   
             // } else
             // {
             //    $user->platitor_tva="Neplatitor de TVA Romania";
             // };

             
            
             return $societate;

    }
    /**
     * Display the specified resource.
     *
     * @param  \App\Societate  $societate
     * @return \Illuminate\Http\Response
     */
    public function show(Societate $societate)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Societate  $societate
     * @return \Illuminate\Http\Response
     */
    public function edit(Societate $societate)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Societate  $societate
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Societate $societate)
    {
        $societate->update([
                'denumire'=>$request->denumire,
                'cui'=>$request->cui,
                'regcom'=>$request->regcom,
                'adresa'=>$request->adresa,
                'localitate'=>$request->localitate,
                'judet'=>$request->judet,
                'telefon'=>$request->telefon,
                'email'=>$request->email,
                'capital_social'=>$request->capital_social,
                'banca'=>$request->banca,
                'cont'=>$request->cont,
                'gps_position'=>$request->gps_position,
                'plan_tarifar'=>''
        ]);
        return $societate;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Societate  $societate
     * @return \Illuminate\Http\Response
     */
    public function destroy(Societate $societate)
    {
        $societate->delete();

    }
}
