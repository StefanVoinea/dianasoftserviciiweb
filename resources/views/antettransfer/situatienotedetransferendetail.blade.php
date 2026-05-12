@extends('layouts.antet')

@section ('content')
  
   <center> <h3> Situație note de transfer </h3> </center>
   <center> <h3>{{$selectie}} tip lista  Sintetic </h3></center>
   <hr>
   <center>
      <table class="table table-condesed" width=100%  >
            
            <tr >
              <th align ="center" rowspan="2" width="10%">
                    <center>Nr <br> crt</center>
                </th>
                <th align ="center"  rowspan="2" width="10%">
                    Nr document
                </th>
                <th align ="center"  rowspan="2" width="10%">
                    Data document
                </th>
               <th align ="center"  rowspan="2" width="30%">
                    Gestiune primitoare
                </th>
                
                <th align ="center" colspan="5" width="10%">
                    <center>Valoare intrare</center> 
               
                </th>
                <th align ="center" colspan="5" width="10%">
                    <center>Valoare vanzare fara TVA</center> 
               
                </th>
                <th align ="center" colspan="5" width="10%">
                    <center>Valoare vanzare TVA</center> 
               
                </th>
                <th align ="center" colspan="5" width="10%">
                    <center>Valoare vanzare cu TVA</center> 
               
                </th>
                
            </tr>
            

     </table>
   </center>
      <hr>
                    @foreach($antettransfer->groupby("gestiunepredatoare['denumire']") as $documentgestiune)
                    GESTIUNE PREDATOARE: {{$documentgestiune[0]->gestiunepredatoare["denumire"]}}
                    <hr>
                    <center>
     				<table class="table table-condesed" style="border-collapse: collapse; " width=100%>      
                    @foreach($documentgestiune as $det_doc)
                       
                      <tr style="border-bottom:1pt solid black; page-break-inside: avoid !important;"> 
                       <td align="center" width="10%">
                       {{ $i++ }}.
                       </td>
                        <td align="center" width="10%">
                         {{$det_doc->nr_document  }}
                         </td>
                       <td align="center" width="10%">
                      {{dateFormatAfisare($det_doc->data_document)  }}
                         
                      </td>
                       <td align="center" width="30%">
                         {{$det_doc->gestiuneprimitoare["denumire"]  }}
                         </td>
                      <td align="right" width="10%">
                      {{ number_format(round($det_doc->valoare_fara_tva,2),2) }}
                         
                      </td>
                     <td align="right" width="10%">
                      {{ number_format(round($det_doc->valoare_vanzare_fara_tva,2),2) }}
                         
                      </td>
                      <td align="right" width="10%">
                      {{ number_format(round($det_doc->valoare_vanzare_tva,2),2) }}
                         
                      </td>
                      <td align="right" width="10%">
                      {{ number_format(round($det_doc->valoare_vanzare,2),2) }}
                         
                      </td>
                      </tr>
                     
                     @endforeach
                    </table>
                     <hr> 
     			     <table class="table table-condesed" width=100%> 
                    <tr>
                    
                      <td width="60%" align="center" colspan="3">
                         <strong>
                          TOTAL GESTIUNE {{$det_doc->gestiunepredatoare["denumire"]}}
                          </strong>
                      </td>
                      <td width="10%" align="right" >
                        <h4>
                        <strong>
                       
                          {{ number_format(round($documentgestiune->sum('valoare_fara_tva'),2),2) }}
                       
                         </strong>
                       </h4>
                      </td>
                     <td width="10%" align="right" >
                        <h4>
                        <strong>
                       
                          {{ number_format(round($documentgestiune->sum('valoare_vanzare_fara_tva'),2),2) }}
                       
                         </strong>
                       </h4>
                      </td>
                      <td width="10%" align="right" >
                        <h4>
                        <strong>
                       
                          {{ number_format(round($documentgestiune->sum('valoare_vanzare_tva'),2),2) }}
                       
                         </strong>
                       </h4>
                      </td>
                      <td width="10%" align="right" >
                        <h4>
                        <strong>
                       
                          {{ number_format(round($documentgestiune->sum('valoare_vanzare'),2),2) }}
                       
                         </strong>
                       </h4>
                      </td>
                    </tr>
               </table>
                <hr> 
    </center>
                    @endforeach 
             
  
     

	
	
@stop