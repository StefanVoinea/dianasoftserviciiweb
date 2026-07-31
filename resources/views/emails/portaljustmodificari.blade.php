@component('mail::message', ['mesaj' => null, 'user' => $user])
# Modificări la dosarele urmărite

Am găsit **{{ $total }}** {{ $total === 1 ? 'modificare' : 'modificări' }} la dosarele pe care le urmăriți în Portal Just.

@foreach ($dosare as $dosar)
**Dosar {{ $dosar['numar'] }}**
@if (!empty($dosar['institutie']))
{{ $dosar['institutie'] }}
@endif
@if (!empty($dosar['urmarit_pentru']))
*urmărit după: {{ $dosar['urmarit_pentru'] }}*
@endif

@foreach ($dosar['modificari'] as $modificare)
- {{ $modificare }}
@endforeach

@endforeach

Modificările pot fi recitite oricând în aplicație, la **Portal Just → Monitorizare**.
@endcomponent
