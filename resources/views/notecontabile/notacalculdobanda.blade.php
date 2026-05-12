@extends('layouts.antet')

@section ('content')

   
      <table class="text-sm table table-condesed" border="1" style="border-collapse: collapse;" width=100%>
            <thead >
            <tr style="height:30px;  page-break-inside: avoid !important;">
              <th align ="center"  colspan="10" width="10%">
                <center> <h3> {{$titluRaport}} </h3></center>
              </th>
            </tr>
            <tr style="height:30px;  page-break-inside: avoid !important;">
              <th align ="center"  rowspan="2" width="10%">
                    Nr contract
              </th>
              <th align ="center"  rowspan="2"  width="8%">
                    Data contract
              </th>
              <th align ="center"  rowspan="2" width="15%">
                     <center>Nume client</center>
              </th>
              <th align ="center"  colspan="3" width="21%">
                   Doabanda ajunsa la scadenta
              </th>
               <th align ="center"  colspan="3" width="21%">
                   Doabanda neajunsa la scadenta
              </th>
              <th align ="center"  rowspan="2"  width="10%">
                    Total dobanda
              </th>
            </tr>
            <tr style="height:30px;  page-break-inside: avoid !important;">
            
              <th align ="center"   width="8%">
                   Data scadenta
              </th>
               <th align ="center"   width="5%">
                   Nr zile
              </th>
              <th align ="center"   width="8%">
                   Valoare
              </th>

              <th align ="center"   width="8%">
                   Data scadenta
              </th>
               <th align ="center"   width="5%">
                   Nr zile
              </th>
              <th align ="center"   width="8%">
                   Valoare
              </th>
            </tr>
            <thead>
       			
                    @foreach($contracte->groupBy("produs") as $produs)
                    <tr  style="height:30px; page-break-inside: avoid !important;">
                    
                     <th align="left"  colspan="9" width="100%">
                       PRODUS {{$produs[0]->produs." CONT:".$produs[0]->cont}}
                       </th>
                   </tr>
                          @foreach($produs->sortBy('nume',SORT_STRING) as $contract)
                            
                            <tr style=" page-break-inside: avoid !important;"> 
                             <td align="left"  width="10%">
                              {{$contract->nr_contract }} 
                             </td>
                             <td align="center" width="8%">
                              {{dateFormatAfisare($contract->data_contract) }} 
                             </td>
                              <td align="left"  width="15%">
                              {{ $contract->nume}} 
                            </td>
                            
                            
                              <td align="center"  width="8%">
                                {{ dateFormatAfisare($contract->data_scadenta_ajunsa)}}
                            </td>
                            <td align="center"  width="5%">
                                {{ $contract->nr_zile_ajunsa}} 
                             </td>
                           <td align="right"  width="8%">
                                {{ number_format($contract->dobanda_ajunsa,2)}}
                            </td>
                              <td align="center"  width="8%">
                                {{ dateFormatAfisare($contract->data_scadenta_neajunsa)}}
                            </td>
                            <td align="center"  width="5%">
                                {{ $contract->nr_zile_neajunsa}} 
                             </td>
                           <td align="right"  width="8%">
                                {{ number_format($contract->dobanda_neajunsa,2)}}
                            </td>
                            <td align="right"  width="10%">
                                {{ number_format($contract->total_dobanda,2)}}
                             </td>
                           </tr>
                           @endforeach 
                    <tr  style="height:30px; page-break-inside: avoid !important;" >
                    
                     <th align="left"  colspan="5" width="61%">
                       TOTAL PRODUS {{$produs[0]->produs." CONT:".$produs[0]->cont}}
                       </th>
                       <th align="right" width="8%">
                          {{ number_format($produs->sum("dobanda_ajunsa"),2)}}
                      </th>
                      <th align="right" colspan="2" width="13%">
                          
                       </th>
                     
                        <th align="right" width="8%">
                          {{ number_format($produs->sum("dobanda_neajunsa"),2)}}
                      </th>
                      <th align="right" width="10%">
                          {{ number_format($produs->sum("total_dobanda"),2)}}
                       </th>
                    
                    </tr>
                          @endforeach 
                   
                    <tr   style="height:30px; page-break-inside: avoid !important;">
                    
                     <th align="left" colspan="5" width="61%">
                       TOTAL GENERAL
                       </th>
                     <th align="right" width="8%">
                          {{ number_format($contracte->sum("dobanda_ajunsa"),2)}}
                      </th>
                      <th align="right" colspan="2" width="13%">
                          
                       </th>
                     
                        <th align="right" width="8%">
                          {{ number_format($contracte->sum("dobanda_neajunsa"),2)}}
                      </th>
                      <th align="right" width="10%">
                          {{ number_format($contracte->sum("total_dobanda"),2)}}
                       </th>
                    
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
     


	
