@extends('layouts.antet')
@section ('content')
<table  class="text-sm table table-condesed" border="1" style="border-collapse: collapse;" width=100%>
            <thead>
               <tr style="page-break-inside: avoid !important;">
              <th align ="center" colspan="6" width="100%">
                  <center> <h3> {{$titluRaport}} </h3></center>
              </th>
              </tr>    
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
                  Sold EURO
              </th> 
              <th align ="center"   width="5%">
                  Sold LEI
              </th>
              
              
            </tr>
         
            
            </thead>
                @foreach($tabel->sortBy("nume")->groupBy("tip_valuta") as $tipvaluta)
                            
                          <tr style="page-break-inside: avoid !important;font-weight: bold;">
                            <td align="left" colspan="6" width="100%">
                              Tip valuta :{{$tipvaluta[0]->tip_valuta}}    
                            </td>
                          </tr> 
                            @foreach($tipvaluta as $contract)
                         
                           <tr style="page-break-inside: avoid !important;">
                              <td align ="center"   width="3%">
                                  {{$i++}}
                              </td>
                             <td align ="left"   width="5%">
                                  {{$contract->agentia}}
                              </td>
                              <td align ="left"   width="7%">
                                  {{$contract->nume}}
                              </td>
                               
                               <td align ="center"    width="5%">
                                  {{$contract->nr_contract."/".dateFormatAfisare($contract->data_contract)}}
                              </td>
                               
                              
                              <td align ="right"   width="5%">
                                  {{number_format($contract->echivalent_euro,2)}}
                              </td>
                              <td align ="right"   width="5%">
                                  {{number_format($contract->sold_lei,2)}}
                                  
                              </td>
                          
                             
                           </tr>
                           
                   
                       </td>
                     </tr>
                           @endforeach 
                  
                   <tr style="page-break-inside: avoid !important; font-weight: bold;">
                    
                      <td align="left"  colspan="4"  width="20%">
                       TOTAL {{$tipvaluta[0]->tip_valuta}}
                      </td>
                     <td align ="right"   width="5%">
                        {{number_format($tipvaluta->sum("echivalent_euro"),2)}}
                      </td>
                      <td align ="right"  width="10%">
                        {{number_format($tipvaluta->sum("sold_lei"),2)}}
                      </td>
                              
                           </tr>
                            
                         @endforeach  
                         <tr style="page-break-inside: avoid !important; font-weight: bold;">
                    
                     <td align="left"  colspan="4"  width="20%">
                       <br><br>
                       </td>
                       <td align ="right"   width="5%">
                        
                      </td>
                      <td align ="right"  width="10%">
                        
                      </td>
                           </tr>
                        
                  
                   <tr style="page-break-inside: avoid !important; font-weight: bold;">
                    
                     <td align="left"  colspan="4"  width="20%">
                       TOTAL CONTRACTE IN EURO
                       </td>
                       <td align ="right"   width="5%">
                        {{number_format($tabel->where("tip_valuta","EUR")->sum("echivalent_euro"),2)}}
                      </td>
                      <td align ="right"  width="10%">
                        {{number_format($tabel->where("tip_valuta","EUR")->sum("sold_lei"),2)}}
                      </td>
                           </tr>  
                   <tr style="page-break-inside: avoid !important; font-weight: bold;">
                    
                     <td align="left"  colspan="4"  width="20%">
                       TOTAL CONTRACTE IN LEI
                       </td>
                       <td align ="right"   width="5%">
                        {{number_format($tabel->where("tip_valuta","LEI")->sum("echivalent_euro"),2)}}
                      </td>
                      <td align ="right"  width="10%">
                        {{number_format($tabel->where("tip_valuta","LEI")->sum("sold_lei"),2)}}
                      </td>
                           </tr>             
                   <tr style="page-break-inside: avoid !important; font-weight: bold;">
                    
                     <td align="left"  colspan="4"  width="20%">
                       TOTAL GENERAL
                       </td>
                       <td align ="right"   width="5%">
                        {{number_format($tabel->sum("echivalent_euro"),2)}}
                      </td>
                      <td align ="right"  width="10%">
                        {{number_format($tabel->sum("sold_lei"),2)}}
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
     


  
