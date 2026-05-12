@extends('layouts.antet')

@section ('content')
  
   <center> <h3> Note contabile </h3> </center>
   <center> <h3>{{$selectie}} SINTETIC </h3></center>
   <hr>
   <center>
      <table class="table table-condesed" width=50%  >
            
            <tr >
              <th align ="center" width="20%">
                    <center>Nr <br> crt</center>
              </th>
              <th align ="center" width="40%">
                    <center>Nr <br> nota</center>
              </th>
              
                <th align ="center" width="40%">
                    Suma 
                </th>
                
                
            </tr>
           
     </table>
   </center>
      <hr>
     <center>              
     				<table class="table table-condesed" style="border-collapse: collapse; " width=50%>      
                    @foreach($notacontabila->groupby("nr_nota") as $nota)
                      
                      <tr style="border-bottom:1pt solid black; page-break-inside: avoid !important;"> 
                       <td align="center" width="20%">
                       {{ $i++ }}.
                       </td>
                        <td align="center" width="40%">
                       {{ $nota[0]->nr_nota }}
                       </td>
                      
                      <td align="right" width="40%">
                     {{ number_format($nota->sum("suma"),2)}}
                         
                      </td>
                     
                      </tr>
                     @endforeach
                      
                    </table>
                   
          </center>   
   <hr>
   <center>
     <table class="table table-condesed" width=50%> 
                    <tr>
                    
                      <td width="80%" align="right" colspan="7">
                         <strong>
                          TOTAL 
                          </strong>
                      </td>
                     
                      <td width="20%" align="right" >
                        <h4>
                        <strong>
                        
                          {{ number_format(round($notacontabila->sum('suma'),2),2) }}
                           </strong>
                         </h4>
                      </td>
                     
                    </tr>
               </table>
             </center>
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