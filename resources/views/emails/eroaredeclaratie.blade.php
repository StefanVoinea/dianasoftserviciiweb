@component('mail::message', ['mesaj' => null, 'user' => null])
<img src="{{ asset('img/logo/logo-2x.png') }}" alt="DianaSoft → SPV Curier" width="322" height="36" style="margin-bottom: 10px;">

# Declarație neprelucrată

O declarație pusă în dosarul urmărit nu a putut fi dusă până la capăt.

**Fișier:** {{ $fisier }}

@if ($cui)
**Firma:** {{ $cui }}
@endif

@if ($certificat)
**Certificat:** {{ $certificat }}
@endif

**Motiv:** {{ $motiv }}

Fișierul a fost mutat în subdosarul **erori** din dosarul urmărit — nu s-a pierdut.
Detaliile complete se văd în aplicație, la **SPV Curier → Declarații fiscale**,
unde apăsând „Ce înseamnă eroarea" primiți explicația pe înțelesul tuturor.
@endcomponent
