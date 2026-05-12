@extends('layouts.antet')

@section ('content')
  
   <center> <h3> Situație consumuri pe articole  </h3> </center>
   <center> <h3>{{$selectie}}  </h3></center>
   <hr>
      <table class="table table-condesed" width=100%  >
            
            <tr >
              
                <th align ="center" width="40%">
                    Bon de consum
                </th>
               
                <th align ="center" width="20%">
                    Denumire
                </th>
                 <th align ="center" width="10%">
                    Cont
                </th>
                <th align ="center" width="5%">
                    Cantitate
                </th>
               <th align ="center" width="5%">
                    Pret intrare
                </th>
                
                <th align ="center" width="12%">
                    Valoare intrare
                </th>
                <th align ="center" width="8%">
                    Interior
                </th>
                
                
                
            </tr>
     </table>
      <hr>
                    @foreach($antetconsum->groupby("cod") as $consum)
                     ARTICOL {{$consum[0]->cod." ".$consum[0]->denumire}}
                    <hr>
     				        <table class="table table-condesed" style="border-collapse: collapse; " width=100%>      
                    @foreach($consum as $articol)
                        
            
                                  <tr style="border-bottom:2pt solid black; page-break-inside: avoid !important;">
                                     <td align="left" width="40%">
                                     {{$articol->antetconsum->nr_document.' / '.dateFormatAfisare($articol->antetconsum->data_document)  .'  '. $articol->antetconsum->gestiune["denumire"] }}
                                       
                                    </td>
                                      <td align="left" width="20%">
                                      {{ $articol->denumire }}
                                       
                                    </td>
                                     <td align="left" width="10%">
                                      {{ $articol->contd }}
                                       
                                    </td>
                                      <td align ="center" width="5%">
                                         {{$articol->cantitate}}
                                      </td>
                                      <td align ="right" width="5%">
                                          {{number_format($articol->pret_intrare,2)}}
                                      </td>
                                    
                                      <td align ="right" width="12%">
                                          {{number_format($articol->valoare_intrare,2)}}
                                      </td>
                                      <td align ="center" width="8%">
                                          {{$articol->antetconsum->interior?"DA":"NU"}}
                                      </td>
                                      
                                      
                                  </tr>
                       
                     @endforeach 
                   </table>
                      
                    
                     <hr> 
     			     <table class="table table-condesed" width=100%> 
                    <tr>
                    
                      <td width="70%" align="center" colspan="3">
                         <strong>
                          TOTAL ARTICOL {{$consum[0]->cod." ".$consum[0]->denumire}}
                          </strong>
                      </td>
                      <td width="5%" align="right" >
                        <h4>
                        <strong>
                       
                          {{ number_format(round($consum->sum('cantitate'),2),2) }}
                       
                         </strong>
                       </h4>
                      </td>
                      <td width="5%" align="right" >
                        
                      </td>
                      <td width="10%" align="right" >
                        <h4>
                        <strong>
                       
                          {{ number_format(round($consum->sum('valoare_intrare'),2),2) }}
                       
                         </strong>
                       </h4>
                      </td>
                      <td width="10%" align="right" >
                        <h4>
                        <strong>
                       
                          
                       
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
                    
                      <td width="70%" align="center" colspan="3">
                         <strong>
                          TOTAL
                          </strong>
                      </td>
                      <td width="5%" align="right" >
                        
                      </td>
                      <td width="5%" align="right" >
                        
                      </td>
                      <td width="10%" align="right" >
                        <h4>
                        <strong>
                       
                          {{ number_format(round($antetconsum->sum('valoare_intrare'),2),2) }}
                       
                         </strong>
                       </h4>
                      </td>
                      <td width="10%" align="right" >
                        <h4>
                        <strong>
                        
                         
                           </strong>
                         </h4>
                      </td>
                      
                    </tr>
               </table>
     <hr> 
     

	
	
@stop