
  <br><br><br><br>
   <center> <h3> Registru jurnal </h3> </center>
   <center> <h3>{{$selectie}} ANALITIC </h3></center>
  
      <table  width=100%  >
            
            <tr >
              <th align ="center" rowspan="2" width="5">
                    <center>Nr <br> crt</center>
              </th>
              <th align ="center" rowspan="2" width="10">
                    <center>Data</center>
              </th>
              <th align ="center" rowspan="2" width="5">
                    <center>Nr <br> nota</center>
              </th>
              <th align ="center" rowspan="2" width="20">
                    <center>Document <br> (tip,nr,data)</center>
              </th>
              <th align ="center" rowspan="2" width="20">
                    Explicatie
              </th>
              <th align ="center" colspan="2" width="20">
                    Simbol conturi
              </th>
              
                <th align ="center" colspan="2" width="20">
                    Sume 
                </th>
                
                
            </tr>
            <tr>
              <th align ="center" width="10">
                    Debitoare
              </th>
              
                <th align ="center" width="10">
                    Creditoare 
                </th>
                <th align ="center" width="10">
                    Debitoare
              </th>
              
                <th align ="center" width="10">
                    Creditoare 
                </th>
            </tr>
     
                    @foreach($notacontabila as $nota)
                      
                      <tr style="border-bottom:1pt solid black; page-break-inside: avoid !important;"> 
                       <td align="center" width="5">
                       {{ $i++ }}.
                       </td>
                       <td align="center" width="10">
                       {{dateFormatAfisare($nota->data_nota) }}
                       </td>
                        <td align="center" width="5">
                       {{ $nota->nr_nota }}
                       </td>
                       <td align="left" width="20">
                        @if($nota->tip_doc||$nota->nr_doc||$nota->data_doc)
                        {{$nota->tip_doc  }}
                        {{$nota->nr_doc."/".dateFormatAfisare($nota->data_doc)  }}
                        @endif
                       </td>
                       <td align="left" width="20">
                         {{ $nota->expl}}
                        
                      </td>
                      <td align="left" width="10">
                      {{ $nota->contd }}
                      </td>
                      <td align="left" width="10">
                      {{ $nota->contc }}
                         
                      </td>
                      <td align="right" width="10">
                     {{ $nota->suma}}
                         
                      </td>
                      <td align="right" width="10">
                     {{ $nota->suma}}
                         
                      </td>
                      </tr>
                     @endforeach 
                    
                    <tr>
                    
                      <td width="80" align="right" colspan="7">
                         <strong>
                          TOTAL 
                          </strong>
                      </td>
                     
                      <td width="10" align="right" >
                        <h4>
                        <strong>
                        
                          {{ $notacontabila->sum('suma') }}
                           </strong>
                         </h4>
                      </td>
                      <td width="10" align="right" >
                        <h4>
                        <strong>
                        
                          {{ $notacontabila->sum('suma') }}
                           </strong>
                         </h4>
                      </td>
                    </tr>
               </table>
    
   

	