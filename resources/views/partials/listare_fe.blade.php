  <table class="table table-condesed" width=100%  >
            
            <tr>
              <th align ="center" width="5%">
                    <center>Nr <br> crt</center>
                </th>
                <th align ="center" width="45%">
                    Denumire produselor sau a serviciilor
                </th>
                 <th align ="center" width="5%">
                    %TVA
                </th>
                <th align ="center" width="5%">
                    Um
                </th>
                
                <th align ="center" width="10%">
                    Cantitate
                </th>
                <th align ="center" width="10%">
                    <center>Pret unitar</center> 
                     <center>(fără TVA)</center>
                </th>
                <th align ="center" width="10%">
                    <center>Valoare</center> 
                    <center>fără TVA</center>
                </th>
                <th align ="center" width="10%">
                    <center>Valoare</center> 
                    <center>TVA</center>
                </th>
               
            </tr>
     </table>
     <hr>       
     <table class="table table-condesed" width=100%>      
                    @foreach($vanzare->detaliuvanzari as $det_fe)
                      
                      <tr> 
                       <td align="center" width="5%">
                       {{ $i++ }}.
                       </td>
                      <td align="left" width="45%">
                       {{$det_fe->denumire  }} <br>
                       {{$det_fe->obs  }} 
                      </td>
                      <td align="center" width="5%">
                      <center>{{$det_fe->procent_tva}}</center>
                      </td>
                      <td align="center" width="5%">
                      <center>buc</center>
                    
                      </td>
                      <td align="center" width="10%">
                      <center> {{ number_format($det_fe->cantitate,2)}}</center>
                                              
                      </td>
                     
                      <td align="right" width="10%">
                      {{ number_format(round($det_fe->pret_vanzare/(1+$det_fe->procent_tva/100),2),2) }}
                         
                      </td>
                      <td align="right" width="10%">
                      {{ number_format(round($det_fe->valoare_fara_tva,2),2) }}
                         
                      </td>
                       <td align="right" width="10%">
                      {{ number_format(round($det_fe->valoare_tva,2),2) }}
                         
                      </td>
                          
                      </tr>
                     
                    @endforeach 
              </table>
              @if($vanzare->contract)
                <strong>[{{ 'Valoare plan: '.number_format($vanzare->contract->valoare_plan,2).' '.$vanzare->contract->tip_valuta.' Achitat: '.number_format($vanzare->contract->totalincasat(),2) .' Sold:'.number_format($vanzare->contract->valoare_plan-$vanzare->contract->totalincasat(),2)}}]</strong> 
              @endif
     <hr> 
     <table class="table table-condesed" width=100%> 
                    <tr>
                     <td width="50%" align="left" >
                        Numele delegatului:{{$vanzare->delegat}}<br>
                        B.I./C.I.:{{$vanzare->ci_delegat}} eliberat(a) {{$vanzare->ci_delegat_politia.' '.dateFormatAfisare($vanzare->ci_delegat_data)}}<br>
                        Mijlocul de transport nr.:{{$vanzare->auto}} 

                      </td>
                      <td width="30%" align="right" colspan="4">
                         <strong>
                          SUBTOTAL (LEI)
                          </strong>
                      </td>
                      <td width="10%" align="right" >
                        <h4>
                        <strong>
                       
                          {{ number_format(round($vanzare->valoare_fara_tva,2),2) }}
                       
                         </strong>
                       </h4>
                      </td>
                      <td width="10%" align="right" >
                        <h4>
                        <strong>
                        
                          {{ number_format(round($vanzare->valoare_tva,2),2)}}
                           </strong>
                         </h4>
                      </td>
                    </tr>
               </table>
     <hr> 
     <table class="table table-condesed" width=100% > 
                 <tr>
                  <td width="20%" align="center" >
                    Semnătura de primire<br><br>
                    _________________________
                  </td>
                   <td width="40%" align="center" >
                    Semnătura furnizorului<br><br>
                    ___________________________________
                  </td>
                  <td width="25%" align="right" colspan="3" >
                        <h3>
                         <strong>
                          TOTAL (LEI)
                          </strong>
                        </h3>
                  </td>
            <td width="15%" colspan="2">
              <h3>
          <strong>
            <center>
            {{ number_format(round($vanzare->valoare,2),2)

            }}
            
               </center>
               </strong>
             </h3>
          </td>
          </tr> 
      </table>   