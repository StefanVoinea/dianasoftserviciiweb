@extends('layouts.antet')

@section ('content')
  
   <center>  Situație încasări  </center>
   <center> {{$selectie}} </center>
   <hr>
      <table class="table table-condesed text-sm" width=100%  >
            
            <tr >
              <th align ="center" width="5%">
                    <center>Nr <br> crt</center>
                </th>
                <th align ="center" width="15%">
                    Document încasare
                
                </th>
                <th align ="center" width="20%">
                    Contract 
                </th>
                <th align ="center" width="15%">
                    Document vânzare
                </th>
               <th align ="center" width="15%">
                    Client
                </th>
                <th align ="center" width="10%">
                    Suma încasată (Lei)
                </th>
               
                <th align ="center" width="10%">
                    Suma în valută
                </th>
                
                
            </tr>
                
                
            </tr>
     </table>
      <hr>
                     @foreach($antetvanzare->groupby("tip_incasare") as $vanzaregestiunetip)
                     <div class="text-sm"><strong>TIP INCASARE: {{$vanzaregestiunetip[0]->tip_incasare}}</strong></div>
                    <hr>
                    @foreach($vanzaregestiunetip->groupby("user_id") as $vanzaregestiune)
                    <div class="text-sm"><strong>UTILIZATOR: {{$vanzaregestiune[0]->user["name"]}} TIP INCASARE: {{$vanzaregestiunetip[0]->tip_incasare}}</strong></div>
                    <hr>
          <table class="table table-condesed  text-sm" style="border-collapse: collapse; " width=100%>      
                    @foreach($vanzaregestiune as $vanzare)
                      
                      <tr style="border-bottom:1pt solid black; page-break-inside: avoid !important;"> 
                       <td align="center" width="5%">
                       {{ $i++ }}.
                       </td>
                      <td align="left" width="15%">
                       @if($vanzare->tip_document=="Ordin de plata")
                       <strong> {{$vanzare->tip_document  }}</strong>
                       @else
                       {{$vanzare->tip_document  }}
                       @endif
                      
                       @if($vanzare->incasat_prin=="Card")
                       <strong>{{" CARD"}}</strong>
                       @endif
                      
                      {{$vanzare->seria }}
                    
                      {{$vanzare->nr_document."/".dateFormatAfisare($vanzare->data_document)  }}
                         
                      </td>
                      <td align="CENTER" width="20%">

                        @if($vanzare->contract)
                         {{$vanzare->contract->tip_contract  }}    
                         {{$vanzare->contract->nr_contract."/".dateFormatAfisare($vanzare->contract->data_contract)  }}
                        @else
                        
                        @endif
                      </td>
                       <td align="left" width="15%">
                        @if($vanzare->antetvanzare)
                         {{ $vanzare->antetvanzare->tip_document }}
                         {{ $vanzare->antetvanzare->numar."/".dateFormatAfisare($vanzare->antetvanzare->data)  }}   
                         @endif
                      </td>
                      <td align="left" width="15%">
                      {{ $vanzare->partener }}
                         
                      </td>
                      <td align="right" width="10%">
                     {{ number_format($vanzare->suma,2)}}
                         
                      </td>
                    
                      <td align="right" width="10%">
                     {{ number_format($vanzare->suma_valuta,2)}}
                         
                      </td>
                      </tr>
                     @endforeach 
                    </table>
                     <hr> 
               <table class="table table-condesed text-sm" width=100%> 
                    <tr>
                    
                      <td width="80%" align="right" colspan="5">
                         <strong>
                         TOTAL ÎNCASĂRI UTILIZATOR {{$vanzare->user["name"]}}  TIP ÎNCASARE {{$vanzaregestiune[0]->tip_incasare}}
                          </strong>
                      </td>
                     
                      <td width="10%" align="right" >
                       
                        <strong>
                        
                          {{ number_format(round($vanzaregestiune->sum('suma'),2),2) }}
                           </strong>
                        
                      </td>
                      
                      <td width="10%" align="right" >
                       
                        <strong>
                        
                          {{ number_format(round($vanzaregestiune->sum('suma_valuta'),2),2) }}
                           </strong>
                        
                      </td>
                    </tr>
               </table>
                <hr> 
                    @endforeach 
           
                     <hr> 
               <table class="table table-condesed text-sm" width=100%> 
                    <tr>
                    
                      <td width="80%" align="right" colspan="4">
                         <strong>
                          TOTAL TIP ÎNCASARE {{$vanzaregestiune[0]->tip_incasare}} 
                          
                          </strong>
                      </td>
                     
                      <td width="10%" align="right" >
                       
                        <strong>
                        
                          {{ number_format(round($vanzaregestiunetip->sum('suma'),2),2) }}
                           </strong>
                        
                      </td>
                      
                      <td width="10%" align="right" >
                       
                        <strong>
                        
                          {{ number_format(round($vanzaregestiunetip->sum('suma_valuta'),2),2) }}
                           </strong>
                        
                      </td>
                    </tr>
               </table>
                <hr> 
                    @endforeach 
             
   <hr>
     <table class="table table-condesed text-sm" width=100%> 
                    <tr>
                    
                      <td width="80%" align="right" colspan="5">
                         <strong>
                          TOTAL 
                          </strong>
                      </td>
                     
                      <td width="10%" align="right" >
                       
                        <strong>
                        
                          {{ number_format(round($antetvanzare->sum('suma'),2),2) }}
                           </strong>
                        
                      </td>
                      
                      <td width="10%" align="right" >
                       
                        <strong>
                        
                          {{ number_format(round($antetvanzare->sum('suma_valuta'),2),2) }}
                           </strong>
                        
                      </td>
                    </tr>
               </table>
     <hr> 
     

  
  
@stop