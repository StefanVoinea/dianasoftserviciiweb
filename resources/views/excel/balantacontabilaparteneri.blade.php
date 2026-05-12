
          

             <center> <h3> Centralizator conturi {{$selectie}} </h3></center>
      <table >
            
            <tr >
              <th align ="center"  width="5">
                   
              </th>
              <th align ="center" width="10">
                   
              </th>
            
               <th align ="center" colspan="2" width="16">
                   Anterior
              </th>
              <th align ="center" colspan="2" width="16">
                   În lună
              </th>
               <th align ="center" colspan="2" width="16">
                   Total
              </th>   
              <th align ="center" colspan="2" width="16">
                   Sold
              </th>    
            
            </tr>
            <tr >
              <th align ="center"  width="5">
                    <center>Cont</center>
              </th>
              <th align ="center" width="10">
                    <center>Partener</center>
              </th>
            
               <th align ="center"  width="8">
                    <center>Debit</center>
              </th>
              <th align ="center"  width="8">
                    <center>Credit</center>
              </th>
               <th align ="center"  width="8">
                    <center>Debit</center>
              </th>
              <th align ="center"  width="8">
                    <center>Credit</center>
              </th>
               <th align ="center"  width="8">
                    <center>Debit</center>
              </th>
              <th align ="center"  width="8">
                    <center>Credit</center>
              </th>
               <th align ="center"  width="8">
                    <center>Debit</center>
              </th>
              <th align ="center"  width="8">
                    <center>Credit</center>
              </th>
            </tr>
            
                          @foreach($balanta->sortBy('cont',SORT_STRING) as $cont)
                            
                            <tr > 
                             <td align="left" width="5">
                              {{$cont->cont }} 
                             </td>
                             <td align="left" width="10">
                              {{$cont->denumire_partener }} 
                             </td>
                            
                            
                              <td align="right" width="8">
                                {{ number_format($cont->rulajAntD,2)}}
                            </td>
                            <td align="right" width="8">
                                {{ number_format($cont->rulajAntC,2)}} 
                             </td>
                           <td align="right" width="8">
                                {{ number_format($cont->rulajD,2)}}
                            </td>
                            <td align="right" width="8">
                                {{ number_format($cont->rulajC,2)}}
                             </td>
                             <td align="right" width="8">
                                {{ number_format($cont->rulajTotalD,2)}}
                            </td>
                            <td align="right" width="8">
                                {{ number_format($cont->rulajTotalC,2)}}
                             </td>
                              <td align="right" width="8">
                                {{ number_format($cont->soldFinalD,2)}}
                            </td>
                            <td align="right" width="8">
                                {{ number_format($cont->soldFinalC,2)}}
                             </td>
                      
                           </tr>
                           @endforeach 
                   
                   
                    <tr  >
                    
                     <th align="left" colspan="2" width="15">
                       TOTAL GENERAL
                       </th>
                      
                        <th align="right" width="8">
                          {{ number_format($balanta->sum("rulajAntD"),2)}}
                      </th>
                      <th align="right" width="8">
                          {{ number_format($balanta->sum("rulajAntC"),2)}}
                       </th>
                     <th align="right" width="8">
                          {{ number_format($balanta->sum("rulajD"),2)}}
                      </th>
                      <th align="right" width="8">
                          {{ number_format($balanta->sum("rulajC"),2)}}
                       </th>
                       <th align="right" width="8">
                          {{ number_format($balanta->sum("rulajTotalD"),2)}}
                      </th>
                      <th align="right" width="8">
                          {{ number_format($balanta->sum("rulajTotalC"),2)}}
                       </th>
                        <th align="right" width="8">
                          {{ number_format($balanta->sum("soldFinalD"),2)}}
                      </th>
                      <th align="right" width="8">
                          {{ number_format($balanta->sum("soldFinalC"),2)}}
                       </th>
                  
                    </tr>
               </table>
    
     

  
