@extends('layouts.antet')

@section ('content')
  
   <center> <h3> Situație N.I.R.-uri </h3> </center>
   <center> <h3>{{$selectie}} tip lista  Sintetic </h3></center>
   <hr>
      <table class="table table-condesed" width=100%  >
            
            <tr >
              <th align ="center" rowspan="2" width="3%">
                    <center>Nr <br> crt</center>
                </th>
                <th align ="center"  rowspan="2" width="5%">
                    Nr nir
                </th>
                <th align ="center"  rowspan="2" width="7%">
                    Data nir
                </th>
                <th align ="center"  rowspan="2" width="20%">
                    Document
               </th>
                
                
                <th align ="center" colspan="4" width="32%">
                    <center>Valoare intrare</center> 
               
                </th>
                <th align ="center" rowspan="2" width="8%">
                    <center>Valoare adaos</center> 
                </th>
                <th align ="center" colspan="3" width="24%">
                    <center>Valoare vanzare</center> 
                </th>
                
            </tr>
            <tr >
              
              
                
                <th  align ="center" width="8%">
                    <center>document</center>
                </th>
                  
                <th  align ="center" width="8%">
                   <center>diferenta (plus)</center>
                </th>
                <th align ="center" width="8%">
                    <center>diferenta (minus)</center>
                </th>
                 <th  align ="center" width="8%">
                   
                    <center>costuri incluse</center>
                </th>
                <th  align ="center" width="8%">
                   <center>fara TVA</center><br>
               </th>
               <th align ="center" width="8%">
                   <center>TVA</center><br>
               </th>
                <th  align ="center" width="8%">
                    <center>cu TVA</center><br>
               </th>
                
            </tr>

     </table>
      <hr>
                    @foreach($antetdocumenteprimite->groupby("gestiune['denumire']") as $documentgestiune)
                    GESTIUNE {{$documentgestiune[0]->gestiune["denumire"]}}
                    <hr>
     				<table class="table table-condesed" style="border-collapse: collapse; " width=100%>      
                    @foreach($documentgestiune as $det_doc)
                       
                      <tr style="border-bottom:1pt solid black; page-break-inside: avoid !important;"> 
                       <td align="center" width="3%">
                       {{ $i++ }}.
                       </td>
                        <td align="center" width="5%">
                         {{$det_doc->nr_nir  }}
                         </td>
                       <td align="center" width="7%">
                      {{dateFormatAfisare($det_doc->data_nir)  }}
                         
                      </td>
                      <td align="left" width="20%">
                       {{$det_doc->tip_document  }}
                      
                      {{$det_doc->seria }}
                    
                      {{$det_doc->nr_document .' / '.dateFormatAfisare($det_doc->data_document)  }}
                      <br>
                      
                      {{ $det_doc->partener["denumire"] }}
                         
                      </td>
                     
                     
                      <td align="right" width="8%">
                      {{ number_format(round($det_doc->detaliunir->sum("valoare_intrare"),2),2) }}
                         
                      </td>
                      <td align="right" width="8%">
                         {{ number_format(round($det_doc->detaliunir->sum(function($value){
                                if($value['cantitate_receptionata']-$value['cantitate']>0){

                              return ($value['cantitate_receptionata']-$value['cantitate'])*$value['pret_intrare'];
                              }
                      }),2),2)}}
                         
                      </td>
                      <td align="right" width="8%">
                       {{ number_format(round($det_doc->detaliunir->sum(function($value){
                                if($value['cantitate_receptionata']-$value['cantitate']<0){

                              return ($value['cantitate']-$value['cantitate_receptionata'])*$value['pret_intrare'];
                              }
                      }
                        ),2),2) }}
                         
                      </td>
                      <td align="right" width="8%">
                      {{ number_format(round($det_doc->detaliunir->sum(function($value){
                               

                             return $value['valoare_intrare']+$value['valoare_taxa']+$value['valoare_transport']
                                +$value['valoare_asigurare']+$value['valoare_alte_cheltuieli']+$value['valoare_navlu'];
                            
                      }
                        ),2),2) }}
                         
                      </td>
                      <td align="right" width="8%">
                       {{ number_format(round($det_doc->detaliunir->sum(function($value){
                        return $value->pret_vanzare*$value->cantitate/(1+$value->procent_tva_vanzare/100);
                      }

                        ),2)-round($det_doc->detaliunir->sum(function($value){
                               

                             return $value['valoare_intrare']+$value['valoare_taxa']+$value['valoare_transport']
                                +$value['valoare_asigurare']+$value['valoare_alte_cheltuieli']+$value['valoare_navlu'];
                            
                      }
                        ),2),2) }}
                         
                      </td>
                      <td align="right" width="8%">
                       {{ number_format(round($det_doc->detaliunir->sum(function($value){
                        return $value->pret_vanzare*$value->cantitate/(1+$value->procent_tva_vanzare/100);
                      }

                        ),2),2) }}
                         
                      </td>
                      <td align="right" width="8%">
                       {{ number_format(round($det_doc->detaliunir->sum(function($value){
                        return ($value->pret_vanzare*$value->cantitate)-($value->pret_vanzare*$value->cantitate/(1+$value->procent_tva_vanzare/100));
                      }

                        ),2),2) }}
                         
                      </td>
                      <td align="right" width="8%">
                       {{ number_format(round($det_doc->detaliunir->sum(function($value){
                        return $value->pret_vanzare*$value->cantitate;
                        }

                        ),2),2) }}
                         
                      </td>

                      </tr>
                     
                     @endforeach
                    </table>
                     <hr> 
     			     <table class="table table-condesed" width=100%> 
                    <tr>
                    
                      <td width="35%" align="center" colspan="4">
                         <strong>
                          TOTAL GESTIUNE {{$det_doc->gestiune["denumire"]}}
                          </strong>
                      </td>
                      <td width="8%" align="right" >
                        <h4>
                        <strong>
                       
                          {{ number_format(round($documentgestiune->sum(function($value){
                              return $value->detaliunir->sum('valoare_intrare');
                          }),2),2) }}
                       
                         </strong>
                       </h4>
                      </td>
                      <td  align="right" width="8%">
                        <strong>
                        {{ number_format(round($documentgestiune->sum(function($val){
                              return $val->detaliunir->sum(function($value){
                                                                  if($value['cantitate_receptionata']-$value['cantitate']>0){

                                                                return ($value['cantitate_receptionata']-$value['cantitate'])*$value['pret_intrare'];
                                                                };
                          });
                        }),2),2) }}
                        </strong>  
                      </td>
                      <td align="right" width="8%">
                        <strong>
                       {{ number_format(round($documentgestiune->sum(function($value){
                              return $value->detaliunir->sum(function($value){
                                                                      if($value['cantitate_receptionata']-$value['cantitate']<0){

                                                                              return ($value['cantitate']-$value['cantitate_receptionata'])*$value['pret_intrare'];
                                                                                }
                                                                    });
                          }),2),2) }}
                          </strong>
                      </td>
                      
                      <td  align="right" width="8%">
                        <strong>
                          {{ number_format(round($documentgestiune->sum(function($val){
                               return $val->detaliunir->sum(function($value){
                                return $value['valoare_intrare']+$value['valoare_taxa']+$value['valoare_transport']
                                +$value['valoare_asigurare']+$value['valoare_alte_cheltuieli']+$value['valoare_navlu'];

                              }); 
                              
                      })
                        ,2),2) }}
                          </strong>
                      </td>
                      <td  align="right" width="8%">
                        <strong>
                          {{ number_format(round($documentgestiune->sum(function($val){
                               return $val->detaliunir->sum(function($value){
                                 return $value->pret_vanzare*$value->cantitate/(1+$value->procent_tva_vanzare/100);

                              }); 
                              
                      })
                        ,2)-round($documentgestiune->sum(function($val){
                               return $val->detaliunir->sum(function($value){
                                return $value['valoare_intrare']+$value['valoare_taxa']+$value['valoare_transport']
                                +$value['valoare_asigurare']+$value['valoare_alte_cheltuieli']+$value['valoare_navlu'];

                              }); 
                              
                      })
                        ,2),2) }}
                          </strong>
                      </td>
                      <td  align="right" width="8%">
                        <strong>
                         {{ number_format(round($documentgestiune->sum(function($val){
                               return $val->detaliunir->sum(function($value){
                                return $value['cantitate']*$value['pret_vanzare']/(1+$value['procent_tva_vanzare']/100);

                              }); 
                              
                      })
                        ,2),2) }}
                          </strong>
                      </td>
                      <td  align="right" width="8%">
                        <strong>
                          {{ number_format(round($documentgestiune->sum(function($val){
                               return $val->detaliunir->sum(function($value){
                                return ($value['cantitate']*$value['pret_vanzare'])-$value['cantitate']*$value['pret_vanzare']/(1+$value['procent_tva_vanzare']/100);


                              }); 
                              
                      })
                        ,2),2) }}
                          </strong>
                      </td>
                      <td  align="right" width="8%">
                        <strong>
                          {{ number_format(round($documentgestiune->sum(function($val){
                               return $val->detaliunir->sum(function($value){
                                return $value['cantitate']*$value['pret_vanzare'];

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