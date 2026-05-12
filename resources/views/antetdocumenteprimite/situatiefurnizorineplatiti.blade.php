@extends('layouts.antet')

@section ('content')
  <br>
  
   

      <table class="table table-condesed text-sm"  border="1"  style="border-collapse: collapse; " width=100%  >
           <thead>
            <tr style="border-top:1pt solid black; page-break-inside: avoid">
              <td colspan="10">
                 <h3>
                 <center><strong> Situație documente neplătite </strong> </center>
                  <center> <strong>{{$selectie}}  </strong></center>
                </h3>
              </td>
            </tr>
            <tr style="border-top:1pt solid black; page-break-inside: avoid !important;">
              <th align ="center" rowspan="2" width="5%">
                    <center>Nr <br> crt</center>
                </th>
                <th align ="center"   colspan="3" width="35%">
                    Document
                </th>
                <th align ="center"  rowspan="2" width="10%">
                    Termen plata
                </th>
                
                <th align ="center"  rowspan="2" width="10%">
                    Valoare
                </th>
                <th align ="center"  rowspan="2" width="10%">
                    Suma platita
                </th>
                <th align ="center"  rowspan="2" width="10%">
                    Rest de plată <br>
                     (Total)
                </th>
                <th align ="center"  rowspan="2" width="10%">
                    Avans
                </th>
             
                
                
            </tr>
            <tr style="border-bottom:1pt solid black; page-break-inside: avoid !important;">
                <th align ="center" width="10%">
                    Tip
                </th>
                <th align ="center" width="15%">
                    Numar
                </th>
                <th align ="center" width="10%">
                    Data
                </th>
              
                
                
            </tr>
            </thead>
     <!-- </table> -->
      <!-- <hr> -->
      <tbody>
                    @foreach($antetdocumenteprimite->groupby("furnizor") as $documentgestiune)
                    <tr style="page-break-inside: avoid !important;">
                      <td colspan="8">
                        <strong>
                        FURNIZOR {{$documentgestiune[0]->furnizor}}
                      </strong>
                      </td>
                    </tr>
                    <!-- <hr> -->
     				<!-- <table class="table table-condesed" style="border-collapse: collapse; " width=100%>       -->
                    @foreach($documentgestiune as $document)
                      
                      <tr class="text-sm" style="border-top:1pt solid black; page-break-inside: avoid !important;"> 
                       <td align="center" width="5%">
                       {{ $i++ }}.
                       </td>
                      <td align="center" width="10%">
                       {{$document->tip_document  }}
                      </td>
                      <td align="center" width="15%">
                       {{$document->nr_document  }}
                      </td>
                      <td align="center" width="10%">
                       {{dateFormatAfisare($document->data_document)  }}
                      </td>
                     
                      <td align="center" width="10%">
                      {{dateFormatAfisare($document->termen_plata)  }}
                         
                      </td>
                     
                      <td align="right" width="10%">
                      {{ number_format($document->valoare,2) }}
                         
                      </td>
                      <td align="right" width="10%">
                      {{ number_format($document->total_platit,2) }}
                         
                      </td>

                      <td align="right" width="10%">
                     {{ number_format($document->valoare-$document->total_platit,2)}}
                         
                      </td>
                      <td align="right" width="10%">
                     {{ number_format($document->avans,2)}}
                         
                      </td>
                       
                      
                      </tr>
                     @endforeach 
            <!--         </table>
                     <hr> 
     			     <table class="table table-condesed" width=100%>  -->
                    <tr style="border-top:1pt solid black; page-break-inside: avoid !important;">
                    
                      <td width="30%" align="left" colspan="5">
                         <strong>
                          TOTAL {{$document->furnizor}}
                          </strong>
                      </td>
                      <td width="10%" align="right" >
                       
                        <strong>
                       
                          {{ number_format(round($documentgestiune->sum('valoare'),2),2) }}
                       
                         </strong>
                       
                      </td>
                      <td width="10%" align="right" >
                       
                        <strong>
                        
                          {{ number_format(round($documentgestiune->sum('total_platit'),2),2) }}
                           </strong>
                       
                      </td>
                      <td width="10%" align="right" >
                        
                        <strong>
                        
                          {{ number_format(round($documentgestiune->sum('valoare')-$documentgestiune->sum('total_platit'),2),2) }}
                           </strong>
                        
                      </td>
                    <td width="10%" align="right" >
                       
                        <strong>
                        
                          {{ number_format(round($documentgestiune->sum('avans'),2),2) }}
                           </strong>
                       
                      </td>
                     
                     
                    </tr>
                    <tr>
                      <td colspan="10">
                        <br>
                      </td>
                    </tr>
                    @endforeach 
             
                    <tr style="border-bottom:1pt solid black; border-top:1pt solid black; page-break-inside: avoid !important;">
                    
                      <td width="30%" align="right" colspan="5">
                         <strong>
                          TOTAL (LEI)
                          </strong>
                      </td>
                      <td width="10%" align="right" >
                        
                        <strong>
                       
                          {{ number_format(round($antetdocumenteprimite->sum('valoare'),2),2) }}
                       
                         </strong>
                       
                      </td>
                      <td width="10%" align="right" >
                       
                        <strong>
                        
                          {{ number_format(round($antetdocumenteprimite->sum('total_platit'),2),2) }}
                           </strong>
                       
                      </td>
                      <td width="10%" align="right" >
                        
                        <strong>
                        
                           {{ number_format(round($antetdocumenteprimite->sum('valoare')-$antetdocumenteprimite->sum('total_platit'),2),2) }}
                           </strong>
                        
                      </td>
                    
                       <td width="10%" align="right" >
                       
                        <strong>
                        
                          {{ number_format(round($antetdocumenteprimite->sum('avans'),2),2) }}
                           </strong>
                       
                      </td>
                     
                    </tr>
                  </tbody>
               </table>
     <!-- <hr>  -->
     

	
	
@stop