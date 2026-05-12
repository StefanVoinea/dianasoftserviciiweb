@extends('layouts.antetfarafirma')

@section ('content')

 
    <table class="table table-condesed">
      <tr>
        <td align="center" colspan="8" width=100>
          <h2><strong>ANEXA 1*/{{dateFormatAfisare($tabel["solicitare"]["data_cerere"])}}
         </strong>
        </h2>
      
      </td>
     </tr>
     <tr>
        <td align="center" colspan="8" width=100>
          <h2><strong>
         LA CONTRACTUL DE CREDIT NR. _______________  DIN DATA DE _______________
         
         </strong>
        </h2>
      
      </td>
     </tr>
     <tr>
        <td align="center" colspan="8" width=100>
          <h2><strong>
         GRAFIC DE RAMBURSARE - EXTRAS
         </strong>
        </h2>
      
      </td>
     </tr>
     <tr>
        <td align="center" colspan="8" width=100>
          <h2><strong><br>
         </strong>
        </h2>
      
      </td>
     </tr>
      <tr>
      <td colspan="8" width=100>
          Creditul se ramburseaza in RATE lunare in {{ $tabel["solicitare"]["perioada_de_creditare"] }} luni.
       
      </td>
  </tr>
  <tr>
      <td colspan="8" width=100>
          Rata se achita lunar conform graficului de mai jos.
      </td>
  </tr>

            <tr style="border-bottom:1pt solid black; border-top:1pt solid black; page-break-inside: avoid !important;">
              <th align ="center" colspan="2" width=20>
                    Durata creditului <br>
                    -luni-
                </th>
               <th align ="center" colspan="2" width=20>
                    Valoare credit
                </th>
                <th align ="center" width=15>
                    Dobanda lunara
                </th>
                 <th align ="center" width=15>
                    Dobanda anuala efectiva
                </th>
                 <th align ="center" width=15>
                   Dobanda anjuala
                </th>
                <th align ="center" width=15>
                    
                </th>
               
            </tr>
            <tr style="border-bottom:1pt solid black; border-top:1pt solid black; page-break-inside: avoid !important;">
              <th align ="center" colspan="2" width=20>
                    {{$tabel["solicitare"]["perioada_de_creditare"]}}
                </th>
               <th align ="center" colspan="2" width=20>
                    {{$tabel["solicitare"]["suma_solicitata"]}}
                </th>
                <th align ="center" width=15>
                    {{$tabel["solicitare"]["procent_dobanda"]}}
                </th>
                 <th align ="center" width=15>
                    {{$tabel["solicitare"]["dae"]}}
                </th>
                 <th align ="center" width=15>
                   {{$tabel["solicitare"]["procent_dobanda"]*12}}
                </th>
                <th align ="center" width=15>
                    
                </th>
               
            </tr>
            <tr style="border-bottom:1pt solid black; border-top:1pt solid black; page-break-inside: avoid !important;">
              <th align ="center" width=10>
                    Nr rata
                </th>
               <th align ="center" width=10>
                    Data scadenta
                </th>
                <th align ="center" width=10>
                    Rata principal
                </th>
                 <th align ="center" width=10>
                    Dobanda <br>
                    {{$tabel["solicitare"]["procent_dobanda"]}} 
                </th>
                 <th align ="center" width=15>
                   Rata + Dobanda
                </th>
                <th align ="center" width=15>
                    Reducere lunara**
                </th>
                <th align ="center" width=15>
                    Rata+dob cu reducere
                </th>
                
                <th align ="center" width=15>
                    Sold credit imprumutat
                </th>
               
               
            </tr>
    
                  
                        
                    
                    @foreach($tabel["grafic"]  as $rata)
                      <tr>
                     <th align ="center" width=10>
                     {{$i++}}
                   </th>
               <th align ="center" width=10>
                    {{dateFormatAfisare($rata->datascadentalucratoare)}}
                </th>
                <th align ="center" width=10>
                    {{numar_formatat($rata->principal,2)}}
                </th>
                 <th align ="center" width=10>
                   
                    {{numar_formatat($rata->dobanda,2)}}
                </th>
                 <th align ="center" width=15>
                   {{numar_formatat($rata->principal+$rata->dobanda,2)}}
                </th>
                <th align ="center" width=15>
                     {{numar_formatat($rata->dobanda-$rata->dobanda_cu_discount,2)}}
                </th>
                <th align ="center" width=15>
                    {{numar_formatat($rata->principal+$rata->dobanda_cu_discount,2)}}
                </th>
                
                <th align ="center" width=15>
                    {{numar_formatat($rata->soldprincipal,2)}}
                </th>
               
               
            </tr>
                     
                    @endforeach 
                    <tr>
                     <th align ="center" colspan="2" width=20>
                     TOTAL
                   </th>
                <th align ="center" width=10>
                    {{numar_formatat($tabel["grafic"]->sum("principal"),2)}}
                </th>
                 <th align ="center" width=10>
                   
                    {{numar_formatat($tabel["grafic"]->sum("dobanda"),2)}}
                </th>
                 <th align ="center" width=15>
                   {{numar_formatat($tabel["grafic"]->sum("principal")+$tabel["grafic"]->sum("dobanda"),2)}}
                </th>
                <th align ="center" width=15>
                     {{numar_formatat($tabel["grafic"]->sum("dobanda")-$tabel["grafic"]->sum("dobanda_cu_discount"),2)}}
                </th>
                <th align ="center" width=15>
                    {{numar_formatat($tabel["grafic"]->sum("principal")+$tabel["grafic"]->sum("dobanda_cu_discount"),2)}}
                </th>
                
                <th align ="center" width=15>
                    
                </th>
               
               
            </tr>
              </table>
  
      * Prezenta anexa face parte integranta din contractul de credit nr. …......... din …............     <br>
     ** Debitorul beneficiaza de o reducere lunara care se aplica la plata ratei lunare sub conditia achitarii  integrale a ratelor lunare datorate,                            
        cel tarziu in ziua scadenta. Aceasta facilitate este acordata lunar, in cazul in care obligatiile de plata lunare sunt achitate integral,                           
        cu respectarea zilei scadentei astfel cum a fost stabilita de parti. Neplata la scadenta a obligatiilor aferente unei luni calendaristice nu duce                           
        la pierderea facilitatii pentru obligatiile scadente viitoare.                          

     <hr> 
     
      <table class="table table-condesed" width=100 > 
                 <tr>
                  <td  width=40>
                     <strong>  CREDITOARE</strong> <br>
                     <strong>  EASY CREDIT IFN S.A.</strong>
                  </td>
                  <td  width=20>
                  </td>  
                  <td  width=40>
                    <strong>
                     @foreach($tabel["parti"] as $parte)
                     {{$parte->tip_participant}}<br>
                     {{$parte->nume}}<br>
                     _________________________________
                     @endforeach 
                    </strong>
                  </td>
                   
          </tr> 
        
      </table>                
     
  

  
  
@stop