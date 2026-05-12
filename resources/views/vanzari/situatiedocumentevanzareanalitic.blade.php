@extends('layouts.antet')

@section ('content')
  
   <center> <h3> Situație documente vânzare </h3> </center>
   <center> <h3>{{$selectie}} tip lista  Analitic </h3></center>
   <hr>
      <table class="table table-condesed" width=100%  >
            
            <tr >
              <th align ="center" width="5%">
                    <center>Nr <br> crt</center>
                </th>
                <th align ="center" width="25%">
                    Document
                
                </th>
                <th align ="center" width="5%">
                    Termen plata
                </th>
                <th align ="center" width="30%">
                    Client
                </th>
           
                
             
                <th align ="center" width="10%">
                    Valoare
                </th>
                
                
            </tr>
     </table>
      <hr>
                    @foreach($antetvanzare->groupby("gestiune") as $vanzaregestiune)
                    GESTIUNE {{$vanzaregestiune[0]->gestiune}}
                    <hr>
     				<table class="table table-condesed" style="border-collapse: collapse; " width=100%>      
                    @foreach($vanzaregestiune as $vanzare)
                       <tr style="border-bottom:1pt solid black; page-break-inside: avoid !important;">
                        <td colspan="8" >
                       <table class="table table-condesed" style="border-collapse: collapse;" width=100%  >
            
                                  <tr style="border-bottom:1pt solid black; page-break-inside: avoid !important;">
                                     
                                      <th align ="right" width="45%">
                                         {{'Denumire    ' }} 
                                      
                                      </th>
                                      <th align ="center" width="5%">
                                          Cantitate
                                      </th>
                                      <th align ="center" width="10%">
                                          Pret 
                                      </th>
                                  
                                     
                                      <th align ="center" width="10%">
                                          Valoare
                                      </th>
                                      
                                      
                                  </tr>
                           </table>
                      @foreach($vanzare->detaliuvanzari as $articol)
                         <table class="table table-condesed" style="border-collapse: collapse;" width=100%  >
            
                                  <tr style="border-bottom:2pt solid black; page-break-inside: avoid !important;">
                                    
                                      <td align ="right" width="45%">
                                          {{$articol->denumire}}
                                      
                                      </td>
                                      <td align ="center" width="5%">
                                         {{$articol->cantitate}}
                                      </td>
                                      <td align ="center" width="10%">
                                          {{number_format($articol->pret_vanzare,2)}}
                                      </td>
                                  
                                      
                                    
                                      <td align ="right" width="10%">
                                          {{number_format($articol->valoare,2)}}
                                      </td>
                                      
                                      
                                  </tr>
                           </table>
                    
                     @endforeach 
                   </td>
                      </tr>
                      <tr style="border-bottom:1pt solid black; page-break-inside: avoid !important;"> 
                       <td align="center" width="5%">
                       {{ $i++ }}.
                       </td>
                      <td align="left" width="25%">
                       {{$vanzare->tip_document  }}
                      
                      {{$vanzare->seria }}
                    
                      {{$vanzare->numar }}
                      
                      {{dateFormatAfisare($vanzare->data)  }}
                         
                      </td>
                      <td align="right" width="5%">
                      {{dateFormatAfisare($vanzare->termen_plata)  }}
                         
                      </td>
                       <td align="left" width="30%">
                      {{ $vanzare->partener }}
                         
                      </td>
                   
                     
                     
                  
                      <td align="right" width="10%">
                     {{ number_format($vanzare->valoare,2)}}
                         
                      </td>
                      </tr>
                     
                     @endforeach
                    </table>
                     <hr> 
     			     <table class="table table-condesed" width=100%> 
                    <tr>
                    
                      <td width="70%" align="right" colspan="4">
                         <strong>
                          TOTAL GESTIUNE {{$vanzare->gestiune}}
                          </strong>
                      </td>
                    
                      <td width="10%" align="right" >
                        <h4>
                        <strong>
                        
                          {{ number_format(round($vanzaregestiune->sum('valoare'),2),2) }}
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
                    
                      <td width="70%" align="right" colspan="4">
                         <strong>
                          TOTAL (LEI)
                          </strong>
                      </td>
                     
                      <td width="10%" align="right" >
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