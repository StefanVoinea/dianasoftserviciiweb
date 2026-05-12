<table class="table table-condesed text-sm" border="1" width="100"  style="border-collapse: collapse; " >
            <thead>
              <tr>
                <td colspan="9">
                   <center> <h3> Fișă cont </h3> </center>
                   <center> <h3>{{$selectie}} </h3></center>
                </td>              
              </tr>
            <tr >
              <th align ="center" width="5">
                    <center>Nr <br> crt</center>
              </th>
              <th align ="center"  width="10">
                    <center>Data</center>
              </th>
               <th align ="center"  width="25">
                    <center>Document </center>
              </th>
               <th align ="center"  width="11">
                    Explicatie
              </th>  
              <th align ="center"  width="13">
                   Cont <br> corespondent
              </th>
              <th align ="center" width="13">
                   Debit
              </th>
                <th align ="center"  width="13">
                   Credit
              </th>
              <th align ="center"  width="13">
                   Sold
              </th>
                
            </tr>
            </thead>
     <tbody>
        @if(!$cont->prin_cont)
              <tr style="border-bottom:1pt solid black; page-break-inside: avoid !important;">
              <th align ="center" colspan="5" width="61">
                    <center>{{'Rulaj precedent '}} </center>
              </th>
              <th align ="center" width="13">
                  {{ number_format($rulajDebit,2)}} 
              </th>
                <th align ="center"  width="13">
                   {{ number_format($rulajCredit,2)}} 
              </th>
              <th align ="center"  width="13">
                 @if($cont->tip_cont=="Bifunctional") 
                            
                  {{ number_format(abs($cont->soldinitial),2)}}
                 @endif 
                 @if($cont->tip_cont!="Bifunctional") 
                            
                  {{ number_format($cont->soldinitial,2)}}
                 @endif 
              </th>
            </tr>
       
         @endif     
          
                  @foreach($notacontabila->groupBy("luna_anul") as $notacontabilaLuna)
                    @foreach($notacontabilaLuna as $nota)
                      
                      <tr style="border-bottom:1pt solid black; page-break-inside: avoid !important;"> 
                       <td align="center" width="5">
                       {{ $i++ }}.
                       </td>
                       <td align="center" width="10">
                       {{dateFormatAfisare($nota->data_nota) }}
                       </td>
                       <td align="left" width="25">
                          
                        {{$nota->tip_doc ." ". $nota->nr_doc."/".dateFormatAfisare($nota->data_doc) . " ". $nota->partener}}
                        
                       </td>
                        <td align="left" width="11">
                         {{ $nota->expl}}
                        
                      </td>
                      <td align="left" width="10">
                       @if($nota->contd==$cont->cont) 
                         {{ $nota->contc }}
                        @endif
                        @if($nota->contc==$cont->cont) 
                         {{ $nota->contd }}
                        @endif
                      </td>
                      
                      <td align="right" width="13">
                        @if($nota->contd==$cont->cont) 
                          {{ number_format($nota->debit,2)}}
                        @endif
                         
                      </td>
                      <td align="right" width="13">
                         @if($nota->contc==$cont->cont) 
                              {{ number_format($nota->credit,2)}}
                         @endif
                         
                      </td>
                      <td align="right" width="13">
                        @if(!$cont->prin_cont)
                         @if($cont->tip_cont=="Pasiv" || $cont->tip_cont=="C") 
                              {{
                                 number_format($sold+=($nota->credit-$nota->debit),2)
                              }}
                         @endif
                         @if($cont->tip_cont=="Activ"  || $cont->tip_cont=="D") 
                              {{
                                 number_format($sold+=($nota->debit-$nota->credit),2)
                              }}
                         @endif
                         @if($cont->tip_cont=="Bifunctional"  || $cont->tip_cont=="B") 
                              {{
                                 number_format(abs($sold+=($nota->debit-$nota->credit)),2)
                              }}
                         @endif
                         @endif
                      </td>
                      
                      </tr>
                     @endforeach 
                      <tr style="border-bottom:1pt solid black; page-break-inside: avoid !important;">
                    
                      <td width="61" align="center"  colspan="5">
                         <strong>
                          TOTAL LUNA {{lunainlitere($notacontabilaLuna[0]->luna)." ".$notacontabilaLuna[0]->anul}}
                          </strong>
                      </td>
                     
                      <td width="13" align="right" >
                       
                        <strong>
                        
                          {{ number_format(round($notacontabilaLuna->sum('debit'),2),2) }}
                           </strong>
                      
                      </td>
                      <td width="13" align="right" >
                      
                        <strong>
                        
                          {{ number_format(round($notacontabilaLuna->sum('credit'),2),2) }}
                           </strong>
                        
                      </td>
                      <td width="13" align="right" >
                       
                        <strong>
                          @if(!$cont->prin_cont)
                          @if($cont->tip_cont=="Bifunctional"  || $cont->tip_cont=="B") 
                            
                              {{ number_format(abs($sold),2)}}
                             @endif 
                             @if($cont->tip_cont!="Bifunctional"  || $cont->tip_cont!="B") 
                                        
                              {{ number_format($sold,2)}}
                             @endif 
                           @endif  
                           </strong>
                       
                      </td>
                    </tr>
                      @endforeach 
                   
                    <tr>
                    
                      <td width="61" align="right" colspan="5">
                         <strong>
                          TOTAL 
                          </strong>
                      </td>
                     
                      <td width="13" align="right" >
                        <h4>
                        <strong>
                        
                          {{ number_format(round($notacontabila->sum('debit'),2),2) }}
                           </strong>
                         </h4>
                      </td>
                      <td width="13" align="right" >
                        <h4>
                        <strong>
                        
                          {{ number_format(round($notacontabila->sum('credit'),2),2) }}
                           </strong>
                         </h4>
                      </td>
                      <td width="13" align="right" >
                        <h4>
                        <strong>
                          @if(!$cont->prin_cont)
                          @if($cont->tip_cont=="Bifunctional"  || $cont->tip_cont=="B") 
                            
                              {{ number_format(abs($sold),2)}}
                             @endif 
                             @if($cont->tip_cont!="Bifunctional"  || $cont->tip_cont!="B") 
                                        
                              {{ number_format($sold,2)}}
                             @endif 
                           @endif  
                           </strong>
                         </h4>
                      </td>
                    </tr>
                  </tbody>
               </table>
   
     <br>
   
   