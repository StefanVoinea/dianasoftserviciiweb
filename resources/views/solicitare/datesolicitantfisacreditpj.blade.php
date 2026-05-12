

<table class="table" style="border-collapse: collapse; " width="100">
<tr>
    <td width="5"></td>
</tr>
<tr>
    <td width="5">
        
     </td>
     <td colspan="2" style="font-size:16px;">
        <!-- Nota : Se introduc manual datele in campurile colorate cu gri , restul datelor sunt preluate -->
          FISA DE CREDITARE
    </td>
</tr>
<tr>
    <td width="5">
         
     </td>
    
</tr>
<tr>
    <td width="5">
         
     </td>
    
</tr>
 <tr>
     <td width="5">
         
     </td>
     <td width="50" style="border: 1px solid black;font-weight: bold;">
         Consilier credit retail
     </td>
     <td width="45" style="border: 1px solid black;background-color:#DCDCDC;font-weight: bold;">
          {{$solicitare->consilier_credite}}
     </td>
   </tr>
   
 <tr>

     <td width="5">
         
     </td>
     <td width="50" style="border: 1px solid black;font-weight: bold;">
         Analist Credit
     </td>
     <td width="45" style="border: 1px solid black;background-color:#DCDCDC;font-weight: bold;">
          {{$solicitare->analist_credite}}
     </td>
  
     
     
 </tr>
 <tr>

     <td width="5">
         
     </td>
     <td width="50" style="border: 1px solid black;font-weight: bold;">
         Juridic 
     </td>
     <td width="45" style="border: 1px solid black;background-color:#DCDCDC;font-weight: bold;">
         {{ "SCA Condulimazi & Asociatii"  }}
     </td>
    
 </tr>
 <tr>

     <td width="5">
         
     </td>
     <td width="50" style="border: 1px solid black;font-weight: bold;">
         Data 
     </td>
     <td width="45" style="border: 1px solid black;background-color:#DCDCDC;font-weight: bold;">
          {{dateFormatAfisare($solicitare->data)}}
     </td>
     
 </tr>
 <tr>
     <td width="5">
         
     </td> 
     
     <td width="50" style="border: 1px solid black;font-weight: bold;">
         Valuta creditului
     </td>
     <td width="45" style="border: 1px solid black;background-color:#DCDCDC;font-weight: bold;">
          {{$solicitare->tip_valuta}}  
     </td>
 </tr>
 <tr>   
    <td width="5">
         
     </td> 
        <td width="50" style="border: 1px solid black;font-weight: bold;">
         Sursa de finantare
     </td>
     <td width="45" style="border: 1px solid black;background-color:#DCDCDC;font-weight: bold;">
          Fortuna Capital IFN SA 
     </td>
 </tr>
 <tr>
   <td width="5">
         
     </td>
     <td width="50" >
       
     </td>
     <td width="66">
        
     </td>
    
 </tr>
  <tr>
   <td width="5">
         
     </td>
     <td width="50" >
       
     </td>
     <td width="45">
        
     </td>
    
 </tr>
 @foreach($solicitare->partisolicitare as $parte)
         <tr>   
         <td colspan="3"></td>
         </tr>
         @if(strtoupper($parte->tip_persoana)=="PERSOANA FIZICA")
            <tr>
            <td width="5">
         
             </td>
              <td width="50"  style="border: 1px solid black;font-weight: bold;background-color:#ffff00">
                 {{" ".$parte->tip_participant." "}}
             </td>
             <td width="45"  style="border: 1px solid black;font-weight: bold;">
                 
             </td>
            </tr>
            <tr>
                <td width="5">
         
             </td>
              <td width="50" style="border: 1px solid black;font-weight: bold;">
                 Nume si prenume
             </td>
             <td width="45" style="border: 1px solid black;background-color:#DCDCDC;font-weight: bold;">
                  {{$parte->nume}}
             </td>    
            </tr>
            <tr>
                <td width="5">
         
             </td>
               <td width="50" style="border: 1px solid black;font-weight: bold;">
                     CNP
                 </td>
                 <td width="45" style="text-align:left;border: 1px solid black;background-color:#DCDCDC;font-weight: bold;">
                      {{str_replace(",","",number_format($parte->cnp,0))." ."}}
                 </td>   
            </tr>
             <tr>
                  <td width="5">
         
                    </td>
                <td width="50" style="border: 1px solid black;font-weight: bold;">
                     BI/CI/PASAPORT (seria, nr.)
                 </td>
                 <td width="45" style="border: 1px solid black;background-color:#DCDCDC;font-weight: bold;">
                      {{$parte->ci_seria." ".$parte->ci_numar}}
                 </td>  
            </tr>
            <tr>
                    <td width="5">
         
                            </td>              
                 <td width="50" style="border: 1px solid black;font-weight: bold;">
                     Sex masculin/feminin
                 </td>
                 <td width="45" style="border: 1px solid black;background-color:#DCDCDC;font-weight: bold;">
                      {{$parte->sex}}
                 </td>
            </tr>
            <tr>
                  <td width="5">
         
                    </td>
                 <td width="50" style="border: 1px solid black;font-weight: bold;">
                     Adresa
                 </td>
                 <td width="45" style="border: 1px solid black;background-color:#DCDCDC;font-weight: bold;">
                      {{$parte->adresa}}
                 </td>
            </tr>
             <tr>
                  <td width="5">
         
                    </td>
                 <td width="50" style="border: 1px solid black;font-weight: bold;">
                     Localitate
                 </td>
                 <td width="45" style="border: 1px solid black;background-color:#DCDCDC;font-weight: bold;">
                      {{$parte->localitate}}
                 </td>
            </tr>
            <tr>
                  <td width="5">
         
                    </td>
                 <td width="50" style="border: 1px solid black;font-weight: bold;">
                     Judet
                 </td>
                 <td width="45" style="border: 1px solid black;background-color:#DCDCDC;font-weight: bold;">
                      {{$parte->judet}}
                 </td>
            </tr>
            <tr>
                  <td width="5">
         
                     </td>
                 <td width="50" style="border: 1px solid black;font-weight: bold;">
                     Telefon
                 </td>
                 <td width="45" style="border: 1px solid black;background-color:#DCDCDC;font-weight: bold;">
                      {{$parte->telefon}}
                 </td>
            </tr>
            <tr>
                  <td width="5">
         
                     </td>
                 <td width="50" style="border: 1px solid black;font-weight: bold;">
                     Mail
                 </td>
                 <td width="45" style="border: 1px solid black;background-color:#DCDCDC;font-weight: bold;">
                      {{$parte->email}}
                 </td>
            </tr>
             <tr>
                  <td width="5">
         
                     </td>
                 <td width="50" style="border: 1px solid black;font-weight: bold;">
                     Denumire angajator
                 </td>
                 <td width="45" style="border: 1px solid black;background-color:#DCDCDC;font-weight: bold;">
                      {{$parte->denumire_angajator.", ".$parte->cui_angajator}}
                 </td>
            </tr>
            <tr>
                  <td width="5">
         
     </td>
                 <td width="50" style="border: 1px solid black;font-weight: bold;">
                     Tip angajator
                 </td>
                 <td width="45" style="border: 1px solid black;background-color:#DCDCDC;font-weight: bold;">
                      {{$parte->tip_angajator}}
                 </td>
            </tr>
            <tr>
                  <td width="5">
         
                    </td>
                 <td width="50" style="border: 1px solid black;font-weight: bold;">
                     Angajat la firma proprie (DA/NU)
                 </td>
                 <td width="45" style="border: 1px solid black;background-color:#DCDCDC;font-weight: bold;">
                      {{$parte->angajat_firma_proprie?"DA":"NU"}}
                 </td>
            </tr>
            <tr>
                  <td width="5">
         
                    </td>
                 <td width="50" style="border: 1px solid black;font-weight: bold;">
                     Functie
                 </td>
                 <td width="45" style="border: 1px solid black;background-color:#DCDCDC;font-weight: bold;">
                      {{$parte->functie}}
                 </td>
            </tr>
            <tr>
                  <td width="5">
         
                     </td>
                 <td width="50" style="border: 1px solid black;font-weight: bold;">
                     Vechime la actualul loc de munca
                 </td>
                 <td width="45" style="border: 1px solid black;background-color:#DCDCDC;font-weight: bold;">
                      {{$parte->vechime_ani." ani ".$parte->vechime_luni." luni"}}
                 </td>
            </tr>
            <tr>
                  <td width="5">
         
                     </td>
                 <td width="50" style="border: 1px solid black;font-weight: bold;">
                     Telefon serviciu
                 </td>
                 <td width="45" style="border: 1px solid black;background-color:#DCDCDC;font-weight: bold;">
                      {{$parte->telefon_servici}}
                 </td>
            </tr>
            <tr>
                  <td width="5">
         
                     </td>
                 <td width="50" style="border: 1px solid black;font-weight: bold;">
                     E-mail serviciu
                 </td>
                 <td width="45" style="border: 1px solid black;background-color:#DCDCDC;font-weight: bold;">
                      {{$parte->email_servici}}
                 </td>
            </tr>
       @else    <!-- PERSOANA JURIDICA   -->
         <tr>
            <td width="5">
         
             </td>
              <td width="50"  style="border: 1px solid black;font-weight: bold;background-color:#ffff00">
                 {{" ".$parte->tip_participant." "}}
             </td>
             <td width="45"  style="border: 1px solid black;font-weight: bold;">
                 
             </td>
            </tr>
            <tr>
                <td width="5">
         
             </td>
              <td width="50" style="border: 1px solid black;font-weight: bold;">
                Denumire societate
             </td>
             <td width="45" style="border: 1px solid black;background-color:#DCDCDC;font-weight: bold;">
                  {{$parte->nume}}
             </td>    
            </tr>
            <tr>
                <td width="5">
         
             </td>
               <td width="50" style="border: 1px solid black;font-weight: bold;">
                     Forma Juridica
                 </td>
                 <td width="45" style="text-align:left;border: 1px solid black;background-color:#DCDCDC;font-weight: bold;">
                      {{$parte->forma_juridica}}
                 </td>   
            </tr>
             <tr>
                  <td width="5">
         
                    </td>
                <td width="50" style="border: 1px solid black;font-weight: bold;">
                     Obiect de activitate
                 </td>
                 <td width="45" style="border: 1px solid black;background-color:#DCDCDC;font-weight: bold;">
                      {{$parte->obiect_de_activitate}}
                 </td>  
            </tr>
             <tr>
                  <td width="5">
         
                    </td>
                <td width="50" style="border: 1px solid black;font-weight: bold;">
                     Banca
                 </td>
                 <td width="45" style="border: 1px solid black;background-color:#DCDCDC;font-weight: bold;">
                      {{$parte->banca}}
                 </td>  
            </tr>
             <tr>
                  <td width="5">
         
                    </td>
                <td width="50" style="border: 1px solid black;font-weight: bold;">
                     Cont
                 </td>
                 <td width="45" style="border: 1px solid black;background-color:#DCDCDC;font-weight: bold;">
                      {{$parte->cont}}
                 </td>  
            </tr>
            <tr>
                    <td width="5">
         
                            </td>              
                 <td width="50" style="border: 1px solid black;font-weight: bold;">
                     Capital social
                 </td>
                 <td width="45" style="border: 1px solid black;background-color:#DCDCDC;font-weight: bold;">
                      {{$parte->capital_social}}
                 </td>
            </tr>
            <tr>
                  <td width="5">
         
                    </td>
                 <td width="50" style="border: 1px solid black;font-weight: bold;">
                     Adresa
                 </td>
                 <td width="45" style="border: 1px solid black;background-color:#DCDCDC;font-weight: bold;">
                      {{$parte->adresa}}
                 </td>
            </tr>
             <tr>
                  <td width="5">
         
                    </td>
                 <td width="50" style="border: 1px solid black;font-weight: bold;">
                     Localitate
                 </td>
                 <td width="45" style="border: 1px solid black;background-color:#DCDCDC;font-weight: bold;">
                      {{$parte->localitate}}
                 </td>
            </tr>
            <tr>
                  <td width="5">
         
                    </td>
                 <td width="50" style="border: 1px solid black;font-weight: bold;">
                     Judet
                 </td>
                 <td width="45" style="border: 1px solid black;background-color:#DCDCDC;font-weight: bold;">
                      {{$parte->judet}}
                 </td>
            </tr>
            <tr>
                  <td width="5">
         
                     </td>
                 <td width="50" style="border: 1px solid black;font-weight: bold;">
                     CUI
                 </td>
                 <td width="45" style="text-align:left;border: 1px solid black;background-color:#DCDCDC;font-weight: bold;">
                      {{$parte->cnp}}
                 </td>
            </tr>
            <tr>
                  <td width="5">
         
                     </td>
                 <td width="50" style="border: 1px solid black;font-weight: bold;">
                     Registrul Comertului
                 </td>
                 <td width="45" style="border: 1px solid black;background-color:#DCDCDC;font-weight: bold;">
                      {{$parte->regcom}}
                 </td>
            </tr>
             <tr>
                  <td width="5">
         
                     </td>
                 <td width="50" style="border: 1px solid black;font-weight: bold;">
                     Administrator
                 </td>
                 <td width="45" style="border: 1px solid black;background-color:#DCDCDC;font-weight: bold;">
                      {{$parte->administrator}}
                 </td>
            </tr>
            <tr>
                  <td width="5">
         
     </td>
                 <td width="50" style="border: 1px solid black;font-weight: bold;">
                     Asociati
                 </td>
                 <td width="45" style="border: 1px solid black;background-color:#DCDCDC;font-weight: bold;">
                      {{$parte->asociati}}
                 </td>
            </tr>
            <tr>
                  <td width="5">
         
                     </td>
                 <td width="50" style="border: 1px solid black;font-weight: bold;">
                     Telefon
                 </td>
                 <td width="45" style="border: 1px solid black;background-color:#DCDCDC;font-weight: bold;">
                      {{$parte->telefon}}
                 </td>
            </tr>
             <tr>
                  <td width="5">
         
                     </td>
                 <td width="50" style="border: 1px solid black;font-weight: bold;">
                     Mail
                 </td>
                 <td width="45" style="border: 1px solid black;background-color:#DCDCDC;font-weight: bold;">
                      {{$parte->email}}
                 </td>
            </tr>
             <tr>
                  <td width="5">
         
                     </td>
                      <td width="50">
         
                     </td>
                      <td width="45">
         
                     </td>
                 
            </tr>
           <tr>
                  <td width="5">
         
                     </td>
                      <td width="50">
         
                     </td>
                      <td width="45">
         
                     </td>
                 
            </tr>
            <tr>
                  <td width="5">
                  </td>
                  <td width="50">
                      ANALIZA FINANCIARA
                  </td>
                 <td width="45">
         
                     </td>
            </tr>
            <tr>
                  <td width="5">
                  </td>
                  <td width="10">
                      Perioada analizata
                  </td>
              </tr>
                  <tr>
                 <td width="5">
                  </td>
                   <td width="50">
                  </td>
                  <td width="45">
                      {{dateFormatAfisare($data1Analizata)}}
                  </td>
                 
              </tr>
              <tr>
                <td width="5">
                  </td>
                   <td width="50">
                  </td>
                 <td width="45">
                      {{dateFormatAfisare($data2Analizata)}}
                  </td>
                   
              </tr>
              <tr>
                <td width="5">
                  </td>
                   <td width="50">
                  </td>
                  <td width="45">
                      {{dateFormatAfisare($dataBalanta)}}
                  </td>
                 
            </tr>
                 
       @endif
   
 @endforeach
</table>

