@extends('layouts.antet')

@section ('content')
  
   <center> <h3> Note contabile </h3> </center>
   <center> <h3>{{$selectie}} ANALITIC </h3></center>
   <hr>
      <table class="table table-condesed" width=100%  >
            
            <tr >
              <th align ="center" width="5%">
                    <center>Nr <br> crt</center>
              </th>
              <th align ="center" width="5%">
                    <center>Nr <br> nota</center>
              </th>
              <th align ="center" width="10%">
                    <center>Data</center>
              </th>
              <th align ="center"  width="25%">
                    <center>Document <br> (tip,nr,data)</center>
              </th>
              <th align ="center"  width="25%">
                    Explicatie
              </th>
              <th align ="center"  width="15%">
                    Debit
              </th>
              <th align ="center" width="15%">
                    Credit
              </th>
                <th align ="center" rowspan="2" width="15%">
                    Suma 
                </th>
                
                
            </tr>
           
     </table>
      <hr>
                   
     				<table class="table table-condesed" style="border-collapse: collapse; " width=100%>      
                    @foreach($notacontabila as $nota)
                      
                      <tr style="border-bottom:1pt solid black; page-break-inside: avoid !important;"> 
                       <td align="center" width="5%">
                       {{ $i++ }}.
                       </td>
                        <td align="center" width="5%">
                       {{ $nota->nr_nota }}
                       </td>
                       <td align="center" width="10%">
                       {{dateFormatAfisare($nota->data_nota) }}
                       </td>
                       
                       <td align="left" width="25%">
                        @if($nota->tip_doc||$nota->nr_doc||$nota->data_doc)
                        {{$nota->tip_doc  }}
                        {{$nota->nr_doc."/".dateFormatAfisare($nota->data_doc)  }}
                        @endif
                       </td>
                       <td align="left" width="25%">
                         {{ $nota->expl}}
                        
                      </td>
                      <td align="left" width="15%">
                      {{ $nota->contd }}
                      </td>
                      <td align="left" width="15%">
                      {{ $nota->contc }}
                         
                      </td>
                      <td align="right" width="15%">
                     {{ number_format($nota->suma,2)}}
                         
                      </td>
                     
                      </tr>
                     @endforeach 
                    </table>
                   
             
   <hr>
     <table class="table table-condesed" width=100%> 
                    <tr>
                    
                      <td width="90%" align="right" colspan="6">
                         <strong>
                          TOTAL 
                          </strong>
                      </td>
                     
                      <td width="15%" align="right" colspan="2">
                        <h4>
                        <strong>
                        
                          {{ number_format(round($notacontabila->sum('suma'),2),2) }}
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
                   DIRECTOR GENERAL,<br><br>
                   {{$company->director_general}}
                   <br><br>
                    _________________________
                  </td>
                   <td  width="10%" >
                
              </td>
                   <td width="30%" align="center" >
                     CONTABIL SEF,<br><br>
                    {{$company->contabil_sef}}
                   <br><br>
                    ___________________________________
                  </td>
                <td  width="15%" >
                
              </td>
          </tr> 
      </table>  

	
	
@stop