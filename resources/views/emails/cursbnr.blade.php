@component('mail::message',['user'=>$user])
  <p>
    Curs BNR pentru  {{ dateFormatAfisare($curs_EUR->data) }}   
    <br>
    <br>
    1 EUR =  {{ $curs_EUR->curs }}  Lei
    <br> <br>
    1 USD =  {{ $curs_USD->curs }}  Lei	 
  </p>
 @endcomponent