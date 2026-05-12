
@extends('layouts.antetfarafirma')

@section ('content')

@foreach($documente->groupby("nr_nir") as $document)
<div class="container">
              
           
<hr> 
       <table class=" table table-condesed" width="100%"  >
            
            <tr>
              <td align ="left" width="60%">
        <div >
      <!-- <h4> -->
      <div >
        
      <div>
      <h3>
      <strong> {{$company->denumire}}</strong>
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
<td align ="center" width="40%">
     <div class="mt-3 mr-24">
      
       <img   src="{{env('APP_URL').'/images/logo/'.$company->slug.'/logo.png'}}" alt="logo" />
      <!-- <a class="navbar-brand" href="{{ url('/') }}">
                    <img   width=100% height=100% src="/logo_alprof.png" alt="logo" />
                    </a>  
       --></div>
 </td>
 </tr>
 </table>      
   
</div>
         <hr>  
   
    <table class="table">
    	<tr>
    		<td width=60%>
	    		<h3><strong>NOTA DE INTRARE - RECEPTIE</strong>
				<strong>{{'NR. '.$document[0]->nr_nir.' / '.dateFormatAfisare($document[0]->data_nir)}}</strong></h3>
				Gestiune:<strong>{{$document[0]->agentia}}</strong> <br> 
				<strong>{{$document[0]->antetdocumenteprimite->tip_document}}</strong> 
				Numărul: <strong>{{$document[0]->antetdocumenteprimite->nr_document}}</strong> Data :   <strong>{{dateFormatAfisare($document[0]->antetdocumenteprimite->data_document)}}</strong><br>
			
			    
			</td>
			<td width=40%>
			      Furnizor: <strong>{{ $document[0]->antetdocumenteprimite->partener->denumire }}</strong>   <br>
				  Cod fiscal: <strong>{{ $document[0]->antetdocumenteprimite->partener->cui }}</strong>   <br>
				  Reg. com.: <strong>{{ $document[0]->antetdocumenteprimite->partener->regcom }}</strong>   <br>
				  Adresa: <strong>{{ $document[0]->antetdocumenteprimite->partener->adresa }}</strong>   <br>
				
				
			</td>
	</tr>
</table>
<hr>
 <div >
 	  <table class="table table-condensed" style="border-collapse: collapse; border : 1px solid #a3a3a3;" width=100%  >
            
            <tr >
              <td  align ="center" style="border : 1px solid #a3a3a3;"  width="5%">
                    <center>Nr</center>
                    <center>crt.</center>
                </td>
                <td  align ="center"  style="border : 1px solid #a3a3a3;"   width="10%">
                   Cod
                </td>
            
                <td align ="center" style="border : 1px solid #a3a3a3;"  width="30%">
                   Denumire
                </td>
                
                <td style="border : 1px solid #a3a3a3;"  align ="center" width="10%">
                    Cantitate
                </td>
                <td style="border : 1px solid #a3a3a3;" align ="center"  width="10%">
                    <center>Pret intrare</center> 
				</td>
                
                <td style="border : 1px solid #a3a3a3;" align ="center"  width="10%">
                    <center>Valoare intrare</center> 
               
                </td>
             
            </tr>
            
    
                    @foreach($document as $det_doc)
                      
                      <tr> 
                       <td style="border : 1px solid #a3a3a3;" align="center" width="5%">
                       {{ $i++ }}.
                       </td>
                      <td style="border : 1px solid #a3a3a3;" align="left" width="10%">
                      {{$det_doc->cod }} 
                      </td>
                      <td style="border : 1px solid #a3a3a3;" align="left" width="30%">
                       {{$det_doc->denumire  }} 
                      </td>
                      <td style="border : 1px solid #a3a3a3;" align="center" width="10%">
                      <center> {{ number_format($det_doc->cantitate,2)}}</center>
                                         
                      </td>
                      <td style="border : 1px solid #a3a3a3;" align="right" width="10%">
                      {{ number_format(round($det_doc->pret_intrare,2),2) }}
                         
                      </td>
                      <td style="border : 1px solid #a3a3a3;" align="right" width="10%">
                      {{ number_format(round($det_doc->valoare_intrare,2),2) }}
                         
                      </td>
                      
                      
                    
                          
                      </tr>
                     
                    @endforeach 
             <tr> 
                       <td style="border : 1px solid #a3a3a3;" align="center" colspan="5" width="90%">
                       TOTAL
                         
                      </td>
                      <td style="border : 1px solid #a3a3a3;" align="right" width="10%">
                      {{ number_format(round($document->sum("valoare_intrare"),2),2) }}
                         
                      </td>
                       
                      
                          
                      </tr>     
                       
                  </table>
     <hr> 
     <br>
     <table class="table table-condesed" width=100% > 
                 <tr>
                  <td  width="15%">
                       
                  </td>
                  <td  width="30%" align="center" >
                   Comisia de receptie<br><br>
                    _________________________
                  </td>
                   <td  width="10%" >
		            
		          </td>
                   <td width="30%" align="center" >
                     Primit in gestiune<br>
                    
                    ___________________________________
                  </td>
		          <td  width="15%" >
		            
		          </td>
          </tr> 
      </table>   
 </div>	
	  
@endforeach   

	
	
@stop