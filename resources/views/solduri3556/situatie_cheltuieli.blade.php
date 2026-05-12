@extends('layouts.antet')

@section ('content')
          
<table class="table table-condesed" border="1" style="border-collapse: collapse; " width="100%">
    <thead>
    <tr style="border-bottom:1pt solid black; page-break-inside: avoid !important;">
        <th  align="center" colspan="10" width="100%" >
          <center> <h3> {!! nl2br($titluRaport) !!} </h3></center>
          <hr>
         </th> 
    </tr> 
    <tr style="border-bottom:1pt solid black; page-break-inside: avoid !important;">
      <th align="center" width="10%">
          Nr contract
      </th>
      <th align="center" width="10%">
          Data contract
      </th>  
      <th align="center" width="10%">
          Agentia
      </th>
      <th align="center" width="10%">
          Partener
      </th>
      <th align="center" width="10%">
          Tip document
      </th>
      <th align="center" width="10%">
          Nr document
      </th>
      <th align="center" width="10%">
          Data document
      </th>

      <th align="center" width="10%">
          Plata
      </th>
      <th align="center" width="10%">
          Recuperare
      </th>
      <th align="center" width="10%">
          Sold
      </th>
    </tr>
    </thead>
    <tbody>
       
            @foreach($tabel->groupBy("nr_contract") as $rand)
             
                <tr style="border-bottom:1pt solid black; page-break-inside: avoid !important;">
                 <td align="left" width="100%" colspan="10" > 
                   Contract {{$rand[0]->nr_contract." ".$rand[0]->nume}}
                 </td>
               </tr>
               @foreach($rand as $contract)
                  <tr style="border-bottom:1pt solid black; page-break-inside: avoid !important;">
                      <td align="center" width="10%">
                         {{$contract->nr_contract}}
                      </td>
                      <td align="center" width="10%">
                         {{dateFormatAfisare($contract->data_contract)}}
                      </td>  
                      <td align="center" width="10%">
                          {{$contract->agentia}}
                      </td>
                      <td align="left" width="10%">
                          {{$contract->partener}}
                      </td>
                      <td align="center" width="10%">
                          {{$contract->tip_doc}}
                      </td>
                      <td align="center" width="10%">
                          {{$contract->nr_doc}}
                      </td>
                      <td align="center" width="10%">
                          {{dateFormatAfisare($contract->data_doc)}}
                      </td>

                      <td align="right" width="10%">
                          {{number_format($contract->plata,2)}}
                      </td>
                      <td align="right" width="10%">
                          {{number_format($contract->recuperare,2)}}
                      </td>
                      <td align="right" width="10%">
                          {{number_format($contract->sold,2)}}
                      </td>
                    </tr>
               @endforeach
                <tr style="border-bottom:1pt solid black; page-break-inside: avoid !important;">
                      <td align="left" colspan="7" width="70%">
                         TOTAL {{$rand[0]->nr_contract." ".$rand[0]->nume}}
                      </td>
                      <td align="right" width="10%">
                         {{number_format($rand->sum("plata"),2)}}
                      </td>  
                      <td align="right" width="10%">
                          {{number_format($rand->sum("recuperare"),2)}}
                      </td>
                      <td align="right" width="10%">
                          {{number_format($rand->sum("sold_total"),2)}}
                      </td>
                      </tr>
           @endforeach   
                  <tr style="border-bottom:1pt solid black; page-break-inside: avoid !important;">
                      <td align="left" colspan="7" width="70%">
                         TOTAL GENERAL
                      </td>
                      <td align="right" width="10%">
                         {{number_format($tabel->sum("plata"),2)}}
                      </td>  
                      <td align="right" width="10%">
                          {{number_format($tabel->sum("recuperare"),2)}}
                      </td>
                      <td align="right" width="10%">
                          {{number_format($tabel->sum("sold_total"),2)}}
                      </td>
                      </tr>
     
    </tbody>
    
</table>
<br><br>
<table class="table table-condensed" width="100%" > 
                 <tr>
                  <td  width="15%">
                       
                  </td>
                  <td width="30" align="center" >
                     CONTABIL SEF,<br><br>
                    {{$company->contabil_sef}}
                   <br><br>
                    ___________________________________
                  </td>
                   <td  width="10%" >
                
              </td>
              <td  width="30%" align="center" >
                   INTOCMIT,<br><br>
                   
                   <br><br>
                    _________________________
                  </td>
                  
                   
                <td  width="15%" >
                
              </td>
          </tr> 
      </table>  
@stop