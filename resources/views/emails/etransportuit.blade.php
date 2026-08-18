@component('mail::message', ['mesaj' => null, 'user' => null])
<img src="{{ asset('img/logo/logo-2x.png') }}" alt="DianaSoft → Dispecer e-Transport" width="322" height="36" style="margin-bottom: 10px;">

# Cod UIT pentru transport

@component('mail::panel')
<span style="font-size: 26px; font-weight: bold; letter-spacing: 2px;">{{ $uit }}</span>
@endcomponent

@if ($vehicul)
**Vehicul:** {{ $vehicul }}
@endif

@if ($dataTransport)
**Data transportului:** {{ $dataTransport }}
@endif

@if ($operatiune)
**Operațiune:** {{ $operatiune }}
@endif

@if ($partener)
**Partener:** {{ $partener }}
@endif

@if ($transportator)
**Transportator:** {{ $transportator }}
@endif

@if ($cif)
**Declarant:** {{ $cif }}
@endif

Transportul cuprinde {{ $linii }} {{ $linii == 1 ? 'fel de marfă' : 'feluri de marfă' }},
declarate în RO e-Transport. Codul UIT însoțește transportul pe tot parcursul
rutier și se arată la control.
@endcomponent
