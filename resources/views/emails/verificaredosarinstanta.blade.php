@component('mail::message',['mesaj'=>$mesaj,'user'=>$user])
{!!nl2br($mesaj)!!}
@endcomponent