
 <table class="table">
      <tr>
        <td width="5">
          <!-- <h2><strong>Grafic de rambursare</strong></h2> -->
         
      
      </td>
      <td width="15">
          Client: <strong>{{ $solicitare->nume }}</strong> 
        
      </td>

  </tr>
 <tr>
        <td width="5">
          <!-- <h2><strong>Grafic de rambursare</strong></h2> -->
         
      
      </td>
      <td width="15">
         
          E-mail: <strong>{{ $solicitare->email }}</strong> 
         
      </td>
  </tr>
   <tr>
        <td width="5">
          <!-- <h2><strong>Grafic de rambursare</strong></h2> -->
         
      
      </td>
      <td width="15">
          Telefon: <strong>{{ $solicitare->telefon }}</strong>
         
      </td>
  </tr>
   <tr>
        <td width="5">
          <!-- <h2><strong>Grafic de rambursare</strong></h2> -->
         
      
      </td>
      <td width="15">
          
          Valoare credit (LEI): <strong>{{ $solicitare->valoare_credit }}</strong> 
         
      </td>
  </tr>
   <tr>
        <td width="5">
          <!-- <h2><strong>Grafic de rambursare</strong></h2> -->
         
      
      </td>
      <td width="15">
        
          Dobanda anuala : <strong>{{ $solicitare->procent_dobanda." " }}</strong> 
        
      </td>
  </tr>
   <tr>
        <td width="5">
          <!-- <h2><strong>Grafic de rambursare</strong></h2> -->
         
      
      </td>
      <td width="15">
         
          Durata imprumut (ani): <strong>{{ round($solicitare->perioada_de_creditare/12,0) }}</strong>
      </td>
  </tr>
   <tr>
        <td width="5">
          <!-- <h2><strong>Grafic de rambursare</strong></h2> -->
         
      
      </td>
      <td width="15">
         
          Rata lunara (LEI): <strong>{{ $solicitare->rata_lunara }}</strong>
         
      </td>
  </tr>
   <tr>
        <td width="5">
          <!-- <h2><strong>Grafic de rambursare</strong></h2> -->
         
      
      </td>
      <td width="15">
          
          Luni de plata: <strong>{{ $solicitare->perioada_de_creditare }}</strong>
      </td>
  </tr>
   <tr>
        <td width="5">
          <!-- <h2><strong>Grafic de rambursare</strong></h2> -->
         
      
      </td>
      <td width="15">
         
          Suma totala platita (LEI): <strong>{{ round($grafic->sum('ratalunara'),2) }}</strong> 
      </td>
  </tr>
   <tr>
        <td width="5">
          <!-- <h2><strong>Grafic de rambursare</strong></h2> -->
         
      
      </td>
      <td width="15">
         
          
            din care dobanda (LEI): <strong>{{ round($grafic->sum('dobanda'),2) }}</strong>
      </td>
  </tr>
      <tr>
       
            
            <tr style="border-bottom:1pt solid black; border-top:1pt solid black; page-break-inside: avoid !important;">
              <th align ="center" width="5">
                    Nr Crt
                </th>
               <th align ="center" width="15">
                    Data Platii
                </th>
                <th align ="center" width="15">
                    Credit
                </th>
                 <th align ="center" width="15">
                    Dobanda
                </th>
                <th align ="center" width="15">
                    Total De Plata
                </th>
               
                
                <th align ="right" width="15">
                    Credit In Sold
                </th>
               
               
            </tr>
    
                  
                     <tr  style="border-bottom:1pt solid black; page-break-inside: avoid !important;"> 
                       <th align="right" width="5">
                          
                      </th>
                      <th align="center" width="15">
                       <strong>   TOTAL</strong>
                      </th>
                      <th align="right" style="vertical-align:top;text-align:right;"  width="15">
                      <strong>{{ round($grafic->sum('principal'),2) }} </strong>
                         
                      </th>
                      <th align="right" style="vertical-align:top;text-align:right;"  width="15">
                          <strong>{{ round($grafic->sum('dobanda'),2) }}</strong>
                      </th>
                     <th align="right" width="15">
                        <strong>  {{ round($grafic->sum('ratalunara'),2) }}</strong>
                      </th>
                      
                      <th align="right" style="vertical-align:top;text-align:right;"  width="15">
                     <strong> {{ round($grafic->sum('principal'),2) }}</strong>
                         
                      </th>
                         
                      </tr>     
                    
                    @foreach($grafic  as $rata)
                      
                      <tr  style="border-bottom:1pt solid black; page-break-inside: avoid !important;"> 
                      <td align="center" width="5">
                       {{ $rata->nr_crt }}
                      </td>
                       <td align="center" width="15">
                       {{ dateFormatAfisare($rata->datascadentalucratoare) }}
                      </td>
                       <td align="right" style="vertical-align:top;text-align:right;"  width="15">
                      {{ round($rata->principal,2) }}
                      </td>
                      <td align="right" style="vertical-align:top;text-align:right;"  width="15">
                       {{ round($rata->dobanda,2) }}
                      </td>
                      <td align="right" width="15">
                       {{ round($rata->ratalunara,2) }}
                      </td>
                      <td align="right" style="vertical-align:top;text-align:right;"  width="15">
                      {{ round($rata->soldprincipal,2) }}
                      </td>
                         
                      </tr>
                     
                    @endforeach 
  
              <tr style="border-bottom:1pt solid black; border-top:1pt solid black; page-break-inside: avoid !important;">
              <th align ="left" colspan="4">
                 Rezumat pe ani
              </th>
            </tr>
            <tr style="border-bottom:1pt solid black; page-break-inside: avoid !important;">
              <th align ="center" width="15">
                    Anul
                </th>
                 <th align ="center" width="15">
                    Credit cumulat
                </th>
                <th align ="center" width="15">
                    Dobanda cumulata
                </th>
               
                <th align ="center" width="15">
                    Credit In Sold
                </th>
               
               
            </tr>
    
                  
                     <tr style="border-bottom:1pt solid black; page-break-inside: avoid !important;"> 
                       <th align="right" width="5">
                          
                      </th>
                      
                      <th align="right" style="vertical-align:top;text-align:right;"  width="15">
                         
                      </th>
                      <th align="right" style="vertical-align:top;text-align:right;"  width="15">
                     
                         
                      </th>
                      <th align="right" style="vertical-align:top;text-align:right;"  width="15">
                      {{ round($grafic->sum('principal'),2) }}
                         
                      </th>
                         
                      </tr>     
                    
                    @foreach($grafic->groupby('anul')  as $rata)
                      
                      <tr style="border-bottom:1pt solid black; page-break-inside: avoid !important;"> 
                      <td align="center" width="5">
                       {{ $rata[0]->anul }}
                      </td>
                     <td align="right" style="vertical-align:top;text-align:right;"  width="15">
                      {{ round($rata->sum('principal'),2) }}
                         
                      </td>
                      <td align="right" style="vertical-align:top;text-align:right;"  width="15">
                       {{ round($rata->sum('dobanda'),2) }}
                                              
                      </td>
                     
                     
                      <td align="right" style="vertical-align:top;text-align:right;"  width="15">
                      {{ round($rata->min('soldprincipal'),2) }}
                      </td>
                         
                      </tr>
                     
                    @endforeach 
     
          </table>  
