@component('mail::message',["mesaj"=>$mesaj])
  <table class="table" width=100%  >
  <tr >
   <td align ="left" width="100%">
    {!! $mesaj !!}<br><br>
  	
   </td>		
  </tr>	
 
</table>

 @endcomponent