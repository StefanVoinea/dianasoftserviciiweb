<table >

        <tr>
            <td >
             ContractId
             </td>
             <td  >
              ContractSiteId
             </td>
             <td  >
              ContractNumber
            </td>
             <td  >
             PartnerId 
            </td>
            <td  >
              PartnerCode
            </td>
            <td  >
              PartnerName
            </td>
             <td  >
              AccountSymbol
             </td>
             <td  >
              AccountId
            </td>
            <td  >
              CurrencyCode
            </td>

            
             <td  >
                 ClosingBalance|Debit 
             </td>
             <td  >
                 ClosingBalance|Credit
             </td>
             <td  >
                EqClosingBalance|Debit
             </td>
             <td  >
                EqClosingBalance|Credit
             </td>
            
             <td  >
                 AN2
             </td>
             <td  >
                 Scadenta
             </td>
             <td  >
                 ScadentaCod
             </td>
             <td  >
                 Tara
             </td>
             <td  >
                 CodESA
             </td>
             <td  >
                 Destinatie
             </td>
              <td  >
                 Active_cotate
             </td>
              <td  >
                 Leas_Op
             </td>
              <td  >
                 Atasata
             </td>
              <td  >
                 Activitate
             </td>
              <td  >
                 AN1
             </td>
         </tr>
         @foreach($balanta as $cont)
        <tr>
            <td  >
             </td>
            <td  >
             </td>
            <td  >
             </td>
            <td  >
             </td>
            <td  >
             </td>
            
             <td  >
              {{$cont->partener}}
             </td>
             <td  >
              {{$cont->cont}}
             </td>
             <td  >
               
             </td>
             <td  >
              {{$cont->tip_valuta}}
             </td>
             <td  >
                {{round($cont->soldFinalD/$cont->curs,0)}}
             </td>
             <td  >
                {{round($cont->soldFinalC/$cont->curs,0)}}
             </td>
            
             <td  >
                {{round($cont->soldFinalD,0)}}
             </td>
             <td  >
                {{round($cont->soldFinalC,0)}}
             </td>
             <td  >
                 {{$cont->bnr_an2}}
             </td>
             <td  >
                 {{$cont->scadenta}}
             </td>
             <td  >
                 {{$cont->cod_scadenta}}
             </td>
             <td  >
                 {{$cont->cod_tara}}
             </td>
             <td  >
                 {{$cont->cod_esa}}
             </td>
             <td  >
                 {{$cont->destinatie}}
             </td>
             <td  >
                 {{$cont->active_cotate}}

             </td>
             <td  >
                 {{$cont->leas_op}}
             </td>
             <td  >
                 {{$cont->atasata}}
             </td>
             <td  >
                 {{$cont->activitate}}
             </td>
             <td  >
                 {{$cont->bnr_an1}}
             </td>
         </tr>
 @endforeach
</table>

