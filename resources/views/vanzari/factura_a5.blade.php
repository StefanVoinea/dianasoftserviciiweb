@extends('layouts.antet_factura_a5')

@section ('content')
    <!-- <hr>  -->
           
   <hr>
    <table class="table table-condesed" width="100%">
      <tr>
      <td align ="center" width="50%">
           <div class="mr-10">
            
             <img   src="{{env('APP_URL').'/images/logo/'.$company->slug.'/logo.png'}}" alt="logo" />
            <!-- <a class="navbar-brand" href="{{ url('/') }}">
                          <img   width=100% height=100% src="/logo_alprof.png" alt="logo" />
                          </a>  
             --></div>
       </td>
        <td width="50%">
          <div class="text-sm" >
          <h3><strong>FACTURĂ</strong></h3>
        
        Seria: <strong>{{$vanzare->seria}}</strong> Număr: <strong>{{$vanzare->numar}}</strong> <br>
        Data emiterii:   <strong>{{dateFormatAfisare($vanzare->data)}}</strong> Data scadenta: <strong>  {{dateFormatAfisare($vanzare->termen_plata)}}</strong> <br>
      <!--     Curs valutar {{dateformatAfisare($vanzare->data)}}: 1 EUR = <strong>{{number_format(cursBNR($vanzare->data,'EUR'),4)}} Lei</strong> -->
          </div>
      </td>
  </tr>
</table>
<hr>
       <table class="table table-condesed" width="100%"  >

            <tr>
              <td class="text-sm" align ="left" width="50%">
        <div >
      <!-- <h4> -->
      <div >
        
      <div>
      
      Furnizor: <strong> {{$company->denumire}}</strong><br>
      
       Reg.com.:<strong> {{$company->regcom}}</strong>  C.U.I.: <strong>{{$company->cui}}</strong>
      </div>
      <div>
      Adresa:<strong>{{$company->adresa}}</strong> 
      </div>
      <div>
      Telefon:<strong>{{$company->telefon}}</strong>
      
      Email:<strong>{{$company->email}}</strong>
      </div>
      <div>

      Banca:<strong>{{$company->banca}}</strong>
      Cont:<strong>{{$company->cont}}</strong>
      </div>
      <div>
      Banca:<strong>{{$company->banca_valuta}}</strong>
      Cont:<strong>{{$company->cont_valuta}}</strong>
      </div>
     <div>

      Capital social:<strong>{{$company->capital_social}}</strong>
      
      </div>
      </div>
  <!-- </h4> -->
</div>
</td>
<td class="text-sm" width="50%">
			Client: <strong>{{ $vanzare->partener }}</strong>  <br>
			C.U.I.: <strong>{{ $vanzare->cui }}</strong>   <br>
			Reg. com.: <strong>{{ $vanzare->regcom }}</strong> Telefon: <strong>{{ $vanzare->telefon }}</strong> <br>
			Adresa: <strong>{{ $vanzare->adresa }}</strong>   <br>
			Banca: <strong>{{ $vanzare->banca }}</strong> 
			Cont: <strong>{{ $vanzare->cont }}</strong> <br>
			
			</td>

 </tr>
 </table>      

  
