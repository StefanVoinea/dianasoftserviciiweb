

<table class="table" style="border-collapse: collapse; " width="100">
<tr>
    <td width="5"></td>
    
</tr>
 <tr>

     <td width="5">
         
     </td>
     <td width="10" style="font-weight: bold;">
          
     
         {{ "Nume si prenume solicitant :".  $solicitare->nume  }}
     </td>
    
 </tr>
 <tr>
     <td width="5">
         
     </td> 
     
     <td width="10"style="font-weight: bold;">
         
          {{"CNP/Cod fiscal: ". $solicitare->cnp}}  
     </td>
      
 </tr>
 <tr>

     <td width="5">
         
     </td>
     <td width="10" style="font-weight: bold;">
         
          {{"Data : ".dateFormatAfisare($solicitare->data)}}
     </td>
      
     
 </tr>
 <tr>
     <td width="5">
         
     </td>
     <td width="10" style="font-weight: bold;">
         
          {{"Consilier credit retail: ".$solicitare->consilier_credite}}
     </td>
     
   </tr>
   
 <tr>

     <td width="5">
         
     </td>
     <td width="10" style="font-weight: bold;">
        
          {{" Analist Credit: ".$solicitare->analist_credite}}
     </td>
   
     
     
 </tr>

 
 
 <tr>
    <td width="5">
         
     </td>
      <td width="5">
         
     </td>
     <td style="font-size:16px;">
        <!-- Nota : Se introduc manual datele in campurile colorate cu gri , restul datelor sunt preluate -->
          ANALIZA FINANCIARA
    </td>
     
 </tr>
  <tr>
   <td width="5">
         
     </td>
  
    
 </tr>
 <tr>
     <td width="5">
     </td>   
     <td width="95" colspan="4" style="border: 1px solid black;font-weight: bold;background-color:#66ff66">
         VENITURI PARTICIPANTI
     </td>
     
 </tr>
 <tr>
     <td width="5">
     </td>   
     <td width="15" style="border: 1px solid black;background-color:#DCDCDC;font-weight: bold;">
         CNP/CUI
     </td>
     <td width="25" style="border: 1px solid black;background-color:#DCDCDC;font-weight: bold;">
         Nume
     </td>
     <td width="40" style="border: 1px solid black;background-color:#DCDCDC;font-weight: bold;">
         Tip venit
     </td>
     <td width="15" style="border: 1px solid black;background-color:#DCDCDC;font-weight: bold;">
         Venituri lunare
     </td>
 </tr>
 @foreach($solicitare->venituripartisolicitari as $venit)
   <tr>
     <td width="5">
     </td>   
     <td width="15" style="border: 1px solid black;">
         {{$venit->partisolicitare->cnp." ."}}
     </td>
     <td width="25" style="border: 1px solid black;">
         {{$venit->partisolicitare->nume}}
     </td>
     <td width="40" style="border: 1px solid black;">
         {{$venit->tip_venit_pf}}
     </td>
     <td width="15" style="border: 1px solid black;">
         {{$venit->valoare}}
     </td>
 </tr>
   
 @endforeach
 <tr>
     <td width="5">
     </td>   
     <td width="85" colspan="3" style="border: 1px solid black;font-weight: bold;background-color:#66ff66">
         TOTAL VENITURI:
     </td>
     <td width="15" style="border: 1px solid black;font-weight: bold;background-color:#66ff66">
         {{$solicitare->venituripartisolicitari->sum("valoare")}}
     </td>
 </tr>

  <tr>
   <td width="5">
         
     </td>
     <td width="40" colspan="2">
       
     </td>
     <td width="55" colspan="2">
        
     </td>
    
 </tr>
  <tr>
   <td width="5">
         
     </td>
     <td width="40" colspan="2">
       
     </td>
     <td width="55" colspan="2">
        
     </td>
    
 </tr>
  <tr>
   <td width="5">
         
     </td>
     <td width="40" colspan="2">
       
     </td>
     <td width="55" colspan="2">
        
     </td>
    
 </tr>
 <tr>
     <td width="5">
     </td>   
     <td width="95" colspan="4" style="border: 1px solid black;font-weight: bold;background-color:#ffc2b3">
         CHELTUIELI PARTICIPANTI
     </td>
     
 </tr>
 <tr>
     <td width="5">
     </td>   
     <td width="15" style="border: 1px solid black;background-color:#DCDCDC;font-weight: bold;">
         CNP/CUI
     </td>
     <td width="25" style="border: 1px solid black;background-color:#DCDCDC;font-weight: bold;">
         Nume
     </td>
     <td width="40" style="border: 1px solid black;background-color:#DCDCDC;font-weight: bold;">
         Tip cheltuiala
     </td>
     <td width="15" style="border: 1px solid black;background-color:#DCDCDC;font-weight: bold;">
         Cheltuieli lunare
     </td>
 </tr>
 @foreach($solicitare->cheltuielipartisolicitari as $cheltuiala)
   <tr>
     <td width="5">
     </td>   
     <td width="15" style="border: 1px solid black;">
         {{$cheltuiala->partisolicitare->cnp." ."}}
     </td>
     <td width="25" style="border: 1px solid black;">
         {{$cheltuiala->partisolicitare->nume}}
     </td>
     <td width="40" style="border: 1px solid black;">
         {{$cheltuiala->tip_cheltuiala_pf}}
     </td>
     <td width="15" style="border: 1px solid black;">
         {{$cheltuiala->valoare}}
     </td>
 </tr>
   
 @endforeach
 <tr>
     <td width="5">
     </td>   
     <td width="85" colspan="3" style="border: 1px solid black;font-weight: bold;background-color:#ffc2b3">
         TOTAL CHELTUIELI:
     </td>
     <td width="15" style="border: 1px solid black;font-weight: bold;background-color:#ffc2b3">
         {{$solicitare->cheltuielipartisolicitari->sum("valoare")}}
     </td>
 </tr>
 <tr>
     <td width="5">
     </td>   
     <td width="85" colspan="3" >
         
     </td>
     <td width="15" >
         
     </td>
 </tr>
 <tr>
 <td width="5">
     </td>   
     <td width="85" colspan="3" >
         
     </td>
     <td width="15" >
         
     </td>
 </tr>
 <tr>
     <td width="5">
     </td>   
     <td width="85" colspan="3" style="border: 1px solid black;font-weight: bold;">
         TOTAL DISPONIBIL RAMAS:
     </td>
     <td width="15" style="border: 1px solid black;font-weight: bold;">
         {{$solicitare->venituripartisolicitari->sum("valoare")-$solicitare->cheltuielipartisolicitari->sum("valoare")}}
     </td>
 </tr>
  <tr>
     <td width="5">
     </td>   
     <td width="85" colspan="3" style="border: 1px solid black;font-weight: bold;">
         GRAD DE INDATORARE INAINTE DE RATA ACTUALA:
     </td>
     <td width="15" style="text-align:right; border: 1px solid black;font-weight: bold;">
        @if ($solicitare->venituripartisolicitari->sum("valoare")!=0)
         {{round(100*
                $solicitare->cheltuielipartisolicitari->filter(function ($item) {
                 return false !== stripos($item->tip_cheltuiala_pf, 'rate');})
                ->sum("valoare")/$solicitare->venituripartisolicitari->sum("valoare"),2)." %"}}
       @endif         
     </td>
 </tr>
 <tr>
     <td width="5">
     </td>   
     <td width="85" colspan="3" style="border: 1px solid black;font-weight: bold;">
         RATA LUNARA:
     </td>
     <td width="15" style="border: 1px solid black;font-weight: bold;">
         {{$solicitare->rata_lunara}}
     </td>
 </tr>
 <tr>
     <td width="5">
     </td>   
     <td width="85" colspan="3" style="border: 1px solid black;font-weight: bold;background-color:#ffff00">
         GRAD DE INDATORARE DUPA RATA ACTUALA:
     </td>
     <td width="15" style="text-align:right;border: 1px solid black;font-weight: bold;background-color:#ffff00">
         
       @if ($solicitare->venituripartisolicitari->sum("valoare")!=0)
         {{round(100*($solicitare->cheltuielipartisolicitari->filter(function ($item) {
                 return false !== stripos($item->tip_cheltuiala_pf, 'rate');})
                ->sum("valoare")+$solicitare->rata_lunara)/$solicitare->venituripartisolicitari->sum("valoare"),2)." %"}}
       @endif          
     </td>
 </tr>
</table>

