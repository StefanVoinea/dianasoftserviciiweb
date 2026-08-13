<?php

namespace App\Services\Anaf\Spv;

use App\Models\AnafDeclaratie;
use App\Models\AnafSocietate;
use App\Models\VectorDeclaratie;
use App\Models\VectorFiscal;
use App\Models\VectorSpv;
use App\Services\Anaf\Format;
use Carbon\Carbon;

/**
 * Situatia unei luni, asa cum iese ea din vectorul fiscal al fiecarei entitati.
 *
 * Vectorul fiscal spune ce datoreaza firma si de cand pana cand: cod de
 * obligatie, semnificatie si periodicitate. De acolo se deduce ce declaratii
 * ii revin pentru luna ceruta, iar pentru fiecare se cauta in evidenta
 * depunerilor: gasita, se arata indexul recipisei si momentul depunerii;
 * negasita, se arata periodicitatea si — daca luna chiar era a ei — se
 * atentioneaza.
 *
 * „Luna" e perioada raportata, nu luna in care s-a depus: D112 pentru 03/2017
 * se depune in aprilie, dar tine de martie, si acolo se si numara.
 */
class RaportVectorLunar
{
    /**
     * Ce declaratii se depun pentru fiecare obligatie din vector.
     *
     * Cheia e codul obligatiei (COD_IMP), asa cum il scrie ANAF in vectorul
     * fiscal. Valoarea e lista declaratiilor care o poarta; „perfisc" gol
     * inseamna ca declaratia urmeaza periodicitatea scrisa in vector, iar una
     * scrisa anume o inlocuieste — impozitul pe profit se plateste trimestrial
     * (D100), dar se regularizeaza o data pe an (D101).
     *
     * Inregistrarea in scopuri de TVA (cod 300) nu inseamna doar decontul:
     * fiecare perioada fiscala isi cere si declaratia informativa D394, iar de
     * cand SAF-T e obligatoriu pentru toti, si D406 — amandoua pe aceeasi
     * periodicitate ca decontul. Ele nu au cod propriu in vector, deci de aici
     * trebuie deduse.
     *
     * Contributiile sociale — tot ce incepe cu 4 — merg toate in D112, ca si
     * impozitul pe veniturile din salarii (602). Codurile care nu se regasesc
     * aici nu se pierd: raportul le trece separat, ca obligatii fara declaratie
     * cunoscuta, ca sa se vada ce nu stie inca aplicatia. Iar declaratiile care
     * nu se pot deduce din vector — D390, depusa doar in lunile cu operatiuni
     * intracomunitare — se invata din istoricul depunerilor firmei.
     */
    public const DECLARATII = [
        '100' => [['tip' => 'D100', 'perfisc' => null], ['tip' => 'D101', 'perfisc' => 'Anual']],
        '120' => [['tip' => 'D100', 'perfisc' => null]],
        '300' => [
            ['tip' => 'D300', 'perfisc' => null],
            ['tip' => 'D394', 'perfisc' => null],
            ['tip' => 'D406', 'perfisc' => null],
        ],
        '301' => [['tip' => 'D301', 'perfisc' => null]],
        '307' => [['tip' => 'D307', 'perfisc' => null]],
        '311' => [['tip' => 'D311', 'perfisc' => null]],
        '390' => [['tip' => 'D390', 'perfisc' => null]],
        '394' => [['tip' => 'D394', 'perfisc' => null]],
        '602' => [['tip' => 'D112', 'perfisc' => null]],
    ];

    /** Lunile in care se incheie perioada raportata, pe periodicitati. */
    protected const LUNILE_PERIOADEI = [
        'Lunar' => [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12],
        'Trimestrial' => [3, 6, 9, 12],
        'Semestrial' => [6, 12],
        'Anual' => [12],
    ];

