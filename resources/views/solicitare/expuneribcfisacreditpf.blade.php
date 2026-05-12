

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
    
     
 </tr>
  <tr>
   <td width="5">
         
     </td>
  
    
 </tr>
 <tr>
     <td width="5">
     </td>   
     <td width="95" colspan="13" style="border: 1px solid black;font-weight: bold;background-color:#ffff00">
         Expuneri debitor
     </td>
     
 </tr>
 <tr>
     <td width="5">
     </td>   
    
     <td width="10" style="border: 1px solid black;background-color:#DCDCDC;font-weight: bold;">
         Tip credit
     </td>
     <td width="10" style="border: 1px solid black;background-color:#DCDCDC;font-weight: bold;">
         Valuta
     </td>
     <td width="10" style="border: 1px solid black;background-color:#DCDCDC;font-weight: bold;">
         Creditor
     </td>
     <td width="10" style="border: 1px solid black;background-color:#DCDCDC;font-weight: bold;">
         Suma acordata
     </td>
     <td width="10" style="border: 1px solid black;background-color:#DCDCDC;font-weight: bold;">
         Suma datorata
     </td>
      <td width="10" style="border: 1px solid black;background-color:#DCDCDC;font-weight: bold;">
         Suma restanta
     </td>
     <td width="10" style="border: 1px solid black;background-color:#DCDCDC;font-weight: bold;">
         Data acordarii
     </td>
     <td width="10" style="border: 1px solid black;background-color:#DCDCDC;font-weight: bold;">
         Scadenta
     </td>
     <td width="15" style="border: 1px solid black;background-color:#DCDCDC;font-weight: bold;">
         Tipul garantiilor
     </td>
     <td width="10" style="border: 1px solid black;background-color:#DCDCDC;font-weight: bold;">
         Perioada ramasa (ani)
     </td>
     <td width="10" style="border: 1px solid black;background-color:#DCDCDC;font-weight: bold;">
         Serviciul datoriei
     </td>
      <td width="10" style="border: 1px solid black;background-color:#DCDCDC;font-weight: bold;">
         Valoare rata lunara
     </td>
     <td width="10" style="border: 1px solid black;background-color:#DCDCDC;font-weight: bold;">
         Valoare rata anuala
     </td>
 </tr>
 @foreach($solicitare->expunerisolicitare as $expunere)
   <tr> 
   <td width="5">
     </td>   
     <td width="10" style="border: 1px solid black;">
         {{$expunere->tip_expunere}}
     </td>
     <td width="10" style="border: 1px solid black;">
         {{$expunere->tip_valuta}}
     </td>
     <td width="10" style="border: 1px solid black;">
         {{$expunere->creditor}}
     </td>
     <td width="10" style="border: 1px solid black;">
         {{$expunere->suma_acordata}}
     </td>
     <td width="10" style="border: 1px solid black;">
         {{$expunere->suma_datorata}}
     </td>
     <td width="10" style="border: 1px solid black;">
         {{$expunere->suma_restanta}}
     </td>
     <td width="10" style="border: 1px solid black;">
         {{dateFormatAfisare($expunere->data_acordarii)}}
     </td>
     <td width="10" style="border: 1px solid black;">
         {{dateFormatAfisare($expunere->scadenta)}}
     </td>
     <td width="15" style="border: 1px solid black;">
         {{$expunere->tipul_garantiilor}}
     </td>
     <td width="10" style="border: 1px solid black;">
         {{$expunere->nr_luni_ramase}}
     </td>
     <td width="10" style="border: 1px solid black;">
         {{$expunere->serviciul_datoriei}}
     </td>
     <td width="10" style="border: 1px solid black;">
         {{$expunere->valoare_rata_lunara}}
     </td>
     <td width="10" style="border: 1px solid black;">
         {{$expunere->valoare_rata_anuala}}
     </td>
   </tr>
 @endforeach
 <tr>
     <td width="5">
     </td>   
     <td width="85" colspan="10" style="border: 1px solid black;font-weight: bold;background-color:#ffc2b3">
         TOTAL :
     </td>
     <td width="10" style="border: 1px solid black;font-weight: bold;background-color:#ffc2b3">
         {{$solicitare->expunerisolicitare->sum("serviciul_datoriei")}}
     </td>
     <td width="10" style="border: 1px solid black;font-weight: bold;background-color:#ffc2b3">
         {{$solicitare->expunerisolicitare->sum("valoare_rata_lunara")}}
     </td>
     <td width="10" style="border: 1px solid black;font-weight: bold;background-color:#ffc2b3">
         {{$solicitare->expunerisolicitare->sum("valoare_rata_anuala")}}
     </td>
 </tr>

  <tr>
   <td width="5">
         
     </td>
     <td width="40" colspan="4">
       
     </td>
     <td width="55" colspan="5">
        
     </td>
    
 </tr>
 
</table>