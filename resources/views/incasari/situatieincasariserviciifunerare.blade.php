@extends('layouts.antet')

@section ('content')
  
      <table class="table table-condesed text-sm" border="1" style="border-collapse: collapse; " width=100%   >
            <thead>
            <tr>
              <td colspan="7">
                 <strong><center> Situație încasări</center></strong>
                 <strong><center> {{$selectie}} </center></strong>
              </td>
            </tr>            
            <tr style="border-bottom:1pt solid black; page-break-inside: avoid !important;">
              <th align ="center" width="5%">
                    <center>Nr <br> crt</center>
                </th>
                <th align ="center" width="55%">
                    Nume Client
                
                </th>
               
                <th align ="center" width="10%">
                    Nr contract
                </th>
               <th align ="center" width="10%">
                    Planuri <br> (avansuri+rate)
                </th>
                <th align ="center" width="10%">
                    Cripte
                </th>
               
                <th align ="center" width="10%">
                    Total
                </th>
                
                
            </tr>

            </thead>
            <tbody>
                    @foreach($antetvanzare as $vanzare)
                      
                      <tr  style="border-bottom:1pt solid black; page-break-inside: avoid !important;"> 
                       <td align="center" width="5%">
                       {{ $i++ }}.
                       </td>
                      <td align="left" width="55%">
                       {{$vanzare->partener  }}
                      </td>
                     
                       <td align="center" width="10%">
                         {{ $vanzare->nr_contract }}
                         
                      </td>
                      <td align="right" width="10%">
                      {{ number_format($vanzare->planuri,2) }}
                         
                      </td>
                      <td align="right" width="10%">
                     {{ number_format($vanzare->cripta,2)}}
                         
                      </td>
                    
                      <td align="right" width="10%">
                     {{ number_format($vanzare->total,2)}}
                         
                      </td>
                      </tr>
                     @endforeach 
                  
             
                    
                    <tr  style="border-bottom:1pt solid black; page-break-inside: avoid !important;">
                    
                      <td width="70%" align="right" colspan="3">
                         <strong>
                          TOTAL 
                          </strong>
                      </td>
                     
                      <td width="10%" align="right" >
                        <h4>
                        <strong>
                        
                          {{ number_format(round($antetvanzare->sum('planuri'),2),2) }}
                           </strong>
                         </h4>
                      </td>
                      
                      <td width="10%" align="right" >
                        <h4>
                        <strong>
                        
                          {{ number_format(round($antetvanzare->sum('cripta'),2),2) }}
                           </strong>
                         </h4>
                      </td>
                      <td width="10%" align="right" >
                        <h4>
                        <strong>
                        
                          {{ number_format(round($antetvanzare->sum('total'),2),2) }}
                           </strong>
                         </h4>
                      </td>
                    </tr>
                  </tbody>
               </table>
   

	
	
@stop