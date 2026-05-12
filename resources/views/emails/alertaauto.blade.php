@component('mail::panel',["alertaAuto"=>$alertaAuto,"revizii"=>$revizii])

@component('mail::table',["alertaAuto"=>$alertaAuto,"revizii"=>$revizii])
  <table class="table" style="border-collapse: collapse; "  width=60%  >
            
            <tr style="border-top:1pt solid black; border-bottom:1pt solid black; page-break-inside: avoid !important;">
              
                
               <td align ="left" width="15%">
                    Model
                </td>
                <td align ="left" width="10%">
                    Nr inmatriculare
                </td>
                
               <td align ="left" width="15%">
                    Tip alerta
                </td>
                <td align ="left" width="10%">
                    Data expirarii
                </td>
                <td align ="left" width="15%">
                    Utilizator
                </td>
               <td align ="left" width="35%">
                    Proprietar
               </td>
                
            </tr>
    
                   
                    @foreach($alertaAuto as $alerta)
                      
                      <tr style="border-bottom:1pt solid black; page-break-inside: avoid !important;"> 
                      
                     
                      <td align="left" width="15%">
                      {{ $alerta->parcauto["marca"] }}
                         
                      </td>
                      <td align="left" width="10%">
                     {{ $alerta->parcauto["nr_inmatriculare"]}}
                         
                      </td>
                      <td align="left" width="15%">
                     {{ $alerta->tip_alerta}}
                         
                      </td>
                      <td align="left" width="10%">
                       {{ dateFormatAfisare($alerta->data_expirare)}}
                      </td>
                      
                      <td align="left" width="15%">
                     {{ $alerta->parcauto["utilizator"]}}
                         
                      </td>
                      <td align="left" width="35%">
                      
                       {{$alerta->parcauto["partener"]["denumire"]  }}
                         
                      </td>
                      </tr>
                     @endforeach 
                     @foreach($revizii as $revizie)
                      
                      <tr style="border-bottom:1pt solid black; page-break-inside: avoid !important;"> 
                      
                     
                      <td align="left" width="15%">
                      {{ $revizie->marca }}
                         
                      </td>
                      <td align="left" width="10%">
                     {{ $revizie->nr_inmatriculare}}
                         
                      </td>
                      <td align="left" width="15%">
                     {{ "Revizie "."Km la bord:".$revizie->ultimii_km_la_bord}}<br>
                     {{ "Km urmatoarea revizie: ".$revizie->km_urmatoarea_revizie}}
                         
                      </td>
                      <td align="left" width="10%">
                       {{ dateFormatAfisare($revizie->datareviziaurmatoare)}}
                      </td>
                      
                      <td align="left" width="15%">
                     {{ $revizie->utilizator}}
                         
                      </td>
                      <td align="left" width="35%">
                      
                       {{$revizie->partener["denumire"]  }}
                         
                      </td>
                      </tr>
                     @endforeach 
               </table>
     
 @endcomponent
 @endcomponent