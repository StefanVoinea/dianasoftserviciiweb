

<table class="table" style="border-collapse: collapse; " width="100">
<tr>
    <td align="left"  width="5"></td>
</tr>
 <tr>

     <td align="left"  width="5">
         
     </td>
     <td align="left"  width="10" style="font-weight: bold;">
          
     
         {{ "Nume si prenume solicitant :".  $solicitare->nume  }}
     </td>
    
 </tr>
 <tr>
     <td align="left"  width="5">
         
     </td> 
     
     <td align="left"  width="10" style="font-weight: bold;">
         
          {{"CNP/Cod fiscal: ". $solicitare->cnp}}  
     </td>
      
 </tr>
 <tr>

     <td align="left"  width="5">
         
     </td>
     <td align="left"  width="10" style="font-weight: bold;">
         
          {{"Data : ".dateFormatAfisare($solicitare->data)}}
     </td>
      
     
 </tr>
 <tr>
     <td align="left"  width="5">
         
     </td>
     <td align="left"  width="10" style="font-weight: bold;">
         
          {{"Consilier credit retail: ".$solicitare->consilier_credite}}
     </td>
     
   </tr>
   
 <tr>

     <td align="left"  width="5">
         
     </td>
     <td align="left"  width="10" style="font-weight: bold;">
        
          {{" Analist Credit: ".$solicitare->analist_credite}}
     </td>
   
     
     
 </tr>

 
 
 <tr>
    <td align="left"  width="5">
         
     </td>
    
     
 </tr>
  <tr>
   <td align="left"  width="5">
         
     </td>
  
    
 </tr>
 
 
 @foreach($solicitare->garantiisolicitare as $garantie)
 <tr>
     <td align="left"  width="5">
     </td>   
     <td align="left"  width="95" colspan="2" style="border: 1px solid black;font-weight: bold;background-color:#ffff00">
         Garantie
     </td>
     
 </tr>
    <tr>
     <td align="left"  width="5">
         
     </td>
         <td align="left"  width="30" style="border: 1px solid black;font-weight: bold;">
            {{"Tip garantie: "}}
        </td>
        <td align="left"  width="65" style="border: 1px solid black">    
            {{$garantie->tip_garantie}}
         </td>
     </tr>
     <tr>
        <td align="left"  width="5">
         
     </td>
     
         <td align="left"  width="30" style="border: 1px solid black;font-weight: bold;">
            {{"Grad ipoteca: "}}
        </td>
        <td align="left"  width="65" style="border: 1px solid black;">    
            {{$garantie->grad_ipoteca}}
         </td>
    </tr>
     <tr>
        <td align="left"  width="5">
         
     </td>
     
         <td align="left"  width="30" style="border: 1px solid black;font-weight: bold;">
            {{"Tip: "}}
        </td>
        <td align="left"  width="65" style="border: 1px solid black;">    
            {{$garantie->tip_constructiv}}
         </td>
         </tr>
     <tr>
        <td align="left"  width="5">
         
     </td>
     
        <td align="left"  width="30" style="border: 1px solid black;font-weight: bold;">
            {{"Proprietar: "}}
        </td>
        <td align="left"  width="65" style="border: 1px solid black;">    
            {{$garantie->proprietar}}
        </td>
        </tr>
     <tr>                     
   <td align="left"  width="5">
         
     </td>
     
    <td align="left"  width="30" style="border: 1px solid black;font-weight: bold;">
        {{"Locuinta: "}}
    </td>
    <td align="left"  width="65" style="border: 1px solid black;">    
        {{$garantie->locuinta?"DA":"NU"}}
    </td>  
    </tr>
     <tr>
        <td align="left"  width="5">
         
     </td>
     
    <td align="left"  width="30" style="border: 1px solid black;font-weight: bold;">
        {{"Suprafata: "}}
    </td>
    <td align="left"  width="65" style="border: 1px solid black;">    
        {{$garantie->suprafata}}
    </td>
    </tr>
     <tr>
      <td align="left"  width="5">
         
     </td>
                           
    <td align="left"  width="30" style="border: 1px solid black;font-weight: bold;">
        {{"Nr camere: "}}
    </td>
    <td align="left"  width="65" style="border: 1px solid black;">    
        {{$garantie->nr_camere}}
    </td>
      </tr>
     <tr>   
     <td align="left"  width="5">
         
     </td>
                       
    <td align="left"  width="30" style="border: 1px solid black;font-weight: bold;">
        {{"Adresa: "}}
    </td>
    <td align="left"  width="65" style="border: 1px solid black;">    
        {{$garantie->adresa}}
    </td>
    </tr>
     <tr>
        <td align="left"  width="5">
         
     </td>
     
    <td align="left"  width="30" style="border: 1px solid black;font-weight: bold;">
        {{"Localitate: "}}
    </td>
    <td align="left"  width="65" style="border: 1px solid black;">    
        {{$garantie->localitate}}
    </td>
    </tr>
     <tr>
        <td align="left"  width="5">
         
     </td>
     
    <td align="left"  width="30" style="border: 1px solid black;font-weight: bold;">
        {{"Judet: "}}
    </td>
    <td align="left"  width="65" style="border: 1px solid black;">    
        {{$garantie->judet}}
    </td>
    </tr>
     <tr>
   <td align="left"  width="5">
         
     </td>
     
    <td align="left"  width="30" style="border: 1px solid black;font-weight: bold;">
    {{"Evaluator: "}}
