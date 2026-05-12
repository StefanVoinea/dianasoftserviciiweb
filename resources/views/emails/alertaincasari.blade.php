@component('mail::panel',["incasari"=>$incasari])
<hr>
 {{'Suma incasata : '.number_format($incasari->sum("echivalent_eur"),2).' EUR' }}
 <hr>
 
  @foreach ($incasari as $incasare)
    {{$incasare->agentia .': '.number_format($incasare->echivalent_eur,2)." EUR"}} 
  @endforeach
 
<hr>
 @endcomponent