    /**
     * Raportul pentru o luna: coloanele, randurile si totalurile.
     *
     * @return array{luna:int, anul:int, tipuri:array, randuri:array, total:array, fara_vector:array, extras_la:string}
     */
    public function pentruLuna(int $luna, int $anul): array
    {
        $inceputulLunii = Carbon::create($anul, $luna, 1)->startOfDay();
        $sfarsitulLunii = $inceputulLunii->copy()->endOfMonth();

        $entitati = AnafSocietate::inLucru()->orderBy('denumire')->orderBy('cif')->get();
        $vectorul = $this->vectorulEntitatilor($entitati->pluck('cif')->all(), $inceputulLunii, $sfarsitulLunii);
        $depunerile = $this->depunerileEntitatilor($entitati->pluck('cif')->all(), $anul);

        $randuri = [];
        $faraVector = [];
        $tipuri = [];

        foreach ($entitati as $entitate) {
            $obligatii = $vectorul->get($entitate->cif, collect());

            if ($obligatii->isEmpty()) {
                $faraVector[] = [
                    'cui' => $entitate->cif,
                    'denumire' => $entitate->denumire,
                    'motiv' => $entitate->vector_la
                        ? 'nicio obligație în vigoare în luna cerută'
                        : 'vectorul fiscal nu a fost încă preluat din SPV',
                ];

                continue;
            }

            $rand = $this->randEntitate($entitate, $obligatii, $depunerile->get($entitate->cif, collect()), $luna, $anul);

            $tipuri = array_merge($tipuri, array_keys($rand['celule']));
            $randuri[] = $rand;
        }

        $tipuri = $this->ordoneaza(array_values(array_unique($tipuri)));

        return [
            'luna' => $luna,
            'anul' => $anul,
            'tipuri' => $tipuri,
            'randuri' => $this->numeroteaza($randuri),
            'total' => $this->totaluri($randuri, $tipuri),
            'fara_vector' => $faraVector,
            'extras_la' => Format::dataOra(now()),
        ];
    }

    /**
     * Obligatiile in vigoare in luna ceruta, pe CUI.
     *
     * O obligatie tine de luna aceasta daca a inceput cel tarziu la sfarsitul
     * ei si nu s-a incheiat inaintea inceputului: vectorul pastreaza si
     * istoricul, iar o firma are adesea acelasi cod de mai multe ori, cu
     * periodicitati diferite de-a lungul anilor.
     */
    protected function vectorulEntitatilor(array $cifuri, Carbon $inceput, Carbon $sfarsit)
    {
        if ($cifuri === []) {
            return collect();
        }

        return VectorSpv::whereIn('cui', $cifuri)
            ->whereDate('data_inceput', '<=', $sfarsit)
            ->where(function ($q) use ($inceput) {
                $q->whereNull('data_sfarsit')->orWhereDate('data_sfarsit', '>=', $inceput);
            })
            ->orderBy('cod_imp')
            ->orderBy('data_inceput')
            ->get()
            ->groupBy('cui');
    }

    /**
     * Declaratiile inregistrate pentru anul cerut si cel dinainte, pe CUI.
     *
     * Anul dinainte nu e pentru potrivire — depunerea se cauta mereu pe anul
     * cerut — ci pentru invatat: din ce a depus firma pana acum se vad
     * declaratiile care nu se pot deduce din vector si periodicitatea lor.
     */
    protected function depunerileEntitatilor(array $cifuri, int $anul)
    {
        if ($cifuri === []) {
            return collect();
        }

        return AnafDeclaratie::whereIn('cui', $cifuri)
            ->whereIn('anul', [$anul, $anul - 1])
            ->orderBy('data_depunere')
            ->get()
            ->groupBy('cui');
    }

