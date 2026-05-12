@extends('layouts.antet')

@section ('content')
  
      <table class="table table-condesed text_sm" border="1" style="border-collapse: collapse; page-break-inside: avoid !important;" width=100%  >
            <thead>
              <tr>
                <th colspan="11">
                     <center> <h3> Situație intrări pe articole  </center>
                    <center>{{$selectie}}  </h3></center>                
                </th>
              </tr>
            <tr >
              
                <th align ="center" colspan="4" width="35%">
                    Document
                </th>
               
                <th align ="center" rowspan="2" width="20%">
                    Denumire
                </th>
                 <th align ="center" rowspan="2" width="5%">
                    Cont
                </th>
                <th align ="center" rowspan="2" width="5%">
                    Cantitate
                </th>
               <th align ="center" rowspan="2" width="5%">
                    Pret intrare
                </th>
               
                <th align ="center" rowspan="2" width=" cu TVA
                </th>
                
                
            </tr>
            <tr>
               <th align ="center"  width="5%">
                    Tip
                </th>
                 <th align ="center"  width="5%">
                    Numar
                </th>
                <th align ="center"  width="5%">
                   Data
                </th>
                <th align ="center" width="20%">
                   Furnizor
                </th>
            </tr>
    </thead>
    <tbody>
                    @foreach($antetdocumenteprimite->groupby("cod") as $intrare)
                    <tr style=" page-break-inside: avoid !important;">
                      <td align="left" colspan="11">
                          <strong> ARTICOL {{$intrare[0]->cod." ".$intrare[0]->denumire}} </strong>
                      </td>
                     </tr> 
                    @foreach($intrare as $articol)
                        
            
                                  <tr style=" page-break-inside: avoid !important;">
                                     <td align="left" width="5%">
                                      {{$articol->antetdocumenteprimite->tip_document  }}
                                     </td>
                                      <td align="left" width="5%">
                                      {{$articol->antetdocumenteprimite->nr_document}}
                                      </td>
                                      <td align="left" width="5%">  
                                      {{dateFormatAfisare($articol->antetdocumenteprimite->data_document)}}
                                      </td>
                                      <td align="left" width="20%">
                                      {{$articol->antetdocumenteprimite->furnizor }}
                                      </td>
                                       
                                    </td>
                                      <td align="left" width="20%">
                                      {{ $articol->denumire }}
                                       
                                    </td>
                                     <td align="center" width="5%">
                                      {{ $articol->contd }}
                                       
                                    </td>
                                      <td align ="center" width="5%">
                                         {{$articol->cantitate}}
                                      </td>
                                      <td align ="right" width="5%">
                                          {{number_format($articol->pret_intrare,2)}}
                                      </td>
                                    
                                   
                                      <td align ="right" width="10%">
                                          {{number_format($articol->valoare_intrare+$articol->valoare_tva_intrare,2)}}
                                      </td>
                                      
                                      
                                  </tr>
                       
                     @endforeach 
                
                    <tr style=" page-break-inside: avoid !important;">
                    
                      <td width="60%" align="left" colspan="6">
                         <strong>
                          TOTAL ARTICOL {{$intrare[0]->cod." ".$intrare[0]->denumire}}
                          </strong>
                      </td>
                      <td width="5%" align="right" >
                        <h4>
                        <strong>
                       
                          {{ number_format(round($intrare->sum('cantitate'),2),2) }}
                       
                         </strong>
                       </h4>
                      </td>
                      <td width="5%" align="right" >
                        
                      </td>
                      <td width="10%" align="right" >
                        <h4>
                        <strong>
                       
                       
                       
                         </strong>
                       </h4>
                      </td>
                      <td width="10%" align="right" >
                        <h4>
                        <strong>
                        
                       
                           </strong>
                         </h4>
                      </td>
                      <td width="10%" align="right" >
                        <h4>
                        <strong>
                          {{ number_format(round($intrare->sum('valoare_intrare')+$intrare->sum('valoare_tva_intrare'),2),2) }}
                        </strong>
                         </h4>
                      </td>
                    </tr>
             
                    @endforeach 
             
                   <tr style=" page-break-inside: avoid !important;">
                    
                      <td width="60%" align="center" colspan="6">
                         <strong>
                          TOTAL
                          </strong>
                      </td>
                      <td width="5%" align="right" >
                        
                      </td>
                      <td width="5%" align="right" >
                        
                      </td>
                      <td width="10%" align="right" >
                        <h4>
                        <strong>
                       
                        
                       
                         </strong>
                       </h4>
                      </td>
                      <td width="10%" align="right" >
                        <h4>
                        <strong>
                        
                        
                           </strong>
                         </h4>
                      </td>
                      <td width="10%" align="right" >
                        <h4>
                        <strong>
                        
                          {{ number_format(round($antetdocumenteprimite->sum('valoare_intrare')+$antetdocumenteprimite->sum('valoare_tva_intrare'),2),2) }}
                           </strong>
                         </h4>
                      </td>
                    </tr>
                  </tbody>
               </table>
     
     

	
	
@stop