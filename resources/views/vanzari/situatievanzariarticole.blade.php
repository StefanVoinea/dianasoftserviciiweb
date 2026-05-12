@extends('layouts.antet')

@section ('content')
  
   <center> <h3> Situație vânzari pe articole</h3> </center>
   <center> <h3>{{$selectie}}  </h3></center>
   <hr>
      <table class="table table-condesed" width=100%  >
            
            <tr >
              
                <th align ="center" width="23%">
                    Document
                </th>
                <th align ="center" width="27%">
                    Client
                </th>
                <th align ="center" width="16%">
                    Denumire
                </th>
                <th align ="center" width="5%">
                    Cantitate
                </th>
               <th align ="center" width="5%">
                    Pret 
                </th>
                
               
                <th align ="center" width="8%">
                    Valoare
                </th>
                
                
            </tr>
     </table>
      <hr>
                    @foreach($antetvanzare->groupby("cod") as $vanzare)
                     ARTICOL {{$vanzare[0]->cod." ".$vanzare[0]->denumire}}
                    <hr>
     				        <table class="table table-condesed" style="border-collapse: collapse; " width=100%>      
                    @foreach($vanzare as $articol)
                        
            
                                  <tr style="border-bottom:2pt solid black; page-break-inside: avoid !important;">
                                     <td align="left" width="23%">
                                      {{$articol->antetvanzare->tip_document  }}
                                    
                                      {{$articol->antetvanzare->seria }}
                                  
                                      {{$articol->antetvanzare->numar }}
                                    
                                      {{dateFormatAfisare($articol->antetvanzare->data)  }}
                                       
                                    </td>
                                    
                                     <td align="left" width="27%">
                                    {{ $articol->antetvanzare->partener }}
                                       
                                    </td>
                                      <td align="left" width="16%">
                                    {{ $articol->denumire }}
                                       
                                    </td>
                                      <td align ="center" width="5%">
                                         {{$articol->cantitate}}
                                      </td>
                                      <td align ="right" width="5%">
                                          {{number_format($articol->pret_vanzare,2)}}
                                      </td>
                                    
                                    
                                      <td align ="right" width="8%">
                                          {{number_format($articol->valoare,2)}}
                                      </td>
                                      
                                      
                                  </tr>
                       
                     @endforeach 
                   </table>
                      
                    
                     <hr> 
     			     <table class="table table-condesed" width=100%> 
                    <tr>
                    
                      <td width="66%" align="right" colspan="4">
                         <strong>
                          TOTAL ARTICOL {{$vanzare[0]->cod." ".$vanzare[0]->denumire}}
                          </strong>
                      </td>
                      <td width="5%" align="right" >
                        <h4>
                        <strong>
                       
                          {{ number_format(round($vanzare->sum('cantitate'),2),2) }}
                       
                         </strong>
                       </h4>
                      </td>
                      <td width="5%" align="right" >
                        
                      </td>
                      
                      <td width="8%" align="right" >
                        <h4>
                        <strong>
                          {{ number_format(round($vanzare->sum('valoare'),2),2) }}
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
                    
                      <td width="66%" align="right" colspan="4">
                         <strong>
                          TOTAL
                          </strong>
                      </td>
                      <td width="5%" align="right" >
                        
                      </td>
                      <td width="5%" align="right" >
                        
                      </td>
                      
                      <td width="8%" align="right" >
                        <h4>
                        <strong>
                        
                          {{ number_format(round($antetvanzare->sum('valoare'),2),2) }}
                           </strong>
                         </h4>
                      </td>
                    </tr>
               </table>
     <hr> 
     

	
	
@stop