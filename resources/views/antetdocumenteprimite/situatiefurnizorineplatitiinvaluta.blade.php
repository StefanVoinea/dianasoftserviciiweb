@extends('layouts.antet')

@section ('content')
  
      <table class="table table-condesed text-sm" style="border-collapse: collapse; " border="1" width=100%  >
            <thead>
              <tr>
                <td colspan="13">
                   <center> <h3> Situație documente neplătite </h3> </center>
                    <center> <h3>{{$selectie}} </h3></center>
                </td>
              </tr>
            <tr >
                <th align ="center" width="5%">
                    <center>Nr <br> crt</center>
                </th>
                <th align ="center" width="5%">
                    Tip document
                
                </th>
                <th align ="center"  width="5%">
                    Nr document
                
                </th>
                <th align ="center"  width="8%">
                    Data document
                
                </th>
                <th align ="center"  width="8%">
                    Termen plata
                </th>
                <th align ="center"  width="4%">
                    Tip valuta
                </th>
                <th align ="center"  width="10%">
                    Valoare document
                </th>
                <th align ="center"  width="10%">
                    Suma platita
                </th>
                <th align ="center"  width="10%">
                    Rest de plată <br>
                     (Total)
                </th>
                <th align ="center"  width="5%">
                    Curs
                </th>
                <th align ="center"  width="10%">
                    Valoare document
                       (RON)
                </th>
                <th align ="center"  width="10%">
                    Suma platita
                      (RON)
                </th>
                <th align ="center"  width="10%">
                    Rest de plată <br>
                     (Total RON)
                </th>
                
            </tr>

            </thead>
           
     
                    @foreach($antetdocumenteprimite->groupby("partener_id") as $documentgestiune)
                 
                    <tr>
                      <td align="left" colspan="13">
                        <strong>
                        FURNIZOR {{$documentgestiune[0]["furnizor"]}}
                      </strong>
                      </td>
                    </tr>
                    @foreach($documentgestiune as $document)
                      
                      <tr style="border-bottom:1pt solid black; page-break-inside: avoid !important;"> 
                       <td align="center" width="5%">
                       {{ $i++ }}.
                       </td>
                      <td align="center" width="5%">
                       {{$document->tip_document  }}
                      </td>
                      <td align="center" width="5%">
                       {{$document->nr_document  }}
                      </td>
                      <td align="center" width="8%">
                       {{dateFormatAfisare($document->data_document) }}
                      </td>
                      
                      <td align="right" width="8%">
                      {{dateFormatAfisare($document->termen_plata)  }}
                         
                      </td>
                     <td align="right" width="4%">
                      {{$document->tip_valuta  }}
                         
                      </td>
                       <td align="right" width="10%">
                      {{ number_format($document->valoare_intrare_in_valuta,2) }}
                         
                      </td>
                      <td align="right" width="10%">
                      {{ number_format($document->total_platit_in_valuta,2) }}
                         
                      </td>
                       
                      <td align="right" width="10%">
                     {{ number_format($document->valoare_intrare_in_valuta-$document->total_platit_in_valuta,2)}}
                         
                      </td>
                      <td align="right" width="5%">
                      {{$document->curs  }}
                         
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
                     
                        
                      </tr>
                     @endforeach 
                  
                    <tr>
                    
                      <td width="35%" align="left" colspan="6">
                         <strong>
                          TOTAL {{$document->furnizor}}
                          </strong>
                      </td>
                      <td width="10%" align="right" >
                        <h4>
                        <strong>
                       
                          {{ number_format(round($documentgestiune->sum('valoare_intrare_in_valuta'),2),2) }}
                       
                         </strong>
                       </h4>
                      </td>
                      <td width="10%" align="right" >
                        <h4>
                        <strong>
                        
                          {{ number_format(round($documentgestiune->sum('total_platit_in_valuta'),2),2) }}
                           </strong>
                         </h4>
                      </td>
                      <td width="10%" align="right" >
                        <h4>
                        <strong>
                        
                          {{ number_format(round($documentgestiune->sum('valoare_intrare_in_valuta')-$documentgestiune->sum('total_platit_in_valuta'),2),2) }}
                           </strong>
                         </h4>
                      </td>
                      <td width="5%" align="right" >
                        
                      </td>
                      <td width="10%" align="right" >
                        <h4>
                        <strong>
                       
                          {{ number_format(round($documentgestiune->sum('valoare'),2),2) }}
                       
                         </strong>
                       </h4>
                      </td>
                      <td width="10%" align="right" >
                        <h4>
                        <strong>
                        
                          {{ number_format(round($documentgestiune->sum('total_platit'),2),2) }}
                           </strong>
                         </h4>
                      </td>
                      <td width="10%" align="right" >
                        <h4>
                        <strong>
                        
                          {{ number_format(round($documentgestiune->sum('valoare')-$documentgestiune->sum('total_platit'),2),2) }}
                           </strong>
                         </h4>
                      </td>
                    
                    </tr>
                       <tr>
                      <td align="left" colspan="13">
                       <br>
                      </td>
                    </tr>
                     @endforeach 
             
                    <tr>
                    
                      <td width="35%" align="right" colspan="6">
                         <strong>
                          TOTAL 
                          </strong>
                      </td>
                      <td width="10%" align="right" >
                        <h4>
                        <strong>
                       
                          {{ number_format(round($antetdocumenteprimite->sum('valoare_intrare_in_valuta'),2),2) }}
                       
                         </strong>
                       </h4>
                      </td>
                      <td width="10%" align="right" >
                        <h4>
                        <strong>
                        
                          {{ number_format(round($antetdocumenteprimite->sum('total_platit_in_valuta'),2),2) }}
                           </strong>
                         </h4>
                      </td>
                      <td width="10%" align="right" >
                        <h4>
                        <strong>
                        
                           {{ number_format(round($antetdocumenteprimite->sum('valoare_intrare_in_valuta')-$antetdocumenteprimite->sum('total_platit_in_valuta'),2),2) }}
                           </strong>
                         </h4>
                      </td>
                      <td width="5%" align="right">
                        
                      </td>
                      <td width="10%" align="right" >
                        <h4>
                        <strong>
                       
                          {{ number_format(round($antetdocumenteprimite->sum('valoare'),2),2) }}
                       
                         </strong>
                       </h4>
                      </td>
                      <td width="10%" align="right" >
                        <h4>
                        <strong>
                        
                          {{ number_format(round($antetdocumenteprimite->sum('total_platit'),2),2) }}
                           </strong>
                         </h4>
                      </td>
                      <td width="10%" align="right" >
                        <h4>
                        <strong>
                        
                           {{ number_format(round($antetdocumenteprimite->sum('valoare')-$antetdocumenteprimite->sum('total_platit'),2),2) }}
                           </strong>
                         </h4>
                      </td>
                    
                    </tr>
               </table>
    
     

	
	
@stop