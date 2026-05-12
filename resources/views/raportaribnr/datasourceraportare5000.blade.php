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
              PartnerType
             </td>
             <td  >
              Dev
             </td>
             <td  >
               EqTotalAmounts|Debit
             </td>
             <td  >
                EqTotalAmounts|Credit
             </td>
             <td  >
                EqClosingBalance|Debit
             </td>
             <td  >
                EqClosingBalance|Credit
             </td>
             <td  >
                BalanceCategory
             </td>
             <td  >
                AccountType
             </td>
             <td  >
                TotalAmounts|Debit 
             </td>
             <td  >
                 TotalAmounts|Credit 
             </td>
             <td  >
                 ClosingBalance|Debit 
             </td>
             <td  >
                 ClosingBalance|Credit
             </td>
             <td  >
                 BNR_5000
             </td>
             <td  >
                 BNR_5000_+
             </td>
             <td  >
                 BNR_5026
             </td>
             <td  >
                 BNR_5027
             </td>
             <td  >
                 BNR_5080
             </td>
             <td  >
                 BNR_5081
             </td>
             
         </tr>
         @foreach($balanta as $cont)
        <tr>
            <td  >
              Principal
             </td>
             <td  >
              BNR
             </td>
             <td  >
              {{$data}}
             </td>
             <td  >
              {{$cont->cont}}
             </td>
             <td  >
               {{$cont->denumire_cont}}
             </td>
             <td  >
              {{$cont->tip_persoana}}
             </td>
             <td  >
              {{$cont->tip_valuta}}
             </td>
             <td  >
               {{round($cont->rulajTotalD,0)}}
             </td>
             <td  >
                {{round($cont->rulajTotalC,0)}}
             </td>
             <td  >
                {{round($cont->soldFinalD,0)}}
             </td>
             <td  >
                {{round($cont->soldFinalC,0)}}
             </td>
             <td  >
                @if($cont->grupa=="6"||$cont->grupa=="7")
                {{"P&L"}}
                @else
                {{"Balance"}}
                @endif
             </td>
             <td  >
                {{substr($cont->tip_cont,0,1)}}
             </td>
             <td  >
               {{round($cont->rulajTotalD/$cont->curs,0)}}
             </td>
             <td  >
                {{round($cont->rulajTotalC/$cont->curs,0)}}
             </td>
             <td  >
                {{round($cont->soldFinalD/$cont->curs,0)}}
             </td>
             <td  >
                {{round($cont->soldFinalC/$cont->curs,0)}}
             </td>
             <td  >
                 {{$cont->bnr_5000}}
             </td>
             <td  >
                 {{$cont->bnr_5000_semn}}
             </td>
             <td  >
                 {{$cont->bnr_5026}}
             </td>
             <td  >
                 {{$cont->bnr_5027}}
             </td>
             <td  >
                 {{$cont->bnr_5080}}
             </td>
             <td  >
                 <!-- BNR_5081 -->
             </td>
             
         </tr>
 @endforeach
</table>

