@extends('layouts.antet')

@section ('content')
   <center>  {{$denregistru.' PENTRU PERIOADA DE '.dateFormatAfisare($datai).' - '.dateFormatAfisare($datasf).' '.$trezoreria["denumire"]}}  </center>
   <br>
   <strong> {{'Sold initial: '. number_format($soldini,2).' LEI '}}</strong>
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
              <th align ="center" rowspan="2" width="25%">
                    Explicatie
              </th>
              <th align ="center" rowspan="2" width="10%">
                    Încasări
              </th>
              <th align ="center" rowspan="2" width="10%">
                    Plăți
              </th>
              <th align ="center" rowspan="2" width="10%">
                    Cont corespondent
                </th>
                <th align ="center" rowspan="2" width="20%">
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
                       <td align="left" width="25%">
                         {{ $nota->explicatie}}
                        
                      </td>
                      <td align="right" width="10%">
                        @if($nota->contd==$trezoreria['cont'])
                          {{ number_format($nota->suma,2) }}
                        @endif
                      </td>
                      <td align="right" width="10%">
                        @if($nota->contc==$trezoreria['cont'])
                          {{ number_format($nota->suma,2) }}
                        @endif
                     </td>
                     
                      <td align="center" width="10%">
                        @if($nota->contd==$trezoreria['cont'])
                          {{ $nota->contc }}
                        @endif
                        @if($nota->contc==$trezoreria['cont'])
                          {{ $nota->contd }}
                        @endif
                      </td>
                      <td align="left" width="20%">
                         @if($nota->denumire_partener)
                        {{$nota->denumire_partener  }}
                        @endif
                      </td>
                      </tr>
                     @endforeach 
                  
                    <tr>
                    
                      <td width="50%" align="left" colspan="5">
                         <strong>
                          {{"TOTAL ".dateFormatAfisare($notadata[0]->data_nota)}}
                          </strong>
                      </td>
                     
                      <td width="10%" align="right" >
                       
                        <strong>
                        
                          {{ number_format(round($notadata->where('tip_operatiune','Incasare')->sum('suma'),2),2) }}
                           </strong>
                       
                      </td>
                      <td width="10%" align="right" >
                        
                        <strong>
                        
                         {{ number_format(round($notadata->where('tip_operatiune','Plata')->sum('suma'),2),2) }}
                           </strong>
                         
                      </td>
                      
                      <td width="10%" align="right">
                         
                      </td>
                      <td width="20%" align="right">
                         
                      </td>
                    </tr>
                    <tr>
                      <td colspan="9">
                         <h4> {{'Sold '.dateFormatAfisare($notadata[0]->data_nota).': '}}{{number_format($soldini+=$notadata->where('tip_operatiune','Incasare')->sum('suma')-$notadata->where('tip_operatiune','Plata')->sum('suma'),2)}}{{' LEI'}}
                         </h4>
                      </td>
                    </tr>
               </table>
          <br> <br> 
@endforeach
	
	
@stop