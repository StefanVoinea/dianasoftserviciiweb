@extends('layouts.antet')

@section ('content')
  
      <table class="table table-condesed text-sm" border="1" style="border-collapse: collapse;" width="100%"  >
            <thead>
              <tr>
                <th colspan="13">
                     <center> <strong> Balanta imobilizarilor corporale si necorporale </strong> </center>
                     <center> <strong>{{$selectie}} </strong></center>
                </th>
              </tr>
            <tr >
            
              <th align ="center" rowspan="2" width="5%">
                    <center>Nr <br> inventar</center>
              </th>
                <th align ="center" rowspan="2" width="7%">
                    <center>Denumire</center>
              </th>
                <th align ="center" rowspan="2" width="5%">
                    <center>Cod <br> clasificare</center>
                </th>
                <th align ="center" width="8%">
                   <center> Data P.I.F.</center>
              </th>
            
              <th align ="center" width="8%">
                    <center>Cantitate</center>
                </th>
                
                <th align ="center" rowspan="2" width="9%">
                    <center>Valoare <br> de inventar</center>
                </th>
                <th align ="center" rowspan="2" width="9%">
                    <center>Valoare <br> de amortizat</center>
                </th>
                <th align ="center" rowspan="2" width="9%">
                    <center>Valoare <br> amortizata </center>
                </th>
                <th align ="center" colspan="3" width="27%">
                    <center>Amortizare</center>
                
                </th>
                <th align ="center" width="3%">
                    <center>D.N.U.</center>
                </th>
               
                <th align ="center"  width="10%">
                    <center>Furnizor</center>
              </th>
              
            </tr>
            <tr style="border-bottom:2pt solid #dab295;">
              
             
            
              <th align ="center" width="8%">
                   <center>Data intrarii</center>
              </th>
              
                <th align ="center" width="8%">
                    <center>Pret intrare</center>
                </th>
               
                <th align ="center"width="9%">
                    <center>lunara</center>
                </th>
                <th align ="center"width="9%">
                    <center>deductibila</center>
                </th>
                <th align ="center"width="9%">
                    <center>nedeductibila</center>
                </th>
                <th align ="center" width="3%">
                    <center>D.R.U.</center>
                </th>
              
              <th align ="center"  width="10%">
                    <center>Nr document</center>
              </th>
            </tr>
            </thead>
    <!--  </table>
      <hr>
                   
     				<table class="table table-condesed" style="border-collapse: collapse;" width="100%">    -->   
                    @foreach($mijloacefixe->groupby("cont") as $mijlocfixcont)
                      @foreach($mijlocfixcont->groupby("gestiune_id") as $mijlocfixgestiune)
                       @foreach($mijlocfixgestiune as $mijlocfix)
                      <tr style="border-bottom:1pt solid black; page-break-inside: avoid !important;"> 
                      
                       <td align="center" width="5%">
                       {{$mijlocfix->nr_inventar }}
                       </td>
                        <td align="left" width="7%">
                       {{ $mijlocfix->denumire }}
                       </td>
                      
                      <td align="center" width="5%">
                      {{ $mijlocfix->cod_clasificare }}
                         
                      </td>
                      <td align="center" width="8%">
                      {{ dateFormatAfisare($mijlocfix->data_punerii_in_functiune) }} <br>
                      {{ dateFormatAfisare($mijlocfix->data_intrarii) }}
                      </td>
                     
                      <td align="center" width="8%">
                       {{ $mijlocfix->cantitate}}
                      <br>
                        {{ number_format($mijlocfix->pret,2)}}
                      </td>
                      <td align="right" width="9%">
                       {{ number_format($mijlocfix->valoare_de_inventar,2)}}
                      </td>
                      <td align="right" width="9%">
                       {{ number_format($mijlocfix->valoare_de_amortizat,2)}}
                      </td>
                      <td align="right" width="9%">
                       {{ number_format($mijlocfix->valoare_amortizata,2)}}
                      </td>
                      <td align="right" width="9%">
                       {{ number_format($mijlocfix->amortizare_lunara,2)}}
                      </td>
                      <td align="right" width="9%">
                       {{ number_format($mijlocfix->amortizare_lunara_deductibila,2)}}
                      </td>
                       <td align="right" width="9%">
                       {{ number_format($mijlocfix->amortizare_lunara-$mijlocfix->amortizare_lunara_deductibila,2)}}
                      </td>
                      <td align="center" width="3%">
                       {{ $mijlocfix->dnu*12}}
                     <br>
                       {{ $mijlocfix->luni_de_amortizat}}
                      </td>
                       <td align="left" width="10%">
                        {{$mijlocfix->partener["denumire"]}} <br> {{$mijlocfix->nr_document}}
                        </td>
                      </tr>
                     @endforeach 
                       <tr style="page-break-inside: avoid !important;">
                    
                      <td width="33%" align="left" colspan="5">
                         <strong>
                          TOTAL CONT {{$mijlocfixcont[0]->cont}} GESTIUNEA {{$mijlocfixgestiune[0]->gestiune["denumire"]}} 
                          </strong>
                      </td>
                     
                      <td width="9%" align="right" >
                        <h4>
                        <strong>
                        
                          {{ number_format(round($mijlocfixgestiune->sum('valoare_de_inventar'),2),2) }}
                           </strong>
                         </h4>
                      </td>
                      <td width="9%" align="right" >
                        <h4>
                        <strong>
                        
                          {{ number_format(round($mijlocfixgestiune->sum('valoare_de_amortizat'),2),2) }}
                           </strong>
                         </h4>
                      </td>
                      <td width="9%" align="right" >
                        <h4>
                        <strong>
                        
                          {{ number_format(round($mijlocfixgestiune->sum('valoare_amortizata'),2),2) }}
                           </strong>
                         </h4>
                      </td>
                      <td width="9%" align="right" >
                        <h4>
                        <strong>
                        
                          {{ number_format(round($mijlocfixgestiune->sum('amortizare_lunara'),2),2) }}
                           </strong>
                         </h4>
                      </td>
                     <td width="9%" align="right" >
                        <h4>
                        <strong>
                        
                          {{ number_format(round($mijlocfixgestiune->sum('amortizare_lunara_deductibila'),2),2) }}
                           </strong>
                         </h4>
                      </td>
                      <td width="9%" align="right" >
                        <h4>
                        <strong>
                        
                          {{ number_format(round($mijlocfixgestiune->sum('amortizare_lunara')-$mijlocfixgestiune->sum('amortizare_lunara_deductibila'),2),2) }}
                           </strong>
                         </h4>
                      </td>
                      <td width="3%" align="right" >
                        
                      </td>
                      
                      <td width="10%" align="right" >
                        
                      </td>
                      
                    
                      

                    </tr>
                      @endforeach 

                        <tr style="page-break-inside: avoid !important;">
                    
                      <td width="33%" align="left" colspan="5">
                         <strong>
                          TOTAL CONT {{$mijlocfixcont[0]->cont}} 
                          </strong>
                      </td>
                     
                      <td width="9%" align="right" >
                        <h4>
                        <strong>
                        
                          {{ number_format(round($mijlocfixcont->sum('valoare_de_inventar'),2),2) }}
                           </strong>
                         </h4>
                      </td>
                      <td width="9%" align="right" >
                        <h4>
                        <strong>
                        
                          {{ number_format(round($mijlocfixcont->sum('valoare_de_amortizat'),2),2) }}
                           </strong>
                         </h4>
                      </td>
                      <td width="9%" align="right" >
                        <h4>
                        <strong>
                        
                          {{ number_format(round($mijlocfixcont->sum('valoare_amortizata'),2),2) }}
                           </strong>
                         </h4>
                      </td>
                      <td width="9%" align="right" >
                        <h4>
                        <strong>
                        
                          {{ number_format(round($mijlocfixcont->sum('amortizare_lunara'),2),2) }}
                           </strong>
                         </h4>
                      </td>
                    
                      <td width="9%" align="right" >
                        <h4>
                        <strong>
                        
                          {{ number_format(round($mijlocfixgestiune->sum('amortizare_lunara_deductibila'),2),2) }}
                           </strong>
                         </h4>
                      </td>
                       <td width="9%" align="right" >
                        <h4>
                        <strong>
                        
                          {{ number_format(round($mijlocfixcont->sum('amortizare_lunara')-$mijlocfixgestiune->sum('amortizare_lunara_deductibila'),2),2) }}
                           </strong>
                         </h4>
                      </td>
                      <td width="3%" align="right" >
                        
                      </td>
                      
                      <td width="10%" align="right" >
                        
                      </td>
                     

                    </tr>

                     @endforeach 
                   
                    <tr style="page-break-inside: avoid !important;">
                    
                      <td width="33%" align="left" colspan="5">
                         <strong>
                          TOTAL GENERAL
                          </strong>
                      </td>
                     
                      <td width="9%" align="right" >
                        <h4>
                        <strong>
                        
                          {{ number_format(round($mijloacefixe->sum('valoare_de_inventar'),2),2) }}
                           </strong>
                         </h4>
                      </td>
                      <td width="9%" align="right" >
                        <h4>
                        <strong>
                        
                          {{ number_format(round($mijloacefixe->sum('valoare_de_amortizat'),2),2) }}
                           </strong>
                         </h4>
                      </td>
                      <td width="9%" align="right" >
                        <h4>
                        <strong>
                        
                          {{ number_format(round($mijloacefixe->sum('valoare_amortizata'),2),2) }}
                           </strong>
                         </h4>
                      </td>
                      <td width="9%" align="right" >
                        <h4>
                        <strong>
                        
                          {{ number_format(round($mijloacefixe->sum('amortizare_lunara'),2),2) }}
                           </strong>
                         </h4>
                      </td>
                     
                      <td width="9%" align="right" >
                         <h4>
                        <strong>
                        
                          {{ number_format(round($mijloacefixe->sum('amortizare_lunara_deductibila'),2),2) }}
                           </strong>
                         </h4>
                      </td>
                      <td width="9%" align="right" >
                         <h4>
                        <strong>
                        
                          {{ number_format(round($mijloacefixe->sum('amortizare_lunara')-$mijloacefixe->sum('amortizare_lunara_deductibila'),2),2) }}
                           </strong>
                         </h4>
                      </td>
                       <td width="3%" align="right" >
                        
                      </td>
                      <td width="15%" align="right" >
                        
                      </td>
                      
                    

                    </tr>
               </table>
    
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