    /** Un rand de raport: entitatea, celulele ei si obligatiile nerecunoscute. */
    protected function randEntitate(AnafSocietate $entitate, $obligatii, $depuneri, int $luna, int $anul): array
    {
        $celule = [];
        $altele = [];

        foreach ($obligatii as $obligatie) {
            $declaratii = $this->declaratiileObligatiei($obligatie->cod_imp);

            if ($declaratii === []) {
                $altele[$obligatie->cod_imp . '|' . $obligatie->perfisc] = [
                    'cod' => $obligatie->cod_imp,
                    'semnificatie' => $obligatie->semnificatie,
                    'perfisc' => $obligatie->perfisc,
                ];

                continue;
            }

            foreach ($declaratii as $declaratie) {
                $tip = $declaratie['tip'];
                $perfisc = $declaratie['perfisc'] ?: $obligatie->perfisc;

                /*
                 * Aceeasi declaratie poate veni din mai multe obligatii — D112
                 * din toate contributiile. Se pastreaza periodicitatea cea mai
                 * deasa: daca o contributie e lunara, declaratia e lunara.
                 */
                if (isset($celule[$tip]) && $this->maiDeasa($celule[$tip]['periodicitate'], $perfisc)) {
                    $celule[$tip]['obligatii'][] = $this->numeleObligatiei($obligatie);
                    $celule[$tip]['valabilitate'] = $this->intinde($celule[$tip]['valabilitate'], $obligatie);

                    continue;
                }

                $vechi = $celule[$tip]['obligatii'] ?? [];
                $valabilitateVeche = $celule[$tip]['valabilitate'] ?? null;
                $celule[$tip] = $this->celula($tip, $perfisc, $depuneri, $luna, $anul);
                $celule[$tip]['obligatii'] = array_merge($vechi, [$this->numeleObligatiei($obligatie)]);
                $celule[$tip]['valabilitate'] = $this->intinde($valabilitateVeche, $obligatie);
            }
        }

        $celule = $this->adaugaDinIstoric($celule, $depuneri, $luna, $anul);

        // Deductia se scrie in tabel inainte de randurile omului: ea e ce a
        // aflat aplicatia, iar ce a scris omul sta deasupra, nu inauntrul ei.
        $this->salveazaDeductia($entitate->cif, $celule);

        $celule = $this->aplicaManualele($entitate->cif, $celule, $depuneri, $luna, $anul);

        return [
            'cui' => $entitate->cif,
            'denumire' => $entitate->denumire ?: '—',
            'celule' => $celule,
            'alte_obligatii' => array_values($altele),
            'lipsa' => count(array_filter($celule, function ($celula) {
                return $celula['datorata'] && !$celula['depusa'];
            })),
        ];
    }

    /**
     * Ce arata o casuta: recipisa, daca declaratia e depusa; altfel
     * periodicitatea, cu atentionare daca luna cerută era chiar a ei.
     */
    protected function celula(string $tip, ?string $perfisc, $depuneri, int $luna, int $anul): array
    {
        $datorata = $this->seDepunePentruLuna($perfisc, $luna);
        $depunere = $this->depunerea($depuneri, $tip, $perfisc, $luna, $anul);

        return [
            'tip' => $tip,
            'periodicitate' => $perfisc,
            'datorata' => $datorata,
            'depusa' => $depunere !== null,
            'index_recipisa' => $depunere ? $depunere->index_recipisa : null,
            'data_depunere' => $depunere ? Format::data($depunere->data_depunere ?: $depunere->data_recipisa) : null,
            'ora_depunere' => $depunere && ($depunere->data_depunere ?: $depunere->data_recipisa)
                ? Carbon::parse($depunere->data_depunere ?: $depunere->data_recipisa)->format('H:i:s')
                : null,
            'stare' => $depunere ? $depunere->stare_declaratie : null,
            'rectificativa' => $depunere ? (bool) $depunere->rectificativa : false,
            // Atentionarea se aprinde numai cand luna chiar e a declaratiei.
            'atentionare' => $datorata && $depunere === null,
            'din_istoric' => false,
            'manuala' => false,
            'valabilitate' => null,
        ];
    }

    /**
     * Depunerea inregistrata pentru perioada ceruta, daca exista.
     *
     * Declaratiile anuale nu poarta luna in XML — acolo se cauta pe an, iar
     * perioada lor se socoteste incheiata in decembrie. Dintre mai multe
     * depuneri (rectificativele) ramane ultima, si numai una cu recipisa: fara
     * index de recipisa depunerea nu e confirmata de ANAF.
     */
    protected function depunerea($depuneri, string $tip, ?string $perfisc, int $luna, int $anul)
    {
        $anuala = $this->periodicitate($perfisc) === 'Anual';

        $potrivite = $depuneri->filter(function (AnafDeclaratie $declaratie) use ($tip, $anuala, $luna, $anul) {
            if ($declaratie->tip !== $tip || !$declaratie->index_recipisa) {
                return false;
            }

            // Istoricul cuprinde si anul dinainte; aceeasi luna a lui nu e a noastra.
            if ((int) $declaratie->anul !== $anul) {
                return false;
            }

            if ($anuala) {
                return $declaratie->luna === null || (int) $declaratie->luna === 12;
            }

            return (int) $declaratie->luna === $luna;
        });

        return $potrivite->sortBy('data_depunere')->last();
    }

