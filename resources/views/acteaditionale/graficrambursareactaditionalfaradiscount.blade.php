@extends('layouts.antetgraficxls')

@section ('content')

 
    <table class="table table-condesed">
      <tr>
         
        <td align="center" colspan="6" width=100>
          <h2><strong>ANEXA _________/_____________
         </strong>
        </h2>
      
      </td>
     </tr>
     <tr>
        <td align="center" colspan="6" width=100>
          <h2><strong>
            LA CONTRACTUL DE CREDIT NR. {{$tabel["contract"]["nr_contract"]}}  DIN DATA DE {{dateFormatAfisare($tabel["contract"]["data_contract"])}}
         </strong>
        </h2>
      
      </td>
     </tr>
     
        @foreach($tabel["parti"] as $parte)
        <tr>
            
        <td align="center" colspan="6" width=100>
          <strong>
                    {{$parte->nume}}
         </strong>
        
      
      </td>
     </tr>
                     
                     @endforeach 
     <tr>
       
        <td align="center" colspan="6" width=100>
          <h2><strong><br>
         </strong>
        </h2>
      
      </td>
     </tr>
      <tr>
     
      <td colspan="6" width=100>
          Creditul se ramburseaza in RATE lunare in {{ $tabel["contract"]["nr_luni"] }} luni.
       
      </td>
  </tr>
  <tr>
      <td colspan="6" width=100>
          Rata se achita lunar conform graficului de mai jos.
      </td>
  </tr>
</table>
 <table class="table table-condesed">
            <tr style="border-bottom:1pt solid black; border-top:1pt solid black; page-break-inside: avoid !important;">
              <th align ="center" colspan="2" width=10>
                    Durata creditului <br>
                    -luni-
                </th>
               <th align ="center"  width=10>
                    Valoare credit
                </th>
                
                 <th align ="center" width=10>
                   Dobanda lunara
                </th>
                <th align ="center" width=10>
                    DAE
                </th>
               
            </tr>
            <tr style="border-bottom:1pt solid black; border-top:1pt solid black; page-break-inside: avoid !important;">
              <th align ="center" colspan="2" width=10>
                    {{$tabel["contract"]["nr_luni"]}}
                </th>
               <th align ="center"  width=10>
                    {{numar_formatat($tabel["contract"]["credit_acordat"],2) .' '.strtolower($tabel["contract"]["tip_valuta"])}}
                </th>
               
                 <th align ="center" width=10>
                   {{numar_formatat($tabel["contract"]["proc_dob_rem"],2)}} %
                </th>
                <th align ="center" width=10>
                    {{numar_formatat($tabel["contract"]["dae"],2)}} %
                </th>
               
            </tr>
         </table>
         <table class="table table-condesed">    
            <tr style="border-bottom:1pt solid black; border-top:1pt solid black; page-break-inside: avoid !important;">
              <th align ="center" width=5>
                    Nr rata
                </th>
               <th align ="center" width=15>
                    Data scadenta
                </th>
                <th align ="center" width=10>
                    Rata principal
                </th>
               
                <th align ="center" width=10>
                    Dobanda <br>
                    {{numar_formatat($tabel["contract"]["proc_dob_rem"],2)}} %
                </th>
                <th align ="center" width=10>
                    Rata+dob 
                </th>
                
                <th align ="center" width=10>
                    Sold credit <br> imprumutat
                </th>
               
               
            </tr>
    
                  
                        
                    
                    @foreach($tabel["grafic"]  as $rata)
                      <tr>
                     <th align ="center" width=5>
                     {{$i++}}
                   </th>
               <th align ="center" width=15>
                    {{dateFormatAfisare($rata->data_scadenta)}}
                </th>
                <th align ="right" width=10>
                    {{numar_formatat($rata->rata_lunara,2).' '.strtolower($tabel["contract"]["tip_valuta"])}}
                </th>
              
                <th align ="right" width=10>
                     {{numar_formatat(nz($rata->dob_rem,0),2).' '.strtolower($tabel["contract"]["tip_valuta"])}}
                </th>
                <th align ="right" width=10>
                    {{numar_formatat(nz($rata->rata_lunara,0)+nz($rata->dob_rem,0),2).' '.strtolower($tabel["contract"]["tip_valuta"])}}
                </th>
                
                <th align ="right" width=10>
                    {{numar_formatat($rata->sold_credit,2).' '.strtolower($tabel["contract"]["tip_valuta"])}}
                </th>
               
               
            </tr>
                     
                    @endforeach 
                    <tr>
                     <th align ="center" colspan="2" width=10>
                     TOTAL
                   </th>
                <th align ="right" width=10>
                    {{numar_formatat($tabel["grafic"]->sum("rata_lunara"),2).' '.strtolower($tabel["contract"]["tip_valuta"])}}
                </th>
                
                <th align ="right" width=10>
                     {{numar_formatat($tabel["grafic"]->sum("dob_rem"),2).' '.strtolower($tabel["contract"]["tip_valuta"])}}
                </th>
                <th align ="right" width=10>
                    {{numar_formatat($tabel["grafic"]->sum("rata_lunara")+$tabel["grafic"]->sum("dob_rem"),2).' '.strtolower($tabel["contract"]["tip_valuta"])}}
                </th>
                
                <th align ="center" width=10>
                    
                </th>
               
               
            </tr>
              </table>
  
     
     
     
      <table class="table table-condesed" width=100 > 
                    <tr>
                  <td  colspan="8" width=100>
                     * Prezenta anexa face parte integranta din contractul de credit nr. {{$tabel["contract"]["nr_contract"]}}  din {{dateFormatAfisare($tabel["contract"]["data_contract"])}} 
                   </td>
                   </tr>
                   <tr>
                  <td  colspan="8" width=100>
                     
                   </td>
                   </tr>  
                        

                 <tr>
                  <td  colspan="4" width=50>
                     <strong>  CREDITOARE</strong> <br>
                     <strong>  EASY CREDIT 4 ALL IFN S.A.</strong>
                  </td>
                  <td  colspan="4"  width=50>
                    <strong>
                     @foreach($tabel["parti"] as $parte)
                     {{$parte->tip_participant}}<br>
                     {{$parte->nume}}<br>
                     _________________________________
                     @endforeach 
                    </strong>
                  </td>
                   
          </tr> 
           <tr>
                  <td  colspan="8"  width=100>
                  </td>  
          </tr>
          <tr>
                  <td  colspan="8"  width=100>
                  </td>  
          </tr>
          <tr>
                  <td  colspan="8"  width=100>
                  </td>  
          </tr>        
                     @foreach($tabel["parti"] as $parte)
           <tr>
                  <td  colspan="4"  width=50>
                     Am primit un exemplar din graficul de rambursare a creditului
                  </td>
                  <td  colspan="4"  width=50>
                    <strong>
                     {{$parte->nume}}
                     _________________________________
                    </strong>
                  </td>
                   
          </tr> 
                     @endforeach 
      </table>                
     
  

  
  
@stop