</td>
<td align="left"  width="65" style="border: 1px solid black;">    
    {{$garantie->evaluator}}
    </td>
     </tr>
     <tr>  
     <td align="left"  width="5">
         
     </td>
                          
    <td align="left"  width="30" style="border: 1px solid black;font-weight: bold;">
        {{"Nr raport: "}}
    </td>
    <td align="left"  width="65" style="border: 1px solid black;">    
        {{$garantie->nr_raport}}
    </td>
     </tr>
     <tr>  
     <td align="left"  width="5">
         
     </td>
                          
    <td align="left"  width="30" style="border: 1px solid black;font-weight: bold;">
        {{"Data raport: "}}
    </td>
    <td align="left"  width="65" style="border: 1px solid black;">    
        {{dateFormatAfisare($garantie->data_raport)}}
    </td>
    </tr>
     <tr>
        <td align="left"  width="5">
         
     </td>
     
    <td align="left"  width="30" style="border: 1px solid black;font-weight: bold;">
        {{"Tip valuta: "}}
    </td>
    <td align="left"  width="65" style="border: 1px solid black;">    
        {{$garantie->tip_valuta}}
    </td>
    </tr>
     <tr>
        <td align="left"  width="5">
         
     </td>
     
    <td align="left"  width="30" style="border: 1px solid black;font-weight: bold;">
        {{"Valoare de piata(Evaluator): "}}
    </td>
    <td align="left"  width="65" style="border: 1px solid black;">    
        {{$garantie->valoare_de_piata}}
    </td>
    </tr>
     <tr>
        <td align="left"  width="5">
         
     </td>
     
    <td align="left"  width="30" style="border: 1px solid black;font-weight: bold;">
        {{"Valoare de piata(Membru CA): "}}
    </td>
    <td align="left"  width="65" style="border: 1px solid black;">    
        {{$garantie->valoare_de_piata_membru_ca}}
    </td>
    </tr>
     <tr>
        <td align="left"  width="5">
         
     </td>
     
    <td align="left"  width="30" style="border: 1px solid black;font-weight: bold;">
        {{"Valoare in garantie: "}}
    </td>
    <td align="left"  width="65" style="border: 1px solid black;">    
        {{$garantie->valoare_in_garantie}}
    </td>
    </tr>
    <tr>
        <td align="left"  width="5">
         
     </td>
     
    <td align="left"  width="30" style="border: 1px solid black;font-weight: bold;">
        {{"Nr cadastral: "}}
    </td>
    <td align="left"  width="65" style="border: 1px solid black;">    
        {{$garantie->nr_cadastral}}
    </td>
    </tr>
     <tr>
      <td align="left"  width="5">
         
     </td>
                           
    <td align="left"  width="30" style="border: 1px solid black;font-weight: bold;">
        {{"Carte funciara: "}}
    </td>
    <td align="left"  width="65" style="border: 1px solid black;">    
        {{$garantie->carte_funciara}}
    </td>
      </tr>
     <tr>    
     <td align="left"  width="5">
         
     </td>
                       
    <td align="left"  width="30" style="border: 1px solid black;font-weight: bold;">
        {{"Data intabulare: "}}
    </td>
    <td align="left"  width="65" style="border: 1px solid black;">    
        {{dateFormatAfisare($garantie->data_intabulare)}}
    </td>
      </tr>
     <tr>  
     <td align="left"  width="5">
         
     </td>
                         
    <td align="left"  width="30" style="border: 1px solid black;font-weight: bold;">
        {{"Data extras: "}}
    </td>
    <td align="left"  width="65" style="border: 1px solid black;">    
        {{dateFormatAfisare($garantie->data_extras)}}
    </td>
    </tr>
     <tr>
        <td align="left"  width="5">
         
     </td>
     
    <td align="left"  width="30" style="border: 1px solid black;font-weight: bold;">
        {{"Descriere: "}}
    </td>
    <td align="left"  width="65" style="border: 1px solid black;">    
        {{$garantie->descriere}}
    </td>
                            
                    
    </tr>
    <tr>
        <td align="left" >
            
        </td>
    </tr>
 @endforeach
 
 
</table>