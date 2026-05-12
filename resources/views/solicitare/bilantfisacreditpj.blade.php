

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
     <td width="55" colspan="4" style="border: 1px solid black;background-color:#DCDCDC;font-weight: bold;">
          {{$solicitare->consilier_credite}}
     </td>
   </tr>
   
 <tr>

     <td width="5">
         
     </td>
     <td width="55" style="border: 1px solid black;font-weight: bold;">
         Analist Credit
     </td>
     <td width="55" colspan="4" style="border: 1px solid black;background-color:#DCDCDC;font-weight: bold;">
          {{$solicitare->analist_credite}}
     </td>
  
     
     
 </tr>
 <tr>

     <td width="5">
         
     </td>
     <td width="55" style="border: 1px solid black;font-weight: bold;">
         Juridic 
     </td>
     <td width="55" colspan="4" style="border: 1px solid black;background-color:#DCDCDC;font-weight: bold;">
         {{ "SCA Condulimazi & Asociatii"  }}
     </td>
    
 </tr>
 <tr>

     <td width="5">
         
     </td>
     <td width="55" style="border: 1px solid black;font-weight: bold;">
         Data 
     </td>
     <td width="55" colspan="4" style="border: 1px solid black;background-color:#DCDCDC;font-weight: bold;">
          {{dateFormatAfisare($solicitare->data)}}
     </td>
     
 </tr>
 <tr>
     <td width="5">
         
     </td> 
     
     <td width="55" style="border: 1px solid black;font-weight: bold;">
         Valuta creditului
     </td>
     <td width="55" colspan="4" style="border: 1px solid black;background-color:#DCDCDC;font-weight: bold;">
          {{$solicitare->tip_valuta}}  
     </td>
 </tr>
 <tr>   
    <td width="5">
         
     </td> 
        <td width="55" style="border: 1px solid black;font-weight: bold;">
         Sursa de finantare
     </td>
     <td width="55" colspan="4" style="border: 1px solid black;background-color:#DCDCDC;font-weight: bold;">
          Fortuna Capital IFN SA 
     </td>
 </tr>
 <tr>
   <td width="5">
         
     </td>
     <td width="55" >
       
     </td>
     <td width="55">
        
     </td>
    
 </tr>
  <tr>
   <td width="5">
         
     </td>
     <td width="55" >
       
     </td>
     <td width="55" colspan="4">
        
     </td>
    
 </tr>
 
         <tr>
           <td width="5">
         
             </td>
             <td width="55" style="border: 1px solid black;background-color:#DCDCDC;font-weight: bold;">
               BILANT CONTABIL
             </td>
             <td width="10" style="border: 1px solid black;background-color:#DCDCDC;font-weight: bold;">
                
             </td>  
             <td width="15" style="border: 1px solid black;background-color:#DCDCDC;font-weight: bold;">
                {{dateFormatAfisare($data1Analizata)}}
             </td>  
             <td width="15" style="border: 1px solid black;background-color:#DCDCDC;font-weight: bold;">
                {{dateFormatAfisare($data2Analizata)}}
             </td>  
             <td width="15" style="border: 1px solid black;background-color:#DCDCDC;font-weight: bold;">
                {{dateFormatAfisare($dataBalanta)}}
             </td>  
             
         </tr>
       
         @foreach($bilant as $rand)
         <tr>
         <td width="5">
         
             </td>
             <td width="55" style="border: 1px solid black;">
               {{$rand->denumire}}
             </td>
             <td width="10" style="border: 1px solid black;">
                {{$rand->nr_rand?substr($rand->nr_rand,5):""}}
             </td>   
             <td width="15" style="border: 1px solid black;">
                {{$rand->suma_col1}}
             </td>  
             <td width="15" style="border: 1px solid black;">
                {{$rand->suma_col2}}
             </td>  
             <td width="15" style="border: 1px solid black;">
                {{$rand->suma_col3}}
             </td>  
         </tr>
 @endforeach
</table>

