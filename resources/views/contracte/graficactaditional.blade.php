@extends('layouts.antet')

@section ('content')

   <table class="table table-condesed" width=100%  >
      <tr>
        <td style="vertical-align:top" width="60%">
    <table class="table">
     
    <tr>
      <td width=100%>
          Client: <strong>{{ $contract->nume }}</strong>   <br>
          Nr contract: <strong>{{ $contract->nr_contract }}</strong>   <br>
          Data contract: <strong>{{ dateFormatAfisare($contract->data_contract) }}</strong>   <br>
          E-mail: <strong>{{ $contract->email }}</strong>   <br>
          Telefon: <strong>{{ $contract->telefon }}</strong> <br>
          Valoare credit (LEI): <strong>{{ number_format($contract->valoare_credit,2) }}</strong> <br>
          Dobanda anuala : <strong>{{ number_format($contract->procent_dobanda,2)." %" }}</strong> <br>
          Durata imprumut (ani): <strong>{{ number_format($contract->perioada_de_creditare/12,0) }}</strong> <br>
          <br>
          Rata lunara (LEI): <strong>{{ number_format($ratalunara,2) }}</strong> <br>
          Luni de plata: <strong>{{ number_format($contract->perioada_de_creditare,0) }}</strong> <br>
          Suma totala platita (LEI): <strong>{{ number_format($grafic->sum('ratalunara'),2) }}</strong> <br>
            din care dobanda (LEI): <strong>{{ number_format($grafic->sum('dobanda'),2) }}</strong> <br>
      </td>
  </tr>
</table>
</td>
<td style="vertical-align:top" width="40%">
              <table class="table table-condesed" style="border-collapse: collapse; "  width=100%   >
                 <tr>
        <td align ="center" colspan="4">
          <h2><strong>Grafic de rambursare</strong></h2>
         
      
      </td>
    </tr>
            <tr style="border-bottom:1pt solid black; border-top:1pt solid black; page-break-inside: avoid !important;">
              <th align ="center" colspan="4">
                 Rezumat pe ani
              </th>
            </tr>
            <tr style="border-bottom:1pt solid black; page-break-inside: avoid !important;">
              <th align ="center" width="25%">
                    Anul
                </th>
                 <th align ="center" width="25%">
                    Credit cumulat
                </th>
                <th align ="center" width="25%">
                    Dobanda cumulata
                </th>
               
                <th align ="center" width="25%">
                    Credit In Sold
                </th>
               
               
            </tr>
    
                  
                     <tr style="border-bottom:1pt solid black; page-break-inside: avoid !important;"> 
                       <th align="center" width="25%">
                          
                      </th>
                      
                      <th align="center" style="vertical-align:top"  width="25%">
                         
                      </th>
                      <th align="center" style="vertical-align:top"  width="25%">
                     
                         
                      </th>
                      <th align="center" style="vertical-align:top"  width="25%">
                      {{ number_format(round($grafic->sum('principal'),2),2) }}
                         
                      </th>
                         
                      </tr>     
                    
                    @foreach($grafic->groupby('anul')  as $rata)
                      
                      <tr style="border-bottom:1pt solid black; page-break-inside: avoid !important;"> 
                      <td align="center" width="25%">
                       {{ $rata[0]->anul }}
                      </td>
                     <td align="center" style="vertical-align:top"  width="25%">
                      {{ number_format(round($rata->sum('principal'),2),2) }}
                         
                      </td>
                      <td align="center" style="vertical-align:top"  width="25%">
                       {{ number_format(round($rata->sum('dobanda'),2),2) }}
                                              
                      </td>
                     
                     
                      <td align="center" style="vertical-align:top"  width="25%">
                      {{ number_format(round($rata->min('soldprincipal'),2),2) }}
                      </td>
                         
                      </tr>
                     
                    @endforeach 
              </table>
            </td>
           </tr>
          </table>  
<hr>
 <div >
           
    </div>  
    
