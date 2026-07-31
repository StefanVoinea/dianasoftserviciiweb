@component('mail::message', ['mesaj' => null, 'user' => null])
# Autentificare respinsă

Cineva a încercat să intre în aplicație de la o adresă care nu este trecută pe
contul respectiv.

**Cont:** {{ $email }}@if ($nume) ({{ $nume }})@endif

**Adresa de la care s-a încercat:** {{ $ip ?: 'necunoscută' }}

**Adrese permise pe cont:** {{ $permise ?: '—' }}

**Când:** {{ $cand }}

@if ($agent)
**Program:** {{ $agent }}
@endif

Dacă este chiar omul dumneavoastră și adresa lui s-a schimbat, adăugați-o în
fila **SPV → Utilizatori**, la contul lui.
@endcomponent
