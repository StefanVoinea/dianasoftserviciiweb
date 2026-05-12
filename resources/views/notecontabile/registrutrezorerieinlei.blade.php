@extends('layouts.antet')

@section ('content')
   @foreach($notacontabila->groupby("data_nota") as $notadata)
   <center>  {{$denregistru.' PENTRU DATA DE '.dateFormatAfisare($notadata[0]->data_nota).' '.$trezoreria["denumire"]}}  </center>
    {{'Sold initial: '. number_format($soldini,2).' LEI'}}
   <hr>
      <table class="text-sm table table-condesed" style="border-collapse: collapse; " width=100%  >
            
            <tr style="border-bottom:1pt solid black; page-break-inside: avoid !important;">
              <th align ="center"  width="5%">
                    <center>Nr <br> crt</center>
              </th>
              <th align ="center"  width="20%">
                    <center>Document <br> (tip,nr,data)</center>
              </th>
              <th align ="center"  width="25%">
                    Explicatie
              </th>
              <th align ="center"  width="10%">
                    Încasări
              </th>
              <th align ="center"  width="10%">
                    Plăți
              </th>
              <th align ="center"  width="10%">
                    Cont corespondent
                </th>
                <th align ="center"  width="20%">
                    Partener
                </th>
            </tr>
           
      
                    @foreach($notadata as $nota)
                      
                      <tr style="border-bottom:1pt solid black; page-break-inside: avoid !important;"> 
                       <td align="center" width="5%">
                       {{ $i++ }}.
                       </td>
                      
                       <td align="left" width="20%">
                        @if($nota->tip_doc||$nota->nr_doc||$nota->data_doc)
                        {{$nota->tip_doc.' '.$nota->nr_doc."/".dateFormatAfisare($nota->data_doc)  }}
                        @endif
                       
                       </td>
                       <td align="left" width="25%">
                         {{ $nota->expl}}
                        
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
                         @if($nota->partener)
                        {{$nota->partener  }}
                        @endif
                      </td>
                      </tr>
                     @endforeach 
                  
                    <tr>
                    
                      <td width="50%" align="right" colspan="3">
                         <strong>
                          TOTAL 
                          </strong>
                      </td>
                     
                      <td width="10%" align="right" >
                       
                        <strong>
                        
                          {{ number_format(round($notadata->where('contd',$trezoreria["cont"])->sum('suma'),2),2) }}
                           </strong>
                       
                      </td>
                      <td width="10%" align="right" >
                        
                        <strong>
                        
                         {{ number_format(round($notadata->where('contc',$trezoreria["cont"])->sum('suma'),2),2) }}
                           </strong>
                         
                      </td>
                      
                      <td width="10%" align="right">
                         
                      </td>
                      <td width="20%" align="right">
                         
                      </td>
                    </tr>
               </table>
     <hr> 
    
      {{'Sold final: '}}{{number_format($soldini+=$notadata->where('contd',$trezoreria["cont"])->sum('suma')-$notadata->where('contc',$trezoreria["cont"])->sum('suma'),2)}}{{' LEI'}}
    
      <!-- <hr> -->
  
     <table class="table table-condesed" width=100% > 
                 <tr>
                  <td  width="15%">
                       
                  </td>
                  <td  width="30%" align="center" >
                  
                  </td>
                   <td  width="10%" >
                
              </td>
                   <td width="30%" align="center" >
                     CASIER,<br>
                    ___________________________________
                  </td>
                <td  width="15%" >
                
              </td>
          </tr> 
      </table> 
   
@endforeach
	
	
@stop