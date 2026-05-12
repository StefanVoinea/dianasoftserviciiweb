@extends('layouts.antet')

@section ('content')

   
    <table class="table">
    	<tr>
    		<td width=60%>
	    		<h3><strong>NOTA DE INTRARE - RECEPTIE</strong>
				<strong>{{'NR. '.$document->nr_nir.' / '.dateFormatAfisare($document->data_nir)}}</strong></h3>
				Gestiune:<strong>{{$document->gestiune->denumire}}</strong> <br> 
				<strong>{{$document->tip_document}}</strong> 
				@if($document->seria)
				Seria: <strong>{{$document->seria}}</strong> 
				@endif
				Numărul: <strong>{{$document->nr_document}}</strong> Data :   <strong>{{dateFormatAfisare($document->data_document)}}</strong><br>
				@if ($document->import)
		    	Curs valutar {{dateformatAfisare($document->data)}}: 1 {{$document->tip_valuta}} = <strong>{{number_format($document->curs,4)}} Lei</strong>
		    	@endif
			    
			</td>
			<td width=40%>
			      Furnizor: <strong>{{ $document->partener->denumire }}</strong>   <br>
				  Cod fiscal: <strong>{{ $document->partener->cui }}</strong>   <br>
				  Reg. com.: <strong>{{ $document->partener->regcom }}</strong>   <br>
				  Adresa: <strong>{{ $document->partener->adresa }}</strong>   <br>
				  Țara: <strong>{{ $document->partener->tara }}</strong>   <br>
				  Import:<strong>{{ $document->import?"Da ":"Nu " }}</strong>Extracomunitar:<strong>{{ $document->extracomunitar?"Da ":"Nu " }}</strong>
			</td>
	</tr>
  @if($document->import)
  <tr >
    <td colspan="2">
      Taxe vamale: <strong>{{ number_format($document->taxe_vamale,2) }}</strong> 
      Navlu: <strong>{{ number_format($document->navlu,2) }}</strong>
      Transport rutier: <strong>{{ number_format($document->transport_rutier,2) }}</strong>
      Asigurare: <strong>{{ number_format($document->asigurare,2) }}</strong>
      Alte cheltuieli: <strong>{{ number_format($document->alte_cheltuieli,2) }}</strong>
      TVA in vama: <strong>{{ number_format($document->tva_in_vama,2) }}</strong>
    </td>
  </tr>
  @endif
