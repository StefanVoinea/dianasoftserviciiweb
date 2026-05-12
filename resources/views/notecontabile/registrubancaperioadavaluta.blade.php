@extends('layouts.antet')

@section ('content')
   <center>  {{$denregistru.' PENTRU PERIOADA DE '.dateFormatAfisare($datai).' - '.dateFormatAfisare($datasf).' '.$trezoreria["denumire"]}}  </center>
   <br>
   <strong> {{'Sold initial: '. number_format($soldiniLEI,2).' LEI  = '. 
               number_format($soldini,2).' '.$trezoreria["tip_valuta"]}}</strong>
   @foreach($notacontabila->groupby("data_nota") as $notadata)
   
      <table class="text-sm table table-condesed" border="1" style="border-collapse: collapse; " width=100%  >
            <thead>
            <tr style="page-break-inside: avoid !important;">
              <th align ="center" rowspan="2" width="5%">
                    <center>Nr <br> crt</center>
              </th>

              <th align ="center" colspan="3" width="20%">
                    <center>Document <br> (tip,nr,data)</center>
              </th>
              <th align ="center" rowspan="2" width="19%">
                    Explicatie
              </th>
              <th align ="center" rowspan="2" width="7%">
                    Încasări (lei)
              </th>
              <th align ="center" rowspan="2" width="7%">
                    Plăți (lei)
              </th>
              <th align ="center" rowspan="2" width="5%">
                    Curs
              </th>
              <th align ="center" rowspan="2" width="7%">
                    Încasări ({{$trezoreria["tip_valuta"]}})
              </th>
              <th align ="center" rowspan="2" width="7%">
                    Plăți ({{$trezoreria["tip_valuta"]}})
              </th>
              <th align ="center" rowspan="2" width="5%">
                    Cont <br>corespondent
                </th>
                <th align ="center" rowspan="2" width="18%">
                    Partener
                </th>

            </tr>
            <tr style="page-break-inside: avoid !important;">
              

              <th align ="center"  width="6%">
                    Tip
              </th>
              <th align ="center"  width="7%">
                    Numar
              </th>
              <th align ="center"  width="7%">
                    Data
              </th>
              
            </tr>
           </thead>
      
                    @foreach($notadata as $nota)
                      
                      <tr style="page-break-inside: avoid !important;"> 
                       <td align="center" width="5%">
                       {{ $i++ }}.
                       </td>
                      
                       <td align="center" width="6%">
                         {{$nota->tip_document}}
                       </td>
                       <td align="center" width="7%">
                        {{$nota->nr_document}}
                       </td>
                       <td align="center" width="7%">
                        {{dateFormatAfisare($nota->data_document)  }}
                        </td>
                       <td align="left" width="19%">
                         {{ $nota->explicatie}}
                        
                      </td>
                      <td align="right" width="7%">
                        @if($nota->contd==$trezoreria['cont'])
                          {{ number_format($nota->suma,2) }}
                        @endif
                      </td>
                      <td align="right" width="7%">
                        @if($nota->contc==$trezoreria['cont'])
                          {{ number_format($nota->suma,2) }}
                        @endif
                     </td>
                      <td align="right" width="5%">
                        @if($nota->contc==$trezoreria['cont'])
                          {{ number_format($nota->curs,4) }}
                        @endif
                     </td>
                      <td align="right" width="7%">
                        @if($nota->contd==$trezoreria['cont'])
                          {{ number_format($nota->suma_in_valuta,2) }}
                        @endif
                      </td>
                      <td align="right" width="7%">
                        @if($nota->contc==$trezoreria['cont'])
                          {{ number_format($nota->suma_in_valuta,2) }}
                        @endif
                     </td>
                      <td align="center" width="5%">
                        @if($nota->contd==$trezoreria['cont'])
                          {{ $nota->contc }}
                        @endif
                        @if($nota->contc==$trezoreria['cont'])
                          {{ $nota->contd }}
                        @endif
                      </td>
                      <td align="left" width="18%">
                         @if($nota->denumire_partener)
                           {{$nota->denumire_partener  }}
                         @endif
                      </td>
                      </tr>
                     @endforeach 
                  
                    <tr>
                    
                      <td width="44%" align="left" colspan="5">
                         <strong>
                          {{"TOTAL ".dateFormatAfisare($notadata[0]->data_nota)}}
                          </strong>
                      </td>
                     
                      <td width="7%" align="right" >
                       
                        <strong>
                        
                          {{ number_format(round($notadata->where('tip_operatiune','Incasare')->sum('suma'),2),2) }}
                           </strong>
                       
                      </td>
                      <td width="7%" align="right" >
                        
                        <strong>
                        
                         {{ number_format(round($notadata->where('tip_operatiune','Plata')->sum('suma'),2),2) }}
                           </strong>
                         
                      </td>
                      <td width="5%" align="right" >
                         
                      </td>
                      <td width="7%" align="right" >
                       
                        <strong>
                        
                          {{ number_format(round($notadata->where('tip_operatiune','Incasare')->sum('suma_in_valuta'),2),2) }}
                           </strong>
                       
                      </td>
                      <td width="7%" align="right" >
                        
                        <strong>
                        
                         {{ number_format(round($notadata->where('tip_operatiune','Plata')->sum('suma_in_valuta'),2),2) }}
                           </strong>
                         
                      </td>
                      <td width="5%" align="right">
                         
                      </td>
                      <td width="18%" align="right">
                         
                      </td>
                    </tr>
                    <tr>
                      <td colspan="12">
                           <h4> {{'Sold '.dateFormatAfisare($notadata[0]->data_nota).': '}}
                              {{number_format($soldiniLEI+=$notadata->where('tip_operatiune','Incasare')->sum('suma')
                                                      -$notadata->where('tip_operatiune','Plata')->sum('suma'),2)}}{{' LEI ='}}
                             {{number_format($soldini+=$notadata->where('tip_operatiune','Incasare')->sum('suma_in_valuta')
                                                        -$notadata->where('tip_operatiune','Plata')->sum('suma_in_valuta'),2)}}{{' '.$trezoreria["tip_valuta"]}}
                         </h4>
                      </td>
                    </tr>
               </table>
          <br> <br> 
@endforeach
	
	
@stop