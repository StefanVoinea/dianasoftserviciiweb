@extends('layouts.antet')

@section ('content')
  
   <center> <h3> FORMULAR <BR>
de raportare a creditelor nou-acordate prevazute la art.20 din regulament </h3> </center>
   <hr>
      <table class="table table-condesed" width=100%  >
            
            <tr >
              <th align ="center" width="55%" rowspan="2" >
                    <center>Denumire indicatori</center>
              </th>
              <th align ="center" width="5%" rowspan="2">
                    <center>Nr <br> rand</center>
              </th>
              <th align ="center" width="10%" rowspan="2">
                    <center>Trimestrul n-1*) <br> {{trimestruanterior($luna,$anul)}}</center>
              </th>
              <th align ="center"  colspan="3" width="30%">
                    <center>Trimestrul n*)  {{trimestrucurent($luna,$anul)}}</center>
              </th>
             </tr >
             <tr > 
              <th align ="center"  width="10%">
                    <center>{{$sfluna1}}</center>
              </th>
              <th align ="center"  width="10%">
                    <center>{{$sfluna2}}</center>
              </th>
              <th align ="center"  width="10%">
                    <center>{{$sfluna3}}</center>
              </th>
              
            </tr>
           
     </table>
      <hr>
                   
     				<table class="table table-condesed" style="border-collapse: collapse; " width=100%>      
                  
                      <tr style="border-bottom:1pt solid black; page-break-inside: avoid !important;"> 
                       <td align="left" width="55%">
                       Volumul total al creditelor de consum acordate**) <br>( ct.2031) CCIP; CGRMI; LC.
                       </td>
                        <td align="center" width="5%">
                          1
                       </td>
                       <td align="center" width="10%">
                       {{number_format($tabel[0]->cont2031_total_trim_anterior) }}
                       </td>
                       
                       <td align="left" width="10%">
                       X
                       </td>
                       <td align="left" width="10%">
                        X
                        
                      </td>
                      <td align="left" width="10%">
                      X
                      </td>
                     
                     
                      </tr>
                    <tr style="border-bottom:1pt solid black; page-break-inside: avoid !important;"> 
                       <td align="left" width="55%">
                       Volumul  creditelor de consum nou-acordate potrivit art.20 alin.(1) lit. a) din regulament**) <br> ( ct.2031)  CCIP; CGRMI; LC. 
                       </td>
                        <td align="center" width="5%">
                          2
                       </td>
                       <td align="center" width="10%">
                       X
                       </td>
                       
                       <td align="left" width="10%">
                       {{number_format($tabel[0]->cont2031_total_trim_curent_i) }}
                       </td>
                       <td align="left" width="10%">
                        {{number_format($tabel[0]->cont2031_total_trim_curent_ii) }}
                        
                      </td>
                      <td align="left" width="10%">
                      {{number_format($tabel[0]->cont2031_total_trim_curent_iii) }}
                      </td>
                     
                     
                      </tr>
                       <tr style="border-bottom:1pt solid black; page-break-inside: avoid !important;"> 
                       <td align="left" width="55%">
                       Volumul total al creditelor pentru investitii imobiliare acordate**)<br>( ct.20611)  CiiPLC. 
                       </td>
                        <td align="center" width="5%">
                        3
                       </td>
                       <td align="right" width="10%">
                       {{number_format($tabel[0]->cont2061_total_trim_anterior) }}
                       </td>
                       
                       <td align="center" width="10%">
                       X
                       </td>
                       <td align="center" width="10%">
                        X
                        
                      </td>
                      <td align="center" width="10%">
                      X
                      </td>
                     
                     
                      </tr>

                      <tr style="border-bottom:1pt solid black; page-break-inside: avoid !important;"> 
                       <td align="left" width="55%">
                       Volumul creditelor pentru investitii imobiliare nou-acordate potrivit art.20 alin.(1) lit.(b) din regulament***) <br> ( ct.20611) CiiP
                       </td>
                        <td align="center" width="5%">
                         4
                       </td>
                       <td align="center" width="10%">
                       X
                       </td>
                       
                       <td align="right" width="10%">
                       {{number_format($tabel[0]->cont2061_total_trim_curent_i) }}
                       </td>
                       <td align="right" width="10%">
                        {{number_format($tabel[0]->cont2061_total_trim_curent_ii) }}
                        
                      </td>
                      <td align="right" width="10%">
                      {{number_format($tabel[0]->cont2061_total_trim_curent_iii) }}
                      </td>
                     
                     
                      </tr>
                    </table>
                   
    *) n-reprezinta trimestrul pentru care se intocmeste raportarea; <br>         
    **) se vor avea in vedere toate creditele acordate in trimestrul pentru care se intocmeste raportarea si, respectiv, in trimestrul anterior, ce se incadreaza in categoria creditelor de consum, inclusiv cele care au fost rambursate si nu se mai regasesc in sold la finele perioadei pentru care se intocmeste raportarea;    (CCIP, CGRMI <br>
    ***)se vor avea in vedere toate creditele acordate in trimestrul pentru care se intocmeste raportarea si, respectiv, in trimestrul anterior, ce se incadreaza in categoria creditelor pentru investitiile imobiliare, inclusiv cele care au fost rambursate si nu se mai regasesc in sold la finele perioadei pentru care se intocmeste raportarea.(CiiP) <br>

   <hr>
   
     <br>
    <table class="table table-condesed" width=100% > 
                 <tr>
                  <td  width="15%">
                       
                  </td>
                  <td  width="30%" align="center" >
                   Conducatorul entitatii,,<br><br>
                   {{$company->director_general}}
                   <br><br>
                    _________________________
                  </td>
                   <td  width="10%" >
                
              </td>
                   <td width="30%" align="center" >
                     Conducatorul compartimentului financiar-contabil,<br><br>
                    {{$company->contabil_sef}}
                   <br><br>
                    ___________________________________
                  </td>
                <td  width="15%" >
                
              </td>
          </tr> 
      </table>  

	
	
@stop