    /**
     * Declaratiile invatate din istoricul depunerilor firmei.
     *
     * Nu tot ce depune o firma se citeste din vector: D390 se depune doar in
     * lunile cu operatiuni intracomunitare, D406 poate veni si fara TVA in
     * vector, iar altele n-au cod acolo deloc. Daca firma a depus vreodata un
     * tip, el ii ramane in raport — cu periodicitatea dedusa din chiar
     * depunerile ei.
     */
    protected function adaugaDinIstoric(array $celule, $depuneri, int $luna, int $anul): array
    {
        $confirmate = $depuneri->filter(function (AnafDeclaratie $declaratie) {
            return (bool) $declaratie->index_recipisa;
        });

        // Periodicitatea decontului de TVA, mostenita de suratele lui la nevoie.
        $tvaPerfisc = isset($celule['D300']) ? $celule['D300']['periodicitate'] : null;

        foreach ($confirmate->groupBy('tip') as $tip => $aleTipului) {
            if (isset($celule[$tip])) {
                continue;
            }

            $perfisc = $this->periodicitateDinIstoric($aleTipului, $tip, $tvaPerfisc);

            $celule[$tip] = $this->celula($tip, $perfisc, $depuneri, $luna, $anul);
            $celule[$tip]['obligatii'] = ['din istoricul depunerilor'];
            $celule[$tip]['din_istoric'] = true;
            // Valabilitatea incepe cu prima perioada depusa; sfarsit nu se stie.
            $celule[$tip]['valabilitate'] = ['inceput' => $this->primaPerioada($aleTipului), 'sfarsit' => null];
        }

        return $celule;
    }

    /** Prima perioada depusa a unui tip — inceputul valabilitatii lui deduse. */
    protected function primaPerioada($depuneri): ?string
    {
        $prima = $depuneri
            ->filter(function (AnafDeclaratie $declaratie) {
                return $declaratie->anul;
            })
            ->sortBy(function (AnafDeclaratie $declaratie) {
                return [(int) $declaratie->anul, (int) ($declaratie->luna ?: 1)];
            })
            ->first();

        if ($prima === null) {
            return null;
        }

        return Carbon::create((int) $prima->anul, (int) ($prima->luna ?: 1), 1)->format('Y-m-d');
    }

    /** Valabilitatea unei celule, intinsa peste inca o obligatie din vector. */
    protected function intinde(?array $valabilitate, VectorSpv $obligatie): array
    {
        $inceput = $obligatie->data_inceput ? $obligatie->data_inceput->format('Y-m-d') : null;
        $sfarsit = $obligatie->data_sfarsit ? $obligatie->data_sfarsit->format('Y-m-d') : null;

        if ($valabilitate === null) {
            return ['inceput' => $inceput, 'sfarsit' => $sfarsit];
        }

        // Cel mai devreme inceput; sfarsitul ramane deschis daca vreo obligatie e in vigoare.
        if ($valabilitate['inceput'] !== null && ($inceput === null || $inceput < $valabilitate['inceput'])) {
            $valabilitate['inceput'] = $inceput;
        }

        if ($valabilitate['sfarsit'] !== null && ($sfarsit === null || $sfarsit > $valabilitate['sfarsit'])) {
            $valabilitate['sfarsit'] = $sfarsit;
        }

        return $valabilitate;
    }

