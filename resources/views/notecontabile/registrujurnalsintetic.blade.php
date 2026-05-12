@extends('layouts.antet')

@section ('content')
  
   <center> <h3> Registru jurnal </h3> </center>
   <center> <h3>{{$selectie}} SINTETIC</h3></center>
   <hr>
      <table class="table table-condesed" width=100%  >
            
            <tr >
              <th align ="center" rowspan="2" width="5%">
                    <center>Nr <br> crt</center>
              </th>
              <th align ="center" rowspan="2" width="10%">
                    <center>Data</center>
              </th>
              <th align ="center" rowspan="2" width="5%">
                    <center>Nr <br> nota</center>
              </th>
             
              <th align ="center" rowspan="2" width="20%">
                    Explicatie
              </th>
            
              
                <th align ="center" colspan="2" width="20%">
                    Sume 
                </th>
                
                
            </tr>
            <tr>
              
                <th align ="center" width="10%">
                    Debitoare
              </th>
              
                <th align ="center" width="10%">
                    Creditoare 
                </th>
            </tr>
     </table>
      <hr>
                   
     				<table class="table table-condesed" style="border-collapse: collapse; " width=100%>      
                    @foreach($notacontabila->groupby(["data_nota","nr_nota"]) as $notaData)
                      @foreach($notaData as $nota)
                      <tr style="border-bottom:1pt solid black; page-break-inside: avoid !important;"> 
                       <td align="center" width="5%">
                       {{ $i++ }}.
                       </td>
                       <td align="center" width="10%">
                       {{dateFormatAfisare($nota[0]->data_nota) }}
                       </td>
                        <td align="center" width="5%">
                       {{ $nota[0]->nr_nota }}
                       </td>
                       <td align="left" width="20%">
                        @if($nota[0]->nr_nota)
                        {{denumireNota($nota[0]->nr_nota)  }}
                        @endif
                       </td>
                       
                      <td align="right" width="10%">
                     {{ number_format($nota->sum("suma"),2)}}
                         
                      </td>
                      <td align="right" width="10%">
                     {{ number_format($nota->sum("suma"),2)}}
                         
                      </td>
                      </tr>
                     @endforeach 
                      @endforeach 
                    </table>
                   
             
   <hr>
     <table class="table table-condesed" width=100%> 
                    <tr>
                    
                      <td width="80%" align="right" colspan="4">
                         <strong>
                          TOTAL 
                          </strong>
                      </td>
                     
                      <td width="10%" align="right" >
                        <h4>
                        <strong>
                        
                          {{ number_format(round($notacontabila->sum('suma'),2),2) }}
                           </strong>
                         </h4>
                      </td>
                      <td width="10%" align="right" >
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