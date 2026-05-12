<table >
         <tr>
        <td >
          Curs BNR : {{$cursBNR}} 
        </td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>

      </tr>  
     
      <tr>
       
        <td>         
          NR. CRT.
        </td>
        <td>
          AGENTIA      
        </td>
        <td>
          DATA CERERII
        </td>
        <td >
          NR CONTRACT CREDIT
        </td>
        <td >
          NUME PRENUME IMPRUMUTAT
        </td>
        <td >
          CNP IMPRUMUTAT
        </td>
        <td>
         NUME PRENUME GARANT
        </td>
        <td >
          CNP GARANT/CUI      
         </td>
        <td >
          TIP GARANTIE
        </td>
        <td >
          GARANTIE
        </td>
        <td >
          VALOAREA JUSTA  EUR
        </td>
        <td >
          VALOAREA JUSTA   LEI
        </td>
        <td >
          VALOAREA CREDIT   EUR
        </td>
        <td >
          VALOAREA CREDIT   LEI
        </td>
        <td >
          DATA SEMNARII
        </td>
        <td >
          PERIOADA
        </td>
        <td >
          DOB %
        </td>
        <td >
          TIP   RAMBURSARE
        </td>
        <td >
          DOB/LUNA   EUR
        </td>
        <td >
          DOB/LUNA   LEI
        </td>
        <td >
          DOB CU DISC /LUNA EUR
        </td>
        <td >
          DOB CU DISC /LUNA LEI
        </td>
         <td >
          RATA PRINCIPAL /LUNA EUR
        </td>
        <td >
          RATA PRINCIPAL /LUNA LEI
        </td>
        <td >
          DATA VIRAMENTULUI
        </td>
        <td >
          URMATOAREA SCADENTA
        </td>
        <td >
          ZILE INTARZIERE
        </td>
        <td >
          CUM NE-A CONTACTAT
        </td>
        <td >
          DEBIT REESALONAT EUR
        </td>
        <td >
          DEBIT REESALONAT LEI
        </td>
        <td >
          SCADENTA REALA
        </td>
        <td >
          PROCENT DOBANDA FARA DISCOUNT
        </td>
        <td >
          PROCENT DOBANDA CU DISCOUNT
        </td>
        <td >
          ZI SCADENTA
        </td>
        <td >
          BIROUL DE CREDIT
        </td>
        <td >
          ANAF
        </td>
        <td >
          ULTIMA DECLARARE CREDIT SCADENT
        </td>
      </tr>    
   
        @foreach ($contracte as $contract)
          <tr>
            <td>         
              {{$i++}}
            </td>
            <td>
              {{$contract->agentia}}      
            </td>
            <td>
              {{dateFormatAfisare($contract->data_cerere)}}      
            </td>
            <td >
              {{$contract->nr_contract}}      
            </td>
            <td >
              {{$contract->nume}}
            </td>
            <td >
              {{$contract->cnp}}
            </td>
            <td>
              @foreach($contract->partisolicitare as $parte)
              {{$parte->nume}}   <br>
              @endforeach
            </td>
            <td >
              @foreach($contract->partisolicitare as $parte)
              {{$parte->cnp}} <br>   
              @endforeach
            </td>
            <td >
              {{$contract->tip_garantie}}
          
            </td>
            <td >
              {{$contract->garantie}}
          
            </td>
            <td >
            
            {{round($contract->valoare_justa/$cursBNR,2)}}
          
            </td>
            <td >
              {{$contract->valoare_justa}}
          
            </td>
            <td >
              {{$contract->valoare_credit_eur}}
            </td>
            <td >
              {{$contract->valoare_credit_lei}}
            </td>
            <td >
              {{dateFormatAfisare($contract->data_contract)}}
            </td>
            <td >
              {{$contract->nr_luni}}
            </td>
            <td >
              {{$contract->proc_dob_rem}} % / {{nz($contract->proc_dob_rem,0)-nz($contract->disc_dob_rem,0)}} %
            </td>
            <td >
              {{$contract->tip_rambursare}}
            </td>
            <td >
               {{$contract->dobanda_lunara_eur}}
            </td>
            <td >
              {{$contract->dobanda_lunara_lei}}
            </td>
            <td >
              {{$contract->dobanda_cu_discount_eur}}
            </td>
            <td >
              {{$contract->dobanda_cu_discount_lei}}
            </td>
             <td >
              {{$contract->rata_principal_eur}}
            </td>
            <td >
              {{$contract->rata_principal_lei}}
            </td>
            <td >
              {{dateFormatAfisare($contract->data_acordarii)}}
            </td>
            <td >
              {{dateFormatAfisare($contract->urmatoarea_scadenta)}}
            </td>
            <td >
              {{$contract->zile_intarziere}}
            </td>
            <td >
              {{$contract->sursa_de_informare}}
            </td>
            <td >
              {{$contract->debit_reesalonat_eur}}
            </td>
            <td >
             {{$contract->debit_reesalonat_lei}}
            </td>
            <td >
              {{dateFormatAfisare($contract->scadenta_reala)}}
            </td>
            <td >
              {{$contract->proc_dob_rem}} %
            </td>
            <td >
              {{nz($contract->proc_dob_rem,0)-nz($contract->disc_dob_rem,0)}} %
            </td>
            <td >
              {{$contract->zi_scadenta}}
            </td>
            <td >
              {{$contract->birou_credit}}
            </td>
            <td >
              {{$contract->anaf}}
            </td>
            <td >
              {{dateFormatAfisare($contract->ultima_declarare_credit_scadent)}}
            </td>
          </tr>
          @endforeach
          <tr>
            <td colspan="10">         
              TOTAL 
            </td>
            <td >
              
              {{$valoarejustaEUR}}  
            </td>
            <td >
              {{$valoarejustaLEI}}  
            </td>
            <td >
              {{collect($contracte)->sum("valoare_credit_eur")}}
            </td>
            <td >
              {{collect($contracte)->sum("valoare_credit_lei")}}
            </td>
            <td >
              
            </td>
            <td >
              
            </td>
            <td >
              
            </td>
            <td >
              
            </td>
            <td >
               {{collect($contracte)->sum("dobanda_lunara_eur")}}
            </td>
            <td >
              {{collect($contracte)->sum("dobanda_lunara_lei")}}
            </td>
            <td >
              {{collect($contracte)->sum("dobanda_cu_discount_eur")}}
            </td>
            <td >
              {{collect($contracte)->sum("dobanda_cu_discount_lei")}}
            </td>
             <td >
              {{collect($contracte)->sum("rata_principal_eur")}}
            </td>
            <td >
              {{collect($contracte)->sum("rata_principal_lei")}}
            </td>
            <td >
              
            </td>
            <td >
              
            </td>
            <td >
              {{collect($contracte)->sum("zile_intarziere")}}
            </td>
            <td >
             
            </td>
            <td >
              {{collect($contracte)->sum("debit_reesalonat_eur")}}
            </td>
            <td >
             {{collect($contracte)->sum("debit_reesalonat_lei")}}
            </td>
            <td >
              
            </td>
            <td >
              
            </td>
            <td >
              
            </td>
            <td >
              
            </td>
            <td >
              
            </td>
            <td >
              
            </td>
            <td >
              
            </td>
          </tr>
