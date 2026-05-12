

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
     <td width="95" colspan="3" style="border: 1px solid black;font-weight: bold;background-color:#ffff00">
         Grup de participanti
     </td>
     
 </tr>
 <tr>
     <td width="5">
     </td>   
    
     <td width="10" style="border: 1px solid black;background-color:#DCDCDC;font-weight: bold;">
         CNP/Cod fiscal
     </td>
     <td width="20" style="border: 1px solid black;background-color:#DCDCDC;font-weight: bold;">
         Nume
     </td>
     <td width="65" style="border: 1px solid black;background-color:#DCDCDC;font-weight: bold;">
         Calitate
     </td>
     
     
 </tr>
 @foreach($solicitare->grupsolicitare as $grup)
   <tr> 
   <td width="5">
     </td>   
     <td width="10" style="border: 1px solid black;">
         {{$grup->cnp." ."}}
     </td>
     <td width="20" style="border: 1px solid black;">
         {{$grup->nume}}
     </td>
     <td width="65" style="border: 1px solid black;">
         {{$grup->calitate}}
     </td>
   
   </tr>
 @endforeach
 
 
</table>