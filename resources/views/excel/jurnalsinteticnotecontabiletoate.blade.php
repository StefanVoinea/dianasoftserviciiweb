
      <table class="table table-condesed" width="100"  >
            
            <tr >
              <th align ="center" width="20">
                    <center>Nr <br> crt</center>
              </th>
              <th align ="center" width="40">
                    <center>Nr <br> nota</center>
              </th>
              
                <th align ="center" width="40">
                    Suma 
                </th>
                
                
            </tr>
           
       
                    @foreach($notacontabila->groupby("nr_nota") as $nota)
                      
                      <tr style="border-bottom:1pt solid black; page-break-inside: avoid !important;"> 
                       <td align="center" width="20">
                       {{ $i++ }}.
                       </td>
                        <td align="center" width="40">
                       {{ $nota[0]->nr_nota }}
                       </td>
                      
                      <td align="right" width="40">
                     {{ number_format($nota->sum("suma"),2)}}
                         
                      </td>
                     
                      </tr>
                     @endforeach
                      
                 
                    <tr>
                    
                      <td width="80" align="right" >
                         <strong>
                          TOTAL 
                          </strong>
                      </td>
                     
                      <td width="20" align="right" >
                        <h4>
                        <strong>
                        
                          {{ number_format(round($notacontabila->sum('suma'),2),2) }}
                           </strong>
                         </h4>
                      </td>
                     
                    </tr>
               </table>
            