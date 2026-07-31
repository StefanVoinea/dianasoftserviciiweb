@component('mail::message', ['mesaj' => null, 'user' => null])
# {{ $titlu }}

@if ($destinatar)
Bună ziua, {{ $destinatar }},
@endif

@if ($importanta === 'urgenta')
**Această înștiințare este urgentă.**
@elseif ($importanta === 'avertizare')
**Vă rugăm să citiți cu atenție.**
@endif

{!! nl2br(e($mesaj)) !!}

Aceeași înștiințare vă așteaptă și în aplicație, pe pagina principală.
@endcomponent
