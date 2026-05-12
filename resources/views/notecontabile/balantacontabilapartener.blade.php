<!-- <!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="{{env('APP_URL').'css/main.css'}}" rel="stylesheet" type="text/css">
      <style type="text/css">
     .bodyfactura { 
        padding-bottom: 10px;
        background-color: white;
        color:black;
        font-family:cambria;

      }
      
}
      
    </style>
    <style>
      .antet {
               display:flex ;
              justify-content: space-between;
              font-size: 16px;
            }
hr { 
   
    border: 2px solid #dab295;
    
} 

</style>
</head>
<body class="bodyfactura"> -->
@extends('layouts.antet')

@section ('content')
 <hr>  
          

             <center> <h3> Centralziator conturi {{$selectie}} </h3></center>
   <hr>
      <table class="table table-condesed" style="border-collapse: collapse;" width=100%>
            
            <tr >
              <th align ="center"  width="5%">
                   <!--  <center>Cont</center> -->
              </th>
              <th align ="center" width="10%">
                    <!-- <center>Denumire</center> -->
              </th>
            
               <th align ="center" colspan="2" width="16%">
                   Anterior
              </th>
              <th align ="center" colspan="2" width="16%">
                   În lună
              </th>
               <th align ="center" colspan="2" width="16%">
                   Total
              </th>   
              <th align ="center" colspan="2" width="16%">
                   Sold
              </th>    
             <!--   <th align ="center"  rowspan="2" width="5%">
                    <center>Cont</center>
              </th> -->
            </tr>
            <tr style="border-bottom:4pt solid #dab295; page-break-inside: avoid !important;">
              <th align ="center"  width="5%">
                    <center>Cont</center>
              </th>
              <th align ="center" width="10%">
                    <center>Partener</center>
              </th>
            
               <th align ="center"  width="8%">
                    <center>Debit</center>
              </th>
              <th align ="center"  width="8%">
                    <center>Credit</center>
              </th>
               <th align ="center"  width="8%">
                    <center>Debit</center>
              </th>
              <th align ="center"  width="8%">
                    <center>Credit</center>
              </th>
               <th align ="center"  width="8%">
                    <center>Debit</center>
              </th>
              <th align ="center"  width="8%">
                    <center>Credit</center>
              </th>
               <th align ="center"  width="8%">
                    <center>Debit</center>
              </th>
              <th align ="center"  width="8%">
                    <center>Credit</center>
              </th>
            </tr>
       				<!-- <table class="table table-condesed" style="border-collapse: collapse;" width=100%>     -->
                          @foreach($balanta->sortBy('cont',SORT_STRING) as $cont)
                            
                            <tr style="height:30px; border-bottom:1pt solid black; page-break-inside: avoid !important;"> 
                             <td align="left" width="5%">
                              {{$cont->cont }} 
                             </td>
                             <td align="left" width="10%">
                              {{$cont->denumire_partener }} 
                             </td>
                            
                            
                              <td align="right" width="8%">
                                {{ number_format($cont->rulajAntD,2)}}
                            </td>
                            <td align="right" width="8%">
                                {{ number_format($cont->rulajAntC,2)}} 
                             </td>
                           <td align="right" width="8%">
                                {{ number_format($cont->rulajD,2)}}
                            </td>
                            <td align="right" width="8%">
                                {{ number_format($cont->rulajC,2)}}
                             </td>
                             <td align="right" width="8%">
                                {{ number_format($cont->rulajTotalD,2)}}
                            </td>
                            <td align="right" width="8%">
                                {{ number_format($cont->rulajTotalC,2)}}
                             </td>
                              <td align="right" width="8%">
                                {{ number_format($cont->soldFinalD,2)}}
                            </td>
                            <td align="right" width="8%">
                                {{ number_format($cont->soldFinalC,2)}}
                             </td>
                          <!--    <td align="left" width="5%">
                             {{$cont->cont }}
                             </td> -->
                           </tr>
                           @endforeach 
                   
                   
                    <tr style="height:50px; border-bottom:4pt solid #dab295; page-break-inside: avoid !important;" >
                    
                     <th align="left" colspan="2" width="15%">
                       TOTAL GENERAL
                       </th>
                      
                        <th align="right" width="8%">
                          {{ number_format($balanta->sum("rulajAntD"),2)}}
                      </th>
                      <th align="right" width="8%">
                          {{ number_format($balanta->sum("rulajAntC"),2)}}
                       </th>
                     <th align="right" width="8%">
                          {{ number_format($balanta->sum("rulajD"),2)}}
                      </th>
                      <th align="right" width="8%">
                          {{ number_format($balanta->sum("rulajC"),2)}}
                       </th>
                       <th align="right" width="8%">
                          {{ number_format($balanta->sum("rulajTotalD"),2)}}
                      </th>
                      <th align="right" width="8%">
                          {{ number_format($balanta->sum("rulajTotalC"),2)}}
                       </th>
                        <th align="right" width="8%">
                          {{ number_format($balanta->sum("soldFinalD"),2)}}
                      </th>
                      <th align="right" width="8%">
                          {{ number_format($balanta->sum("soldFinalC"),2)}}
                       </th>
                     <!--  <td align="right" width="5%">
                        
                      </td>  -->
                    </tr>
               </table>
    
      
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
<!-- </body>
</html> -->

	
