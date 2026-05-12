@extends('layouts.antet')
@section ('content')
<center> <h3> {{$titluRaport}} </h3></center>
<table  class="text-sm table table-condesed" border="1" style="border-collapse: collapse;" width=100%>
            <thead>
           <tr style="page-break-inside: avoid !important;">
              <th align ="center"  width="3%">
                Nr crt
              </th>  
              <th align ="center"  width="5%">
                  Agentia
              </th>
              <th align ="center"  width="7%">
                  Nume client
              </th>
               <th align ="center"  width="5%">
                  Contract
              </th>
              <th align ="center"   width="5%">
                  Clasificare
              </th> 
              <th align ="center"   width="5%">
                  Dob reesalonata
                   <hr style="border:0;border-top:1px solid #000;margin:4px 0;">
                   Pen reesalonate
              </th>
              <th align ="center"   width="5%">
                  Penalitati
              </th>
              <th align ="center"   width="5%">
                  Principal
                   <hr style="border:0;border-top:1px solid #000;margin:4px 0;">
                     Dobanda
              </th>
               <th align ="center"   width="10%">
                  <=0
              </th>
              <th align ="center"  width="10%">
                  0-15
              </th>
              <th align ="center"  width="10%">
                  16-30
              </th>
              <th align ="center"  width="5%">
                  31-60
              </th>
              <th align ="center"   width="5%">
                  61-90
              </th>
              <th align ="center"  width="5%">
                  Peste 90
              </th>
              <th align ="center"  width="5%">
                  Serviciul datoriei
              </th>
              
            </tr>
         
            
            </thead>
                @foreach($tabel->groupBy("tip_contract") as $tipcontract)
                            
                          <tr style="page-break-inside: avoid !important;font-weight: bold;">
                            <td align="left" colspan="15" width="100%">
                              Tip contract :{{$tipcontract[0]->tip_contract}}    
                            </td>
                          </tr> 
                    @foreach($tipcontract->groupBy("expunere") as $tiprisc)
                            
                          <tr style="page-break-inside: avoid !important;font-weight: bold;">
                            <td align="left" colspan="15" width="100%">
                              Tip risc :{{$tiprisc[0]->expunere}}    
                            </td>
                          </tr> 
                    
                            @foreach($tiprisc as $contract)
                           <tr style="page-break-inside: avoid !important;">
                            <td align="left" colspan="15" width="100%">
                           
                           <tr style="page-break-inside: avoid !important;">
                              <td align ="center"   width="3%">
                                  {{$i++}}
                              </td>
                             <td align ="center"   width="5%">
                                  {{$contract->agentia}}
                              </td>
                              <td align ="left"   width="7%">
                                  {{$contract->nume}}
                              </td>
                               
                               <td align ="center"    width="5%">
                                  {{$contract->nr_contract}}<br>
                                  {{dateFormatAfisare($contract->data_contract)}}
                              </td>
                               
                              
                              <td align ="right"   width="5%">
                                  {{$contract->expunere}}
                              </td>
                              <td align ="right"   width="5%">
                                  {{number_format($contract->dob_reesalonata_lei,2)}}
                                  <hr style="border:0;border-top:1px solid #000;margin:4px 0;">
                                   {{number_format($contract->pen_reesalonate_lei,2)}}
                              </td>
                          
                               <td align ="right"  width="10%">
                                  {{number_format($contract->penalitati_lei,2)}}

                              </td>
                              <td align ="right" width="10%">
                                  {{number_format($contract->principal_lei,2)}}
                                  <hr style="border:0;border-top:1px solid #000;margin:4px 0;">
                                   {{number_format($contract->dobanzi_lei,2)}}
                              </td>
                              <td align ="right" width="10%">
                                  {{number_format($contract->psub_0_lei,2)}}
                                  <hr style="border:0;border-top:1px solid #000;margin:4px 0;">
                                  {{number_format($contract->dsub_0_lei,2)}}
                              </td>
                              <td align ="right" width="5%">
                                  {{number_format($contract->p0_15_lei,2)}}
                                  <hr style="border:0;border-top:1px solid #000;margin:4px 0;">
                                  {{number_format($contract->d0_15_lei,2)}}
                              </td>
                              <td align ="right" width="5%">
                                  {{number_format($contract->p16_30_lei,2)}}
                                  <hr style="border:0;border-top:1px solid #000;margin:4px 0;">
                                  {{number_format($contract->d16_30_lei,2)}}
                              </td>
                              <td align ="right" width="5%">
                                  {{number_format($contract->p31_60_lei,2)}}
                                  <hr style="border:0;border-top:1px solid #000;margin:4px 0;">
                                  {{number_format($contract->d31_60_lei,2)}}
                              </td>
                              <td align ="right" width="5%">
                                  {{number_format($contract->p61_90_lei,2)}}
                                  <hr style="border:0;border-top:1px solid #000;margin:4px 0;">
                                  {{number_format($contract->d61_90_lei,2)}}
                              </td>
                              <td align ="right" width="5%">
                                  {{number_format($contract->ppeste_90_lei,2)}}
                                  <hr style="border:0;border-top:1px solid #000;margin:4px 0;">
                                  {{number_format($contract->dpeste_90_lei,2)}}
                              </td>
                            <td align ="center"  width="5%">
                                  {{$contract->serviciul_datoriei}}
                              </td>
                           </tr>
                           
                   
                       </td>
                     </tr>
                           @endforeach 
                  
                   <tr style="page-break-inside: avoid !important; font-weight: bold;">
                    
                     <td align="left"  colspan="5"  width="20%">
                       TOTAL {{$tiprisc[0]->expunere}}
                       </td>
                     
                              <td align ="right"   width="5%">
                                  {{number_format($tiprisc->sum("dob_reesalonata_lei"),2)}}
                                  <hr style="border:0;border-top:1px solid #000;margin:4px 0;">
                                  {{number_format($tiprisc->sum("pen_reesalonate_lei"),2)}}
                              </td>
                          
                               <td align ="right"  width="10%">
                                  {{number_format($tiprisc->sum("penalitati_lei"),2)}}
                              </td>
                              <td align ="right" width="10%">
                                  {{number_format($tiprisc->sum("principal_lei"),2)}}
                                  <hr style="border:0;border-top:1px solid #000;margin:4px 0;">
                                  {{number_format($tiprisc->sum("dobanzi_lei"),2)}}
                              </td>
                              <td align ="right" width="10%">
                                  {{number_format($tiprisc->sum("psub_0_lei"),2)}}
                                  <hr style="border:0;border-top:1px solid #000;margin:4px 0;">
                                  {{number_format($tiprisc->sum("dsub_0_lei"),2)}}
                              </td>
                              <td align ="right" width="5%">
                                  {{number_format($tiprisc->sum("p0_15_lei"),2)}}
                                  <hr style="border:0;border-top:1px solid #000;margin:4px 0;">
                                  {{number_format($tiprisc->sum("d0_15_lei"),2)}}
                              </td>
                              <td align ="right" width="5%">
                                  {{number_format($tiprisc->sum("p16_30_lei"),2)}}
                                  <hr style="border:0;border-top:1px solid #000;margin:4px 0;">
                                  {{number_format($tiprisc->sum("d16_30_lei"),2)}}
                              </td>
                              <td align ="right" width="5%">
                                  {{number_format($tiprisc->sum("p31_60_lei"),2)}}
                                  <hr style="border:0;border-top:1px solid #000;margin:4px 0;">
                                  {{number_format($tiprisc->sum("d31_60_lei"),2)}}
                              </td>
                              <td align ="right" width="5%">
                                  {{number_format($tiprisc->sum("p61_90_lei"),2)}}
                                  <hr style="border:0;border-top:1px solid #000;margin:4px 0;">
                                  {{number_format($tiprisc->sum("d61_90_lei"),2)}}
                              </td>
                              <td align ="right" width="5%">
                                  {{number_format($tiprisc->sum("ppeste_90_lei"),2)}}
                                  <hr style="border:0;border-top:1px solid #000;margin:4px 0;">
                                  {{number_format($tiprisc->sum("dpeste_90_lei"),2)}}
                              </td>
                             <td align ="right" rowspan ="2" width="5%">
                                  
                              </td>
                           </tr>
                            
                         @endforeach  
                           <tr style="page-break-inside: avoid !important; font-weight: bold;">
                    
                     <td align="left"  colspan="5"  width="20%">
                       TOTAL {{$tipcontract[0]->tip_contract}}
                       </td>
                     
                              <td align ="right"   width="5%">
                                  {{number_format($tipcontract->sum("dob_reesalonata_lei"),2)}}
                                  <hr style="border:0;border-top:1px solid #000;margin:4px 0;">
                                  {{number_format($tipcontract->sum("pen_reesalonate_lei"),2)}}
                              </td>
                          
                               <td align ="right"  width="10%">
                                  {{number_format($tipcontract->sum("penalitati_lei"),2)}}
                              </td>
                              <td align ="right" width="10%">
                                  {{number_format($tipcontract->sum("principal_lei"),2)}}
                                  <hr style="border:0;border-top:1px solid #000;margin:4px 0;">
                                  {{number_format($tipcontract->sum("dobanzi_lei"),2)}}
                              </td>
                              <td align ="right" width="10%">
                                  {{number_format($tipcontract->sum("psub_0_lei"),2)}}
                                  <hr style="border:0;border-top:1px solid #000;margin:4px 0;">
                                  {{number_format($tipcontract->sum("dsub_0_lei"),2)}}
                              </td>
                              <td align ="right" width="5%">
                                  {{number_format($tipcontract->sum("p0_15_lei"),2)}}
                                  <hr style="border:0;border-top:1px solid #000;margin:4px 0;">
                                  {{number_format($tipcontract->sum("d0_15_lei"),2)}}
                              </td>
                              <td align ="right" width="5%">
                                  {{number_format($tipcontract->sum("p16_30_lei"),2)}}
                                  <hr style="border:0;border-top:1px solid #000;margin:4px 0;">
                                  {{number_format($tipcontract->sum("d16_30_lei"),2)}}
                              </td>
                              <td align ="right" width="5%">
                                  {{number_format($tipcontract->sum("p31_60_lei"),2)}}
                                  <hr style="border:0;border-top:1px solid #000;margin:4px 0;">
                                  {{number_format($tipcontract->sum("d31_60_lei"),2)}}
                              </td>
                              <td align ="right" width="5%">
                                  {{number_format($tipcontract->sum("p61_90_lei"),2)}}
                                  <hr style="border:0;border-top:1px solid #000;margin:4px 0;">
                                  {{number_format($tipcontract->sum("d61_90_lei"),2)}}
                              </td>
                              <td align ="right" width="5%">
                                  {{number_format($tipcontract->sum("ppeste_90_lei"),2)}}
                                  <hr style="border:0;border-top:1px solid #000;margin:4px 0;">
                                  {{number_format($tipcontract->sum("dpeste_90_lei"),2)}}
                              </td>
                             <td align ="right" rowspan ="2" width="5%">
                                  
                              </td>
                           </tr>
                           
                         @endforeach  
                   <tr style="page-break-inside: avoid !important; font-weight: bold;">
                    
                     <td align="left"  colspan="5"  width="20%">
                       TOTAL GENERAL
                       </td>
                      <td align ="right"   width="5%">
                                  {{number_format($tabel->sum("dob_reesalonata_lei"),2)}}
                                  <hr style="border:0;border-top:1px solid #000;margin:4px 0;">
                                  {{number_format($tabel->sum("pen_reesalonate_lei"),2)}}
                              </td>
                          
                               <td align ="right"  width="10%">
                                  {{number_format($tabel->sum("penalitati_lei"),2)}}
                              </td>
                              <td align ="right" width="10%">
                                  {{number_format($tabel->sum("principal_lei"),2)}}
                                  <hr style="border:0;border-top:1px solid #000;margin:4px 0;">
                                  {{number_format($tabel->sum("dobanzi_lei"),2)}}
                              </td>
                              <td align ="right" width="10%">
                                  {{number_format($tabel->sum("psub_0_lei"),2)}}
                                  <hr style="border:0;border-top:1px solid #000;margin:4px 0;">
                                  {{number_format($tabel->sum("dsub_0_lei"),2)}}
                              </td>
                              <td align ="right" width="5%">
                                  {{number_format($tabel->sum("p0_15_lei"),2)}}
                                  <hr style="border:0;border-top:1px solid #000;margin:4px 0;">
                                  {{number_format($tabel->sum("d0_15_lei"),2)}}
                              </td>
                              <td align ="right" width="5%">
                                  {{number_format($tabel->sum("p16_30_lei"),2)}}
                                  <hr style="border:0;border-top:1px solid #000;margin:4px 0;">
                                  {{number_format($tabel->sum("d16_30_lei"),2)}}
                              </td>
                              <td align ="right" width="5%">
                                  {{number_format($tabel->sum("p31_60_lei"),2)}}
                                  <hr style="border:0;border-top:1px solid #000;margin:4px 0;">
                                  {{number_format($tabel->sum("d31_60_lei"),2)}}
                              </td>
                              <td align ="right" width="5%">
                                  {{number_format($tabel->sum("p61_90_lei"),2)}}
                                  <hr style="border:0;border-top:1px solid #000;margin:4px 0;">
                                  {{number_format($tabel->sum("d61_90_lei"),2)}}
                              </td>
                              <td align ="right" width="5%">
                                  {{number_format($tabel->sum("ppeste_90_lei"),2)}}
                                  <hr style="border:0;border-top:1px solid #000;margin:4px 0;">
                                  {{number_format($tabel->sum("dpeste_90_lei"),2)}}
                              </td>
                             <td align ="right" rowspan ="2" width="5%">
                                  
                              </td>
                           </tr>
                            
               </table>
    
      
     <br> <br>
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
                   <td  width="5%" >
                
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
     


  
