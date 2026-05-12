
<br><br><br><br><br><br>
   @foreach($notacontabila->groupby("data_nota") as $notadata)
   <center>  {{$denregistru.' PENTRU DATA DE '.dateFormatAfisare($notadata[0]->data_nota).' '.$trezoreria["denumire"]}}  </center>
    {{'Sold initial: '. number_format($soldini,2).' LEI'}}
   
      <table class="text-sm table table-condesed" style="border-collapse: collapse; " width=100%  >
            
            <tr style="border-bottom:1pt solid black; page-break-inside: avoid !important;">
              <th align ="center"  width=5>
                    <center>Nr <br> crt</center>
              </th>
              <th align ="center"  width=20>
                    <center>Document <br> (tip,nr,data)</center>
              </th>
              <th align ="center"  width=25>
                    Explicatie
              </th>
              <th align ="center"  width=10>
                    Încasări
              </th>
              <th align ="center"  width=10>
                    Plăți
              </th>
              <th align ="center"  width=10>
                    Cont corespondent
                </th>
                <th align ="center"  width=20>
                    Partener
                </th>
            </tr>
           
      
                    @foreach($notadata as $nota)
                      
                      <tr style="border-bottom:1pt solid black; page-break-inside: avoid !important;"> 
                       <td align="center" width=5>
                       {{ $i++ }}.
                       </td>
                      
                       <td align="left" width=20>
                        @if($nota->tip_document||$nota->nr_document||$nota->data_document)
                        {{$nota->tip_document.' '.$nota->nr_document."/".dateFormatAfisare($nota->data_document)  }}
                        @endif
                       
                       </td>
                       <td align="left" width=25>
                         {{ $nota->explicatie}}
                        
                      </td>
                      <td align="right" width=10>
                        @if($nota->contd==$trezoreria['cont'])
                          {{ number_format($nota->suma,2) }}
                        @endif
                      </td>
                      <td align="right" width=10>
                        @if($nota->contc==$trezoreria['cont'])
                          {{ number_format($nota->suma,2) }}
                        @endif
                     </td>
                     
                      <td align="center" width=10>
                        @if($nota->contd==$trezoreria['cont'])
                          {{ $nota->contc }}
                        @endif
                        @if($nota->contc==$trezoreria['cont'])
                          {{ $nota->contd }}
                        @endif
                      </td>
                      <td align="left" width=20>
                         @if($nota->denumire_partener)
                        {{$nota->denumire_partener  }}
                        @endif
                      </td>
                      </tr>
                     @endforeach 
                  
                    <tr>
                    
                      <td width=50 align="right" colspan="3">
                         <strong>
                          TOTAL 
                          </strong>
                      </td>
                     
                      <td width=10 align="right" >
                       
                        <strong>
                        
                          {{ number_format(round($notadata->where('tip_operatiune','Incasare')->sum('suma'),2),2) }}
                           </strong>
                       
                      </td>
                      <td width=10 align="right" >
                        
                        <strong>
                        
                         {{ number_format(round($notadata->where('tip_operatiune','Plata')->sum('suma'),2),2) }}
                           </strong>
                         
                      </td>
                      
                      <td width=10 align="right">
                         
                      </td>
                      <td width=20 align="right">
                         
                      </td>
                    </tr>
               </table>
   
    
      {{'Sold final: '}}{{number_format($soldini+=$notadata->where('tip_operatiune','Incasare')->sum('suma')-$notadata->where('tip_operatiune','Plata')->sum('suma'),2)}}{{' LEI'}}
    
      <!-- <hr> -->
  
     <br><br><br>
   
@endforeach
	
