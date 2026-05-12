

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
     <td width="10" style="font-weight: bold;">
         
          {{"Data verificare: ".dateFormatAfisare($solicitare->controlintern_dataverificare)}}
     </td>
     
   </tr>
   
 <tr>

     <td width="5">
         
     </td>
     <td width="10" style="font-weight: bold;">
        
          {{" Verificat de: ".$solicitare->controlintern_verificatde}}
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
     <td width="95" colspan="2" style="border: 1px solid black;font-weight: bold;background-color:#ffff00">
         Control intern
     </td>
     
 </tr>
 <tr>
     <td width="5">
     </td>   
    
     <td width="70" style="border: 1px solid black;background-color:#DCDCDC;font-weight: bold;">
         Verificare
     </td>
     <td width="25" style="border: 1px solid black;background-color:#DCDCDC;font-weight: bold;">
         Observatii
     </td>
     
     
     
 </tr>
 @foreach($solicitare->controlinternsolicitare as $control)
   <tr> 
   <td width="5">
     </td>   
     <td width="70" style="border: 1px solid black;">
         {{$control->verificare}}
     </td>
     <td width="25" style="border: 1px solid black;">
         {{$control->observatii}}
     </td>
     
   </tr>
 @endforeach
 
 
</table>