<hr>
 <div >
       	   
	  </div>	
	  
	     <table class="table table-condesed" width=100%  >
            
            <tr class="text-sm">
              <th align ="center" width="5%">
                    <center>Nr <br> crt</center>
                </th>
                <th align ="center" width="45%">
                    Denumire produselor sau a serviciilor
                </th>
               <!--   <th align ="center" width="5%">
                    %TVA
                </th> -->
                <th align ="center" width="5%">
                    Um
                </th>
                
                <th align ="center" width="10%">
                    Cantitate
                </th>
                <th align ="center" width="10%">
                    <center>Pret unitar</center> 
                     <!-- <center>(fara TVA)</center> -->
                </th>
                <th align ="center" width="10%">
                    <center>Valoare</center> 
                    <!-- <center>fara TVA</center> -->
                </th>
              <!--   <th align ="center" width="10%">
                    <center>Valoare</center> 
                    <center>TVA</center>
                </th> -->
               
            </tr>
     </table>
     <hr>       
     <table class="table table-condesed" style="border-collapse: collapse;" width=100%>
          @if($vanzare->anulat)
          <tr>
            <td  align="center" colspan="5" >
              <h1>{{"ANULATA"}}</h1>
            </td>
          </tr>      
          @endif
                    @foreach($vanzare->detaliuvanzari as $det_fe)
                      
                      <tr> 
                       <td align="center" width="5%">
                       {{ $i++ }}.
                       </td>
                      <td class="text-sm" align="left" width="45%">
                       {{$det_fe->denumire  }} <br>
                       {{$det_fe->obs  }} 
                      </td>
                     <!--   <td align="center" width="5%">
                      <center>{{$det_fe->procent_tva}}</center>
                    
                      </td> -->
                      <td align="center" width="5%">
                      <center>buc</center>
                    
                      </td>
                      <td align="center" width="10%">
                      <center> {{ number_format($det_fe->cantitate,2)}}</center>
                                              
                      </td>
                     
                      <td align="right" width="10%">
                      {{ number_format(round($det_fe->pret_vanzare,2),2) }}
                         
                      </td>
                      <td align="right" width="10%">
                      {{ number_format(round($det_fe->valoare,2),2) }}
                         
                      </td>
                    <!--    <td align="right" width="10%">
                      {{ number_format(round($det_fe->valoare_tva,2),2) }}
                         
                      </td> -->
                          
                      </tr>
                     
                    @endforeach 
           
                    <tr style="border-top:4pt solid #6d6fe3; page-break-inside: avoid !important;">
                    	<td width="60%" colspan="3" class="text-sm" align="left" >
                      Numele delegatului:{{$vanzare->delegat}}<br>
                        B.I./C.I.:{{$vanzare->ci_delegat}} eliberat(a) {{$vanzare->ci_delegat_politia.' '.dateFormatAfisare($vanzare->ci_delegat_data)}}
                        Mijlocul de transport nr.:{{$vanzare->auto}} 
                  
                  </td>
                   
                      <td width="20%" colspan="2" align="left" >
                      <!--    <strong>
                          SUBTOTAL (LEI)
                          </strong> -->
                      </td>
                      <td width="10%" align="right" >
                        
                    <!--     <strong>
                       
                          {{ number_format(round($vanzare->valoare_fara_tva,2),2) }}
                       
                         </strong>
                        -->
                      </td>
                  
                    </tr>
               
                 <tr>
                    <td width="60%" class="text-sm" colspan="3" align="left" >
                    	 Semnatura furnizorului________________ Semnatura de primire________________
                        

                      </td>
                  <td width="20%" colspan="1" align="left" >
                        <h4>
                         <strong>
                          TOTAL (LEI)
                          </strong>
                        </h4>
                  </td>
            <td width="20%" colspan="2">
              <h4>
          <strong>
            <center>
            {{ number_format(round($vanzare->valoare,2),2)

            }}
            
               </center>
               </strong>
             </h4>
          </td>
          </tr> 
      </table>  
     
	  <hr>
	  <div class="text-xs">
      <strong>
   
       @if($vanzare->numerar||$vanzare->card)
          {{$vanzare->card?"Achitat cu ".$vanzare->tip_incasare." nr ".$vanzare->numar_incasare." ".$vanzare->trezorerie["denumire"]:"Achitat cu ".$vanzare->tip_incasare." nr ".$vanzare->numar_incasare }}
        @endif   
		 
    </strong>
          Intocmita de : {{Auth::user()->name .' '.datasioracurenta()}} <br>
		  Factura circula fara semnatura si stampila cf. art. V, alin (2) din Ordonanța nr.17\2015 si art. 319 alin (29) din Legea nr. 227\2015 privind Codul fiscal.
		  Prin acceptarea acestei facturi sunt de acord ca datele mele cu caracter personal sa fie folosite în conformitate cu Reg.UE 679/2016
	  </div>
	  <hr>
 @for ($i=0;$i<5-count($vanzare->detaliuvanzari) ;$i++)
    <br>
 @endfor

     <hr>

