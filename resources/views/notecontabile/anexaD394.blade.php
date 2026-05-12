
@extends('layouts.antet')

@section ('content')

             <center> <h3> ANEXA DECLARATIA 394 {{$selectie}} </h3></center>
   
      <table class="text-sm table table-condesed" border="1" style="border-collapse: collapse;" width=100%>
            <thead>
            <tr style=" page-break-inside: avoid !important;">
              <th align ="center"  width="5%">
                   Nr <br> crt
              </th>
              <th align ="center" width="30%">
                    <center>Denumire partener</center>
              </th>
              <th align ="center" width="5%">
                      <center>Tip</center>
              </th>
               <th align ="center" width="10%">
                   Cod fiscal
              </th>
              <th align ="center" width="20%">
                   Adresa
              </th>
              <th align ="center"  width="5%">
                   % <br> TVA
              </th> 
               <th align ="center"  width="5%">
                   Nr<br> facturi
              </th>   
              <th align ="center" width="10%">
                   BAZA
              </th>    
              <th align ="center" width="10%">
                   TVA
              </th>    


            </tr>

            <thead>
       			
                    @foreach($operatiuni->groupBy("tip") as $opGrupa)
                          <tr style="height:30px; page-break-inside: avoid !important;">
                            <th align="left" colspan="9">
                              {{explicatieTipPartener($opGrupa[0]["tip"])}}
                            </th>
                          </tr>
                          @foreach($opGrupa->sortBy('denP',SORT_STRING) as $operatiune)
                            
                            <tr style="height:30px; page-break-inside: avoid !important;"> 
                             <td align="center" width="5%">
                              {{$i++ }} 
                             </td>
                             <td align="left" width="30%">
                              {{$operatiune["denP"] }} 
                             </td>
                             <td align="center" width="5%">
                              {{ $operatiune["tip_partener"]}} 
                            </td>
                            <td align="left" width="10%">
                                {{ $operatiune["cuiP"]}} 
                             </td>
                            
                              <td align="left" width="20%">
                                {{ $operatiune["detP"]}}
                            </td>
                            <td align="center" width="5%">
                                {{ $operatiune["cota"]}} 
                             </td>
                           <td align="center" width="5%">
                                {{ $operatiune["nrFact"]}}
                            </td>
                            <td align="right" width="10%">
                                {{ number_format($operatiune["baza"],0)}}
                             </td>
                             <td align="right" width="10%">
                                {{ number_format($operatiune["tva"],0)}}
                            </td>
                            
                           </tr>
                           @endforeach 
                    <tr style="height:50px;page-break-inside: avoid !important;" >
                    
                     <th align="left"  colspan="6" width="75%">
                       TOTAL {{explicatieTipPartener($opGrupa[0]["tip"])}}
                       </th>
                       <th align="center" width="5%">
                        {{number_format($opGrupa->sum("nrFact"),0)}}
                      </th>
                      <th align="right" width="10%">
                          {{ number_format($opGrupa->sum("baza"),0)}}
                       </th>
                     
                        <th align="right" width="10%">
                         {{ number_format($opGrupa->sum("tva"),0)}}
                      </th>
                    
                    </tr>
                          @endforeach 
                   
                    <tr style="height:50px;  page-break-inside: avoid !important;" >
                    
                     <th align="left"  colspan="6" width="75%">
                       TOTAL GENERAL
                       </th>
                       <th align="center" width="5%">
                       {{ number_format($operatiuni->sum("nrFact"),0)}}
                      </th>
                      <th align="right" width="10%">
                          {{ number_format($operatiuni->sum("baza"),0)}}
                       </th>
                     
                        <th align="right" width="10%">
                         {{ number_format($operatiuni->sum("tva"),0)}}
                      </th>
                    </tr>
               </table>
    
      
     <br>
     
     
      @stop
     