<table >

        <tr>
            <td >
              Contract
             </td>
             <td  >
              Client
             </td>
             <td  >
              Incadrare
             </td>
             <td  >
              PF
             </td>
             <td  >
               RiscValutar
             </td>
             <td  >
              ExchangeRate
             </td>
             <td  >
              ExpunereBruta_Capital
             </td>
             <td  >
               ExpunereBruta_Dobanda
             </td>
             <td  >
                ExpunereNeta_Capital
             </td>
             <td  >
                ExpunereNeta_Dobanda
             </td>
             <td  >
                Provizion_Capital
             </td>
             <td  >
                Provizion_Dobanda
             </td>
             <td  >
                ExpunereExtrabilant
             </td>
             
             
         </tr>
         @foreach($reclasificare as $cont)
        <tr>
        <td >
              {{$cont->nr_contract}}
             </td>
             <td  >
              {{$cont->nume}}
             </td>
             <td  >
              {{$cont->tiprisc}}
             </td>
             <td  >
              {{$cont->pf}}
             </td>
             <td  >
               {{$cont->risc_valutar}}
             </td>
             <td  >
              {{$cont->exchange_rate}}
             </td>
             <td  >
              {{$cont->expunere_capital}}
             </td>
             <td  >
               {{$cont->expunere_dobanda}}
             </td>
             <td  >
                {{$cont->expunere_capital}}
             </td>
             <td  >
                {{$cont->expunere_dobanda}}
             </td>
             <td  >
                {{$cont->provizion_capital}}
             </td>
             <td  >
                {{$cont->provizion_dobanda}}
             </td>
             <td  >
                0
             </td>
             
             
         </tr>
 @endforeach
</table>

