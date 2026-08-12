@component('mail::message')
# {{ $titlu }}

@if ($denumire)
**{{ $denumire }}**@if ($cif) — CUI {{ $cif }}@endif
@elseif ($cif)
**CUI {{ $cif }}**
@endif

{{ $vorba }}

{{ $indemn }}

@component('mail::button', ['url' => rtrim(config('app.url'), '/') . '/spv'])
Deschide SPV Curier
@endcomponent

@slot('subcopy')
Primiți acest email fiindcă aveți o alertă configurată în SPV Curier pentru
{{ $constatare === \App\Models\AlertaMesajSpv::CAND_VECTOR_MODIFICAT
    ? 'modificările vectorului fiscal'
    : 'obligațiile de plată restante' }}.
Ea se trimite numai când se constată acest lucru, nu la fiecare document sosit.
@endslot
@endcomponent
