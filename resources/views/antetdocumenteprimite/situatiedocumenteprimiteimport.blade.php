@extends('layouts.antet')

@section ('content')
  
   <center> <h3> Situație documente primite </h3> </center>
   <center> <h3>{{$selectie}} tip lista  Sintetic </h3></center>
   
                    @foreach($antetdocumenteprimite->groupby("tip_valuta") as $documentgestiune)
                    <hr>
                       <table class="table table-condesed" width=100%  >
            
                            <tr >
                              <th align ="center" rowspan="2" width="5%">
                                    <center>Nr <br> crt</center>
                                </th>
                                <th align ="center"  rowspan="2" width="35%">
                                    Document
                                
                                </th>
                                <th align ="center"  rowspan="2" width="10%">
                                    Tip valuta
                                </th>
                                <th align ="center"  rowspan="2" width="10%">
                                    Curs
                                </th>
                                
                                <th align ="center" colspan="5" width="10%">
                                    <center>Valoare {{$documentgestiune[0]->tip_valuta}}</center> 
                                </th>
                                <th align ="center" colspan="5" width="10%">
                                    <center>Valoare fara TVA</center> 
                                    <center>(RON)</center> 
                                </th>
                                <th align ="center" colspan="5" width="10%">
                                    <center>Valoare TVA</center> 
                                    <center>(RON)</center> 
                                </th>
                                <th align ="center" colspan="5" width="10%">
                                    <center>Valoare cu TVA</center> 
                                    <center>(RON)</center> 
                                </th>
                            </tr>
                           

                     </table>
                      <hr>
                    TIP VALUTA {{$documentgestiune[0]->tip_valuta}}
                    <hr>
     				<table class="table table-condesed" style="border-collapse: collapse; " width=100%>      
                    @foreach($documentgestiune as $det_doc)
                       
                      <tr style="border-bottom:1pt solid black; page-break-inside: avoid !important;"> 
                       <td align="center" width="5%">
                       {{ $i++ }}.
                       </td>
                        
                      <td align="left" width="35%">
                       {{$det_doc->tip_document  }}
                      
                      {{$det_doc->seria }}
                    
                      {{$det_doc->nr_document .' / '.dateFormatAfisare($det_doc->data_document)  }}
                      <br>
                      
                      {{ $det_doc->partener["denumire"] }}
                         
                      </td>
                      <td align="center" width="10%">
                         {{$det_doc->tip_valuta }}
                         </td>
                       <td align="center" width="10%">
                      {{number_format(round($det_doc->curs,4),4) }}
                         
                      </td>
                     
                      <td align="right" width="10%">
                      {{ number_format(round($det_doc->detaliudocumenteprimite->sum("valoare_intrare_in_valuta"),2),2) }}
                         
                      </td>
                      <td align="right" width="10%">
                         {{ number_format(round($det_doc->detaliudocumenteprimite->sum("valoare_intrare"),2),2) }}
                         
                      </td>
                      <td align="right" width="10%">
                       {{ number_format(round($det_doc->detaliudocumenteprimite->sum("valoare_tva_intrare"),2),2) }}
                         
                      </td>
                      <td align="right" width="10%">
                      {{ number_format(round($det_doc->detaliudocumenteprimite->sum(function($value){
                               

                             return $value['valoare_intrare']+$value['valoare_tva_intrare'];
                            
                      }
                        ),2),2) }}
                         
                      </td>
                      </tr>
                     
                     @endforeach
                    </table>
                     <hr> 
     			     <table class="table table-condesed" width=100%> 
                    <tr>
                    
                      <td width="60%" align="center" colspan="4">
                         <strong>
                          TOTAL TIP VALUTA {{$det_doc->tip_valuta}}
                          </strong>
                      </td>
                      <td width="10%" align="right" >
                        <h4>
                        <strong>
                       
                          {{ number_format(round($documentgestiune->sum(function($value){
                              return $value->detaliudocumenteprimite->sum('valoare_intrare_in_valuta');
                          }),2),2) }}
                       
                         </strong>
                       </h4>
                      </td>
                      <td  align="right" width="10%">
                        <strong>
                        {{ number_format(round($documentgestiune->sum(function($value){
                              return $value->detaliudocumenteprimite->sum('valoare_intrare');
                          }),2),2) }}
                        </strong>  
                      </td>
                      <td align="right" width="10%">
                        <strong>
                       {{ number_format(round($documentgestiune->sum(function($value){
                              return $value->detaliudocumenteprimite->sum('valoare_tva_intrare');
                          }),2),2) }}
                          </strong>
                      </td>
                      
                      <td  align="right" width="10%">
                        <strong>
                          {{ number_format(round($documentgestiune->sum(function($val){
                               return $val->detaliudocumenteprimite->sum(function($value){
                                return $value['valoare_intrare']+$value['valoare_tva_intrare'];

                              }); 
                              
                      })
                        ,2),2) }}
                          </strong>
                      </td>
                    </tr>
               </table>
                <hr> 
                    @endforeach 
             
  
    
     

	
	
@stop