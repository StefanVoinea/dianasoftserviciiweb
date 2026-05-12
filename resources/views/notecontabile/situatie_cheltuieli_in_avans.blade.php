@extends('layouts.antet')

@section ('content')
  
   <center><h3>SITUAȚIE CHELTUIELI ÎN AVANS</h3></center>
   <center><h3>{{$selectie}}</h3></center>
   <hr>
      <table class="table table-condesed" width=100%  >
            
            <tr >
              <th align ="center"  rowspan="2" width="3%">
                    <center>Nr <br> crt</center>
              </th>
              <th align ="center"  rowspan="2" width="10%">
                    <center>Data <br> nota</center>
              </th>
              <th align ="center" rowspan="2" width="20%">
                    <center>Document <br> (tip,nr,data,client,cui)</center>
              </th>
              <th align ="center" width="17%">
                   Explicatie
              </th>
              <th align ="center" width="10%">
                    Suma
              </th>
             <th align ="center" width="10%">
                    Debit
              </th>
              <th align ="center" width="10%">
                    Credit
              </th>
               <th align ="center" width="10%">
                    Data <br> inceput
              </th>
              <th align ="center" width="5%">
                    Nr <br> rate
              </th>
              <th align ="center" width="5%">
                    Cont <br> cheltuiala
              </th> 
            </tr>
           
     </table>
      <hr>
                   
            <table class="table table-condesed" style="border-collapse: collapse; " width=100%>      
                    @foreach($notacontabila as $cheltuiala)
                      
                      <tr style="border-bottom:1pt solid black; page-break-inside: avoid !important;"> 
                       <td align="center" width="3%">
                       {{ $i++ }}.
                       </td>
                       <td align="center" width="10%">
                       {{ dateFormatAfisare($cheltuiala->data_nota) }}
                       </td>
                       <td align="left" width="20%">
                        
                        {{$cheltuiala->tip_document." ". $cheltuiala->nr_document." ".dateFormatAfisare($cheltuiala->data_document).' '}}<br>
                        {{$cheltuiala->denumire_partener}}
                        
                       </td>
                       <td align="center" width="17%">
                       {{ $cheltuiala->explicatie }}
                       </td>
                       <td align="right" width="10%">
                         {{ number_format($cheltuiala->suma,2)}}
                      </td>
                      <td align="center" width="10%">
                        
                            {{ $cheltuiala->contd }}
                        
                      </td>
                      <td align="center" width="10%">
                        
                            {{ $cheltuiala->contc }}
                        
                      </td>
                      <td align="center" width="10%">
                        
                            {{ dateFormatAfisare($cheltuiala->data_inceput) }}
                        
                      </td>
                      <td align="center" width="5%">
                        
                            {{ $cheltuiala->nr_rate }}
                        
                      </td>
                      <td align="center" width="5%">
                        
                            {{ $cheltuiala->cont_cheltuiala }}
                        
                      </td>
                      </tr>
                     @endforeach 
                    </table>
                   
             
   <hr>
     <table class="table table-condesed" style="border-collapse: collapse; " width=100%> 
                  
                     <tr>
                    
                      <td width="50%" align="right" colspan="4">
                         <strong>
                          {{"TOTAL GENERAL   "}}
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
                       
                      </td>
                      <td width="10%" align="right" >
                        
                      </td>
                       <td width="10%" align="right" >
                        
                      </td>
                       <td width="5%" align="right" >
                        
                      </td>
                       <td width="5%" align="right" >
                        
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