    /**
     * Deductia se pastreaza in tabela vector_declaratii, ca sa poata fi vazuta
     * si indreptata de om.
     *
     * Cheia e (cui, tip, inceputul valabilitatii): aceeasi declaratie cu
     * ferestre diferite de valabilitate — TVA lunar o vreme, apoi trimestrial —
     * are randul ei pentru fiecare fereastra, iar raportul unei luni vechi nu
     * strica randul de azi. Randurile scrise de om nu se ating.
     */
    protected function salveazaDeductia(string $cui, array $celule): void
    {
        foreach ($celule as $tip => $celula) {
            $valabilitate = $celula['valabilitate'] ?: ['inceput' => null, 'sfarsit' => null];

            VectorDeclaratie::updateOrCreate(
                [
                    'cui' => $cui,
                    'tip' => $tip,
                    'sursa' => 'dedusa',
                    'data_inceput' => $valabilitate['inceput'],
                ],
                [
                    'perfisc' => $celula['periodicitate'] ?: 'Lunar',
                    'data_sfarsit' => $valabilitate['sfarsit'],
                    'obligatii' => mb_substr(implode(', ', $celula['obligatii'] ?? []), 0, 250) ?: null,
                ]
            );
        }
    }

    /**
     * Randurile scrise de om se aseaza peste deductie.
     *
     * Ce nu se poate deduce — bilantul semestrial, o declaratie aparte — se
     * adauga din tabelul de actualizare, cu periodicitatea si valabilitatea
     * lui. Pe acelasi tip, cuvantul omului bate deductia: el stie firma.
     */
    protected function aplicaManualele(string $cui, array $celule, $depuneri, int $luna, int $anul): array
    {
        $inceputulLunii = Carbon::create($anul, $luna, 1)->startOfDay();
        $sfarsitulLunii = $inceputulLunii->copy()->endOfMonth();

        $manuale = VectorDeclaratie::where('cui', $cui)
            ->where('sursa', 'manuala')
            ->valabileIntre($inceputulLunii, $sfarsitulLunii)
            ->orderBy('tip')
            ->get();

        foreach ($manuale as $manuala) {
            $vechi = $celule[$manuala->tip]['obligatii'] ?? [];

            $celule[$manuala->tip] = $this->celula($manuala->tip, $manuala->perfisc, $depuneri, $luna, $anul);
            $celule[$manuala->tip]['obligatii'] = array_merge($vechi, ['stabilită manual']);
            $celule[$manuala->tip]['manuala'] = true;
            $celule[$manuala->tip]['valabilitate'] = [
                'inceput' => $manuala->data_inceput ? $manuala->data_inceput->format('Y-m-d') : null,
                'sfarsit' => $manuala->data_sfarsit ? $manuala->data_sfarsit->format('Y-m-d') : null,
            ];
        }

        return $celule;
    }

    /**
     * Cat de des depune firma acest tip, judecand dupa depunerile ei.
     *
     * Intai vorbeste declaratia insasi: D406 isi scrie felul perioadei in XML
     * (L/T/A). Apoi distanta dintre perioadele depuse: depuneri pe luni
     * alaturate inseamna lunar, din trei in trei luni — trimestrial. Cu o
     * singura depunere nu se vede nimic: suratele TVA-ului iau periodicitatea
     * decontului, restul se socotesc lunare, ca sa nu treaca neatentionate.
     */
    protected function periodicitateDinIstoric($depuneri, string $tip, ?string $tvaPerfisc): string
    {
        $dinPerioadaTip = ['L' => 'Lunar', 'T' => 'Trimestrial', 'A' => 'Anual'];
        $ultima = $depuneri->sortBy(function (AnafDeclaratie $declaratie) {
            return [(int) $declaratie->anul, (int) $declaratie->luna];
        })->last();

        if ($ultima->perioada_tip && isset($dinPerioadaTip[$ultima->perioada_tip])) {
            return $dinPerioadaTip[$ultima->perioada_tip];
        }

        $perioade = $depuneri
            ->filter(function (AnafDeclaratie $declaratie) {
                return $declaratie->luna && $declaratie->anul;
            })
            ->map(function (AnafDeclaratie $declaratie) {
                return (int) $declaratie->anul * 12 + (int) $declaratie->luna;
            })
            ->unique()
            ->sort()
            ->values();

        if ($perioade->isEmpty()) {
            // Depuneri doar fara luna — bilantul, regularizarile: o data pe an.
            return 'Anual';
        }

        if ($perioade->count() >= 2) {
            $pas = null;

            for ($i = 1; $i < $perioade->count(); $i++) {
                $distanta = $perioade[$i] - $perioade[$i - 1];
                $pas = $pas === null ? $distanta : min($pas, $distanta);
            }

            if ($pas <= 1) {
                return 'Lunar';
            }

            if ($pas <= 3) {
                return 'Trimestrial';
            }

            return $pas <= 6 ? 'Semestrial' : 'Anual';
        }

        if (in_array($tip, ['D390', 'D394', 'D406'], true) && $tvaPerfisc !== null) {
            return $tvaPerfisc;
        }

        return 'Lunar';
    }

