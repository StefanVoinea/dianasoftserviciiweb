

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
         Conditii de eligibilitate
     </td>
     
 </tr>
 <tr>
     <td width="5">
     </td>   
     <td width="5" style="border: 1px solid black;background-color:#DCDCDC;font-weight: bold;">
         Nr crt
     </td>
    
     <td width="70" style="border: 1px solid black;background-color:#DCDCDC;font-weight: bold;">
         Conditie
     </td>
     <td width="20" style="border: 1px solid black;background-color:#DCDCDC;font-weight: bold;">
         Indeplinire
     </td>
     
     
     
 </tr>
 @foreach($solicitare->conditiideeligibilitatesolicitare as $conditie)
   <tr> 
   <td width="5">
     </td>   
     <td width="5" style="border: 1px solid black;">
         {{$i++}}
     </td>
     <td width="70" style="border: 1px solid black;">
         {{$conditie->conditie}}
     </td>
     <td width="20" style="border: 1px solid black;">
         {{$conditie->indeplinire}}
     </td>
   </tr>
 @endforeach
 
 
</table>