</table>
<table>
    <tr>
        <td colspan="16">
              
        </td>
      </tr>
 <tr>
       
        <td>         
         
        </td>
        <td>
          Agentia      
        </td>
        <td>
          Tip valuta
        </td>
        <td >
          Nr contracte
        </td>
       
        <td >
          VALOAREA JUSTA (EUR)
        </td>
        <td >
          VALOAREA JUSTA (LEI)
        </td>
        <td >
          VALOAREA CREDIT (EUR)
        </td>
        <td >
          VALOAREA CREDIT (LEI)
        </td>
        <td >
          Dobanda lunara fara discount (EUR)
        </td>
        <td >
          Dobanda lunara fara discount (LEI)
        </td>
        <td >
          Dobanda lunara cu discount (EUR)
        </td>
        <td >
          Dobanda lunara cu discount (LEI)
        </td>
         <td >
          Rata principal (EUR)
        </td>
        <td >
          Rata principal (LEi)
        </td>
       
        <td >
          Debit reesalonat (EUR)
        </td>
        <td >
          Debit reesalonat (LEI)
        </td>
        </tr>  
        @foreach($totalpeAgentiisiValuta as $linie)
        <tr>
        <td>         
        </td>
        <td>
         {{ $linie->agentia}}
        </td>
        <td>
          {{ $linie->tip_valuta}}
        </td>
        <td >
          {{ $linie->nr_contracte}}
        </td>
       
        <td>
          {{ $linie->valoare_justa_eur}}
        </td>
        <td>
          {{ $linie->valoare_justa_lei}}
        </td>
        <td >
          {{ $linie->valoare_credit_eur}}
        </td>
        <td >
          {{ $linie->valoare_credit_lei}}
        </td>
        <td >
          {{ $linie->dobanda_lunara_eur}}
        </td>
        <td >
          {{ $linie->dobanda_lunara_lei}}
        </td>
        <td >
          {{ $linie->dobanda_cu_discount_eur}}
        </td>
        <td >
          {{ $linie->dobanda_cu_discount_lei}}
        </td>
         <td >
          {{ $linie->rata_principal_eur}}
        </td>
        <td >
          {{ $linie->rata_principal_lei}}
        </td>
       
        <td >
          {{ $linie->debit_reesalonat_eur}}
        </td>
        <td >
          {{ $linie->debit_reesalonat_lei}}
        </td>
        </tr>  
        @endforeach
          <tr>
        <td colspan="16">
             
        </td>
      </tr>
        @foreach(collect($totalpeAgentiisiValuta)->groupBy("agentia") as $linie)
        <tr>
        <td>         
        </td>
        <td>
          TOTAL
        </td>
        <td>
         {{ $linie[0]->agentia}}
          
        </td>
        <td >
          {{ $linie->sum("nr_contracte")}}
        </td>
       
        <td >
          {{ $linie->sum("valoare_justa_eur")}}
        </td>
        <td >
          {{ $linie->sum("valoare_justa_lei")}}
        </td>
        <td >
          {{ $linie->sum("valoare_credit_eur")}}
        </td>
        <td >
          {{ $linie->sum("valoare_credit_lei")}}
        </td>
        <td >
          {{ $linie->sum("dobanda_lunara_eur")}}
        </td>
        <td >
          {{ $linie->sum("dobanda_lunara_lei")}}
        </td>
        <td >
          {{ $linie->sum("dobanda_cu_discount_eur")}}
        </td>
        <td >
          {{ $linie->sum("dobanda_cu_discount_lei")}}
        </td>
         <td >
          {{ $linie->sum("rata_principal_eur")}}
        </td>
        <td >
          {{ $linie->sum("rata_principal_lei")}}
        </td>
       
        <td >
          {{ $linie->sum("debit_reesalonat_eur")}}
        </td>
        <td >
          {{ $linie->sum("debit_reesalonat_lei")}}
        </td>
        </tr>  
        @endforeach
</table>