<table class="table table-condesed" style="border-collapse: collapse; " width=100%  >
            
            <tr style="border-bottom:1pt solid black; border-top:1pt solid black; page-break-inside: avoid !important;">
              <td align ="center" width="5%">
                    Nr Crt
                </td>
               <td align ="center" width="10%">
                    Data Platii
                </td>
                <td align ="center" width="10%">
                    Credit
                </td>
                 <td align ="center" width="10%">
                    Dobanda
                </td>
                <td align ="center" width="10%">
                    Total De Plata
                </td>
                 <td align ="center" width="10%">
                    Comision de gestiune
                </td>
                 <td align ="center" width="10%">
                    Dobanda reesalonat
                </td>
                <td align ="center" width="10%">
                    Penalitati capital reesalonat
                </td>
                 <td align ="center" width="10%">
                    Penalitati dobanda reesalonat
                </td>
                <td align ="center" width="10%">
                    Comision de gestiune reesalonat
                </td>
               
                
                <td align ="center" width="10%">
                    Credit In Sold
                </td>
               
               
            </tr>
    
                  
                     <tr  style="border-bottom:1pt solid black; page-break-inside: avoid !important;"> 
                       <th align="center" width="5%">
                          
                      </th>
                      <th align="center" width="10%">
                          
                      </th>
                      <th align="center" style="vertical-align:top"  width="10%">
                      {{ number_format(round($grafic->sum('principal'),2),2) }}
                         
                      </th>
                      <th align="center" style="vertical-align:top"  width="10%">
                          {{ number_format(round($grafic->sum('dobanda'),2),2) }}
                      </th>
                     <th align="center" width="10%">
                          {{ number_format(round($grafic->sum('ratalunara'),2)+round($grafic->sum('comision_de_gestiune'),2),2) }}
                      </th>
                       <th align="center" style="vertical-align:top"  width="10%">
                          {{ number_format(round($grafic->sum('comision_de_gestiune'),2),2) }}
                      </th>
                      <th align="center" style="vertical-align:top"  width="10%">
                          {{ number_format(round($grafic->sum('dobanda_reesalonat'),2),2) }}
                      </th>
                      <th align="center" style="vertical-align:top"  width="10%">
                          {{ number_format(round($grafic->sum('penalitati_capital_reesalonat'),2),2) }}
                      </th>
                      <th align="center" style="vertical-align:top"  width="10%">
                          {{ number_format(round($grafic->sum('penalitati_dobanda_reesalonat'),2),2) }}
                      </th>
                      <th align="center" style="vertical-align:top"  width="10%">
                          {{ number_format(round($grafic->sum('comision_de_gestiune_reesalonat'),2),2) }}
                      </th>
                      <th align="center" style="vertical-align:top"  width="10%">
                      {{ number_format(round($grafic->sum('principal'),2),2) }}
                         
                      </th>
                         
                      </tr>     
                    
                    @foreach($grafic  as $rata)
                      
                      <tr  style="border-bottom:1pt solid black; page-break-inside: avoid !important;"> 
                      <td align="center" width="5%">
                       {{ number_format(round($rata->nr_crt,0),0) }}
                      </td>
                       <td align="center" width="10%">
                       {{ dateFormatAfisare($rata->datascadentalucratoare) }}
                      </td>
                       <td align="right" style="vertical-align:top"  width="10%">
                      {{ number_format(round($rata->principal,2),2) }}
                      </td>
                      <td align="right" style="vertical-align:top"  width="10%">
                       {{ number_format(round($rata->dobanda,2),2) }}
                      </td>
                      <td align="right" width="10%">
                       {{ number_format(round($rata->ratalunara,2)+round($rata->comision_de_gestiune,2),2) }}
                      </td>
                       <td align="right" style="vertical-align:top"  width="10%">
                       {{ number_format(round($rata->comision_de_gestiune,2),2) }}
                      </td>
                      <td align="right" style="vertical-align:top"  width="10%">
                       {{ number_format(round($rata->dobanda_reesalonat,2),2) }}
                      </td>
                      <td align="right" style="vertical-align:top"  width="10%">
                       {{ number_format(round($rata->penalitati_capital_reesalonat,2),2) }}
                      </td>
                      <td align="right" style="vertical-align:top"  width="10%">
                       {{ number_format(round($rata->penalitati_dobanda_reesalonat,2),2) }}
                      </td>
                      <td align="right" style="vertical-align:top"  width="10%">
                       {{ number_format(round($rata->comision_de_gestiune_reesalonat,2),2) }}
                      </td>
                      <td align="right" style="vertical-align:top"  width="10%">
                      {{ number_format(round($rata->soldprincipal,2),2) }}
                      </td>
                         
                      </tr>
                     
                    @endforeach 
              </table>

            
     <hr> 
     
                    
     
   <table class="table table-condesed" width=100% > 
                 <tr>
                  <td  width="15%">
                       
                  </td>
                  <td  width="30%" align="left" >
                   <strong>FORTUNA CAPITAL IFN SA,</strong>
                  </td>
                   <td  width="10%" >
                
              </td>
                   <td width="30%" align="center" >
                    <strong>Imprumutat,</strong>
                  </td>
                <td  width="15%" >
                
              </td>
          </tr> 
          <tr>
                  <td  width="15%">
                       
                  </td>
                  <td  width="30%" align="left" >
                   Prin:
                  </td>
                   <td  width="10%" >
                
              </td>
                   <td width="30%" align="center" >
               
                  </td>
                <td  width="15%" >
                
              </td>
          </tr>
           <tr>
                  <td  width="15%">
                       
                  </td>
                  <td  width="30%" align="left" >
                   Nume: Dragomir Gheorghe
                  </td>
                   <td  width="10%" >
                
              </td>
                   <td width="30%" align="center" >
               
                  </td>
                <td  width="15%" >
                
              </td>
          </tr>
          <tr>
                  <td  width="15%">
                       
                  </td>
                  <td  width="30%" align="left" >
                   Functia: Director general
                  </td>
                   <td  width="10%" >
                
              </td>
                   <td width="30%" align="center" >
               
                  </td>
                <td  width="15%" >
                
              </td>
          </tr>
      </table>  

  
  
@stop