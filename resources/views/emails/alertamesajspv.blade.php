@component('mail::message', ['mesaj' => null, 'user' => null])
<img src="{{ asset('img/logo/logo-2x.png') }}" alt="DianaSoft → SPV Curier" width="322" height="36" style="margin-bottom: 10px;">

# {{ $tip }}

A intrat în Spațiul Privat Virtual un document de tipul urmărit.

**Firma:** {{ $denumire ?: 'necunoscută' }}@if ($cif) ({{ $cif }})@endif

**Tip document:** {{ $tip }}

@if ($dataCreare)
**Data la ANAF:** {{ \Carbon\Carbon::parse($dataCreare)->format('d.m.Y H:i') }}
@endif

@if ($detalii)
**Detalii:** {{ $detalii }}
@endif

Documentul poate fi deschis în aplicație, la **SPV → Mesaje ANAF**.
@endcomponent
