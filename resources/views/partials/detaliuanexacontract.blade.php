<table class="table table-condesed" width=100%  >
            
            <tr>
              <th align ="center" width="5%">
                    <center>Nr <br> crt</center>
                </th>
                <th align ="center" width="65%">
                    Denumire produselor sau a serviciilor
                </th>
               
                <th align ="center" width="10%">
                    Cantitate
                </th>
                <th align ="center" width="10%">
                    <center>Pret</center> 
                     <center>(TVA inclus)</center>
                </th>
                
                <th align ="center" width="10%">
                    <center>Valoare</center> 
                    
                </th>
               
            </tr>
     </table>
     <hr>       
     <table class="table table-condesed" width=100%>      
                    @foreach($contract->lucraricontract as $det_anexa)
                      
                      <tr> 
                       <td align="center" style="vertical-align:top" width="5%">
                       {{ $i++ }}.
                       </td>
                      <td align="left" width="65%">
                       <strong>{{$det_anexa->denumire  }} </strong><br>
                       @if($det_anexa->obs)
                       {{"Detalii: " . $det_anexa->obs  }} 
                       @endif
                      </td>
                      <td align="center" style="vertical-align:top"  width="10%">
                      <center> {{ number_format($det_anexa->cantitate,2)}}</center>
                                              
                      </td>
                     
                      <td align="right" style="vertical-align:top"  width="10%">
                      {{ number_format(round($det_anexa->pret_vanzare,2),2) }}
                         
                      </td>
                      <td align="right" style="vertical-align:top"  width="10%">
                      {{ number_format(round($det_anexa->cantitate*$det_anexa->pret_vanzare,2),2) }}
                         
                      </td>
                         
                      </tr>
                     
                    @endforeach 
              </table>
            
     <hr> 
     
                    
     <table class="table table-condesed" width=100% > 
                 <tr>
                  <td width="20%" align="center" >
                    Vânzător:<br>
                    {{$company->denumire}}<br>
                    {{Auth::user()->name}}<br><br>
                    _________________________
                  </td>
                   <td width="40%" align="center" >
                    Cumpărător:<br>
                    {{$contract->nume}}<br><br><br>
                    ___________________________________
                  </td>
                  <td width="25%" align="right" colspan="3" >
                        <h3>
                         <strong>
                          TOTAL ({{$contract->tip_valuta}})
                          </strong>
                        </h3>
                  </td>
            <td width="15%" colspan="2">
              <h3>
          <strong>
            <center>
            {{ number_format(round($contract->valoare_plan,2),2)

            }}
            
               </center>
               </strong>
             </h3>
          </td>
          </tr> 
      </table>   