<!-- EXEMPLARUL 2 -->
  
     <table class="table table-condesed" width="100%">
      <tr>
      <td align ="center" width="50%">
           <div class="mr-10">
            
             <img   src="{{env('APP_URL').'images/logo/'.$company->slug.'/logo.png'}}" alt="logo" />
            <!-- <a class="navbar-brand" href="{{ url('/') }}">
                          <img   width=100% height=100% src="/logo_alprof.png" alt="logo" />
                          </a>  
             --></div>
       </td>
        <td width="50%">
          <div class="text-sm" >
          <h3><strong>FACTURĂ</strong></h3>
        
        Seria: <strong>{{$vanzare->seria}}</strong> Număr: <strong>{{$vanzare->numar}}</strong> <br>
        Data emiterii:   <strong>{{dateFormatAfisare($vanzare->data)}}</strong> Data scadenta: <strong>  {{dateFormatAfisare($vanzare->termen_plata)}}</strong> <br>
      <!--     Curs valutar {{dateformatAfisare($vanzare->data)}}: 1 EUR = <strong>{{number_format(cursBNR($vanzare->data,'EUR'),4)}} Lei</strong> -->
          </div>
      </td>
  </tr>
</table>
<hr>
       <table class="table table-condesed" width="100%"  >

            <tr>
              <td class="text-sm" align ="left" width="50%">
        <div >
      <!-- <h4> -->
      <div >
        
      <div>
      
      Furnizor: <strong> {{$company->denumire}}</strong><br>
      
       Reg.com.:<strong> {{$company->regcom}}</strong>  C.U.I.: <strong>{{$company->cui}}</strong>
      </div>
      <div>
      Adresa:<strong>{{$company->adresa}}</strong> 
      </div>
      <div>
      Telefon:<strong>{{$company->telefon}}</strong>
      
      Email:<strong>{{$company->email}}</strong>
      </div>
      <div>

      Banca:<strong>{{$company->banca}}</strong>
      Cont:<strong>{{$company->cont}}</strong>
      </div>
      <div>
      Banca:<strong>{{$company->banca_valuta}}</strong>
      Cont:<strong>{{$company->cont_valuta}}</strong>
      </div>
     <div>

      Capital social:<strong>{{$company->capital_social}}</strong>
      
      </div>
      </div>
  <!-- </h4> -->
</div>
</td>
<td class="text-sm" width="50%">
      Client: <strong>{{ $vanzare->partener }}</strong>  <br>
      C.U.I.: <strong>{{ $vanzare->cui }}</strong>   <br>
      Reg. com.: <strong>{{ $vanzare->regcom }}</strong> Telefon: <strong>{{ $vanzare->telefon }}</strong> <br>
      Adresa: <strong>{{ $vanzare->adresa }}</strong>   <br>
      Banca: <strong>{{ $vanzare->banca }}</strong> 
      Cont: <strong>{{ $vanzare->cont }}</strong> <br>
      
      </td>

 </tr>
 </table>      

  
