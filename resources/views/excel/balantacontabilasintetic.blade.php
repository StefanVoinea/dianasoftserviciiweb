
      <table class="text-sm table table-condesed" border="1" style="border-collapse: collapse;" width=100>
            <thead>
              <tr>
                <td colspan="12">
                    <center> <strong> Balanța contabilă {{$selectie}} </strong></center>
                </td>
              </tr>
                <tr>
                <td colspan="12">
                   
                </td>
              </tr>

            <tr >
              <th align ="center"  width="5">
                   
              </th>
              <th align ="center" width="10">
                   
              </th>
              <th align ="center" colspan="2" width="16">
                <strong> <center>  Solduri de deschidere </center> </strong>
              </th>
               <th align ="center" colspan="2" width="16">
                   <strong> <center> Anterior </center></strong>
              </th>
              <th align ="center" colspan="2" width="16">
                   <strong> <center> În lună </center></strong>
              </th>
               <th align ="center" colspan="2" width="16">
                   <strong> <center> Total</center></strong>
              </th>   
              <th align ="center" colspan="2" width="16">
                   <strong> <center>Sold</center></strong>
              </th>    
            
            </tr>
            <tr style=" page-break-inside: avoid !important;">
              <th align ="center"  width="5">
                   <strong> <center>Cont</center></strong>
              </th>
              <th align ="center" width="10">
                    <strong> <center>Denumire</center></strong>
              </th>
              <th align ="center"  width="8">
                    <strong> <center>Debit</center></strong>
              </th>
              <th align ="center"  width="8">
                    <strong> <center>Credit</center></strong>
              </th>
               <th align ="center"  width="8">
                    <strong> <center>Debit</center></strong>
              </th>
              <th align ="center"  width="8">
                    <strong> <center>Credit</center></strong>
              </th>
               <th align ="center"  width="8">
                    <strong> <center>Debit</center></strong>
              </th>
              <th align ="center"  width="8">
                    <strong> <center>Credit</center></strong>
              </th>
               <th align ="center"  width="8">
                    <strong> <center>Debit</center></strong>
              </th>
              <th align ="center"  width="8">
                    <strong> <center>Credit</center></strong>
              </th>
               <th align ="center"  width="8">
                    <strong> <center>Debit</center></strong>
              </th>
              <th align ="center"  width="8">
                    <strong> <center>Credit</center></strong>
              </th>
            </tr>
            <thead>
       			
                    @foreach($balanta->groupBy("grupa") as $grupa)
                          @foreach($grupa->sortBy('cont',SORT_STRING)->groupBy("cont_sintetic") as $cont)
                            
                            <tr style="height:30px; page-break-inside: avoid !important;"> 
                             <td align="left" width="5">
                              {{$cont[0]->cont_sintetic }} 
                             </td>
                             <td align="left" width="10">
                              {{$cont[0]->denumire }} 
                             </td>
                             <td align="right" width="8">
                              {{ $cont->sum("soldiniD")}} 
                            </td>
                            <td align="right" width="8">
                                {{ $cont->sum("soldiniC")}} 
                             </td>
                            
                              <td align="right" width="8">
                                {{ $cont->sum("rulajAntD")}}
                            </td>
                            <td align="right" width="8">
                                {{ $cont->sum("rulajAntC")}} 
                             </td>
                           <td align="right" width="8">
                                {{ $cont->sum("rulajD")}}
                            </td>
                            <td align="right" width="8">
                                {{ $cont->sum("rulajC")}}
                             </td>
                             <td align="right" width="8">
                                {{ $cont->sum("rulajTotalD")}}
                            </td>
                            <td align="right" width="8">
                                {{ $cont->sum("rulajTotalC")}}
                             </td>
                              <td align="right" width="8">
                                {{ $cont->sum("soldFinalD")}}
                            </td>
                            <td align="right" width="8">
                                {{ $cont->sum("soldFinalC")}}
                             </td>
                           </tr>
                            @if($cont->count()>1)
                           @foreach($cont->sortBy('cont',SORT_STRING) as $contA)
                            
                            <tr style="height:30px; page-break-inside: avoid !important;"> 
                             <td align="left" width="5">
                              {{$contA->cont }} 
                             </td>
                             <td align="left" width="10">
                              {{$contA->denumire }} 
                             </td>
                             <td align="right" width="8">
                              {{ number_format($contA->soldiniD,2)}} 
                            </td>
                            <td align="right" width="8">
                                {{ number_format($contA->soldiniC,2)}} 
                             </td>
                            
                              <td align="right" width="8">
                                {{ number_format($contA->rulajAntD,2)}}
                            </td>
                            <td align="right" width="8">
                                {{ number_format($contA->rulajAntC,2)}} 
                             </td>
                           <td align="right" width="8">
                                {{ number_format($contA->rulajD,2)}}
                            </td>
                            <td align="right" width="8">
                                {{ number_format($contA->rulajC,2)}}
                             </td>
                             <td align="right" width="8">
                                {{ number_format($contA->rulajTotalD,2)}}
                            </td>
                            <td align="right" width="8">
                                {{ number_format($contA->rulajTotalC,2)}}
                             </td>
                              <td align="right" width="8">
                                {{ number_format($contA->soldFinalD,2)}}
                            </td>
                            <td align="right" width="8">
                                {{ number_format($contA->soldFinalC,2)}}
                             </td>
                          
                           </tr>
                         
                           @endforeach 
                             @endif
                           @endforeach 
                    <tr style="height:50px;page-break-inside: avoid !important;" >
                    
                     <th align="left"  colspan="2" width="15">
                       <strong> <h3> TOTAL GRUPA {{$grupa[0]->grupa}} </h3></strong>
                       </th>
                       <th align="right" width="8">
                          {{ $grupa->sum("soldiniD")}}
                      </th>
                      <th align="right" width="8">
                          {{ $grupa->sum("soldiniC")}} 
                       </th>
                     
                        <th align="right" width="8">
                          {{ $grupa->sum("rulajAntD")}}
                      </th>
                      <th align="right" width="8">
                          {{ $grupa->sum("rulajAntC")}}
                       </th>
                     <th align="right" width="8">
                          {{ $grupa->sum("rulajD")}}
                      </th>
                      <th align="right" width="8">
                          {{ $grupa->sum("rulajC")}}
                       </th>
                       <th align="right" width="8">
                          {{ $grupa->sum("rulajTotalD")}}
                      </th>
                      <th align="right" width="8">
                          {{ $grupa->sum("rulajTotalC")}}
                       </th>
                        <th align="right" width="8">
                          {{ $grupa->sum("soldFinalD")}}
                      </th>
                      <th align="right" width="8">
                          {{ $grupa->sum("soldFinalC")}}
                       </th>
                     
                    </tr>
                          @endforeach 
                   
                    <tr style="height:50px;  page-break-inside: avoid !important;" >
                    
                     <th align="left" colspan="2" width="15">
                      <strong> <h3> TOTAL GENERAL</h3></strong>
                       </th>
                       <th align="right" width="8">
                          {{ $balanta->sum("soldiniD")}}
                      </th>
                      <th align="right" width="8">
                          {{ $balanta->sum("soldiniC")}}
                       </th>
                     
                        <th align="right" width="8">
                          {{ $balanta->sum("rulajAntD")}}
                      </th>
                      <th align="right" width="8">
                          {{ $balanta->sum("rulajAntC")}}
                       </th>
                     <th align="right" width="8">
                          {{ $balanta->sum("rulajD")}}
                      </th>
                      <th align="right" width="8">
                          {{ $balanta->sum("rulajC")}}
                       </th>
                       <th align="right" width="8">
                          {{ $balanta->sum("rulajTotalD")}}
                      </th>
                      <th align="right" width="8">
                          {{ $balanta->sum("rulajTotalC")}}
                       </th>
                        <th align="right" width="8">
                          {{ $balanta->sum("soldFinalD")}}
                      </th>
                      <th align="right" width="8">
                          {{ $balanta->sum("soldFinalC")}}
                       </th>
                     
                    </tr>
               </table>
    
      
   
     
      
     


	
