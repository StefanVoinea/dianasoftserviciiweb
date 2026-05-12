
@extends('layouts.antet')

@section ('content')

             <center> <h3> Balanța contabilă {{$selectie}} </h3></center>
   
      <table class="text-sm table table-condesed" border="1" style="border-collapse: collapse;" width=100%>
            <thead>
            <tr >
              <th align ="center"  width="5%">
                   <!--  <center>Cont</center> -->
              </th>
              <th align ="center" width="10%">
                    <!-- <center>Denumire</center> -->
              </th>
              <th align ="center" colspan="2" width="16%">
                   Solduri de deschidere
              </th>
               <th align ="center" colspan="2" width="16%">
                   Anterior
              </th>
              <th align ="center" colspan="2" width="16%">
                   În lună
              </th>
               <th align ="center" colspan="2" width="16%">
                   Total
              </th>   
              <th align ="center" colspan="2" width="16%">
                   Sold
              </th>    
          
            </tr>
            <tr style=" page-break-inside: avoid !important;">
              <th align ="center"  width="5%">
                    <center>Cont</center>
              </th>
              <th align ="center" width="10%">
                    <center>Denumire</center>
              </th>
              <th align ="center"  width="8%">
                    <center>Debit</center>
              </th>
              <th align ="center"  width="8%">
                    <center>Credit</center>
              </th>
               <th align ="center"  width="8%">
                    <center>Debit</center>
              </th>
              <th align ="center"  width="8%">
                    <center>Credit</center>
              </th>
               <th align ="center"  width="8%">
                    <center>Debit</center>
              </th>
              <th align ="center"  width="8%">
                    <center>Credit</center>
              </th>
               <th align ="center"  width="8%">
                    <center>Debit</center>
              </th>
              <th align ="center"  width="8%">
                    <center>Credit</center>
              </th>
               <th align ="center"  width="8%">
                    <center>Debit</center>
              </th>
              <th align ="center"  width="8%">
                    <center>Credit</center>
              </th>
            </tr>
            <thead>
            
                    @foreach($balanta->groupBy("grupa") as $grupa)
                      
                      @foreach($grupa->sortBy('cont',SORT_STRING)->groupBy("cont_sintetic") as $grupaII)
                            <tr style="height:30px; page-break-inside: avoid !important;"> 
                             <td align="left" width="5%">
                             <b> {{$grupaII[0]->cont_sintetic }} </b>
                             </td>
                             <td align="left" width="10%">
                              {{$grupaII[0]->denumire }} 
                             </td>
                             <td align="right" width="8%">
                              {{ number_format($grupaII->sum("soldiniD"),2)}} 
                            </td>
                            <td align="right" width="8%">
                                {{ number_format($grupaII->sum("soldiniC"),2)}} 
                             </td>
                            
                              <td align="right" width="8%">
                                {{ number_format($grupaII->sum("rulajAntD"),2)}}
                            </td>
                            <td align="right" width="8%">
                                {{ number_format($grupaII->sum("rulajAntC"),2)}} 
                             </td>
                           <td align="right" width="8%">
                                {{ number_format($grupaII->sum("rulajD"),2)}}
                            </td>
                            <td align="right" width="8%">
                                {{ number_format($grupaII->sum("rulajC"),2)}}
                             </td>
                             <td align="right" width="8%">
                                {{ number_format($grupaII->sum("rulajTotalD"),2)}}
                            </td>
                            <td align="right" width="8%">
                                {{ number_format($grupaII->sum("rulajTotalC"),2)}}
                             </td>
                              <td align="right" width="8%">
                                @if($grupaII->sum("soldFinalD")-$grupaII->sum("soldFinalC")>0)
                                {{ number_format($grupaII->sum("soldFinalD")-$grupaII->sum("soldFinalC"),2)}}
                                @else
                                0.00
                               @endif
                            </td>
                            <td align="right" width="8%">
                                @if($grupaII->sum("soldFinalC")-$grupaII->sum("soldFinalD")>0)
                                {{ number_format($grupaII->sum("soldFinalC")-$grupaII->sum("soldFinalD"),2)}}
                                @else
                                0.00
                               @endif
                             </td>
                       
                           </tr>
                            @foreach($grupaII->sortBy('cont',SORT_STRING)->groupBy("cont_sinteticII") as $cont)
                            @if($cont[0]->cont_sintetic!=$cont[0]->cont && $cont[0]->cont_sintetic!=$cont[0]->cont_sinteticII)
                            <tr style="height:30px; page-break-inside: avoid !important;"> 
                             <td align="left" width="5%">
                             <b> {{$cont[0]->cont_sinteticII }} </b>
                             </td>
                             <td align="left" width="10%">
                              {{$cont[0]->denumire }} 
                             </td>
                             <td align="right" width="8%">
                              {{ number_format($cont->sum("soldiniD"),2)}} 
                            </td>
                            <td align="right" width="8%">
                                {{ number_format($cont->sum("soldiniC"),2)}} 
                             </td>
                            
                              <td align="right" width="8%">
                                {{ number_format($cont->sum("rulajAntD"),2)}}
                            </td>
                            <td align="right" width="8%">
                                {{ number_format($cont->sum("rulajAntC"),2)}} 
                             </td>
                           <td align="right" width="8%">
                                {{ number_format($cont->sum("rulajD"),2)}}
                            </td>
                            <td align="right" width="8%">
                                {{ number_format($cont->sum("rulajC"),2)}}
                             </td>
                             <td align="right" width="8%">
                                {{ number_format($cont->sum("rulajTotalD"),2)}}
                            </td>
                            <td align="right" width="8%">
                                {{ number_format($cont->sum("rulajTotalC"),2)}}
                             </td>
                              <td align="right" width="8%">
                                @if($cont->sum("soldFinalD")-$cont->sum("soldFinalC")>0)
                                {{ number_format($cont->sum("soldFinalD")-$cont->sum("soldFinalC"),2)}}
                                @else
                                0.00
                                @endif
                            </td>
                            <td align="right" width="8%">
                                @if($cont->sum("soldFinalC")-$cont->sum("soldFinalD")>0)
                                {{ number_format($cont->sum("soldFinalC")-$cont->sum("soldFinalD"),2)}}
                                @else
                                0.00
                                @endif
                             </td>
                       
                           </tr>
                           @endif
                           @if($cont->count()>0)
                           @foreach($cont->sortBy('cont',SORT_STRING) as $contA)
                            @if($contA->cont!=$contA->cont_sintetic && $contA->cont!=$contA->cont_sinteticII)
                            <tr style="height:30px; page-break-inside: avoid !important;"> 
                             <td align="left" width="5%">
                              {{$contA->cont }} 
                             </td>
                             <td align="left" width="10%">
                              {{$contA->denumire }} 
                             </td>
                             <td align="right" width="8%">
                              {{ number_format($contA->soldiniD,2)}} 
                            </td>
                            <td align="right" width="8%">
                                {{ number_format($contA->soldiniC,2)}} 
                             </td>
                            
                              <td align="right" width="8%">
                                {{ number_format($contA->rulajAntD,2)}}
                            </td>
                            <td align="right" width="8%">
                                {{ number_format($contA->rulajAntC,2)}} 
                             </td>
                           <td align="right" width="8%">
                                {{ number_format($contA->rulajD,2)}}
                            </td>
                            <td align="right" width="8%">
                                {{ number_format($contA->rulajC,2)}}
                             </td>
                             <td align="right" width="8%">
                                {{ number_format($contA->rulajTotalD,2)}}
                            </td>
                            <td align="right" width="8%">
                                {{ number_format($contA->rulajTotalC,2)}}
                             </td>
                              <td align="right" width="8%">
                                {{ number_format($contA->soldFinalD,2)}}
                            </td>
                            <td align="right" width="8%">
                                {{ number_format($contA->soldFinalC,2)}}
                             </td>
                          
                           </tr>
                          @endif
                           @endforeach
                             @endif
                           @endforeach 
                           @endforeach 
                    <tr style="height:50px;page-break-inside: avoid !important;" >
                    
                     <th align="left"  colspan="2" width="15%">
                       TOTAL GRUPA {{$grupa[0]->grupa}}
                       </th>
                       <th align="right" width="8%">
                          {{ number_format($grupa->sum("soldiniD"),2)}}
                      </th>
                      <th align="right" width="8%">
                          {{ number_format($grupa->sum("soldiniC"),2)}}
                       </th>
                     
                        <th align="right" width="8%">
                          {{ number_format($grupa->sum("rulajAntD"),2)}}
                      </th>
                      <th align="right" width="8%">
                          {{ number_format($grupa->sum("rulajAntC"),2)}}
                       </th>
                     <th align="right" width="8%">
                          {{ number_format($grupa->sum("rulajD"),2)}}
                      </th>
                      <th align="right" width="8%">
                          {{ number_format($grupa->sum("rulajC"),2)}}
                       </th>
                       <th align="right" width="8%">
                          {{ number_format($grupa->sum("rulajTotalD"),2)}}
                      </th>
                      <th align="right" width="8%">
                          {{ number_format($grupa->sum("rulajTotalC"),2)}}
                       </th>
                        <th align="right" width="8%">
                          {{ number_format($grupa->sum("soldFinalD"),2)}}
                      </th>
                      <th align="right" width="8%">
                          {{ number_format($grupa->sum("soldFinalC"),2)}}
                       </th>
                     <!--  <td align="right" width="5%">
                        
                      </td>  -->
                    </tr>
                          @endforeach 
                   
                    <tr style="height:50px;  page-break-inside: avoid !important;" >
                    
                     <th align="left" colspan="2" width="15%">
                       TOTAL GENERAL
                       </th>
                       <th align="right" width="8%">
                          {{ number_format($balanta->sum("soldiniD"),2)}}
                      </th>
                      <th align="right" width="8%">
                          {{ number_format($balanta->sum("soldiniC"),2)}}
                       </th>
                     
                        <th align="right" width="8%">
                          {{ number_format($balanta->sum("rulajAntD"),2)}}
                      </th>
                      <th align="right" width="8%">
                          {{ number_format($balanta->sum("rulajAntC"),2)}}
                       </th>
                     <th align="right" width="8%">
                          {{ number_format($balanta->sum("rulajD"),2)}}
                      </th>
                      <th align="right" width="8%">
                          {{ number_format($balanta->sum("rulajC"),2)}}
                       </th>
                       <th align="right" width="8%">
                          {{ number_format($balanta->sum("rulajTotalD"),2)}}
                      </th>
                      <th align="right" width="8%">
                          {{ number_format($balanta->sum("rulajTotalC"),2)}}
                       </th>
                        <th align="right" width="8%">
                          {{ number_format($balanta->sum("soldFinalD"),2)}}
                      </th>
                      <th align="right" width="8%">
                          {{ number_format($balanta->sum("soldFinalC"),2)}}
                       </th>
                     <!--  <td align="right" width="5%">
                        
                      </td>  -->
                    </tr>
               </table>
    
      
     <br>
     <table class="table table-condesed" width=100% > 
                 <tr>
                  <td  width="15%">
                       
                  </td>
                  <td  width="30%" align="center" >
                   DIRECTOR GENERAL,<br><br>
                   {{$company->director_general}}
                   <br><br>
                    _________________________
                  </td>
                   <td  width="10%" >
                
              </td>
                   <td width="30%" align="center" >
                     DIRECTOR ECONOMIC,<br><br>
                    {{$company->contabil_sef}}
                   <br><br>
                    ___________________________________
                  </td>
                <td  width="15%" >
                
              </td>
          </tr> 
      </table>  
     
      @stop
     


  
