{{--
    Vectorul fiscal al unei luni, pe hartie.

    Randurile sunt entitatile inrolate, coloanele — declaratiile deduse din
    vectorul lor fiscal. In casuta sta fie recipisa (index, data si ora), fie
    periodicitatea obligatiei, cu semn de atentionare cand luna chiar era a ei.

    Culoarea si sigla sunt cele ale modulului SPV Curier (#22406f).
--}}
<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="utf-8">
    <title>Vector fiscal {{ sprintf('%02d', $raport['luna']) }}/{{ $raport['anul'] }}</title>
    <style>
        @php $navy = '#22406f'; @endphp

        * { box-sizing: border-box; }

        body {
            font-family: 'DejaVu Sans', 'Segoe UI', Arial, sans-serif;
            font-size: 8pt;
            color: #2b2b2b;
            margin: 0;
        }

        .antet {
            border-bottom: 2px solid {{ $navy }};
            padding-bottom: 6px;
            margin-bottom: 10px;
        }

        .antet td { vertical-align: middle; }

        .sigla { width: 200px; }

        .titlu {
            text-align: right;
            color: {{ $navy }};
        }

        .titlu .mare {
            font-size: 15pt;
            font-weight: bold;
            letter-spacing: -0.3px;
        }

        .titlu .mica {
            font-size: 9pt;
            font-weight: normal;
        }

        table.date {
            width: 100%;
            border-collapse: collapse;
        }

        table.date th,
        table.date td {
            border: 0.5px solid #c9d2df;
            padding: 3px 4px;
        }

        table.date thead th {
            background: {{ $navy }};
            color: #fff;
            font-size: 7.5pt;
            font-weight: bold;
            text-align: center;
        }

        table.date thead th.stanga { text-align: left; }

        table.date tbody tr.impar td { background: #f5f7fa; }

        td.nr { text-align: right; width: 22px; }
        td.cui { white-space: nowrap; width: 70px; }
        td.denumire { width: 190px; }

        td.celula {
            text-align: center;
            font-size: 7pt;
            line-height: 1.25;
        }

        .index {
            font-weight: bold;
            color: {{ $navy }};
        }

        .moment { color: #4a5768; }

        .periodicitate { color: #6b7684; }

        .lipsa {
            color: #b3221c;
            font-weight: bold;
        }

        .goala { color: #c2c8d0; }

        .rectificativa {
            color: #8a6d00;
            font-style: italic;
        }

        tr.total td {
            background: rgba(34, 64, 111, 0.10);
            font-weight: bold;
            color: {{ $navy }};
            text-align: center;
        }

        tr.total td.eticheta { text-align: right; }

        .sectiune {
            margin-top: 12px;
            font-size: 7.5pt;
        }

        .sectiune .cap {
            color: {{ $navy }};
            font-weight: bold;
            margin-bottom: 3px;
        }

        .sectiune ul { margin: 0; padding-left: 14px; }

        .subsol {
            margin-top: 14px;
            padding-top: 5px;
            border-top: 0.5px solid #c9d2df;
            font-size: 7pt;
            color: #6b7684;
        }

        .legenda span { margin-right: 14px; }
    </style>
</head>
<body>

<table class="antet" width="100%">
    <tr>
        <td class="sigla">{!! $sigla !!}</td>
        <td class="titlu">
            <div class="mare">Vector fiscal</div>
            <div class="mica">pentru luna {{ sprintf('%02d', $raport['luna']) }} anul {{ $raport['anul'] }}</div>
        </td>
    </tr>
</table>

@if (count($raport['randuri']) === 0)
    <p>Nicio entitate înrolată nu are obligații în vigoare în luna cerută.</p>
@else
    <table class="date">
        <thead>
        <tr>
            <th class="stanga">Nr<br>crt</th>
            <th class="stanga">CUI</th>
            <th class="stanga">Denumire</th>
            @foreach ($raport['tipuri'] as $tip)
                <th>{{ $tip === 'BILANT' ? 'Bilanț' : $tip }}</th>
            @endforeach
        </tr>
        </thead>
        <tbody>
        @foreach ($raport['randuri'] as $rand)
            <tr class="{{ $loop->odd ? 'impar' : '' }}">
                <td class="nr">{{ $rand['nr'] }}</td>
                <td class="cui">{{ $rand['cui'] }}</td>
                <td class="denumire">{{ $rand['denumire'] }}</td>
                @foreach ($raport['tipuri'] as $tip)
                    @php $celula = $rand['celule'][$tip] ?? null; @endphp
                    <td class="celula">
                        @if ($celula === null)
                            <span class="goala">–</span>
                        @elseif ($celula['depusa'])
                            <div class="index">{{ $celula['index_recipisa'] }}</div>
                            <div class="moment">{{ $celula['data_depunere'] }}</div>
                            <div class="moment">{{ $celula['ora_depunere'] }}</div>
                            @if ($celula['rectificativa'])
                                <div class="rectificativa">rectificativă</div>
                            @endif
                        @else
                            <div class="periodicitate">{{ $celula['periodicitate'] ?: 'periodicitate necunoscută' }}</div>
                            @if ($celula['atentionare'])
                                <div class="lipsa">! nedepusă</div>
                            @endif
                        @endif
                    </td>
                @endforeach
            </tr>
        @endforeach
        <tr class="total">
            <td class="eticheta" colspan="3">TOTAL depuse / datorate</td>
            @foreach ($raport['tipuri'] as $tip)
                <td>{{ $raport['total'][$tip]['depuse'] }}/{{ $raport['total'][$tip]['datorate'] }}</td>
            @endforeach
        </tr>
        </tbody>
    </table>
@endif

@php
    $alteObligatii = collect($raport['randuri'])
        ->flatMap(function ($rand) {
            return collect($rand['alte_obligatii'])->map(function ($obligatie) use ($rand) {
                return $rand['denumire'] . ' (' . $rand['cui'] . '): ' . $obligatie['cod']
                    . ' ' . $obligatie['semnificatie'] . ' — ' . $obligatie['perfisc'];
            });
        });
@endphp

@if ($alteObligatii->isNotEmpty())
    <div class="sectiune">
        <div class="cap">Obligații din vector fără declarație cunoscută</div>
        <ul>
            @foreach ($alteObligatii as $obligatie)
                <li>{{ $obligatie }}</li>
            @endforeach
        </ul>
    </div>
@endif

@if (count($raport['fara_vector']))
    <div class="sectiune">
        <div class="cap">Entități fără obligații în luna cerută</div>
        <ul>
            @foreach ($raport['fara_vector'] as $entitate)
                <li>{{ $entitate['denumire'] ?: '—' }} ({{ $entitate['cui'] }}) — {{ $entitate['motiv'] }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="subsol">
    <div class="legenda">
        <span><strong class="index">index recipisă</strong> + data și ora depunerii — declarație depusă</span>
        <span><span class="periodicitate">periodicitate</span> — obligația există, depunerea nu e înregistrată</span>
        <span><span class="lipsa">! nedepusă</span> — perioada se încheie în luna aceasta</span>
    </div>
    <div style="margin-top: 4px;">
        Luna este perioada raportată, nu luna depunerii: declarația lunii
        {{ sprintf('%02d', $raport['luna']) }}/{{ $raport['anul'] }} se depune în luna următoare.
        Se numără depunerile făcute prin această aplicație, cele care au primit index de recipisă de la ANAF.
        Coloanele reies din vectorul fiscal (TVA-ul aduce cu el D394 și D406) și din istoricul
        depunerilor firmei — o declarație depusă vreodată, precum D390, rămâne urmărită.
    </div>
    <div style="margin-top: 4px;">
        Vector fiscal al lunii {{ sprintf('%02d', $raport['luna']) }}/{{ $raport['anul'] }},
        extras în {{ $raport['extras_la'] }}.
    </div>
</div>

</body>
</html>
