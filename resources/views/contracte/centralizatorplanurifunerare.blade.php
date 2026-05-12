
<table class="table">
    <thead>
    <tr>
        <th>Nr crt</th>
        <th>Data scadenta</th>
        <th>Persoana</th>
        <th>Lotizare</th>
        <th>Data contract</th>
        <th>Valoare contract</th>
        <th>Moneda</th>
        <th>Numar rate</th>
        <th>Valoare rata</th>
        <th>Data primei rate</th>
        <th>Avans</th>
        <th>Data avans</th>
        <th>Incasari</th>
        <th>Rest de plata</th>
        <th>Restanta</th>
        <th>Valoare penalitati</th>
        <th>Rata curenta</th>
        <th>Nr rate trecute</th>
        <th>Numar rate de achitat</th>
        <th>Numar rate achitate</th>
        <th>Numar incasari</th>
        <th>Data ultimei incasari</th>
        <th>Anulat</th>
        <th>Predat</th>
        <th>Data predarii</th>
        <th>Agent vanzari<th>
    </tr>
    </thead>
    <tbody>
    @foreach ($scadentarVar as $datascadenta)
    @foreach ($datascadenta as $contract)
      <tr>
        <td>{{$i++}}</td>
        <td>{{dateFormatAfisare($contract["N2"])}}</td>
        <td>{{$contract["N3"]}}</td>
        <td>{{$contract["N4"]}}</td>
        <td>{{$contract["N5"]}}</td>
        <td>{{number_format($contract["N6"],2)}}</td>
        <td>{{$contract["N7"]}}</td>
        <td>{{$contract["N8"]}}</td>
        <td>{{$contract["N9"]}}</td>
        <td>{{$contract["N10"]}}</td>
        <td>{{$contract["N11"]}}</td>
        <td>{{$contract["N12"]}}</td>
        <td>{{$contract["N13"]}}</td>
        <td>{{$contract["N14"]}}</td>
        <td>{{$contract["N15"]}}</td>
        <td>{{$contract["N16"]}}</td>
        <td>{{$contract["N17"]}}</td>
        <td>{{$contract["N18"]}}</td>
        <td>{{$contract["N19"]}}</td>
        <td>{{$contract["N20"]}}</td>
        <td>{{$contract["N21"]}}</td>
        <td>{{$contract["N22"]}}</td>
        <td>{{$contract["N23"]}}</td>
        <td>{{$contract["N24"]}}</td>
        <td>{{$contract["N25"]}}</td>
        <td>{{$contract["N26"]}}</td>
    </tr>
    @endforeach
    <!--   <tr>
        <td colspan="5"><strong>{{"TOTAL ".dateFormatAfisare($datascadenta[0]["N2"])}}</strong></td>
        <td>{{number_format($datascadenta->sum("N6"),2)}}</td>
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
        
    </tr> -->
    @endforeach
    </tbody>
</table>
