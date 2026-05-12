@extends('layouts.antet')

@section ('content')
  
   <center> <h3> Situația consumului de carburant </h3> </center>
   <center> <h3>{{$selectie}} </h3></center>
   
                    <hr>
                       <table class="table table-condesed" width=100%  >
            
                            <tr >
                              <th align ="center"  width="5%">
                                    <center>Nr <br> crt</center>
                                </th>
                                <th align ="left"  width="17%">
                                    Auto <br>
                                    Utilizator<br>
                                    Ultima revizie
                                </th>
                                 <th align ="center"  width="6%">
                                    Km/O.func.<br>
                                    inceput perioada 
                                </th>
                                <th align ="center"  width="6%">
                                    Km/O.func.<br>
                                    sfarsit perioada 
                                </th>
                                <th align ="center"  width="6%">
                                    Km parcursi<br>
                                    in perioada 
                                </th>
                                <th align ="center"  width="6%">
                                    Rest rezervor<br>
                                    inceput perioada 
                                </th>
                                <th align ="center"  width="6%">
                                    Alimentari<br>
                                    externe
                                </th>
                                <th align ="center"  width="6%">
                                    Alimentari<br>
                                    interne
                                </th>
                                <th align ="center"  width="6%">
                                    Total <br>alimentari
                                </th>
                                <th align ="center"  width="6%">
                                    Rest rezervor<br>
                                    sfarsit perioada 
                                </th>
                                <th align ="center"  width="6%">
                                    Consum
                                </th>
                                <th align ="center"  width="6%">
                                    Consum<br>mediu<br>
                                    (l/100km)
                                </th>
                                <th align ="center"  width="6%">
                                    Consum<br>normat<br>
                                    (l/100km)
                                </th>
                                <th align ="center"  width="6%">
                                    Diferenta<br>consum
                                </th>
                                <th align ="center"  width="6%">
                                    Valoare<br>carburant
                                </th>
                            </tr>
                           

                     </table>
                    
                    <hr>
     				         <table class="table table-condesed" style="border-collapse: collapse; " width=100%>      
                    @foreach($parcauto as $auto)
                       
                      <tr style="border-bottom:1pt solid black; page-break-inside: avoid !important;"> 
                                <td align ="center"  width="5%">
                                    {{$i++}}
                                </td>
                                <td align ="left"  width="17%">
                                    {{$auto->nr_inmatriculare." ".$auto->marca}}<br>
                                    {{$auto->utilizator}}
                                    {{$auto->ultimarevizie?dateFormatAfisare($auto->ultimarevizie[0]->data):""}} 
                                </td>
                                  <td align ="center"  width="6%">
                                    {{number_format($auto->km_la_bord_initial,0)}} 
                                </td>
                                <td align ="center"  width="6%">
                                   {{number_format($auto->km_la_bord_final,0)}}
                                </td>
                                <td align ="center"  width="6%">
                                    {{number_format($auto->km_parcursi,0)}} 
                                </td>
                                <td align ="center"  width="6%">
                                   {{number_format($auto->rest_in_rezervor_initial,0)}}
                                </td>
                                <td align ="center"  width="6%">
                                    {{number_format($auto->alimentari_externe,0)}}
                                </td>
                                <td align ="center"  width="6%">
                                    {{number_format($auto->alimentari_interne,0)}}
                                </td>
                                <td align ="center"  width="6%">
                                    {{number_format($auto->total_alimentari,0)}}
                                </td>
                                <td align ="center"  width="6%">
                                    {{number_format($auto->rest_in_rezervor_final,0)}}
                                </td>
                                <td align ="center"  width="6%">
                                    {{number_format($auto->consum,0)}}
                                </td>
                                <td align ="center"  width="6%">
                                    {{number_format($auto->consum_mediu,2)}}
                                </td>
                                <td align ="center"  width="6%">
                                    {{number_format($auto->consum_normat,0)}}
                                </td>
                                <td align ="center"  width="6%">
                                    {{number_format($auto->diferente_consum,0)}}
                                </td>
                                <td align ="center"  width="6%">
                                    {{number_format($auto->valoare_carburant,2)}}
                                </td>
                      </tr>
                     
                     @endforeach
                    </table>
                     <hr> 
     			     <table class="table table-condesed" width=100%> 
                    <tr>
                    
                                <td align ="center" colspan="2" width="22%">
                                    TOTAL
                                </td>
                                <td align ="center"  width="6%">
                                    {{number_format($parcauto->sum("km_la_bord_initial"),0)}} 
                                </td>
                                <td align ="center"  width="6%">
                                   {{number_format($parcauto->sum("km_la_bord_final"),0)}}
                                </td>
                                <td align ="center"  width="6%">
                                    {{number_format($parcauto->sum("km_parcursi"),0)}} 
                                </td>
                                <td align ="center"  width="6%">
                                   {{number_format($parcauto->sum("rest_in_rezervor_initial"),0)}}
                                </td>
                                <td align ="center"  width="6%">
                                    {{number_format($parcauto->sum("alimentari_externe"),0)}}
                                </td>
                                <td align ="center"  width="6%">
                                    {{number_format($parcauto->sum("alimentari_interne"),0)}}
                                </td>
                                <td align ="center"  width="6%">
                                    {{number_format($parcauto->sum("total_alimentari"),0)}}
                                </td>
                                <td align ="center"  width="6%">
                                    {{number_format($parcauto->sum("rest_in_rezervor_final"),0)}}
                                </td>
                                <td align ="center"  width="6%">
                                    {{number_format($parcauto->sum("consum"),0)}}
                                </td>
                                <td align ="center"  width="6%">
                                    {{number_format($parcauto->sum("consum_mediu"),0)}}
                                </td>
                                <td align ="center"  width="6%">
                                    {{number_format($parcauto->sum("consum_normat"),2)}}
                                </td>
                                <td align ="center"  width="6%">
                                    {{number_format($parcauto->sum("diferente_consum"),0)}}
                                </td>
                                <td align ="center"  width="6%">
                                    {{number_format($parcauto->sum("valoare_carburant"),2)}}
                                </td>
                    </tr>
               </table>
                <hr> 
                 
  
    
     

	
	
@stop