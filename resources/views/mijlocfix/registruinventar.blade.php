@extends('layouts.antet')

@section ('content')
  
   <center> <h3> Registru numerelor de inventar </h3> </center>
   <center> <h3>{{$selectie}} </h3></center>
   <hr>
      <table class="table table-condesed" width=100%  >
            
            <tr >
              <th align ="center" width="5%">
                    <center>Nr <br> crt</center>
              </th>
              <th align ="center" width="5%">
                    <center>Nr <br> inventar</center>
              </th>
              <th align ="center" width="15%">
                    <center>Denumire</center>
              </th>
              <th align ="center"  width="15%">
                    <center>Furnizor</center>
              </th>
              <th align ="center"  width="5%">
                    <center>Nr <br> document</center>
              </th>
              <th align ="center" width="10%">
                   <center> Data intrarii</center>
              </th>
              
                <th align ="center" width="10%">
                    <center>Cod clasificare</center>
                </th>
                <th align ="center" width="10%">
                    <center>Gestiune</center>
                </th>
                <th align ="center" width="5%">
                    <center>Cantitate</center>
                </th>
                <th align ="center" width="10%">
                    <center>Pret intrare</center>
                </th>
                <th align ="center" width="10%">
                    <center>Valoare de inventar</center>
                </th>
            </tr>
           
     </table>
      <hr>
                   
     				<table class="table table-condesed" style="border-collapse: collapse; " width=100%>      
                    @foreach($mijloacefixe as $mijlocfix)
                      
                      <tr style="border-bottom:1pt solid black; page-break-inside: avoid !important;"> 
                       <td align="center" width="5%">
                       {{ $i++ }}.
                       </td>
                       <td align="center" width="5%">
                       {{$mijlocfix->nr_inventar }}
                       </td>
                        <td align="left" width="15%">
                       {{ $mijlocfix->denumire }}
                       </td>
                       <td align="center" width="15%">
                        {{$mijlocfix->partener["denumire"]  }}
                        
                       </td>
                       <td align="left" width="5%">
                         {{ $mijlocfix->nr_document}}
                        
                      </td>
                      <td align="center" width="10%">
                      {{ dateFormatAfisare($mijlocfix->data_intrarii) }}
                      </td>
                      <td align="center" width="10%">
                      {{ $mijlocfix->cod_clasificare }}
                         
                      </td>
                      <td align="left" width="10%">
                      {{ $mijlocfix->gestiune["denumire"]}}
                      </td>
                      <td align="center" width="5%">
                       {{ $mijlocfix->cantitate}}
                      </td>
                      <td align="right" width="10%">
                        {{ number_format($mijlocfix->pret,2)}}
                      </td>
                      <td align="right" width="10%">
                       {{ number_format($mijlocfix->valoare_de_inventar,2)}}
                      </td>
                      </tr>
                     @endforeach 
                    </table>
                   
             
   <hr>
     <table class="table table-condesed" width=100%> 
                    <tr>
                    
                      <td width="90%" align="right" colspan="10">
                         <strong>
                          TOTAL 
                          </strong>
                      </td>
                     
                      <td width="10%" align="right" >
                        <h4>
                        <strong>
                        
                          {{ number_format(round($mijloacefixe->sum('valoare_de_inventar'),2),2) }}
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