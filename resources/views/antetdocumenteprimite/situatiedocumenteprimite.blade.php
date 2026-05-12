@extends('layouts.antet')

@section ('content')
  
   <center> <h3> Situație documente primite </h3> </center>
   <center> <h3>{{$selectie}} tip lista  Sintetic </h3></center>
   
                    <hr>
                       <table class="table table-condesed" width=100%  >
            
                            <tr >
                              <th align ="center"  width="5%">
                                    <center>Nr <br> crt</center>
                                </th>
                                <th align ="center"  width="65%">
                                    Document
                                
                                </th>
                               
                              
                                <th align ="center" colspan="5" width="10%">
                                    <center>Valoare</center> 
                                    <center>(RON)</center> 
                                </th>
                            </tr>
                           

                     </table>
                    
                    <hr>
     				         <table class="table table-condesed" style="border-collapse: collapse; " width=100%>      
                    @foreach($antetdocumenteprimite as $det_doc)
                       
                      <tr style="border-bottom:1pt solid black; page-break-inside: avoid !important;"> 
                       <td align="center" width="5%">
                       {{ $i++ }}.
                       </td>
                        
                      <td align="left" width="65%">
                       {{$det_doc->tip_document  }}
                      
                      {{$det_doc->seria }}
                    
                      {{$det_doc->nr_document .' / '.dateFormatAfisare($det_doc->data_document) .' '. $det_doc->partener["denumire"] }}
                         
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
                    
                      <td width="70%" align="center" colspan="2">
                         <strong>
                          TOTAL 
                          </strong>
                      </td>
                      
                     
                      
                      <td  align="right" width="10%">
                        <strong>
                          {{ number_format(round($antetdocumenteprimite->sum(function($val){
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
                 
  
    
     

	
	
@stop