@component('mail::panel',["context"=>$context,"mesaj"=>$mesaj,"eroaare"=>$eroare,"user"=>$user])
  <table class="table" width=100%  >
  <tr >
   <td align ="left" width="100%">
    {{'EASY CREDIT 4 ALL IFN S.A.'}}<br><br>
  	{{'Utilizator: '. $user->name." ". $user->email}}<br><br>
    {{'Context: '. $context}}<br><br>
    {{'Mesaj eroare: '. $mesaj}}<br><br>
    {{'Eroare: '. $eroare}}<br><br>
  		

   </td>		
  </tr>	
 <tr >
 	<td align ="left" width="100%">

 </td>
</tr>
</table>

 @endcomponent