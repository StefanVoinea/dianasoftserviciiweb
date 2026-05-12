<br><br><br><br><br>
   @foreach($notacontabila->groupby("data_nota") as $notadata)
   <center> <h3> {{$denregistru.' PENTRU DATA DE '.dateFormatAfisare($notadata[0]->data_nota)}}  </h3> </center>
   <h3> {{$trezoreria["denumire"]}}</h3>
   <h4>{{'Sold initial: '.number_format($soldini,2).' '.$trezoreria["tip_valuta"]}}</h4>
   
      <table class="table table-condesed" width=100%  >
            
            <tr >
              <th align ="center"  width=5>
                    <center>Nr <br> crt</center>
              </th>
              <th align ="center"  width=20>
                    <center>Document <br> (tip,nr,data,partener)</center>
              </th>
              <th align ="center"  width=40>
                    Explicatie
              </th>
              <th align ="center"  width=10>
                    Încasări
              </th>
              <th align ="center"  width=10>
                    Plăți
              </th>
                <th align ="center"  width=5>
                    Curs 
                </th>
                <th align ="center"  width=20>
                    Corespondent LEI 
                </th>
                 <th align ="center"  width=20>
                    Cont corespondent
                </th>
            </tr>
           
     </table>
      
                   
     				<table class="table table-condesed" style="border-collapse: collapse; " width=100%>      
                    @foreach($notadata as $nota)
                      
                      <tr style="border-bottom:1pt solid black; page-break-inside: avoid !important;"> 
                       <td align="center" width=5>
                       {{ $i++ }}.
                       </td>
                      
                       <td align="left" width=30>
                        @if($nota->tip_document||$nota->nr_document||$nota->data_document)
                        {{$nota->tip_document.' '.$nota->nr_document."/".dateFormatAfisare($nota->data_document)  }}
                        @endif
                        @if($nota->denumire_partener)
                        <br>
                        {{$nota->denumire_partener  }}
                        @endif
                       </td>
                       <td align="left" width=40>
                         {{ $nota->explicatie}}
                        
                      </td>
                      <td align="right" width=10>
                        @if($nota->contd==$trezoreria['cont'])
                          {{ number_format($nota->suma_in_valuta,2) }}
                        @endif
                      </td>
                      <td align="right" width=10>
                        @if($nota->contc==$trezoreria['cont'])
                          {{ number_format($nota->suma_in_valuta,2) }}
                        @endif
                     </td>
                      <td align="right" width=5>
                     {{ number_format($nota->curs,4)}}
                         
                      </td>
                      <td align="right" width=20>
                     {{ number_format($nota->suma,2)}}
                         
                      </td>
                      <td align="center" width=20>
                        @if($nota->contd==$trezoreria['cont'])
                          {{ $nota->contc }}
                        @endif
                        @if($nota->contc==$trezoreria['cont'])
                          {{ $nota->contd }}
                        @endif
                      </td>
                      </tr>
                     @endforeach 
                    </table>
                   
             
   <br>
     <table class="table table-condesed" width=100%> 
                    <tr>
                    
                      <td width=55 align="right" colspan="3">
                         <strong>
                          TOTAL 
                          </strong>
                      </td>
                     
                      <td width=10 align="right" >
                        <h4>
                        <strong>
                        
                          {{ number_format(round($notadata->where('tip_operatiune','Incasare')->sum('suma_in_valuta'),2),2) }}
                           </strong>
                         </h4>
                      </td>
                      <td width=10 align="right" >
                        <h4>
                        <strong>
                        
                         {{ number_format(round($notadata->where('tip_operatiune','Plata')->sum('suma_in_valuta'),2),2) }}
                           </strong>
                         </h4>
                      </td>
                      <td width=5 align="right">
                         
                      </td>
                      <td width=10 align="right">
                         
                      </td>
                      <td width=10 align="right">
                         
                      </td>
                    </tr>
               </table>
     <hr> 
     <h4>
      {{'Sold final: '}}{{number_format($soldini+=$notadata->where('tip_operatiune','Incasare')->sum('suma_in_valuta')-$notadata->where('tip_operatiune','Plata')->sum('suma_in_valuta'),2)}}{{' '.$trezoreria["tip_valuta"]}}
    </h4>
    
     <br>
    
<br><br><br>
@endforeach