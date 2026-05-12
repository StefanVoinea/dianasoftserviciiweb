@extends('layouts.antet')

@section ('content')
  
   <center> <h3> Situație bonuri de consum </h3> </center>
   <center> <h3>{{$selectie}} tip lista  Sintetic </h3></center>
   <hr>
     
                    @foreach($documente->groupby("agentia") as $documentgestiune)
                    
                    Gestiune: {{$documentgestiune[0]->agentia}}
                    
     				<table class="table table-condesed" style="border-collapse: collapse; " width=100%>      
                          <tr  style="border-bottom:1pt solid black;border-top:1pt solid black; page-break-inside: avoid !important;">
              <th align ="center" width="5%">
                    <center>Nr <br> crt</center>
                </th>
                <th align ="center"   width="10%">
                    Nr bon de consum
                </th>
                <th align ="center"   width="10%">
                    Data bon de consum
                </th>
                
                
                <th align ="center"  width="10%">
                    <center>Valoare intrare</center> 
               
                </th>
                
            </tr>
            
                    @foreach($documentgestiune->groupby("nr_bc") as $det_doc)
                       
                      <tr style="border-bottom:1pt solid black;border-top:1pt solid black; page-break-inside: avoid !important;"> 
                       <td align="center" width="5%">
                       {{ $i++ }}.
                       </td>
                        <td align="center" width="10%">
                         {{$det_doc[0]->nr_bc  }}
                         </td>
                       <td align="center" width="10%">
                      {{dateFormatAfisare($det_doc[0]->data_bc)  }}
                         
                      </td>
                   
                     
                     
                      <td align="right" width="10%">
                      {{ number_format(round($det_doc->sum("valoare_intrare"),2),2) }}
                         
                      </td>
                      </tr>
                     
                     @endforeach
                    </table>
                     
     			     <table class="table table-condesed" width=100%> 
                    <tr style="border-bottom:1pt solid black;border-top:1pt solid black; page-break-inside: avoid !important;">
                    
                      <td width="90%" align="left" colspan="3">
                         <strong>
                          Total gestiune {{$det_doc[0]->agentia}}
                          </strong>
                      </td>
                      
                    
                      <td align="right" width="10%">
                        <strong>
                       {{ number_format(round($documentgestiune->sum("valoare_intrare"),2),2) }}
                          </strong>
                      </td>
                      
                      
                    </tr>
               </table>
                <hr> 
                    @endforeach 
             
  
    
     

	
	
@stop
