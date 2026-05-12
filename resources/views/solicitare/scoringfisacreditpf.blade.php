<table class="table" style="border-collapse: collapse; " width="100">

<tr>
    <td width="5">
         
     </td>
    <td width="30" style="font-size:20px;font-weight: bold;text-align:left">
      Fisa de evaluare
    </td>
     <td width="30"  >
        
     </td>
     <td width="10">
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
     <td width="10">
     </td> 
     
  
</tr>
 <tr>

     <td width="5">
         
     </td>
     <td width="30" style="border: 1px solid black;font-weight: bold;">
         Nume si prenume solicitant 
     </td>
     <td width="30"  style="border: 1px solid black;background-color:#DCDCDC;font-weight: bold;">
         {{ $solicitare->nume  }}
     </td>
     <td width="10">
     </td> 
     <td width="10">
     </td> 
     
   
 </tr>
 <tr>
     <td width="5">
         
     </td> 
     
     <td width="30" style="border: 1px solid black;font-weight: bold;">
         CNP
     </td>
     <td width="30" style="text-align:left; border: 1px solid black;background-color:#DCDCDC;font-weight: bold;">
          {{$solicitare->cnp." ."}}  
     </td>
     <td width="10">
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
     <td width="10">
     </td>
  
   </tr>
   


 
 
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
 @foreach(collect($scoring)->groupBy("capitol") as $caracteristica)
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
         {{collect($scoring)->sum("scor")}}
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

