<table class="table table-condesed" width=100%  >
            
            <tr>
              <th align ="center" width="40%">
                    
                </th>
                <th align ="center" width="20%">
                    Plata
                </th>
               
                <th align ="center" width="20%">
                    Data scadenta
                </th>
                <th align ="center" width="20%">
                    <center>Rata ({{$contract->tip_valuta}})</center> 
                     <center>(TVA inclus)</center>
                </th>
                
               
               
            </tr>
     </table>
     <hr>       
     <table class="table table-condesed" width=100%> 
                  @if($contract->valoare_avans!=null &&$contract->valoare_avans!=0)
                     <tr style=" page-break-inside: avoid !important;"> 
                       <td align="left" width="50%">
                      
                      </td>
                      <td align="left" width="10%">
                       <strong>{{"Avans " }} </strong><br>
                      </td>
                      <td align="center" style="vertical-align:top"  width="20%">
                      <center> {{ dateFormatAfisare($contract->data_contract)}}</center>
                      </td>
                      <td align="right" style="vertical-align:top"  width="20%">
                      {{ number_format(round($contract->valoare_avans,2),2) }}
                         
                      </td>
                      
                         
                      </tr>     
                     @endif 
                    @foreach($contract->scadentar as $det_scadentar)
                      
                      <tr style="page-break-inside: avoid !important;"> 
                      <td align="left" width="50%">
                       
                      </td>
                      <td align="left" width="10%">
                       <strong>{{"Rata ". $i++  }} </strong><br>
                      </td>
                      <td align="center" style="vertical-align:top"  width="20%">
                      <center> {{ dateFormatAfisare($det_scadentar->data)}}</center>
                                              
                      </td>
                     
                      <td align="right" style="vertical-align:top"  width="20%">
                      {{ number_format(round($det_scadentar->suma,2),2) }}
                         
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
            {{ number_format(round($contract->valoare_plan,2),2) }}
            </center>
          </strong>
             </h3>
          </td>
          </tr> 
      </table>   