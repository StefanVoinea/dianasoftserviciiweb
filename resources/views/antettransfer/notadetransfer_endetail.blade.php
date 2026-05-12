@extends('layouts.antet')

@section ('content')

   
    <table class="table">
    	<tr>
        
    		<td width=30% align="center">
	    		<h3><strong>NOTA DE TRANSFER</strong>
				<strong>{{'NR. '.$document->nr_document.' / '.dateFormatAfisare($document->data_document)}}</strong></h3>
				<strong>{{'DE LA GESTIUNEA '.$document->gestiunepredatoare["denumire"].' CĂTRE GESTIUNEA '.$document->gestiuneprimitoare["denumire"]}}</strong> <br> 
				 
			</td>
      
		
	</tr>
</table>
<hr>
 <div >
 	  <table class="table table-condensed" style="border-collapse: collapse; border : 1px solid #a3a3a3;" width=100%  >
            
            <tr >
              <td  align ="center" style="border : 1px solid #a3a3a3;" width="5%">
                    <center>Nr</center>
                    <center>crt.</center>
                </td>
                <td align ="center" style="border : 1px solid #a3a3a3;"  width="20%">
                   Denumire
                </td>
                <td  align ="center"  style="border : 1px solid #a3a3a3;"  width="5%">
                   Um
                </td>
                
                <td style="border : 1px solid #a3a3a3;" align ="center" width="10%">
                    Cantitate
                </td>
                <td style="border : 1px solid #a3a3a3;" align ="center" width="10%">
                    <center>Pret intrare</center> 
				        </td>
                
                <td style="border : 1px solid #a3a3a3;" align ="center"  width="10%">
                    <center>Valoare intrare</center> 
               
                </td>
                <td style="border : 1px solid #a3a3a3;" align ="center" width="10%">
                    <center>Pret vanzare</center> 
                </td>
                
                <td style="border : 1px solid #a3a3a3;" align ="center"  width="10%">
                    <center>Valoare vanzare fara TVA</center> 
               
                </td>
                <td style="border : 1px solid #a3a3a3;" align ="center"  width="10%">
                    <center>Valoare vanzare TVA</center> 
               
                </td>
                <td style="border : 1px solid #a3a3a3;" align ="center"  width="10%">
                    <center>Valoare vanzare cu TVA</center> 
               
                </td>
            </tr>
           
    
                    @foreach($document->detaliutransfer as $det_doc)
                      
                      <tr> 
                       <td style="border : 1px solid #a3a3a3;" align="center" width="5%">
                       {{ $i++ }}.
                       </td>
                      <td style="border : 1px solid #a3a3a3;" align="left" width="20%">
                       {{$det_doc->denumire  }} <br>
                       {{'cod: '.$det_doc->cod .' contare :'.$det_doc->contd.' => '.$det_doc->contc }} 
                      </td>
                      <td style="border : 1px solid #a3a3a3;" align="center" width="5%">
                      <center>{{$det_doc->um }} </center>
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
                     <td style="border : 1px solid #a3a3a3;" align="right" width="10%">
                      {{ number_format(round($det_doc->pret_vanzare,2),2) }}
                         
                      </td>
                     
                      <td style="border : 1px solid #a3a3a3;" align="right" width="10%">
                      {{ number_format(round($det_doc->valoare_vanzare/(1+$det_doc->procent_tva/100),2),2) }}
                         
                      </td>
                    <td style="border : 1px solid #a3a3a3;" align="right" width="10%">
                      {{ number_format(round($det_doc->valoare_vanzare-$det_doc->valoare_vanzare/(1+$det_doc->procent_tva/100),2),2) }}
                         
                      </td>
                      <td style="border : 1px solid #a3a3a3;" align="right" width="10%">
                      {{ number_format(round($det_doc->valoare_vanzare,2),2) }}
                         
                      </td>
                        
                    
                          
                      </tr>
                     
                    @endforeach 
             <tr> 
                       <td style="border : 1px solid #a3a3a3;" align="center" colspan="5" width="55%">
                       TOTAL
                         
                      </td>
                      <td style="border : 1px solid #a3a3a3;" align="right" width="10%">
                      {{ number_format(round($document->detaliutransfer->sum("valoare_intrare"),2),2) }}
                         
                      </td>
                       <td style="border : 1px solid #a3a3a3;" align="right" width="10%">
                      
                         
                      </td>
                      <td style="border : 1px solid #a3a3a3;" align="right" width="10%">
                      {{ number_format(round($document->valoare_vanzare_fara_tva,2),2) }}
                         
                      </td>
                      <td style="border : 1px solid #a3a3a3;" align="right" width="10%">
                      {{ number_format(round($document->valoare_vanzare_tva,2),2) }}
                         
                      </td>
                      <td style="border : 1px solid #a3a3a3;" align="right" width="10%">
                      {{ number_format(round($document->valoare_vanzare,2),2) }}
                         
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
                   COMISIA DE RECEPTIE<br><br>
                    _________________________
                  </td>
                   <td  width="10%" >
		            
		          </td>
                   <td width="30%" align="center" >
                     GESTIONAR<br>
                    {{$document->gestiunepredatoare["gestionar"]}}<br><br>
                    ___________________________________
                  </td>
		            <td  width="15%" >
		            
		          </td>
          </tr> 
      </table>   
 </div>	
	  
	   

	
	
@stop