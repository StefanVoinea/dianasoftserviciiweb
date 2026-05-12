@extends('layouts.antet')

@section ('content')
  
      <table class="table table-condesed text-sm" style="border-collapse: collapse; page-break-inside: avoid !important;" border="1" width=100%  >
            <thead>
              <tr style="page-break-inside: avoid !important;">
                <th colspan="9">
                   <center> <h3> Situație documente neîncasate </h3> </center>
                    <center> <h3>{{$selectie}}  </h3></center>
                </th>
              </tr>
            <tr style="page-break-inside: avoid !important;" >
              <th align ="center" rowspan="2" width="5%">
                    <center>Nr <br> crt</center>
                </th>
                <th align ="center" colspan="3" width="45%">
                    Document
                
                </th>
                <th align ="center" rowspan="2" width="10%">
                    Termen plata
                </th>
               
            
                
                <th align ="center"  rowspan="2" width="10%">
                    Valoare document
                </th>
                <th align ="center"  rowspan="2" width="10%">
                    Suma încasată
                </th>
                <th align ="center"  rowspan="2" width="10%">
                    Rest de plată
                </th>
                
                
            </tr>
             <tr style="page-break-inside: avoid !important;" >
               <th align ="center" width="15%">
                  Tip
               </th>
               <th align ="center" width="15%">
                  Numar
               </th>
               <th align ="center" width="15%">
                  Data
               </th>
             </tr>
          </thead>
          <tbody>
                    @foreach($antetvanzare->groupby("partener") as $vanzaregestiune)
                     <tr style="height:30px; page-break-inside: avoid !important;"> 
                       <td align="left" colspan="9">
                       
                           <strong> {{$vanzaregestiune[0]->partener}} </strong>
                       </td>
                     </tr>   
                   
                    @foreach($vanzaregestiune as $vanzare)
                      
                      <tr style="border-bottom:1pt solid black; page-break-inside: avoid !important;"> 
                       <td align="center" width="5%">
                       {{ $i++ }}.
                       </td>
                      <td align="left" width="15%">
                       {{$vanzare->tip_document  }}
                      </td> 
                      <td align="left" width="15%">
                      {{$vanzare->numar }}
                      <td align="center" width="15%">
                      {{dateFormatAfisare($vanzare->data)  }}
                      </td>
                      <td align="center" width="10%">
                      {{dateFormatAfisare($vanzare->termen_plata)  }}
                         
                      </td>
                      
                  
                     
                      <td align="right" width="10%">
                      {{ number_format($vanzare->valoare,2) }}
                         
                      </td>
                      <td align="right" width="10%">
                      {{ number_format($vanzare->total_incasat,2) }}
                         
                      </td>
                      <td align="right" width="10%">
                     {{ number_format($vanzare->valoare-$vanzare->total_incasat,2)}}
                         
                      </td>
                      </tr>
                     @endforeach 
                  
                    <tr style="height:30px; page-break-inside: avoid !important;">
                    
                      <td width="70%" align="right" colspan="5">
                         <strong>
                          TOTAL {{$vanzare->partener}}
                          </strong>
                      </td>
                      <td width="10%" align="right" >
                        
                        <strong>
                       
                          {{ number_format(round($vanzaregestiune->sum('valoare'),2),2) }}
                       
                         </strong>
                        
                      </td>
                      <td width="10%" align="right" >
                      
                        <strong>
                        
                          {{ number_format(round($vanzaregestiune->sum('total_incasat'),2),2) }}
                           </strong>
                        
                      </td>
                      <td width="10%" align="right" >
                       
                        <strong>
                        
                          {{ number_format(round($vanzaregestiune->sum('valoare')-$vanzaregestiune->sum('total_incasat'),2),2) }}
                           </strong>
                       
                      </td>
                    </tr>
                    
                    @endforeach 
    
                    <tr style="border-bottom:1pt solid black; page-break-inside: avoid !important;">
                    
                      <td width="70%" align="right" colspan="5">
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
                      <td width="10%" align="right" >
                        <h4>
                        <strong>
                        
                          {{ number_format(round($antetvanzare->sum('total_incasat'),2),2) }}
                           </strong>
                         </h4>
                      </td>
                      <td width="10%" align="right" >
                        <h4>
                        <strong>
                        
                           {{ number_format(round($antetvanzare->sum('valoare')-$antetvanzare->sum('total_incasat'),2),2) }}
                           </strong>
                         </h4>
                      </td>
                    </tr>
                   </tbody> 
               </table>
     
     

	
	
@stop