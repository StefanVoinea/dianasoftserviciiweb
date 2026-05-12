

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
     <td width="95" colspan="4" style="border: 1px solid black;font-weight: bold;background-color:#ffff00">
         Conditii de aprobare
     </td>
     
 </tr>
 <tr>
     <td width="5">
     </td>   
     <td width="15" style="border: 1px solid black;background-color:#DCDCDC;font-weight: bold;">
        Tip conditie
     </td>
    
     <td width="15" style="border: 1px solid black;background-color:#DCDCDC;font-weight: bold;">
         Data indeplinirii
     </td>
     <td width="15" style="border: 1px solid black;background-color:#DCDCDC;font-weight: bold;">
         Responsabil
     </td>
     <td width="50" style="border: 1px solid black;background-color:#DCDCDC;font-weight: bold;">
         Descriere
     </td>
     
     
 </tr>
 @foreach($solicitare->conditiiaprobaresolicitare as $conditie)
   <tr> 
   <td width="5">
     </td>   
     <td width="15" style="border: 1px solid black;">
         {{$conditie->tip_conditie}}
     </td>
    
     <td width="15" style="border: 1px solid black;">
         {{dateFormatAfisare($conditie->data_indeplinirii)}}
     </td>
     <td width="15" style="border: 1px solid black;">
         {{$conditie->responsabil}}
     </td>
      <td width="50" style="border: 1px solid black;">
         {{$conditie->descriere}}
     </td>
   </tr>
 @endforeach
 
 
</table>