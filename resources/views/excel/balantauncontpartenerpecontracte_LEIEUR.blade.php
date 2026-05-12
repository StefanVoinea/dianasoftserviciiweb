
      <table class="table table-condesed text-sm" border="1" style="border-collapse: collapse;" width=100>
           <thead>
      <tr>
        <th colspan="8" width=100>
        </th>
        
      </tr>  
      <tr>
        <th colspan="8"  width=100>
        </th>
      </tr>  
      <tr>
        <th colspan="8"  width=100>
        </th>
      </tr>  
      <tr>
        <th colspan="8" width=100>
        </th>
      </tr>  
     
            <tr>
              <td align="left" colspan="8"  width=100>
             <h3> Balanța {{$selectie}} {{$selectie2}}</h3>
              </td>
            </tr>  
              @if (!$balantadetaliata)
            <tr >
              <th align ="center" rowspan="2" width=5>
                   <center>Nr crt.</center>
              </th>
              <th align ="center" rowspan="2" width=15>
                    <center>Partener</center>
              </th>
              <th align ="center" rowspan="2" width=7>
                    <center>Nr contract</center>
              </th>
              <th align ="center" rowspan="2" width=8>
                    <center>Data contract</center>
              </th>
            
               <th align ="center" colspan="2" width=20>
                   Anterior LEI
              </th>
              <th align ="center" colspan="2" width=20>
                   Rulaj in perioada LEI
              </th>
              
              <th align ="center" colspan="2" width=20>
                   Sold final LEI
              </th>    

               <th align ="center" colspan="2" width=20>
                   Anterior EUR
              </th>
              <th align ="center" colspan="2" width=20>
                   Rulaj in perioada EUR
              </th>
              
              <th align ="center" colspan="2" width=20>
                   Sold final EUR
              </th>    
             <!--   <th align ="center"  rowspan="2" width=5>
                    <center>Cont</center>
              </th> -->
            </tr>
            <tr style=" page-break-inside: avoid !important;">
            
             
               <th align ="center"  width=10>
                    <center>Debit</center>
              </th>
              <th align ="center"  width=10>
                    <center>Credit</center>
              </th>
               <th align ="center"  width=10>
                    <center>Debit</center>
              </th>
              <th align ="center"  width=10>
                    <center>Credit</center>
              </th>
               
               <th align ="center"  width=10>
                    <center>Debit</center>
              </th>
              <th align ="center"  width=10>
                    <center>Credit</center>
              </th>
               <th align ="center"  width=10>
                    <center>Debit</center>
              </th>
              <th align ="center"  width=10>
                    <center>Credit</center>
              </th>
               <th align ="center"  width=10>
                    <center>Debit</center>
              </th>
              <th align ="center"  width=10>
                    <center>Credit</center>
              </th>
               
               <th align ="center"  width=10>
                    <center>Debit</center>
              </th>
              <th align ="center"  width=10>
                    <center>Credit</center>
              </th>
            </tr>
            @endif
          </thead>
          <tbody>
       				<!-- <table class="table table-condesed" style="border-collapse: collapse;" width=100%>     -->
                          @foreach($balanta->sortBy('denumire',SORT_STRING) as $partener)
                            @if (!$balantadetaliata)
                            <tr style="height:30px; page-break-inside: avoid !important;"> 
                             <td align="center" width=5>
                              {{$i++ }} 
                             </td>
                             <td align="left" width=15>
                              {{$partener->denumire }} 
                             </td>
                              <td align="left" width=7>
                              {{$partener->nr_contract }} 
                             </td>
                            <td align="left" width=8>
                              {{dateFormatAfisare($partener->data_contract) }} 
                             </td>
                           
                              <td align="right" width=10>
                                {{ number_format($partener->soldIniPerioadaD,2)}}
                            </td>
                            <td align="right" width=10>
                                {{ number_format($partener->soldIniPerioadaC,2)}} 
                             </td>
                           <td align="right" width=10>
                                {{ number_format($partener->rulajD,2)}}
                            </td>
                            <td align="right" width=10>
                                {{ number_format($partener->rulajC,2)}}
                             </td>
                            
                              <td align="right" width=10>
                                {{ number_format($partener->soldFinalD,2)}}
                            </td>
                            <td align="right" width=10>
                                {{ number_format($partener->soldFinalC,2)}}
                             </td>
                         
                          
                              <td align="right" width=10>
                                {{ number_format($partener->soldIniPerioadaD_EUR,2)}}
                            </td>
                            <td align="right" width=10>
                                {{ number_format($partener->soldIniPerioadaC_EUR,2)}} 
                             </td>
                           <td align="right" width=10>
                                {{ number_format($partener->rulajD_EUR,2)}}
                            </td>
                            <td align="right" width=10>
                                {{ number_format($partener->rulajC_EUR,2)}}
                             </td>
                            
                              <td align="right" width=10>
                                {{ number_format($partener->soldFinalD_EUR,2)}}
                            </td>
                            <td align="right" width=10>
                                {{ number_format($partener->soldFinalC_EUR,2)}}
                             </td>
                         
                           </tr>
                           @endif
                           @if ($balantadetaliata)
                            </tbody>
               </table>
                           <br>
                                  <table class="table table-condesed text-sm" border="1" style="border-collapse: collapse;" width=100%>
                                    <thead>
                                  <tr style=" page-break-inside: avoid !important;">
                                  
                                    <th align ="center"  width=5>
                                          <center>Nr crt.</center>
                                    </th>
                                    <th align ="center" width=10>
                                          <center>Tip document</center>
                                    </th>
                                   <th align ="center" width=10>
                                          <center>Nr document</center>
                                    </th>
                                    <th align ="center" width=10>
                                          <center>Data document</center>
                                    </th>
                                  <th align ="center" width=5>
                                          <center>Tip valuta</center>
                                    </th>
                                    <th align ="center"  width=10>
                                          <center>Cont</center> 
                                    </th> 
                                   
                                    <th align ="center"  width=10>
                                      Rulaj Debit    
                                    </th>
                                    <th align ="center"  width=10>
                                       Rulaj Credit   
                                    </th>
                                     <th align ="center"  width=10>
                                       Sold  
                                    </th>
                                     <th align ="center"  width=20>
                                          <center>Explicatie</center>
                                    </th> 
                                  </tr>
                                </thead>
                                <tbody>
                                  @foreach($balantadetaliata->groupBy("luna") as $docLuna)
                                  @foreach($docLuna as $doc)
                                    <tr style=" page-break-inside: avoid !important;">
                                   
                                    <td align ="center"  width=5>
                                          <center>{{$j++}}</center>
                                    </td>
                                    <td align ="left" width=10>
                                          {{$doc->tip_document}}
                                    </td>
                                     <td align ="left" width=10>
                                          
                                          {{$doc->nr_document}}
                                    </td>
                                     <td align ="left" width=10>
                                         
                                          {{dateFormatAfisare($doc->data_document)}}
                                    </td>
                                   <td align ="center"  width=5>
                                         LEI
                                    </td> 
                                     <td align ="center"  width=10>
                                         {{$doc->cont_partener}}
                                    </td> 
                                  
                                     <td align ="right"  width=10>
                                          {{$doc->debit!=0?number_format($doc->debit,2):''}}
                                    </td>
                                    <td align ="right"  width=10>
                                          {{$doc->credit!=0?number_format($doc->credit,2):''}}
                                    </td>
                                    <td align ="right"  width=10>
                                        
                                          {{number_format($doc->sold,2)}}
                                       
                                    </td>
                                       <td align ="left"  width=20>
                                          {{$doc->expl}}
                                    </td> 
                                     
                                  </tr>
                                  <tr style=" page-break-inside: avoid !important;">
                                   
                                    <td align ="center"  width=5>
                                          
                                    </td>
                                    <td align ="left" width=10>
                                          
                                    </td>
                                     <td align ="left" width=10>
                                          
                                          
                                    </td>
                                     <td align ="left" width=10>
                                         
                                          
                                    </td>
                                   <td align ="center"  width=5>
                                         EUR
                                    </td> 
                                     <td align ="center"  width=10>
                                         
                                    </td> 
                                  
                                     <td align ="right"  width=10>
                                          {{$doc->debit!=0?number_format($doc->debit_EUR,2):''}}
                                    </td>
                                    <td align ="right"  width=10>
                                          {{$doc->credit!=0?number_format($doc->credit_EUR,2):''}}
                                    </td>
                                    <td align ="right"  width=10>
                                        
                                          {{number_format($doc->sold_EUR,2)}}
                                       
                                    </td>
                                       <td align ="left"  width=20>
                                          
                                    </td> 
                                     
                                  </tr>
                                  @endforeach
                                   <tr style=" page-break-inside: avoid !important;">
                                   
                                    <td align ="center"  colspan="5" width=65>
                                         
                                    
                                       <strong>   Total rulaj {{$docLuna->max("lunalitere") }} </strong>
                                    </td>
                                  
                                     <td align ="right"  width=10>
                                       <strong>   {{number_format($docLuna->sum("debit"),2)}} </strong>
                                    </td>
                                    <td align ="right"  width=10>
                                        <strong>  {{number_format($docLuna->sum("credit"),2)}} </strong>
                                    </td>
                                    <td align ="right"  width=10>
                                        
                                       <strong>   {{number_format($doc->sold,2)}} </strong>
                                       
                                    </td>
                                    
                                     
                                
                                     <td align ="right"  width=10>
                                       <strong>   {{number_format($docLuna->sum("debit_EUR"),2)}} </strong>
                                    </td>
                                    <td align ="right"  width=10>
                                        <strong>  {{number_format($docLuna->sum("credit_EUR"),2)}} </strong>
                                    </td>
                                    <td align ="right"  width=10>
                                        
                                       <strong>   {{number_format($doc->sold_EUR,2)}} </strong>
                                       
                                    </td>
                                    
                                     
                                  </tr>
                                  @endforeach
                                  <tr style=" page-break-inside: avoid !important;">
                                   
                                    
                                    <th align ="center" colspan="5" width=65>
                                         TOTAL
                                    </th>
                                  
                                     <th align ="right"  width=10>
                                          {{number_format($balantadetaliata->sum("debit"),2)}}
                                    </th>
                                    <th align ="right"  width=10>
                                          {{number_format($balantadetaliata->sum("credit"),2)}}
                                    </th>
                                    <th align ="right"  width=10>
                                        
                                          {{number_format($doc->sold,2)}}
                                       
                                    </th>
                                    
                                     
                                
                                     <th align ="right"  width=10>
                                          {{number_format($balantadetaliata->sum("debit_EUR"),2)}}
                                    </th>
                                    <th align ="right"  width=10>
                                          {{number_format($balantadetaliata->sum("credit_EUR"),2)}}
                                    </th>
                                    <th align ="right"  width=10>
                                        
                                          {{number_format($doc->sold_EUR,2)}}
                                       
                                    </th>
                                    
                                     
                                  </tr>
                                  </tbody>
                                </table>
                           @endif
                           @endforeach 
                   
                   @if(!$balantadetaliata)
                    <tr style="height:50px; page-break-inside: avoid !important;" >
                    
                     <th align="left" colspan="4" width=35>
                       TOTAL GENERAL
                       </th>
                       
                        <th align="right" width=10>
                          {{ number_format($balanta->sum("soldIniPerioadaD"),2)}}
                      </th>
                      <th align="right" width=10>
                          {{ number_format($balanta->sum("soldIniPerioadaC"),2)}}
                       </th>
                     <th align="right" width=10>
                          {{ number_format($balanta->sum("rulajD"),2)}}
                      </th>
                      <th align="right" width=10>
                          {{ number_format($balanta->sum("rulajC"),2)}}
                       </th>
                       
                        <th align="right" width=10>
                          {{ number_format($balanta->sum("soldFinalD"),2)}}
                      </th>
                      <th align="right" width=10>
                          {{ number_format($balanta->sum("soldFinalC"),2)}}
                       </th>
                     <!--  <td align="right" width=5>
                        
                      </td>  -->
                   
                        <th align="right" width=10>
                          {{ number_format($balanta->sum("soldIniPerioadaD_EUR"),2)}}
                      </th>
                      <th align="right" width=10>
                          {{ number_format($balanta->sum("soldIniPerioadaC_EUR"),2)}}
                       </th>
                     <th align="right" width=10>
                          {{ number_format($balanta->sum("rulajD_EUR"),2)}}
                      </th>
                      <th align="right" width=10>
                          {{ number_format($balanta->sum("rulajC_EUR"),2)}}
                       </th>
                       
                        <th align="right" width=10>
                          {{ number_format($balanta->sum("soldFinalD_EUR"),2)}}
                      </th>
                      <th align="right" width=10>
                          {{ number_format($balanta->sum("soldFinalC_EUR"),2)}}
                       </th>
                     <!--  <td align="right" width=5>
                        
                      </td>  -->
                    </tr>
                  </tbody>
               </table>
                    @endif
    
      
     
   

	
