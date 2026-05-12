<table class="table" style="border-collapse: collapse; " width="100">

<tr>
    <td width="5">
         
     </td>
    <td width="30" style="font-size:20px;font-weight: bold;text-align:left">
      Fisa de evaluare {{$anN0}}
    </td>
     <td width="30"  >
        
     </td>
     <td width="10">
     </td> 
  
     
   
</tr>
<tr>
    <td width="5">
         
     </td>
    <td width="30" >
     
    </td>
     <td width="30"  >
        
     </td>
     <td width="10">
     </td> 
  
     
  
</tr>
 <tr>

     <td width="5">
         
     </td>
     <td width="30" style="border: 1px solid black;font-weight: bold;">
         Denumire persoana juridica: 
     </td>
     <td width="30"  style="border: 1px solid black;background-color:#DCDCDC;font-weight: bold;">
         {{ $solicitare->nume  }}
     </td>
     <td width="10">
     </td> 
    
     
   
 </tr>
 <tr>
     <td width="5">
         
     </td> 
     
     <td width="30" style="border: 1px solid black;font-weight: bold;">
         Cod fiscal:
     </td>
     <td width="30" style="text-align:left; border: 1px solid black;background-color:#DCDCDC;font-weight: bold;">
          {{$solicitare->cnp." ."}}  
     </td>
     <td width="10">
     </td> 

   
 </tr>
 
<tr>
     <td width="5">
         
     </td> 
     
     <td width="30" style="border: 1px solid black;font-weight: bold;">
         Perioada analizata:
     </td>
     <td width="30" style="text-align:left; border: 1px solid black;background-color:#DCDCDC;font-weight: bold;">
          {{$anN0}}  
     </td>
     <td width="10">
     </td> 
   
   
 </tr>
 <tr>
     <td width="5">
         
     </td>
     <td width="30" style="border: 1px solid black;font-weight: bold;">
         Data 
     </td>
     <td width="30" style="border: 1px solid black;background-color:#DCDCDC;font-weight: bold;">
          {{dateFormatAfisare($solicitare->data)}}
     </td>
     <td width="10">
     </td> 
   
 
 </tr>
 <tr>
     <td width="5">
         
     </td>
     <td width="30" style="border: 1px solid black;font-weight: bold;">
         Analist credite
     </td>
     <td width="30" style="border: 1px solid black;background-color:#DCDCDC;font-weight: bold;">
          {{$solicitare->consilier_credite}}
     </td>
     <td width="10">
     </td> 
   
  
   </tr>
   


 
    </table>

<br><br>

<table class="table" style="border-collapse: collapse; " width="100">
  
<tr>
    
     <td width="5"  style="border: 1px solid black;text-align: center;background-color:#DCDCDC;font-weight: bold;">
         Nr.crt.
     </td>
     <td width="30" style="border: 1px solid black;text-align: center;background-color:#DCDCDC;font-weight: bold;">
         Criteriu
     </td>
     <td width="30" style="border: 1px solid black;text-align: center;background-color:#DCDCDC;font-weight: bold;">
         {{$anN0}}
     </td>
  
 </tr>
@foreach(collect($datefinanciareN0) as $caracteristica)
 <tr>
    
      <td width="5">
        {{$i++}}
     </td>   
     <td width="30"  style="border: 1px solid black;">
         {{$caracteristica->val_den_indicator}}
     </td>
     <td width="30"  style="text-align:right;border: 1px solid black;">
         {{$caracteristica->val_indicator}}
     </td>
    
 </tr>
@endforeach

</table>

<table class="table" style="border-collapse: collapse; " width="100">
 <tr>
     <td width="5">
     </td>   
     <td width="30"  style="border: 1px solid black;background-color:#DCDCDC;font-weight: bold;">
         Caracteristica
     </td>
     <td width="30" style="border: 1px solid black;background-color:#DCDCDC;font-weight: bold;">
         Valoare
     </td>
     <td width="10"  style="border: 1px solid black;background-color:#DCDCDC;font-weight: bold;">
         Punctaj
     </td>
   
     <td width="10"  >
     </td>
 </tr>
 @foreach(collect($scoringN0)->groupBy("capitol") as $caracteristica)
  <tr>
     <td width="5">
     </td>   
     <td width="80" colspan="3" style="border: 1px solid black;font-weight: bold;background-color:#ffff00">
         {{$caracteristica[0]->capitol}}
     </td>
   
 </tr>
 @foreach($caracteristica as $scor)
   @if($scor->caracteristica!=$scor->capitol)
   <tr>
     <td width="5">
     </td>   
     <td width="30"  style="border: 1px solid black;">
         {{$scor->caracteristica}}
     </td>
     <td width="30"  style="text-align:left;border: 1px solid black;">
         {{$scor->valoare}}
     </td>
     <td width="10"   style="border: 1px solid black;">
         {{$scor->punctaj}}
     </td>
    
     <td width="10"  >
     </td>   
 </tr>
 @endif  
 @endforeach
 <tr>
   <td width="5" >
         
     </td>
     <td width="30">
     </td> 
     <td width="30">
     </td>
     <td width="10">
     </td> 
     <td width="10">
     </td>
   
     
 </tr>
 @endforeach
 <tr>
     <td width="5">
     </td>   
     <td width="70" colspan="2" style="border: 1px solid black;font-weight: bold;background-color:#ffff00">
         TOTAL :
     </td>
     <td width="10"  style="border: 1px solid black;font-weight: bold;background-color:#ffff00">
         {{collect($scoringN0)->sum("punctaj")}}
     </td>
    
 </tr>

  <tr>
   <td width="5">
         
     </td>
     <td width="30" >
       
     </td>
     <td width="30" >
       
     </td>
     <td width="10" >
       
     </td>
     <td width="10" >
       
     </td>
   
    
 </tr>
  <tr>
   <td width="5">
         
     </td>
     <td width="60"  style="border: 1px solid black;font-weight: bold;background-color:#ffff00">
         
         {{$solicitare->clasa_de_risc_evaluare}}
     </td>
     
  
 </tr>
  <tr>
   <td width="5">
         
     </td>

    <td width="60"  >
    
     </tr>
 <tr>

   <td width="5">
         
     </td>

     <td width="60"   style="border: 1px solid black;font-weight: bold;background-color:#ffff00">
         {{$solicitare->rezultat_evaluare}}
     </td>
   
 </tr>
 
</table>

