@component('mail::message', ['mesaj' => null, 'user' => null])
<img src="{{ asset('img/logo/logo-2x.png') }}" alt="DianaSoft → SPV Curier" width="322" height="36" style="margin-bottom: 10px;">

# Eroare la {{ $date['unde'] }}

**Când:** {{ $date['cand'] }}

@if ($date['client'])
**Client:** {{ $date['client'] }}
@endif

@if ($date['certificat'])
**Certificat:** {{ $date['certificat'] }}
@endif

@if ($date['utilizator'])
**Utilizator:** {{ $date['utilizator'] }}
@endif

**Eroarea:**

> {{ $date['mesaj'] }}

@if (!empty($date['context']))
## Ce se lucra

@foreach ($date['context'] as $cheie => $valoare)
- **{{ $cheie }}:** {{ $valoare }}
@endforeach
@endif

## Ce e de făcut

{{ $date['rezolvare'] }}

@if (!empty($date['urma']))
## De unde a pornit

```
{{ $date['urma'] }}
```
@endif

Lucrarea nu s-a oprit din pricina acestui email: aplicația și programul de la
client merg mai departe, iar eroarea e doar consemnată. Aceeași eroare nu se mai
trimite încă o dată prea curând, ca să rămână citibilă cutia poștală.
@endcomponent
