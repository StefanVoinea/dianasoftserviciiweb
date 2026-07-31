@component('mail::message', ['mesaj' => null, 'user' => null])
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