<hr>
 <div >
           
    </div>  
    
       <table class="table table-condesed" width=100%  >
            
            <tr class="text-sm">
              <th align ="center" width="5%">
                    <center>Nr <br> crt</center>
                </th>
                <th align ="center" width="45%">
                    Denumire produselor sau a serviciilor
                </th>
               <!--   <th align ="center" width="5%">
                    %TVA
                </th> -->
                <th align ="center" width="5%">
                    Um
                </th>
                
                <th align ="center" width="10%">
                    Cantitate
                </th>
                <th align ="center" width="10%">
                    <center>Pret unitar</center> 
                     <!-- <center>(fara TVA)</center> -->
                </th>
                <th align ="center" width="10%">
                    <center>Valoare</center> 
                    <!-- <center>fara TVA</center> -->
                </th>
              <!--   <th align ="center" width="10%">
                    <center>Valoare</center> 
                    <center>TVA</center>
                </th> -->
               
            </tr>
     </table>
     <hr>       
     <table class="table table-condesed" style="border-collapse: collapse;" width=100%>
          @if($vanzare->anulat)
          <tr>
            <td  align="center" colspan="5" >
              <h1>{{"ANULATA"}}</h1>
            </td>
          </tr>      
          @endif
                    @foreach($vanzare->detaliuvanzari as $det_fe)
                      
                      <tr> 
                       <td align="center" width="5%">
                       {{ $i++ }}.
                       </td>
                      <td class="text-sm" align="left" width="45%">
                       {{$det_fe->denumire  }} <br>
                       {{$det_fe->obs  }} 
                      </td>
                     <!--   <td align="center" width="5%">
                      <center>{{$det_fe->procent_tva}}</center>
                    
                      </td> -->
                      <td align="center" width="5%">
                      <center>buc</center>
                    
                      </td>
                      <td align="center" width="10%">
                      <center> {{ number_format($det_fe->cantitate,2)}}</center>
                                              
                      </td>
                     
                      <td align="right" width="10%">
                      {{ number_format(round($det_fe->pret_vanzare,2),2) }}
                         
                      </td>
                      <td align="right" width="10%">
                      {{ number_format(round($det_fe->valoare,2),2) }}
                         
                      </td>
                    <!--    <td align="right" width="10%">
                      {{ number_format(round($det_fe->valoare_tva,2),2) }}
                         
                      </td> -->
                          
                      </tr>
                     
                    @endforeach 
           
                    <tr style="border-top:4pt solid #6d6fe3; page-break-inside: avoid !important;">
                      <td width="60%" colspan="3" class="text-sm" align="left" >
                      Numele delegatului:{{$vanzare->delegat}}<br>
                        B.I./C.I.:{{$vanzare->ci_delegat}} eliberat(a) {{$vanzare->ci_delegat_politia.' '.dateFormatAfisare($vanzare->ci_delegat_data)}}
                        Mijlocul de transport nr.:{{$vanzare->auto}} 
                  
                  </td>
                   
                      <td width="20%" colspan="2" align="left" >
                      <!--    <strong>
                          SUBTOTAL (LEI)
                          </strong> -->
                      </td>
                      <td width="10%" align="right" >
                        
                    <!--     <strong>
                       
                          {{ number_format(round($vanzare->valoare_fara_tva,2),2) }}
                       
                         </strong>
                        -->
                      </td>
                  
                    </tr>
               
                 <tr>
                    <td width="60%" class="text-sm" colspan="3" align="left" >
                       Semnatura furnizorului________________ Semnatura de primire________________
                        

                      </td>
                  <td width="20%" colspan="1" align="left" >
                        <h4>
                         <strong>
                          TOTAL (LEI)
                          </strong>
                        </h4>
                  </td>
            <td width="20%" colspan="2">
              <h4>
          <strong>
            <center>
            {{ number_format(round($vanzare->valoare,2),2)

            }}
            
               </center>
               </strong>
             </h4>
          </td>
          </tr> 
      </table>  
     
    <hr>
    <div class="text-xs">
      <strong>
   
       @if($vanzare->numerar||$vanzare->card)
          {{$vanzare->card?"Achitat cu ".$vanzare->tip_incasare." nr ".$vanzare->numar_incasare." ".$vanzare->trezorerie["denumire"]:"Achitat cu ".$vanzare->tip_incasare." nr ".$vanzare->numar_incasare }}
        @endif   
     
    </strong>
          Intocmita de : {{Auth::user()->name .' '.datasioracurenta()}} <br>
      Factura circula fara semnatura si stampila cf. art. V, alin (2) din Ordonanța nr.17\2015 si art. 319 alin (29) din Legea nr. 227\2015 privind Codul fiscal.
      Prin acceptarea acestei facturi sunt de acord ca datele mele cu caracter personal sa fie folosite în conformitate cu Reg.UE 679/2016
    </div>
    <hr>
 @for ($i=0;$i<5-count($vanzare->detaliuvanzari) ;$i++)
    <br>
 @endfor