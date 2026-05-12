

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
              
             <td width="30" style="border: 1px solid black;background-color:#DCDCDC;font-weight: bold;">
               
             </td>
             <td width="15" style="border: 1px solid black;background-color:#DCDCDC;font-weight: bold;">
         
             </td>
             <td width="10" style="border: 1px solid black;background-color:#DCDCDC;font-weight: bold;">
                {{dateFormatAfisare($data1Analizata)}}
             </td>  
             <td width="5" style="border: 1px solid black;background-color:#DCDCDC;font-weight: bold;">
                
             </td>  
             <td width="15" style="border: 1px solid black;background-color:#DCDCDC;font-weight: bold;">
             </td>   
             <td width="10" style="border: 1px solid black;background-color:#DCDCDC;font-weight: bold;">
                {{dateFormatAfisare($data2Analizata)}}
             </td>  
             <td width="5" style="border: 1px solid black;background-color:#DCDCDC;font-weight: bold;">
                
             </td>  
             <td width="15" style="border: 1px solid black;background-color:#DCDCDC;font-weight: bold;">
             </td> 
             <td width="15" style="border: 1px solid black;background-color:#DCDCDC;font-weight: bold;">
                {{dateFormatAfisare($dataBalanta)}}
             </td>  
             
         </tr>
                                        
          <tr>
             <td width="5">
         
             </td>
              
             <td width="30" style="border: 1px solid black;background-color:#DCDCDC;font-weight: bold;">
                Indicator
             </td>
             <td width="15" style="border: 1px solid black;background-color:#DCDCDC;font-weight: bold;">
                 Valoare
             </td>
             <td width="10" style="border: 1px solid black;background-color:#DCDCDC;font-weight: bold;">
                Puncte
             </td>  
             <td width="5" style="border: 1px solid black;background-color:#DCDCDC;font-weight: bold;">
                
             </td>  
             <td width="15" style="border: 1px solid black;background-color:#DCDCDC;font-weight: bold;">
                Valoare
             </td>   
             <td width="10" style="border: 1px solid black;background-color:#DCDCDC;font-weight: bold;">
                Puncte
             </td>  
             <td width="5" style="border: 1px solid black;background-color:#DCDCDC;font-weight: bold;">
                
             </td>  
             <td width="15" style="border: 1px solid black;background-color:#DCDCDC;font-weight: bold;">
                Valoare
             </td> 
             <td width="15" style="border: 1px solid black;background-color:#DCDCDC;font-weight: bold;">
                Puncte
             </td>  
             
         </tr>                                
         <tr>
             <td width="5">
         
             </td>
              
             <td width="30" style="border: 1px solid black;background-color:#DCDCDC;font-weight: bold;">
                 {{"Lichiditatea  curenta"}}
             </td>
             <td width="15" style="border: 1px solid black;background-color:#DCDCDC;font-weight: bold;">
                 {{"=+'Rezumat financiar'!B44"}}
             </td>
             <td width="10" style="border: 1px solid black;background-color:#DCDCDC;font-weight: bold;">
                {{"=IF(C11<0.6;0;IF(C11<0.8;1;IF(C11<=1;2;IF(C11>1;4))))"}}
             </td>  
             <td width="5" style="border: 1px solid black;background-color:#DCDCDC;font-weight: bold;">
                
             </td>  
             <td width="15" style="border: 1px solid black;background-color:#DCDCDC;font-weight: bold;">
                {{"=+'Rezumat financiar'!D44"}}
             </td>   
             <td width="10" style="border: 1px solid black;background-color:#DCDCDC;font-weight: bold;">
                {{"=IF(F11<0.6;0;IF(F11<0.8;1;IF(F11<=1;2;IF(F11>1;4))))"}}
             </td>  
             <td width="5" style="border: 1px solid black;background-color:#DCDCDC;font-weight: bold;">
                
             </td>  
             <td width="15" style="border: 1px solid black;background-color:#DCDCDC;font-weight: bold;">
                {{"=+'Rezumat financiar'!H44"}}
             </td> 
             <td width="15" style="border: 1px solid black;background-color:#DCDCDC;font-weight: bold;">
               {{"=IF(L11<0.6;0;IF(L11<0.8;1;IF(L11<=1;2;IF(L11>1;4))))"}}
             </td>  
             
         </tr>
         <tr>
             <td width="5">
         
             </td>
              
             <td width="30" style="border: 1px solid black;background-color:#DCDCDC;font-weight: bold;">
                  {{"Grad de indatorare "}}
             </td>
             <td width="15" style="border: 1px solid black;background-color:#DCDCDC;font-weight: bold;">
                 {{"=+'Bilant'!D16"}}
             </td>
             <td width="10" style="border: 1px solid black;background-color:#DCDCDC;font-weight: bold;">
                {{"=IF(C12>0.95;0;IF(C12>0.9;1;IF(C12>=0.8;2;IF(C12<0.8;4))))"}}
             </td>  
             <td width="5" style="border: 1px solid black;background-color:#DCDCDC;font-weight: bold;">
                
             </td>  
             <td width="15" style="border: 1px solid black;background-color:#DCDCDC;font-weight: bold;">
               {{"=+'Rezumat financiar'!D45"}}
             </td>   
             <td width="10" style="border: 1px solid black;background-color:#DCDCDC;font-weight: bold;">
               {{"=IF(F12>0.95;0;IF(F12>0.9;1;IF(F12>=0.8;2;IF(F12<0.8;4))))"}}
             </td>  
             <td width="5" style="border: 1px solid black;background-color:#DCDCDC;font-weight: bold;">
                
             </td>  
             <td width="15" style="border: 1px solid black;background-color:#DCDCDC;font-weight: bold;">
                {{"=+'Rezumat financiar'!H45"}}
             </td> 
             <td width="15" style="border: 1px solid black;background-color:#DCDCDC;font-weight: bold;">
               {{"=IF(L12>0.95;0;IF(L12>0.9;1;IF(L12>=0.8;2;IF(L12<0.8;4))))"}}
             </td>  
             
         </tr>
 
</table>

