@extends('layouts.antet')

@section ('content')
  
   <center> <h3> Situație rapoarte de producție </h3> </center>
   <center> <h3>{{$selectie}}</h3></center>
   <hr>
      <table class="table table-condesed" width=100%  >
            
            <tr >
              <th align ="center" width="5%">
                    <center>Nr <br> crt</center>
                </th>
              
                <th align ="center" width="15%">
                    Data
                </th>
                 <th align ="center" width="10%">
                    Valoare fara tva
                </th>
                 <th align ="center" width="10%">
                    Valoare tva
                </th>
                <th align ="center" width="10%">
                    Valoare
                </th>
                <th align ="center" width="15%">
                    Status realizare
                </th>
               
               <th align ="center" width="5%">
                    Nr contract
                </th>
                <th align ="center" width="15%">
                    Client
                </th>
                <th align ="center" width="15%">
                
                    Observatii
                </th>
                
                
            </tr>
     </table>
      <hr>
                    
     				<table class="table table-condesed" style="border-collapse: collapse; " width=100%>      
                    @foreach($raportdeproductie as $raport)
                       
                      <tr style="border-bottom:1pt solid black; page-break-inside: avoid !important;"> 
                       <td align="center" width="5%">
                       {{ $i++ }}.
                       </td>
                      
                      <td align="center" width="15%">
                      {{dateFormatAfisare($raport->data)  }}
                         
                      </td>
                      <td align="right" width="10%">
                      {{ number_format(round($raport->valoare/1.19,2),2)}}
                      </td>
                      <td align="right" width="10%">
                      {{ number_format(round($raport->valoare-$raport->valoare/1.19,2),2)}}
                      </td>
                      <td align="right" width="10%">
                       {{ number_format($raport->valoare,2)}}
                      </td>
                       <td align="center" width="15%">
                      {{ $raport->statusrealizare }}
                         
                      </td>
                      <td align="center" width="5%">
                      {{ $raport->contract?$raport->contract->nr_contract:"" }}
                         
                      </td>
                      <td align="left" width="15%">
                      {{ $raport->contract?$raport->contract->nume:"" }}
                         
                      </td>
                      <td align="left" width="15%">
                      {{ $raport->obs }}
                         
                      </td>
                     
                      </tr>
                     
                     @endforeach
                    </table>
                     <hr> 
     			     <table class="table table-condesed" width=100%> 
                    <tr>
                    
                      <td width="20%" align="right" colspan="2">
                         <strong>
                          TOTAL 
                          </strong>
                      </td>
                      <td width="10%" align="right" >
                        <h4>
                        <strong>
                       
                          {{ number_format(round($raportdeproductie->sum('valoare')/1.19,2),2) }}
                       
                         </strong>
                       </h4>
                      </td>
                      <td width="10%" align="right" >
                        <h4>
                        <strong>
                       
                          {{ number_format(round($raportdeproductie->sum('valoare')-$raportdeproductie->sum('valoare')/1.19,2),2) }}
                       
                         </strong>
                       </h4>
                      </td>
                      <td width="10%" align="right" >
                        <h4>
                        <strong>
                       
                          {{ number_format(round($raportdeproductie->sum('valoare'),2),2) }}
                       
                         </strong>
                       </h4>
                      </td>
                      <td align="left" width="15%">
                      
                         
                      </td>
                      <td align="left" width="10%">
                      
                         
                      </td>
                      <td align="right" width="15%">
                      
                         
                      </td>
                      <td align="right" width="15%">
                      
                         
                      </td>
                    </tr>
               </table>
             
             
   <hr>
    
     

	
	
@stop
