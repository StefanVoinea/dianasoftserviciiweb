@extends('layouts.antet')

@section ('content')
  
   <center> <h3> Situație încasări </h3> </center>
   <center> <h3>{{$selectie}} </h3></center>
   <hr>
      <table class="table table-condesed" width=100%  >
            
            <tr >
              <th align ="center" width="5%">
                    <center>Nr <br> crt</center>
                </th>
                <th align ="center" width="15%">
                    Document încasare
                
                </th>
              
                <th align ="center" width="25%">
                    Document vânzare
                </th>
               <th align ="center" width="15%">
                    Client
                </th>
                <th align ="center" width="10%">
                    Suma încasată (Lei)
                </th>
              
                
                
            </tr>
     </table>
      <hr>
                    @foreach($antetvanzare->groupby("incasat_prin") as $vanzaregestiune)
                    INCASAT PRIN {{$vanzaregestiune[0]->incasat_prin}}
                    <hr>
     				<table class="table table-condesed"  style="border-collapse: collapse; " width=100%>      
                    @foreach($vanzaregestiune as $vanzare)
                      
                      <tr style="border-bottom:1pt solid black; page-break-inside: avoid !important;"> 
                       <td align="center" width="5%">
                       {{ $i++ }}.
                       </td>
                      <td align="left" width="25%">
                       {{$vanzare->tip_document  }}
                      
                      {{$vanzare->seria }}
                    
                      {{$vanzare->nr_document."/".dateFormatAfisare($vanzare->data_document)  }}
                         
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
                    
                      </tr>
                     @endforeach 
                    </table>
                     <hr> 
     			     <table class="table table-condesed" width=100%> 
                    <tr>
                    
                      <td width="90%" align="right" colspan="4">
                         <strong>
                          TOTAL ÎNCASĂRI PRIN {{$vanzare->incasat_prin}}
                          </strong>
                      </td>
                     
                      <td width="10%" align="right" >
                        <h4>
                        <strong>
                        
                          {{ number_format(round($vanzaregestiune->sum('suma'),2),2) }}
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
                    
                      <td width="90%" align="right" colspan="5">
                         <strong>
                          TOTAL 
                          </strong>
                      </td>
                     
                      <td width="10%" align="right" >
                        <h4>
                        <strong>
                        
                          {{ number_format(round($antetvanzare->sum('suma'),2),2) }}
                           </strong>
                         </h4>
                      </td>
                      
                     
                    </tr>
               </table>
     <hr> 
     

	
	
@stop