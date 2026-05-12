@extends('layouts.antet')

@section ('content')


 
  @foreach($tabel->groupBy("cont") as $cont)
    @foreach($cont->groupBy("gestiune") as $gestiuneIndex => $gestiune)
      @foreach($gestiune->groupBy("loc_folosinta") as $locfolosinta)
    
     <table class="text-sm table table-condesed" border="1" style="border-collapse: collapse;" width=100%>
        <thead>
             <tr>
            
            <th  colspan="2" >
             <h4>   LISTA DE INVENTARIERE  DATA DE : {{dateFormatAfisare($data)}}
             </h4>
            </th>
            <th  colspan="8" >
                GESTIUNEA: {{$locfolosinta[0]->gestiune}} 
                LOC DE DEPOZITARE: {{$locfolosinta[0]->loc_folosinta}} 
            </th>
            <th  colspan="5"> 
                CONT: {{$locfolosinta[0]->cont}}
                STARE: {{$locfolosinta[0]->stare}} 
            </th>
        </tr>  

        <tr>
            <th align="center" rowspan="3" width="5%">Nr.crt.</th>
            <th align="center" rowspan="3" width="31%">Denumirea bunurilor inventariate</th>
            <th align="center" rowspan="3" width="10%">Codul sau numarul de inventar</th>
            <th align="center" rowspan="3" width="5%">Cont ctb.</th>
            <th align="center" colspan="4" width="16%">CANTITATI</th>
            <th align="center" rowspan="3" width="5%">Pret unitar</th>   
            <th align="center" colspan="3" width="16%">VALOAREA CONTABILA</th>    
            <th align="center" rowspan="3" width="10%">Valoarea de inventar</th>
            <th align="center" colspan="2" rowspan="2" width="10%">DEPRECIEREA</th>        
        </tr>
        <tr>
            <th align="center" colspan="2" width="10%">Stocuri</th>
            <th align="center" colspan="2" width="6%">Diferente</th>
            <th align="center" rowspan="2" width="6%">VALOAREA LEI</th>    
            <th align="center" colspan="2" width="6%">Diferente</th>    
        </tr>
        <tr>
            <th align="center" width="5%">Faptice</th>
            <th align="center" width="5%">Scriptice</th>
            <th align="center" width="3%">Plus</th>
            <th align="center" width="3%">Minus</th>
            <th align="center" width="3%">Plus</th>
            <th align="center" width="3%">Minus</th>
            <th align="center" class="text-sm" width="5%">Valoarea</th>
            <th align="center" width="4%">Motivul</th> 
        </tr>
        <tr>
            <th align="center">0</th><th align="center">1</th><th align="center">2</th><th align="center">3</th>
            <th align="center">4</th><th align="center">5</th><th align="center">6</th><th align="center">7</th>
            <th align="center">8</th><th align="center">9</th><th align="center">10</th><th align="center">11</th>
            <th align="center">12</th><th align="center">13</th><th align="center">14</th>
        </tr>
      </thead>
      <tbody>

   
        @foreach($locfolosinta as $linie)      
        <tr>
            <td align="center">{{$i++}}</td>
            <td align="left">{{$linie->denumire}}</td>
            <td align="center">{{$linie->nr_inventar}}</td>
            <td align="center">{{$linie->cont}}</td>
            <td align="center">{{$linie->cantitate}}</td>
            <td align="center">{{$linie->cantitate}}</td>
            <td align="center"></td>
            <td align="center"></td>
            <td align="right">{{$linie->pret}}</td>
            <td align="right">{{$linie->valoare_de_inventar}}</td>
            <td align="center"></td>
            <td align="center"></td>
            <td align="right">{{$linie->valoare_de_inventar}}</td>
            <td align="center"></td>
            <td align="center"></td>
        </tr>    
        @endforeach

    <tr >
        <td colspan="8">TOTAL {{$locfolosinta[0]->cont ." => GESTIUNEA ". $locfolosinta[0]->gestiune ." LOC DE DEPOZITARE ". $locfolosinta[0]->loc_folosinta}}</td>
        <td colspan="2" align="right">{{$locfolosinta->sum("valoare_de_inventar")}}</td>
        
        <td colspan="3"  align="right">{{$locfolosinta->sum("valoare_de_inventar")}}</td>
        <td colspan="2">
           
        </td>
      
        
      
    </tr>
</tbody>
</table>
    
<div class="page-break"></div>
    
        @endforeach
   
@endforeach
      <table class="text-sm table table-condesed" border="1" style="border-collapse: collapse;" width=100%>
    <tr>
            <td colspan="8">TOTAL {{$cont[0]->cont}}</td>
            <td align="right" colspan="2">{{$cont->sum("valoare_de_inventar")}}</td>
            <td align="right" colspan="3">{{$cont->sum("valoare_de_inventar")}}</td>
            <td colspan="2"></td>
        </tr>    
        </table>
        <div class="page-break"></div>
    @endforeach

   <table class="text-sm table table-condesed" border="1" style="border-collapse: collapse;" width=100%> 
      <tr>
            <td colspan="8">TOTAL GENERAL</td>
            <td align="right" colspan="2">{{$tabel->sum("valoare_de_inventar")}}</td>
            <td align="right" colspan="3">{{$tabel->sum("valoare_de_inventar")}}</td>
            <td colspan="2"></td>
        </tr>  
   
</table>

@stop
