

<table class="table" style="border-collapse: collapse; " width="100">
<tr>
    <td width="5"></td>
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
    
</tr>
 <tr>
     <td width="5">
         
     </td>
     <td width="55" style="border: 1px solid black;font-weight: bold;">
         Consilier credit retail
     </td>
     <td width="40" colspan="2" style="border: 1px solid black;background-color:#DCDCDC;font-weight: bold;">
          {{$solicitare->consilier_credite}}
     </td>
   </tr>
   
 <tr>

     <td width="5">
         
     </td>
     <td width="55" style="border: 1px solid black;font-weight: bold;">
         Analist Credit
     </td>
     <td width="40" colspan="2" style="border: 1px solid black;background-color:#DCDCDC;font-weight: bold;">
          {{$solicitare->analist_credite}}
     </td>
  
     
     
 </tr>
 <tr>

     <td width="5">
         
     </td>
     <td width="55" style="border: 1px solid black;font-weight: bold;">
         Juridic 
     </td>
     <td width="40" colspan="2" style="border: 1px solid black;background-color:#DCDCDC;font-weight: bold;">
         {{ "SCA Condulimazi & Asociatii"  }}
     </td>
    
 </tr>
 <tr>

     <td width="5">
         
     </td>
     <td width="55" style="border: 1px solid black;font-weight: bold;">
         Data 
     </td>
     <td width="40" colspan="2" style="border: 1px solid black;background-color:#DCDCDC;font-weight: bold;">
          {{dateFormatAfisare($solicitare->data)}}
     </td>
     
 </tr>
 <tr>
     <td width="5">
         
     </td> 
     
     <td width="55" style="border: 1px solid black;font-weight: bold;">
         Valuta creditului
     </td>
     <td width="40" colspan="2" style="border: 1px solid black;background-color:#DCDCDC;font-weight: bold;">
          {{$solicitare->tip_valuta}}  
     </td>
 </tr>
 <tr>   
    <td width="5">
         
     </td> 
        <td width="55" style="border: 1px solid black;font-weight: bold;">
         Sursa de finantare
     </td>
     <td width="40" colspan="2" style="border: 1px solid black;background-color:#DCDCDC;font-weight: bold;">
          Fortuna Capital IFN SA 
     </td>
 </tr>
 <tr>
   <td width="5">
         
     </td>
     <td width="55" >
       
     </td>
     <td width="40">
        
     </td>
    
 </tr>
  <tr>
   <td width="5">
         
     </td>
     <td width="55" >
       
     </td>
     <td width="40" colspan="2">
        
     </td>
    
 </tr>
 
         <tr>
           <td width="5">
         
             </td>
             <td width="55" style="border: 1px solid black;background-color:#DCDCDC;font-weight: bold;">
               Data ultimei balante de verificare
             </td>
             <td width="20" style="border: 1px solid black;background-color:#DCDCDC;font-weight: bold;">
                {{dateFormatAfisare($dataBalanta)}}
             </td>  
             <td width="20" style="border: 1px solid black;background-color:#DCDCDC;font-weight: bold;">
                {{dateFormatAfisare($dataBalanta)}}
             </td>  
             
         </tr>
        <tr>
           <td width="5">
         
             </td>
             <td width="55" style="border: 1px solid black;background-color:#DCDCDC;font-weight: bold;">
               DENUMIRE CONT
             </td>
             <td width="20" style="border: 1px solid black;background-color:#DCDCDC;font-weight: bold;">
                SOLD DEBIT
             </td>  
             <td width="20" style="border: 1px solid black;background-color:#DCDCDC;font-weight: bold;">
                SOLD CREDIT
             </td>  
             
         </tr>
         @foreach($solicitare->balantasolicitare as $balanta)
         <tr>
         <td width="5">
         
             </td>
             <td width="55" style="border: 1px solid black;">
               {{$balanta->denumire}}
             </td>
             <td width="20" style="border: 1px solid black;">
                {{$balanta->sold_debit}}
             </td>  
             <td width="20" style="border: 1px solid black;">
                {{$balanta->sold_credit}}
             </td>  
             
         </tr>
 @endforeach
</table>

