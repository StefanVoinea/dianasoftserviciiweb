@extends('layouts.antet')

@section ('content')
  
   
      <table class="table table-condesed text-sm" border="1" style="border-collapse: collapse; " width=100%>
            <thead>
            <tr>
              <td align="center" colspan="15">
                <center> <h3> JURNAL DE CUMPĂRĂRI </h3> </center>
                 <center> <h3>{{$selectie}} ANALITIC </h3></center>
   
              </td>
            </tr>          
            <tr >
              <th align ="center"  rowspan="2" width="3%">
                    <center>Nr <br> crt</center>
              </th>
              
                 <th align ="center" colspan="3" width="15%">
                    <center>Document</center>
              </th>
               <th align ="center" rowspan="2" width="13%">
                    <center>Furnizor/Cod fiscal </center>
              </th>
              <th align ="center" rowspan="2" width="7%">
                    Total document inclusiv TVA
              </th>
              <th align ="center" colspan="3" width="17%">
                    Achizitii de bunuri sau servicii din tara/import 
              </th>
              
               
                 <th align ="center" rowspan="2" width="10%">
                    Document de plata 
                    (nr,data)
                 </th>
                 <th align ="center" rowspan="2" width="7%">
                    Valoare platita inclusiv TVA
                </th>

                 <th align ="center" colspan="2" width="14%">
                    Operatiuni exigibile
              </th>
               <th align ="center" colspan="2" width="14%">
                    Operatiuni neexigibile
              </th>
                
            </tr>
            <tr>
              <th align ="center"  width="5%">
                    <center>Data</center>
              </th>
               <th align ="center"  width="5%">
                    <center>Tip</center>
              </th>
               <th align ="center" width="5%">
                    <center>Numar</center>
              </th>
              <th align ="center" width="3%">
                    Cota
              </th>
              
                <th align ="center" width="7%">
                    Baza
                </th>
                <th align ="center" width="7%">
                    TVA
                </th>
                
              <th align ="center" width="7%">
                    Baza
                </th>
                <th align ="center" width="7%">
                    TVA
              </th>
              <th align ="center" width="7%">
                    Baza
                </th>
                <th align ="center" width="7%">
                    TVA
              </th>
            </tr>

            </thead>
      <tbody>
                    @foreach($jurnal as $documentprimit)
                      
                      <tr style="page-break-inside: avoid !important;"> 
                       <td align="center" width="3%">
                       {{ $i++ }}.
                       </td>
                       <td align="center" width="5%">
                        {{dateFormatAfisare($documentprimit->data_document)}}
                        </td>
                         <td align="center" width="5%">
                        {{$documentprimit->tip_document}}
                        </td> 
                        <td align="center" width="5%">
                        {{$documentprimit->nr_document}}
                        </td> 
                        <td align ="leftr" width="13%">
                        {{$documentprimit->partener." ".$documentprimit->cui  }}
                        
                       </td>
                       <td align="right" width="7%">
                         @if($documentprimit->total!=0)
                         {{ number_format($documentprimit->total,2)}}
                          @endif
                      </td>
                      <td align="center" width="3%">
                        
                            {{ $documentprimit->procent_tva."%" }}
                        
                      </td>
                      <td align="right" width="7%">
                        @if($documentprimit->baza!=0)
                          {{ number_format($documentprimit->baza,2) }}
                        @endif
                      </td>
                      <td align="right" width="7%">
                          @if($documentprimit->tva!=0)
                            {{ number_format($documentprimit->tva,2) }}
                          @endif
                         
                      </td>
                     
                       <td align="right" width="5%">
                        @if($documentprimit->nr_plata!="")
                            {{ $documentprimit->nr_plata." / ".dateFormatAfisare($documentprimit->data_plata)}}
                        @endif
                      </td>
                       <td align="right" width="7%">
                        @if($documentprimit->suma_platita!=0)
                            {{ number_format($documentprimit->suma_platita,2)}}
                        @endif
                      </td>
                     <td align="right" width="7%">
                         @if($documentprimit->baza_exigibil!=0)
                          {{ number_format($documentprimit->baza_exigibil,2) }}
                         @endif
                      </td>
                      <td align="right" width="7%">
                        @if($documentprimit->tva_exigibil!=0)
                            {{ number_format($documentprimit->tva_exigibil,2) }}
                        @endif
                         
                      </td>
                      <td align="right" width="7%">
                        @if($documentprimit->baza_neexigibil!=0)
                          {{ number_format($documentprimit->baza_neexigibil,2) }}
                        @endif
                      </td>
                      <td align="right" width="7%">
                        @if($documentprimit->tva_neexigibil!=0)
                            {{ number_format($documentprimit->tva_neexigibil,2) }}
                        @endif
                         
                      </td>
                      </tr>
                     @endforeach 
                   
                    @foreach(collect($jurnal)->groupby("procent_tva_str") as $documentprimit)
                    <tr   style= "page-break-inside: avoid !important;">
                    
                      <td width="31%" align="left" colspan="5" >
                         <strong>
                          {{"TOTAL COTA   ". $documentprimit[0]->procent_tva."%  -> ".
                          collect($documentprimit)->where('tip_document','Factura')->count('nr_document')." facturi ".
                          (collect($documentprimit)->where('tip_document','Bon fiscal cu CUI')->count('nr_document')?collect($documentprimit)->where('tip_document','Bon fiscal cu CUI')->count('nr_document')." bonuri fiscale cu CUI ":"")
                          }}
                          </strong>
                      </td>
                     
                      <td width="7%" align="right" >
                           <strong>
                           {{ number_format(round(collect($documentprimit)->sum('total'),2),2) }}
                           </strong>
                        
                      </td>
                      <td width="3%" align="right" >
                       
                      </td>
                      <td width="7%" align="right" >
                       
                        <strong>
                        
                          {{ number_format(round(collect($documentprimit)->sum('baza'),2),2) }}
                           </strong>
                       
                      </td>
                      <td width="7%" align="right" >
                       
                        <strong>
                        
                          {{ number_format(round(collect($documentprimit)->sum('tva'),2),2) }}
                           </strong>

                      </td>
                    
                      <td width="10%" align="right" >
                      
                      
                      </td>
                      <td width="7%" align="right" >
                      
                        <strong>
                        
                          {{ number_format(round(collect($documentprimit)->sum('suma_platita'),2),2) }}
                           </strong>
                         
                      </td>
                       <td width="7%" align="right" >
                        
                        <strong>
                        
                          {{ number_format(round(collect($documentprimit)->sum('baza_exigibil'),2),2) }}
                           </strong>
                         
                      </td>
                      <td width="7%" align="right" >
                     
                        <strong>
                        
                          {{ number_format(round(collect($documentprimit)->sum('tva_exigibil'),2),2) }}
                           </strong>
                        
                      </td>
                      <td width="7%" align="right" >
                       
                        <strong>
                        
                          {{ number_format(round(collect($documentprimit)->sum('baza_neexigibil'),2),2) }}
                           </strong>
                        
                      </td>
                      <td width="7%" align="right" >
                       
                        <strong>
                        
                          {{ number_format(round(collect($documentprimit)->sum('tva_neexigibil'),2),2) }}
                           </strong>
                        
                      </td>
                    </tr>
                    @endforeach
                     <tr   style= "page-break-inside: avoid !important;">
                    
                      <td width="31%" align="left" colspan="5" >
                         <strong>
                          {{"TOTAL GENERAL   "}}
                          </strong>
                      </td>
                     
                      <td width="7%" align="right" >
                        <h4>
                        <strong>
                        
                          {{ number_format(round(collect($jurnal)->sum('total'),2),2) }}
                           </strong>
                         </h4>
                      </td>
                      <td width="3%" align="right" >
                       
                      </td>
                      <td width="7%" align="right" >
                        <h4>
                        <strong>
                        
                          {{ number_format(round(collect($jurnal)->sum('baza'),2),2) }}
                           </strong>
                         </h4>
                      </td>
                      <td width="7%" align="right" >
                        <h4>
                        <strong>
                        
                          {{ number_format(round(collect($jurnal)->sum('tva'),2),2) }}
                           </strong>
                         </h4>
                      </td>
                     
                      <td width="10%" align="right" >
                      
                       
                      </td>
                      <td width="7%" align="right" >
                         <h4>
                        <strong>
                        
                          {{ number_format(round(collect($jurnal)->sum('suma_platita'),2),2) }}
                           </strong>
                         </h4>
                      </td>
                       <td width="7%" align="right" >
                        <h4>
                        <strong>
                        
                          {{ number_format(round(collect($jurnal)->sum('baza_exigibil'),2),2) }}
                           </strong>
                         </h4>
                      </td>
                      <td width="7%" align="right" >
                        <h4>
                        <strong>
                        
                          {{ number_format(round(collect($jurnal)->sum('tva_exigibil'),2),2) }}
                           </strong>
                         </h4>
                      </td>
                      <td width="7%" align="right" >
                        <h4>
                        <strong>
                        
                          {{ number_format(round(collect($jurnal)->sum('baza_neexigibil'),2),2) }}
                           </strong>
                         </h4>
                      </td>
                      <td width="7%" align="right" >
                        <h4>
                        <strong>
                        
                          {{ number_format(round(collect($jurnal)->sum('tva_neexigibil'),2),2) }}
                           </strong>
                         </h4>
                      </td>
                    </tr>
                     <tr  style= "page-break-inside: avoid !important;">
                    
                      <td width="31%" align="left" colspan="5">
                         <strong>
                          {{"DIN CARE TVA NEDEDUCTIBIL   "}}
                          </strong>
                      </td>
                     
                      <td width="7%" align="right" >
                       
                      </td>
                      <td width="3%" align="right" >
                       
                      </td>
                      <td width="7%" align="right" >
                    
                      </td>
                      <td width="7%" align="right" >
                       
                        <strong>
                        
                          {{ number_format($tvaNED[0]->tva_nedeductibil,2) }}
                           </strong>
                       
                      </td>
                     
                      <td width="10%" align="right" >
                      
                       
                      </td>
                      <td width="7%" align="right" >
                        
                      </td>
                       <td width="7%" align="right" >
                        
                      </td>
                      <td width="7%" align="right" >
                       
                      </td>
                      <td width="7%" align="right" >
                       
                      </td>
                      <td width="7%" align="right" >
                       
                      </td>
                    </tr>
                     <tr  style= "page-break-inside: avoid !important;">
                    
                      <td width="31%" align="left" colspan="5">
                         <strong>
                          {{"DIN CARE IN ULTIMELE 6 LUNI   "}}
                          </strong>
                      </td>
                     
                      <td width="7%" align="right" >
                       
                      </td>
                      <td width="3%" align="right" >
                       
                      </td>
                      <td width="7%" align="right" >
                    
                      </td>
                      <td width="7%" align="right" >
                        <h4>
                       
                         </h4>
                      </td>
                     
                      <td width="10%" align="right" >
                      
                       
                      </td>
                      <td width="7%" align="right" >
                        
                      </td>
                       <td width="7%" align="right" >
                        
                      </td>
                      <td width="7%" align="right" >
                       
                      </td>
                      <td width="7%" align="right" >
                        <strong>
                        {{ number_format(round(collect($jurnal)->where('data_document','>=',$data6Luni)->sum('baza_neexigibil'),2),2) }}
                        </strong>
                      </td>
                      <td width="7%" align="right" >
                        <strong>
                        {{ number_format(round(collect($jurnal)->where('data_document','>=',$data6Luni)->sum('tva_neexigibil'),2),2) }}
                        </strong>
                      </td>
                    </tr>
                     </tbody>
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