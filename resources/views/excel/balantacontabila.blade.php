
      <table >
            <thead>
              <tr>
                <td colspan="13">
                    <center> <strong> Balanța contabilă {{$selectie}} </strong></center>
                </td>
              </tr>
                <tr>
                <td colspan="13">
                   
                </td>
              </tr>

            
            <tr >
              <th  width="15">
                   <strong> <center>Simbol cont</center></strong>
              </th>
              <th width="20">
                    <strong> <center>Denumire cont</center></strong>
              </th>
              <th  width="15">
                    <strong> <center>Sold initial|Debitor</center></strong>
              </th>
              <th  width="15">
                    <strong> <center>Sold initial|Creditor</center></strong>
              </th>
               <th  width="15">
                    <strong> <center>Total Sume anterioare|Debit</center></strong>
              </th>
              <th  width="15">
                    <strong> <center>Total Sume anterioare|Credit</center></strong>
              </th>
               <th  width="15">
                    <strong> <center>Rulaje lunare|Debit</center></strong>
              </th>
              <th  width="15">
                    <strong> <center>Rulaje lunare|Credit</center></strong>
              </th>
               <th  width="15">
                    <strong> <center>Total sume|Debit</center></strong>
              </th>
              <th  width="15">
                    <strong> <center>Total sume|Credit</center></strong>
              </th>
               <th  width="15">
                    <strong> <center>Sold final|Debit</center></strong>
              </th>
              <th  width="15">
                    <strong> <center>Sold final|Creditor</center></strong>
              </th>
               <th  >
                   <strong> <center>Simbol cont</center></strong>
              </th>
            </tr>
            </thead>
            <tbody>
       			
                    @foreach($balanta->groupBy("grupa") as $grupa)
                          @foreach($grupa->sortBy('cont',SORT_STRING) as $cont)
                            
                            <tr width="15" > 
                             <td align="left" >
                              {{$cont->cont }} 
                             </td>
                             <td align="left" width="20">
                              {{$cont->denumire }} 
                             </td>
                             <td align="right" width="15">
                              {{ round($cont->soldiniD,2)}} 
                            </td>
                            <td align="right" width="15">
                                {{ round($cont->soldiniC,2)}} 
                             </td>
                            
                              <td align="right" width="15">
                                {{ round($cont->rulajAntD,2)}}
                            </td>
                            <td align="right" width="15">
                                {{ round($cont->rulajAntC,2)}} 
                             </td>
                           <td align="right" width="15">
                                {{ round($cont->rulajD,2)}}
                            </td>
                            <td align="right" width="15">
                                {{ round($cont->rulajC,2)}}
                             </td>
                             <td align="right" width="15">
                                {{ round($cont->rulajTotalD,2)}}
                            </td>
                            <td align="right" width="15">
                                {{ round($cont->rulajTotalC,2)}}
                             </td>
                              <td align="right" width="15">
                                {{ round($cont->soldFinalD,2)}}
                            </td>
                            <td align="right" width="15">
                                {{ round($cont->soldFinalC,2)}}
                             </td>
                             <td align="left" width="15" >
                              {{$cont->cont }} 
                             </td>
                          <!--    <td align="left" >
                             {{$cont->cont }}
                             </td> -->
                           </tr>
                           @endforeach 
                    <!-- <tr  >
                    
                     <th align="left"  colspan="2" width="15">
                       <strong> <h3> TOTAL GRUPA {{$grupa[0]->grupa}} </h3></strong>
                       </th>
                       <th align="right" width="15">
                          {{ $grupa->sum("soldiniD")}}
                      </th>
                      <th align="right" width="15">
                          {{ $grupa->sum("soldiniC")}} 
                       </th>
                     
                        <th align="right" width="15">
                          {{ $grupa->sum("rulajAntD")}}
                      </th>
                      <th align="right" width="15">
                          {{ $grupa->sum("rulajAntC")}}
                       </th>
                     <th align="right" width="15">
                          {{ $grupa->sum("rulajD")}}
                      </th>
                      <th align="right" width="15">
                          {{ $grupa->sum("rulajC")}}
                       </th>
                       <th align="right" width="15">
                          {{ $grupa->sum("rulajTotalD")}}
                      </th>
                      <th align="right" width="15">
                          {{ $grupa->sum("rulajTotalC")}}
                       </th>
                        <th align="right" width="15">
                          {{ $grupa->sum("soldFinalD")}}
                      </th>
                      <th align="right" width="15">
                          {{ $grupa->sum("soldFinalC")}}
                       </th>
                     
                    </tr> -->
                          @endforeach 
                   
                    <tr  >
                    
                     <th align="left" colspan="2" width="35">
                      <strong> <h3> TOTAL GENERAL</h3></strong>
                       </th>
                       <th align="right" width="15">
                         <strong> <h3> {{ round($balanta->sum("soldiniD"),2)}}</h3></strong>
                      </th>
                      <th align="right" width="15">
                         <strong> <h3> {{ round($balanta->sum("soldiniC"),2)}}</h3></strong>
                       </th>
                     
                        <th align="right" width="15">
                         <strong> <h3> {{ round($balanta->sum("rulajAntD"),2)}}</h3></strong>
                      </th>
                      <th align="right" width="15">
                         <strong> <h3> {{ round($balanta->sum("rulajAntC"),2)}}</h3></strong>
                       </th>
                     <th align="right" width="15">
                         <strong> <h3> {{ round($balanta->sum("rulajD"),2)}}</h3></strong>
                      </th>
                      <th align="right" width="15">
                         <strong> <h3> {{ round($balanta->sum("rulajC"),2)}}</h3></strong>
                       </th>
                       <th align="right" width="15">
                         <strong> <h3> {{ round($balanta->sum("rulajTotalD"),2)}}</h3></strong>
                      </th>
                      <th align="right" width="15">
                         <strong> <h3> {{ round($balanta->sum("rulajTotalC"),2)}}</h3></strong>
                       </th>
                        <th align="right" width="15">
                         <strong> <h3> {{ round($balanta->sum("soldFinalD"),2)}}</h3></strong>
                      </th>
                      <th align="right" width="15">
                         <strong> <h3> {{ round($balanta->sum("soldFinalC"),2)}}</h3></strong>
                       </th>
                     
                    </tr>
                  </tbody>
               </table>
    
      
   
     
      
     


	
