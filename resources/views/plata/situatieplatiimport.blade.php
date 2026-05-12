@extends('layouts.antet')

@section ('content')
  
   <center> <h3> Situație plăți </h3> </center>
   <center> <h3>{{$selectie}} </h3></center>
   <hr>
      
                    @foreach($plati->groupby("platit_prin") as $platagestiune)
                     <hr>
                   
                    <table class="table table-condesed" width=100%  >
            
            <tr >
              <th align ="center" width="5%">
                    <center>Nr <br> crt</center>
                </th>
                <th align ="center" width="20%">
                    Document plată
                
                </th>
               <th align ="center" width="15%">
                    Document primit
                </th>
               <th align ="center" width="10%">
                    Furnizor
                </th>
                <th align ="center" width="10%">
                    Suma plătită
                    {{$platagestiune[0]->tip_valuta}}
                </th>
                <th align ="center" width="10%">
                    Curs plată
                </th>
                <th align ="center" width="10%">
                    Curs factură
                </th>
                <th align ="center" width="10%">
                    Diferență de curs
                </th>
                
                <th align ="center" width="10%">
                    Suma plătită
                    RON
                </th>
                
                
            </tr>
     </table>
      PLĂTIT PRIN {{$platagestiune[0]->platit_prin}}
                    <hr>
     				<table class="table table-condesed" style="border-collapse: collapse; " width=100%>      
                    @foreach($platagestiune as $plata)
                      
                      <tr style="border-bottom:1pt solid black; page-break-inside: avoid !important;"> 
                       <td align="center" width="5%">
                       {{ $i++ }}.
                       </td>
                      <td align="left" width="20%">
                       {{$plata->tip_document  }}
                      {{$plata->nr_document."/".dateFormatAfisare($plata->data_document)  }}
                         
                      </td>
                       <td align="left" width="15%">
                        @if($plata->antetdocumenteprimite)
                         {{ $plata->antetdocumenteprimite->tip_document }}
                         {{ $plata->antetdocumenteprimite->nr_document."/".dateFormatAfisare($plata->antetdocumenteprimite->data_document)  }}   
                         @endif
                      </td>
                      <td align="left" width="10%">
                      {{ $plata->partener }}
                         
                      </td>
                      <td align="right" width="10%">
                     {{ number_format($plata->suma_in_valuta,2)}}
                         
                      </td>
                      <td align="right" width="10%">
                     {{ number_format($plata->curs,4)}}
                         
                      </td>
                      <td align="right" width="10%">
                     {{ number_format($plata->curs_factura,4)}}
                         
                      </td>
                      <td align="right" width="10%">
                     {{ number_format($plata->diferenta_de_curs,2)}}
                         
                      </td>
                      <td align="right" width="10%">
                     {{ number_format($plata->suma,2)}}
                         
                      </td>
                      </tr>
                     @endforeach 
                    </table>
                     <hr> 
     			     <table class="table table-condesed" width=100%> 
                    <tr>
                    
                      <td width="50%" align="right" colspan="5">
                         <strong>
                          TOTAL PLĂȚI PRIN {{$plata->platit_prin}}
                          </strong>
                      </td>
                     
                      <td width="10%" align="right" >
                        <h4>
                        <strong>
                        
                          {{ number_format(round($platagestiune->sum('suma_in_valuta'),2),2) }}
                           </strong>
                         </h4>
                      </td>
                      <td width="10%" align="right" >
                       
                      </td>
                      <td width="10%" align="right" >
                        
                      </td>
                      <td width="10%" align="right" >
                        <h4>
                        <strong>
                        
                          {{ number_format(round($platagestiune->sum('diferenta_de_curs'),2),2) }}
                           </strong>
                         </h4>
                      </td>
                      <td width="10%" align="right" >
                        <h4>
                        <strong>
                        
                          {{ number_format(round($platagestiune->sum('suma'),2),2) }}
                           </strong>
                         </h4>
                      </td>
                    </tr>
               </table>
                <hr> 
                    @endforeach 
             
   <hr>
     <table class="table table-condesed" width=100%> 
                    <tr>
                    
                      <td width="50%" align="right" colspan="5">
                         <strong>
                          TOTAL
                          </strong>
                      </td>
                     
                      <td width="10%" align="right" >
                        <h4>
                        <strong>
                        
                          <!-- {{ number_format(round($plati->sum('suma'),2),2) }} -->
                           </strong>
                         </h4>
                      </td>
                      <td width="10%" align="right" >
                       
                      </td>
                      <td width="10%" align="right" >
                        
                      </td>
                      <td width="10%" align="right" >
                        <h4>
                        <strong>
                        
                          {{ number_format(round($plati->sum('diferenta_de_curs'),2),2) }}
                           </strong>
                         </h4>
                      </td>
                      <td width="10%" align="right" >
                        <h4>
                        <strong>
                        
                          {{ number_format(round($plati->sum('suma'),2),2) }}
                           </strong>
                         </h4>
                      </td>
                    </tr>
               </table>
     <hr> 
     

	
	
@stop