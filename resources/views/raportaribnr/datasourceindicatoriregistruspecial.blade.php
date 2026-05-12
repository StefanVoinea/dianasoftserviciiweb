<table >

        <tr>
            <td >
              Site
             </td>
             <td  >
              AccountingSystem
             </td>
             <td  >
              ReportingTime
             </td>
             <td  >
              AccountSymbol
             </td>
             <td  >
               AccountName
             </td>
             <td  >
              AccountType
             </td>
            <td  >
                BalanceCategory
             </td>
             
             <td  >
               EqClosingBalance|Debit_N_2
             </td>
             <td  >
                EqClosingBalance|Credit_N_2

             </td>
             <td  >
                EqClosingBalance|Debit_N_1

             </td>
             <td  >
                EqClosingBalance|Credit_N_1

             </td>
            
             <td  >
                EqClosingBalance|Debit_N

             </td>
             <td  >
                 EqClosingBalance|Credit_N

             </td>
             <td  >
                 BNR_A3_D

             </td>
             <td  >
                 BNR_A3_Cr

             </td>
             <td  >
                BNR_A3_A4

             </td>
             
             
         </tr>
         @foreach($balanta->groupby("cont") as $cont)
        <tr>
            <td  >
              Principal
             </td>
             <td  >
              BNR
             </td>
             <td  >
              {{$data_raportare}}
             </td>
             <td  >
              {{$cont[0]->cont}}
             </td>
             <td  >
               {{$cont[0]->denumire_cont}}
             </td>
             <td  >
                {{substr($cont[0]->tip_cont,0,1)}}
             </td>
             <td  >
                @if($cont[0]->grupa=="6"||$cont[0]->grupa=="7")
                {{"P&L"}}
                @else
                {{"Balance"}}
                @endif
             </td>
            
             <td  >
                {{round($cont->sum('soldFinalD_N_2'),0)}}
             </td>
             <td  >
                {{round($cont->sum('soldFinalC_N_2'),0)}}
             </td>
              <td  >
                {{round($cont->sum('soldFinalD_N_1'),0)}}
             </td>
             <td  >
                {{round($cont->sum('soldFinalC_N_1'),0)}}
             </td>
             <td  >
                {{round($cont->sum('soldFinalD_N'),0)}}
             </td>
             <td  >
                {{round($cont->sum('soldFinalC_N'),0)}}
             </td>
            
             <td  >
                 {{$cont[0]->bnr_a3_d}}
             </td>
             <td  >
                 {{$cont[0]->bnr_a3_cr}}
             </td>
             <td  >
                 {{$cont[0]->bnr_a3_a4}}
             </td>
            
             
         </tr>
 @endforeach
</table>