    /** Declaratiile care poarta obligatia cu acest cod. */
    protected function declaratiileObligatiei(?string $cod): array
    {
        $cod = trim((string) $cod);

        if (isset(self::DECLARATII[$cod])) {
            return self::DECLARATII[$cod];
        }

        // Contributiile sociale — codurile de 4xx — se declara toate in D112.
        if (preg_match('/^4\d{2}$/', $cod)) {
            return [['tip' => 'D112', 'perfisc' => null]];
        }

        return [];
    }

    /** Perioada raportata se incheie in luna aceasta? */
    protected function seDepunePentruLuna(?string $perfisc, int $luna): bool
    {
        $periodicitate = $this->periodicitate($perfisc);

        if ($periodicitate === null) {
            return false;
        }

        return in_array($luna, self::LUNILE_PERIOADEI[$periodicitate], true);
    }

    /** „Lunara", „lunar", „Lunar " — toate inseamna acelasi lucru. */
    protected function periodicitate(?string $valoare): ?string
    {
        $curatat = ucfirst(strtolower(rtrim(trim((string) $valoare), 'ăa')));

        return isset(self::LUNILE_PERIOADEI[$curatat]) ? $curatat : null;
    }

    /** Lunar bate trimestrial, trimestrial bate semestrial, si asa mai departe. */
    protected function maiDeasa(?string $pastrata, ?string $noua): bool
    {
        $rang = ['Lunar' => 4, 'Trimestrial' => 3, 'Semestrial' => 2, 'Anual' => 1];

        return ($rang[$this->periodicitate($pastrata)] ?? 0) >= ($rang[$this->periodicitate($noua)] ?? 0);
    }

    protected function numeleObligatiei(VectorSpv $obligatie): string
    {
        return trim($obligatie->cod_imp . ' ' . (string) $obligatie->semnificatie);
    }

    /**
     * Coloanele, in ordinea de pe hartia ANAF: intai D112, apoi TVA-urile, apoi
     * impozitul pe profit. Ce nu e in lista se aseaza la coada, alfabetic.
     */
    protected function ordoneaza(array $tipuri): array
    {
        $stiute = array_values(array_intersect(VectorFiscal::DECLARATII, $tipuri));
        $restul = array_values(array_diff($tipuri, $stiute));

        sort($restul);

        return array_merge($stiute, $restul);
    }

    protected function numeroteaza(array $randuri): array
    {
        foreach ($randuri as $i => $rand) {
            $randuri[$i]['nr'] = $i + 1;
        }

        return $randuri;
    }

    /** Cate s-au depus din cate erau datorate, pe fiecare coloana — randul „TOTAL". */
    protected function totaluri(array $randuri, array $tipuri): array
    {
        $total = [];

        foreach ($tipuri as $tip) {
            $datorate = 0;
            $depuse = 0;

            foreach ($randuri as $rand) {
                $celula = $rand['celule'][$tip] ?? null;

                if ($celula === null || !$celula['datorata']) {
                    continue;
                }

                $datorate++;

                if ($celula['depusa']) {
                    $depuse++;
                }
            }

            $total[$tip] = ['depuse' => $depuse, 'datorate' => $datorate];
        }

        return $total;
    }
}
