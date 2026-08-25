@component('mail::message', ['mesaj' => null, 'user' => null])
<img src="{{ asset('img/logo/logo-2x.png') }}" alt="DianaSoft → Dispecer e-Transport" width="322" height="36" style="margin-bottom: 10px;">

# Codurile UIT pentru transport

În fișierul atașat sunt codurile UIT pentru {{ $foi }} {{ $foi == 1 ? 'destinație' : 'destinații' }},
câte o foaie pentru fiecare magazin, cu punctul de trecere a frontierei,
vehiculul și locul de descărcare.

Codurile UIT trebuie înscrise pe CMR-uri și însoțesc transportul pe tot
parcursul rutier din România.
@endcomponent
