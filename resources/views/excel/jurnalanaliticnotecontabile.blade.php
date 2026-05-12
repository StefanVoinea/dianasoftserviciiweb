
      <table class="table table-condesed" width=100  >
            
            <tr >
              <th align ="center" width="5">
                    <center>Nr <br> crt</center>
              </th>
              <th align ="center" width="5">
                    <center>Nr <br> nota</center>
              </th>
              <th align ="center" width="10">
                    <center>Data</center>
              </th>
              <th align ="center"  width="25">
                    <center>Document <br> (tip,nr,data)</center>
              </th>
              <th align ="center"  width="25">
                    Explicatie
              </th>
              <th align ="center"  width="15">
                    Debit
              </th>
              <th align ="center" width="15">
                    Credit
              </th>
                <th align ="center" rowspan="2" width="15">
                    Suma 
                </th>
                
                
            </tr>
           
     </table>
      <hr>
                   
     				<table class="table table-condesed" style="border-collapse: collapse; " width=100>      
                    @foreach($notacontabila as $nota)
                      
                      <tr style="border-bottom:1pt solid black; page-break-inside: avoid !important;"> 
                       <td align="center" width="5">
                       {{ $i++ }}.
                       </td>
                        <td align="center" width="5">
                       {{ $nota->nr_nota }}
                       </td>
                       <td align="center" width="10">
                       {{dateFormatAfisare($nota->data_nota) }}
                       </td>
                       
                       <td align="left" width="25">
                        @if($nota->tip_doc||$nota->nr_doc||$nota->data_doc)
                        {{$nota->tip_doc  }}
                        {{$nota->nr_doc."/".dateFormatAfisare($nota->data_doc)  }}
                        @endif
                       </td>
                       <td align="left" width="25">
                         {{ $nota->expl}}
                        
                      </td>
                      <td align="left" width="15">
                      {{ $nota->contd }}
                      </td>
                      <td align="left" width="15">
                      {{ $nota->contc }}
                         
                      </td>
                      <td align="right" width="15">
                     {{ number_format($nota->suma,2)}}
                         
                      </td>
                     
                      </tr>
                     @endforeach 
                 
                    <tr>
                    
                      <td width="90" align="right" colspan="7">
                         <strong>
                          TOTAL 
                          </strong>
                      </td>
                     
                      <td width="15" align="right" >
                        <h4>
                        <strong>
                        
                          {{ number_format(round($notacontabila->sum('suma'),2),2) }}
                           </strong>
                         </h4>
                      </td>
                     
                    </tr>
               </table>
    