@extends('layouts.antet')

@section ('content')
  
      <table class="table table-condesed text-sm" border="1" style="border-collapse: collapse; " width=100%  >
            <thead>
              <tr>
                <th align ="center" colspan="10" width="100%">
                     <h3><center> JURNAL DE VÂNZĂRI </center>
                     <center> {{$selectie}} ANALITIC </center>
                   </h3>
                </th>
              </tr>
   <!-- <hr> -->
            <tr style="border-top:1pt solid black; page-break-inside: avoid !important;">
              <th align ="center"  rowspan="2" width="5%">
                    <center>Nr <br> crt</center>
              </th>
              
               <th align ="center" colspan="3" width="20%">
                    <center>Document</center>
              </th>
               <th align ="center" rowspan="2" width="30%">
                    <center>Client/Cod fiscal </center>
              </th>

              <th align ="center" rowspan="2" width="10%">
                    Total document inclusiv TVA
              </th>
              <th align ="center" colspan="3" width="25%">
                    Livrari de bunuri / prestari servicii taxabile 
              </th>
              
                <th align ="center" rowspan="2" width="10%">
                    Operatiuni neimpozabile
                </th>
                
                
            </tr>
            <tr style="border-bottom:1pt solid black;">
              <th align ="center"  width="10%">
                    <center>Data</center>
              </th>
               <th align ="center"  width="5%">
                    <center>Tip</center>
              </th>
               <th align ="center" width="5%">
                    <center>Numar</center>
              </th>
              <th align ="center" width="5%">
                    Cota
              </th>
              
                <th align ="center" width="10%">
                    Baza
                </th>
                <th align ="center" width="10%">
                    TVA
              </th>
             
            </tr>
            </thead>
            <tbody>
                    @foreach($jurnal as $vanzare)
                      
                      <tr style="border-bottom:1pt solid black; page-break-inside: avoid !important;"> 
                       <td align="center" width="5%">
                       {{ $i++ }}.
                       </td>
                       <td align="center" width="10%"> 
                       {{dateFormatAfisare($vanzare->data_document)}}
                       </td>
                       <td align="left" width="5%">
                         {{$vanzare->tip_document}}
                       </td> 
                       <td align="center" width="5%">
                         {{$vanzare->nr_document}}
                       </td>
                       <td align="left" width="30%">
                         {{$vanzare->partener." ". $vanzare->cui}}
                       </td>
                       
                       <td align="right" width="10%">
                         {{ number_format($vanzare->total,2)}}
                      </td>
                      <td align="center" width="5%">
                        
                            {{ $vanzare->procent_tva."%" }}
                        
                      </td>
                      <td align="right" width="10%">
                        @if($vanzare->baza!=0)
                          {{ number_format($vanzare->baza,2) }}
                        @endif
                      </td>
                      <td align="right" width="10%">
                          @if($vanzare->tva!=0)
                            {{ number_format($vanzare->tva,2) }}
                          @endif
                         
                      </td>
                      <td align="right" width="10%">
                        @if($vanzare->neimpozabil!=0)
                            {{ number_format($vanzare->neimpozabil,2)}}
                        @endif
                      </td>
                    
                      </tr>
                     @endforeach 
                     </tbody>
                    </table>
                   
             
   <hr>
     <table class="table table-condesed" style="border-collapse: collapse; " width=100%> 
                    @foreach(collect($jurnal)->groupby("procent_tva_str") as $vanzare)
                    <tr  style="border-bottom:1pt solid black; page-break-inside: avoid !important;">
                    
                      <td width="55%" align="left" colspan="5">
                         <strong>
                          {{"Total cota ". $vanzare[0]->procent_tva."%   numar facturi: ".collect($vanzare)->where('tip_document','Factura')->count('nr_document')}}
                          </strong>
                      </td>
                     
                      <td width="10%" align="right" >
                      
                        <strong>
                        
                          {{ number_format(round(collect($vanzare)->sum('total'),2),2) }}
                           </strong>
                        
                      </td>
                      <td width="5%" align="right" >
                       
                      </td>
                      <td width="10%" align="right" >
                       
                        <strong>
                        
                          {{ number_format(round(collect($vanzare)->sum('baza'),2),2) }}
                           </strong>
                       
                      </td>
                      <td width="10%" align="right" >
                       
                        <strong>
                        
                          {{ number_format(round(collect($vanzare)->sum('tva'),2),2) }}
                           </strong>

                      </td>
                      <td width="10%" align="right" >
                      
                        <strong>
                        
                          {{ number_format(round(collect($vanzare)->sum('neimpozabil'),2),2) }}
                           </strong>
                         
                      </td>
                      
                    </tr>
                    @endforeach
                     <tr>
                    
                      <td width="55%" align="left" colspan="5">
                         <strong>
                          {{"TOTAL GENERAL   "}}
                          </strong>
                      </td>
                     
                      <td width="10%" align="right" >
                        <h4>
                        <strong>
                        
                          {{ number_format(round(collect($jurnal)->sum('total'),2),2) }}
                           </strong>
                         </h4>
                      </td>
                      <td width="5%" align="right" >
                       
                      </td>
                      <td width="10%" align="right" >
                        <h4>
                        <strong>
                        
                          {{ number_format(round(collect($jurnal)->sum('baza'),2),2) }}
                           </strong>
                         </h4>
                      </td>
                      <td width="10%" align="right" >
                        <h4>
                        <strong>
                        
                          {{ number_format(round(collect($jurnal)->sum('tva'),2),2) }}
                           </strong>
                         </h4>
                      </td>
                      <td width="10%" align="right" >
                        <h4>
                        <strong>
                        
                          {{ number_format(round(collect($jurnal)->sum('neimpozabil'),2),2) }}
                           </strong>
                         </h4>
                      </td>
                     
                    </tr>
               </table>
     <hr> 
      
     <br>
     <table class="table table-condesed" width=100% > 
                 <tr>
                  <td  width="15%">
                       
                  </td>
                  <td  width="30%" align="center" >
                   ADMINISTRATOR,<br><br>
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