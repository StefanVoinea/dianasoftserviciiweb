@extends('layouts.antet')

@section ('content')
  
   <center> <h3> Situație acte adiționale </h3> </center>
   <center> <h3>{{$selectie}} </h3></center>
   <hr>
      <table class="table table-condesed" width=100%  >
            
            <tr >
              <th align ="center" width="5%">
                    <center>Nr <br> crt</center>
                </th>
              
                <th align ="center" width="30%">
                    Contract
                </th>
                <th align ="center" width="20%">
                    Act aditional
                </th>
               <th align ="center" width="45%">
                   Modificari
                </th>
                
                
            </tr>
     </table>

      <hr>          
      <table class="table table-condesed" style="border-collapse: collapse; " width=100%>   
                    @foreach($antetvanzare as $vanzare)
                    
     				          <tr style="border-bottom:1pt solid black; page-break-inside: avoid !important;"> 
                       <td align="center" width="5%">
                       {{ $i++ }}.
                       </td>
                      
                      <td align="left" width="30%">
                        @if($vanzare->contract)
                         {{$vanzare->contract->tip_contract  }}    
                         {{$vanzare->contract->nr_contract."/".dateFormatAfisare($vanzare->contract->data_contract)  }}
                         <br>
                         {{$vanzare->contract->nume  }}    
                        @endif
                      </td>
                       <td align="left" width="20%">
                         {{ $vanzare->tip_document }}
                         {{ $vanzare->nr_document."/".dateFormatAfisare($vanzare->data_document)  }}   
                        
                      </td>
                      <td align="left" width="45%">
                      {{ $vanzare->modificari }}
                         
                      </td>
                      
                      </tr>
                     @endforeach 
                    </table>
                     <hr> 
     			    
     

	
	
@stop