</table>
<hr>
 <div >
 	  <table class="table table-condensed" style="border-collapse: collapse; border : 1px solid #a3a3a3;" width=100%  >
            
            <tr >
              <td  align ="center" style="border : 1px solid #a3a3a3;" rowspan="2" width="2%">
                    <center>Nr</center>
                    <center>crt.</center>
                </td>
                <td align ="center" style="border : 1px solid #a3a3a3;" rowspan="2" width="25%">
                   Denumire
                </td>
                <td  align ="center"  style="border : 1px solid #a3a3a3;" rowspan="2"  width="2%">
                   Um
                </td>
                
                <td style="border : 1px solid #a3a3a3;" colspan="2" align ="center" width="8%">
                    Cantitate
                </td>
                <td style="border : 1px solid #a3a3a3;" align ="center" colspan="2" width="5%">
                    <center>Pret intrare</center> 
				</td>
                
                <td style="border : 1px solid #a3a3a3;" align ="center" colspan="5" width="5%">
                    <center>Valoare intrare</center> 
               
                </td>
               <td style="border : 1px solid #a3a3a3;" rowspan="2" align ="center" width="5%">
                    <center>Valoare</center>
                    <center>adaos</center>
               </td>
               <td style="border : 1px solid #a3a3a3;" rowspan="2" align ="center" width="5%">
                    <center>Pret</center>
                    <center>vanzare</center>
               </td>
               <td style="border : 1px solid #a3a3a3;" align ="center" colspan="3" width="5%">
                    <center>Valoare vanzare</center><br>
               </td>
            </tr>
            <tr>
             
                <td style="border : 1px solid #a3a3a3;"  align ="center" width="4%">
                    document
                </td>
                 <td style="border : 1px solid #a3a3a3;" align ="center" width="4%">
                    receptionata
                </td>
                <td style="border : 1px solid #a3a3a3;" align ="center" width="5%">
                    <center>document</center> 

                </td>
                <td style="border : 1px solid #a3a3a3;" align ="center" width="5%">
                    <center>costuri incluse</center>
                </td>
                <td style="border : 1px solid #a3a3a3;" align ="center" width="5%">
                    <center>document</center>
                </td>
                  <td style="border : 1px solid #a3a3a3;" align ="center" width="5%">
                   
                    <center>TVA</center>
                </td>
                <td style="border : 1px solid #a3a3a3;" align ="center" width="5%">
                   <center>diferenta (plus)</center>
                </td>
                <td style="border : 1px solid #a3a3a3;" align ="center" width="5%">
                    <center>diferenta (minus)</center>
                </td>
                 <td style="border : 1px solid #a3a3a3;" align ="center" width="5%">
                   
                    <center>costuri incluse</center>
                </td>
              
              
               <td style="border : 1px solid #a3a3a3;" align ="center" width="5%">
                   <center>fara TVA</center><br>
               </td>
               <td style="border : 1px solid #a3a3a3;" align ="center" width="5%">
                   <center>TVA</center><br>
               </td>
                <td style="border : 1px solid #a3a3a3;" align ="center" width="5%">
                    <center>cu TVA</center><br>
               </td>
            </tr>
    
                    @foreach($document->detaliunir as $det_doc)
                      
                      <tr> 
                       <td style="border : 1px solid #a3a3a3;" align="center" width="2%">
                       {{ $i++ }}.
                       </td>
                      <td style="border : 1px solid #a3a3a3;" align="left" width="25%">
                       {{$det_doc->denumire  }} <br>
                       {{'cod: '.$det_doc->cod .' cont :'.$det_doc->contd }} 
                      </td>
                      <td style="border : 1px solid #a3a3a3;" align="center" width="2%">
                      <center>{{$det_doc->um }} </center>
                      </td>
                      <td style="border : 1px solid #a3a3a3;" align="center" width="4%">
                      <center> {{ number_format($det_doc->cantitate,2)}}</center>
                                         
                      </td>
                      <td style="border : 1px solid #a3a3a3;" align="center" width="4%">
                      <center> {{ number_format($det_doc->cantitate_receptionata,2)}}</center>
                      
                      </td>
                      <td style="border : 1px solid #a3a3a3;" align="right" width="5%">
                      {{ number_format(round($det_doc->pret_intrare,2),2) }}
                         
                      </td>
                      <td style="border : 1px solid #a3a3a3;" align="right" width="5%">
                      {{ number_format(round((round($det_doc->valoare_intrare,2)+$det_doc->valoare_taxa+$det_doc->valoare_navlu+$det_doc->valoare_transport+$det_doc->valoare_asigurare+$det_doc->valoare_alte_cheltuieli)/$det_doc->cantitate,2),2) }}
                         
                      </td>
                      <td style="border : 1px solid #a3a3a3;" align="right" width="5%">
                      {{ number_format(round($det_doc->valoare_intrare,2),2) }}
                         
                      </td>
                       <td style="border : 1px solid #a3a3a3;" align="right" width="5%">
                      {{ number_format(round($det_doc->valoare_tva_intrare,2),2) }}
                         
                      </td>
                      <td style="border : 1px solid #a3a3a3;" align="right" width="5%">
                      	@if($det_doc->cantitate-$det_doc->cantitate_receptionata<0)
                      {{ number_format(round($det_doc->pret_intrare*($det_doc->cantitate_receptionata-$det_doc->cantitate),2),2) }}
                      @endif
                      </td>
                      <td style="border : 1px solid #a3a3a3;" align="right" width="5%">
                      	@if($det_doc->cantitate-$det_doc->cantitate_receptionata>0)
                      {{ number_format(round($det_doc->pret_intrare*($det_doc->cantitate-$det_doc->cantitate_receptionata),2),2) }}
                        @endif 
                      </td>
                      
                      <td style="border : 1px solid #a3a3a3;" align="right" width="5%">
                      {{ number_format(round($det_doc->valoare_intrare+$det_doc->valoare_taxa+$det_doc->valoare_navlu+$det_doc->valoare_transport+$det_doc->valoare_asigurare+$det_doc->valoare_alte_cheltuieli,2),2) }}
                         
                      </td>
                      
                      <td style="border : 1px solid #a3a3a3;" align ="right" width="5%">
		                     {{ number_format(round($det_doc->pret_vanzare*$det_doc->cantitate/(1+$det_doc->procent_tva_vanzare/100),2)-($det_doc->valoare_intrare+$det_doc->valoare_taxa+$det_doc->valoare_navlu+$det_doc->valoare_transport+$det_doc->valoare_asigurare+$det_doc->valoare_alte_cheltuieli),2) }}
		               </td>
		               <td style="border : 1px solid #a3a3a3;" align ="right" width="5%">
		                    {{ number_format(round($det_doc->pret_vanzare,2),2) }}
		               </td>
		               <td style="border : 1px solid #a3a3a3;" align ="right" width="5%">
		                    {{ number_format(round($det_doc->pret_vanzare*$det_doc->cantitate/(1+$det_doc->procent_tva_vanzare/100),2),2) }}
		               </td>
		               <td style="border : 1px solid #a3a3a3;" align ="right" width="5%">
		                    {{ number_format(round($det_doc->pret_vanzare*$det_doc->cantitate,2)-round($det_doc->pret_vanzare*$det_doc->cantitate/(1+$det_doc->procent_tva_vanzare/100),2),2) }}
		               </td>
		                <td style="border : 1px solid #a3a3a3;" align ="right" width="5%">
		                    {{ number_format(round($det_doc->pret_vanzare*$det_doc->cantitate,2),2) }}
		               </td>
                          
                      </tr>
                     
                    @endforeach 
             <tr> 
                       <td style="border : 1px solid #a3a3a3;" align="center" colspan="7" width="2%">
                       TOTAL
                         
                      </td>
                      <td style="border : 1px solid #a3a3a3;" align="right" width="5%">
                      {{ number_format(round($document->detaliunir->sum("valoare_intrare"),2),2) }}
                         
                      </td>
                       <td style="border : 1px solid #a3a3a3;" align="right" width="5%">
                      {{ number_format(round($document->detaliunir->sum("valoare_tva_intrare"),2),2) }}
                         
                      </td>
                      <td style="border : 1px solid #a3a3a3;" align="right" width="5%">
                      		 {{ number_format(round($document->detaliunir->sum(function($value){
                      					if($value['cantitate_receptionata']-$value['cantitate']>0){

                      				return ($value['cantitate_receptionata']-$value['cantitate'])*$value['pret_intrare'];
                      				}
                      }
                      	),2),2) }}
                      </td>
                      <td style="border : 1px solid #a3a3a3;" align="right" width="5%">
                       	{{ number_format(round($document->detaliunir->sum(function($value){
                      					if($value['cantitate_receptionata']-$value['cantitate']<0){

                      				return ($value['cantitate']-$value['cantitate_receptionata'])*$value['pret_intrare'];
                      				}
                      }
                      	),2),2) }}
                      </td>
                      
                      <td style="border : 1px solid #a3a3a3;" align="right" width="5%">
                      {{ number_format(round($document->detaliunir->sum(function($value){
                      	return $value['valoare_intrare']+($value['valoare_taxa']+$value['valoare_navlu']+$value['valoare_transport']+$value['valoare_asigurare']+$value['valoare_alte_cheltuieli']);
                      }
                      	),2),2) }}
                         
                      </td>
                      
                      <td style="border : 1px solid #a3a3a3;" align ="right" width="5%">
		                     {{ number_format(round($document->detaliunir->sum(function($value){
		                     		return $value['pret_vanzare']*$value['cantitate']/(1+$value['procent_tva_vanzare']/100)	;
		                     }),2) -round($document->detaliunir->sum(function($value){
                        return $value['valoare_intrare']+($value['valoare_taxa']+$value['valoare_navlu']+$value['valoare_transport']+$value['valoare_asigurare']+$value['valoare_alte_cheltuieli']);
                      }
                        ),2),2)}}
		               </td>
		               <td style="border : 1px solid #a3a3a3;" align ="right" width="5%">
		                  
		               </td>
		               <td style="border : 1px solid #a3a3a3;" align ="right" width="5%">
		                    {{ number_format(round($document->detaliunir->sum(function($value){
		                    		return $value['pret_vanzare']*$value['cantitate']/(1+$value['procent_tva_vanzare']/100);
		                    }),2),2) }}
		               </td>
		               <td style="border : 1px solid #a3a3a3;" align ="right" width="5%">
		                    {{ number_format(round($document->detaliunir->sum(function($value) {
									       return $value['pret_vanzare']*$value['cantitate']-$value['pret_vanzare']*$value['cantitate']/(1+$value['procent_tva_vanzare']/100);
									}),2),2) }}
		               </td>
		                <td style="border : 1px solid #a3a3a3;" align ="right" width="5%">
		                    {{ number_format(round($document->detaliunir->sum(function($value) {
									    return $value['cantitate']*$value['pret_vanzare'];
									}),2),2) }}
		               </td>
                          
                      </tr>
                      <tr> 
                       <td style="border : 1px solid #a3a3a3;" align="right" colspan="7" width="2%">
                       TOTAL DOCUMENT
                         
                      </td>
                      <td style="border : 1px solid #a3a3a3;" colspan="2" align="center" width="10%">
                      {{ number_format(round($document->detaliunir->sum("valoare_intrare"),2)+round($document->detaliunir->sum("valoare_tva_intrare"),2),2) }}
                         
                      </td>
                     
                      <td style="border : 1px solid #a3a3a3;" colspan="8" width="5%">
                     
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
                   COMISIA DE RECEPTIE<br><br><br>
                    _________________________
                  </td>
                   <td  width="10%" >
		            
		          </td>
                   <td width="30%" align="center" >
                    GESTIONAR<br>
                    {{$document->gestiune->gestionar}}<br><br>
                    ___________________________________
                  </td>
		            <td  width="15%" >
		            
		          </td>
          </tr> 
      </table>   
 </div>	
	  
	   

	
	
@stop