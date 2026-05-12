@extends('layouts.antet_factura_a5')

@section ('content')

   <hr> 
       <table class="table table-condesed text-sm" width="100%"  >
            
            <tr>
          
<td align ="center" width="50%">
     <div class="mt-3 mr-24">
      
       <img   src="{{env('APP_URL').'/images/logo/'.$company->slug.'/logo.png'}}" alt="logo" />
      <!-- <a class="navbar-brand" href="{{ url('/') }}">
                    <img   width=100% height=100% src="/logo_alprof.png" alt="logo" />
                    </a>  
       --></div>
 </td>
 <td width=50%>
	    		<h3><strong>FACTURA</strong></h3>
				
				Seria: <strong>{{$vanzare->seria}}</strong> Număr: <strong>{{$vanzare->numar}}</strong> <br>
				Data emiterii:   <strong>{{dateFormatAfisare($vanzare->data)}}</strong> 
		    	Data scadentă: <strong>  {{dateFormatAfisare($vanzare->termen_plata)}}</strong> <br>
		    	
		 <!--    	Curs valutar {{dateformatAfisare($vanzare->data)}}: 1 EUR = <strong>{{number_format(cursBNR($vanzare->data,'EUR'),4)}} Lei</strong> -->
			    
			</td>
 </tr>
 </table>  
 <hr>
    <table class="table table-condesed text-sm" width="100%" >
    	<tr>
    		    <td align ="left" width="50%">
        <div >
      <!-- <h4> -->
      <div >
        
      <div>
      <h3>
      Furnizor: <strong> {{$company->denumire}}</strong>
      </h3>
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
			<td width=50%>
			      Client: <strong>{{ $vanzare->partener }}</strong>   <br>
				  C.U.I.: <strong>{{ $vanzare->cui }}</strong>   <br>
				  Reg. com.: <strong>{{ $vanzare->regcom }}</strong>   <br>
				  Adresa: <strong>{{ $vanzare->adresa }}</strong>   <br>
				  Banca: <strong>{{ $vanzare->banca }}</strong>   <br>
				  Cont: <strong>{{ $vanzare->cont }}</strong> <br>
				  Telefon: <strong>{{ $vanzare->telefon }}</strong> <br>
				 
				 
			</td>
	</tr>
</table>
<hr>
 <div >
       	   
	  </div>	
	  
	     <table class="table table-condesed text-sm" width=100%  >
            
            <tr>
              <th align ="center" width="5%">
                    <center>Nr <br> crt</center>
                </th>
                <th align ="center" width="45%">
                    Denumire produselor sau a serviciilor
                </th>
                <!--  <th align ="center" width="5%">
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
                     <!-- <center>(fără TVA)</center> -->
                </th>
                <th align ="center" width="10%">
                    <center>Valoare</center> 
                    <!-- <center>fără TVA</center> -->
                </th>
             <!--    <th align ="center" width="10%">
                    <center>Valoare</center> 
                    <center>TVA</center>
                </th> -->
               
            </tr>
     </table>
     <hr>       
     <table class="table table-condesed text-sm" width=100%>      
                    @foreach($vanzare->detaliuvanzari as $det_fe)
                      
                      <tr> 
                       <td align="center" width="5%">
                       {{ $i++ }}.
                       </td>
                      <td align="left" width="45%">
                       {{$det_fe->denumire  }} <br>
                       {{$det_fe->obs  }} 
                      </td>
                     <!--  <td align="center" width="5%">
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
                     <!--   <td align="right" width="10%">
                      {{ number_format(round($det_fe->valoare_tva,2),2) }}
                         
                      </td> -->
                          
                      </tr>
                     
                    @endforeach 
              </table>
              
              <div class="text-sm">
              	
                <strong>
      <!-- @if($vanzare->contract["tip_contract"]=="Servicii funerare") -->
               <!-- {{ number_format(round($vanzare->valoare/$vanzare->curs,2),2).' EUR (1 EUR= '.$vanzare->curs.' LEI)'}} -->
        @if($vanzare->numerar||$vanzare->card)
           {{$vanzare->card?"Achitat cu ".$vanzare->tip_incasare." nr ".$vanzare->numar_incasare." ".$vanzare->trezorerie["denumire"]:"Achitat cu ".$vanzare->tip_incasare." nr ".$vanzare->numar_incasare }}
        @endif       
      
      <!-- @endif -->
     
    </strong>
              </div>
           
     <hr> 
     <table class="table table-condesed text-sm" width=100%> 
                    <tr>
                     <td width="50%" align="left" >
                        Numele delegatului:{{$vanzare->delegat}}<br>
                        B.I./C.I.:{{$vanzare->ci_delegat}} eliberat(a) {{$vanzare->ci_delegat_politia.' '.dateFormatAfisare($vanzare->ci_delegat_data)}}<br>
                        Mijlocul de transport nr.:{{$vanzare->auto}} 

                      </td>
                      <td width="30%" align="right" colspan="4">
                       <!--   <strong>
                          SUBTOTAL (LEI)
                          </strong> -->
                      </td>
                      <td width="10%" align="right" >
                       <!--  <h4>
                        <strong>
                       
                          {{ number_format(round($vanzare->valoare_fara_tva,2),2) }}
                       
                         </strong>
                       </h4> -->
                      </td>
                      <td width="10%" align="right" >
                       <!--  <h4>
                        <strong>
                        
                          {{ number_format(round($vanzare->valoare_tva,2),2)}}
                           </strong>
                         </h4> -->
                      </td>
                    </tr>
               </table>
     <hr> 
     <table class="table table-condesed text-sm" width=100% > 
                 <tr>
                  <td width="20%" align="center" >
                    Semnătura furnizorului<br><br>
                    _________________________
                  </td>
                   <td width="40%" align="center" >
                    Semnătura de primire<br><br>
                    ___________________________________
                  </td>
                  <td width="25%" align="right" colspan="3" >
                        <h3>
                         <strong>
                          TOTAL (LEI)
                          </strong>
                        </h3>
                  </td>
            <td width="15%" colspan="2">
              <h3>
          <strong>
            <center>
            {{ number_format(round($vanzare->valoare,2),2)

            }}
            
               </center>
               </strong>
             </h3>
          </td>
          </tr> 
      </table>   
	  <hr>
	  <div class="text-sm" >
	  Întocmită de : {{Auth::user()->name}} <br>
	  Factura circula fără semnătura și ștampila cf. art. V, alin (2) din Ordonanța nr.17\2015 și art. 319 alin (29) din Legea nr. 227\2015 privind Codul fiscal.
	  <br>
	  Prin acceptarea acestei facturi sunt de acord ca datele mele cu caracter personal să fie folosite în conformitate cu Reg.UE 679/2016
	</div>
	

	
	
@stop