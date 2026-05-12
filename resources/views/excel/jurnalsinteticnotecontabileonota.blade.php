
      <table class="table table-condesed" width=50  >
            
            <tr >
              <th align ="center" width="20">
                    <center>Nr <br> crt</center>
              </th>
              <th align ="center" width="20">
                    <center>Nr <br> nota</center>
              </th>
              
              <th align ="center"  width="20">
                    Debit
              </th>
              <th align ="center" width="20">
                    Credit
              </th>
                <th align ="center" width="20">
                    Suma 
                </th>
                
                
            </tr>
           
     </table>
      <hr>
                   
     				<table class="table table-condesed" style="border-collapse: collapse; " width=50>      
                    @foreach($notacontabila->groupby("contd") as $notagrupat)
                      @foreach($notagrupat->groupby("contc") as $nota)
                      <tr style="border-bottom:1pt solid black; page-break-inside: avoid !important;"> 
                       <td align="center" width="20">
                       {{ $i++ }}.
                       </td>
                        <td align="center" width="20">
                       {{ $nota[0]->nr_nota }}
                       </td>
                      
                      <td align="left" width="20">
                      {{ $nota[0]->contd }}
                      </td>
                      <td align="left" width="20">
                      {{ $nota[0]->contc }}
                         
                      </td>
                      <td align="right" width="20">
                     {{ number_format($notagrupat->sum("suma"),2)}}
                         
                      </td>
                     
                      </tr>
                     @endforeach
                      @endforeach 
                  
                    <tr>
                    
                      <td width="90" align="right" colspan="7">
                         <strong>
                          TOTAL 
                          </strong>
                      </td>
                     
                      <td width="10" align="right" >
                        <h4>
                        <strong>
                        
                          {{ number_format(round($notacontabila->sum('suma'),2),2) }}
                           </strong>
                         </h4>
                      </td>
                     
                    </tr>
               </table>
  