@extends('layouts.antet')

@section ('content')

   
    <table class="table">
      <tr>
        <td width=60%>
          <h2><strong>Simulare creditare</strong></h2>
         
      
      </td>
      <td width=40%>
          Client: <strong>{{ $simularecredit->nume }}</strong>   <br>
          E-mail: <strong>{{ $simularecredit->email }}</strong>   <br>
          Telefon: <strong>{{ $simularecredit->telefon }}</strong> <br>
          Valoare credit (LEI): <strong>{{ number_format($simularecredit->valoare_credit,2) }}</strong> <br>
          Dobanda anuala : <strong>{{ number_format($simularecredit->procent_dobanda,2)." %" }}</strong> <br>
          Durata imprumut (ani): <strong>{{ number_format($simularecredit->perioada_de_creditare/12,0) }}</strong> <br>
          <br>
          Rata lunara (LEI): <strong>{{ number_format($ratalunara,2) }}</strong> <br>
          Luni de plata: <strong>{{ number_format($simularecredit->perioada_de_creditare,0) }}</strong> <br>
          Suma totala platita (LEI): <strong>{{ number_format($grafic->sum('ratalunara'),2) }}</strong> <br>
            din care dobanda (LEI): <strong>{{ number_format($grafic->sum('dobanda'),2) }}</strong> <br>
      </td>
  </tr>
</table>
<hr>
 <div >
           
    </div>  
    <table class="table table-condesed" width=100%  >
      <tr>
        <td style="vertical-align:top" width="60%">
<table class="table table-condesed" style="border-collapse: collapse; page-break-inside: avoid !important;" width=100%  >
            
            <tr style="border-bottom:1pt solid black; border-top:1pt solid black; page-break-inside: avoid !important;">
               <th align ="center" width="5%">
                    Nr crt
                </th>
              <th align ="center" width="15%">
                    Luna
                </th>
                <th align ="center" width="20%">
                    Rata 
                </th>
               
                <th align ="center" width="20%">
                    Dobanda
                </th>
                <th align ="center" width="20%">
                    Principal
                </th>
                <th align ="center" width="20%">
                    Sold
                </th>
               
               
            </tr>
    
                  
                     <tr  style="border-bottom:1pt solid black; page-break-inside: avoid !important;"> 
                        <th align="center" width="5%">
                          
                      </th>
                       <th align="center" width="15%">
                          
                      </th>
                      <th align="center" width="20%">
                          {{ number_format(round($grafic->sum('dobanda'),2)+round($grafic->sum('principal'),2),2) }}
                      </th>
                      <th align="center" style="vertical-align:top"  width="20%">
                          {{ number_format(round($grafic->sum('dobanda'),2),2) }}
                      </th>
                      <th align="center" style="vertical-align:top"  width="20%">
                      {{ number_format(round($grafic->sum('principal'),2),2) }}
                         
                      </th>
                      <th align="center" style="vertical-align:top"  width="20%">
                      {{ number_format(round($grafic->sum('principal'),2),2) }}
                         
                      </th>
                         
                      </tr>     
                    
                    @foreach($grafic  as $rata)
                      
                      <tr  style="border-bottom:1pt solid black; page-break-inside: avoid !important;"> 
                        <td align="center" width="5%">
                       {{ $i++ }}
                      </td>
                      <td align="left" width="15%">
                       {{ lunainlitere(round($rata->luna,0)) }}
                      </td>
                      <td align="center" width="20%">
                       {{ number_format(round($rata->dobanda,2)+round($rata->principal,2),2) }}
                      </td>
                      <td align="center" style="vertical-align:top"  width="20%">
                       {{ number_format(round($rata->dobanda,2),2) }}
                                              
                      </td>
                     
                      <td align="center" style="vertical-align:top"  width="20%">
                      {{ number_format(round($rata->principal,2),2) }}
                         
                      </td>
                      <td align="center" style="vertical-align:top"  width="20%">
                      {{ number_format(round($rata->soldprincipal,2),2) }}
                      </td>
                         
                      </tr>
                     
                    @endforeach 
              </table>
</td>
<td style="vertical-align:top" width="40%">
              <table class="table table-condesed" style="border-collapse: collapse; page-break-inside: avoid !important;"  width=100%   >
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
                    Dobanda cumulata
                </th>
                <th align ="center" width="25%">
                    Principal cumulat
                </th>
                <th align ="center" width="25%">
                    Sold
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
                       {{ number_format(round($rata->sum('dobanda'),2),2) }}
                                              
                      </td>
                     
                      <td align="center" style="vertical-align:top"  width="25%">
                      {{ number_format(round($rata->sum('principal'),2),2) }}
                         
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
     
                    
     
  

  
  
@stop