

<table class="table" style="border-collapse: collapse; " width="100">

<tr>
    <td width="5"></td>
</tr>
 <tr>

     <td width="5">
         
     </td>
     <td width="95" style="font-weight: bold;">
          
     
         {{ "Nume si prenume solicitant :".  $solicitare->nume  }}
     </td>
    
 </tr>
 <tr>
     <td width="5">
         
     </td> 
     
     <td width="95"style="font-weight: bold;">
         
          {{"CNP/Cod fiscal: ". $solicitare->cnp}}  
     </td>
      
 </tr>
 <tr>

     <td width="5">
         
     </td>
     <td width="95" style="font-weight: bold;">
         
          {{"Data : ".dateFormatAfisare($solicitare->data)}}
     </td>
      
     
 </tr>
 <tr>
     <td width="5">
         
     </td>
     <td width="95" style="font-weight: bold;">
         
          {{"Consilier credit retail: ".$solicitare->consilier_credite}}
     </td>
     
   </tr>
   
 <tr>

     <td width="5">
         
     </td>
     <td width="95" style="font-weight: bold;">
        
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
     <td width="95" style="border: 1px solid black;font-weight: bold;background-color:#ffff00">
         Conditii derogatorii
     </td>
     
 </tr>
 <tr>
     <td width="5">
     </td>   
     
     <td width="95" style="border: 1px solid black;background-color:#DCDCDC;font-weight: bold;">
         Descriere
     </td>
     
     
 </tr>
 @foreach($solicitare->conditiiderogatoriisolicitare as $conditie)
   <tr> 
   <td width="5">
     </td>   
     
      <td width="95" style="border: 1px solid black;">
         {{$conditie->descriere}}
     </td>
   </tr>
 @endforeach
 
 
</table>