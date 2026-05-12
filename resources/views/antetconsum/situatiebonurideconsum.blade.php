@extends('layouts.antet')

@section ('content')
  
   <center> <h3> Situație bonuri de consum </h3> </center>
   <center> <h3>{{$selectie}} tip lista  Sintetic </h3></center>
   <hr>
   <center>
      <table class="table table-condesed" width=50%  >
            
            <tr >
              <th align ="center" rowspan="2" width="10%">
                    <center>Nr <br> crt</center>
                </th>
                <th align ="center"  rowspan="2" width="30%">
                    Nr document
                </th>
                <th align ="center"  rowspan="2" width="30%">
                    Data document
                </th>
               
                
                <th align ="center" colspan="5" width="30%">
                    <center>Valoare</center> 
               
                </th>
                
            </tr>
            

     </table>
   </center>
      <hr>
                    @foreach($antetconsum->groupby("gestiune['denumire']") as $documentgestiune)
                    GESTIUNE {{$documentgestiune[0]->gestiune["denumire"]}}
                    <hr>
                    <center>
     				<table class="table table-condesed" style="border-collapse: collapse; " width=50%>      
                    @foreach($documentgestiune as $det_doc)
                       
                      <tr style="border-bottom:1pt solid black; page-break-inside: avoid !important;"> 
                       <td align="center" width="10%">
                       {{ $i++ }}.
                       </td>
                        <td align="center" width="30%">
                         {{$det_doc->nr_document  }}
                         </td>
                       <td align="center" width="30%">
                      {{dateFormatAfisare($det_doc->data_document)  }}
                         
                      </td>
                      
                      <td align="right" width="30%">
                      {{ number_format(round($det_doc->detaliuconsum->sum("valoare_intrare"),2),2) }}
                         
                      </td>
                     
                      </tr>
                     
                     @endforeach
                    </table>
                     <hr> 
     			     <table class="table table-condesed" width=50%> 
                    <tr>
                    
                      <td width="70%" align="center" colspan="3">
                         <strong>
                          TOTAL GESTIUNE {{$det_doc->gestiune["denumire"]}}
                          </strong>
                      </td>
                      <td width="30%" align="right" >
                        <h4>
                        <strong>
                       
                          {{ number_format(round($documentgestiune->sum(function($value){
                              return $value->detaliuconsum->sum('valoare_intrare');
                          }),2),2) }}
                       
                         </strong>
                       </h4>
                      </td>
                     
                    </tr>
               </table>
                <hr> 
    </center>
                    @endforeach 
             
  
     

	
	
@stop