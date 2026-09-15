<?php

namespace App\Services\Anaf\Etransport;

use App\Models\EtransportCodVamal;
use App\Models\EtransportDeclaratie;
use DOMDocument;
use DOMElement;

/**
 * Declarația Intrastat, întocmită din declarațiile e-Transport cu UIT.
 *
 * Cine declară transporturile în e-Transport a spus deja tot ce cere și
 * Intrastat-ul lunar: codul NC8, greutatea netă, valoarea în lei, țara
 * partenerului. De aici iese fișierul XML pe schema INS
 * (http://www.intrastat.ro/xml/InsSchema), care se încarcă în aplicația
 * Intrastat (online sau offline) — aceasta îl validează și îl criptează la
 * depunere, conform ghidului INS de implementare XML.
 *
 * Sosirile ies din achizițiile intracomunitare (AIC), expedierile din
 * livrările intracomunitare (LIC), cu liniile adunate pe cod NC8 și țară.
 *
 * [2026-09-14] Se întocmește și declarația NULĂ, pentru luna în care nu s-a
 * mișcat nimic pe fluxul acela. Ea nu e o formalitate de prisos: cine e obligat
 * să declare și nu trimite nimic e trecut nerespondent și amendat. Schema INS o
 * are ca atare, cu rădăcina ei (`InsNillDispatch`, `InsNillArrival`) și cu
 * același cuprins ca oricare alta, doar fără linii de marfă.
 *
 * [2026-09-14] Trei lucruri s-au îndreptat, după o lună de august care ieșea pe
 * jumătate:
 *
 *   - luna se ia după DATA DOCUMENTULUI, nu după a transportului. Marfa
 *     facturată pe 31 august și plecată pe 3 septembrie ține de august; socotită
 *     după transport, ea cădea în septembrie, iar augustul ieșea cu 11
 *     declarații în loc de 24.
 *   - nu se mai cere cod UIT. e-Transport și Intrastat sunt două obligații
 *     deosebite: un transport nedeclarat la ANAF — sau declarat prea târziu ca
 *     să mai primească UIT — tot trebuie declarat la statistică.
 *   - întârziații se iau din urmă. O factură emisă pe 31 iulie, ajunsă după ce
 *     s-a depus iulie, apare la august ca „mai veche, neintrată nicăieri". De
 *     aceea fiecare transport își ține minte luna în care a intrat.
 *
 * Ce iese se vede întâi în centralizator, document cu document și adunat pe cod
 * NC8, ca omul să verifice înainte de a trimite ceva la INS.
 */
class IntrastatXml
{
    public const NAMESPACE = 'http://www.intrastat.ro/xml/InsSchema';

    /** Fluxurile declarației și tipul de operațiune e-Transport din care ies. */
    public const FLUXURI = [
        'sosiri' => 10,
        'expedieri' => 20,
    ];

    /** Rădăcina documentului, pe flux și pe fel de declarație. */
    protected const RADACINI = [
        'sosiri' => ['obisnuita' => 'InsNewArrival', 'nula' => 'InsNillArrival'],
        'expedieri' => ['obisnuita' => 'InsNewDispatch', 'nula' => 'InsNillDispatch'],
    ];

    /**
     * Versiunile nomenclatoarelor, cerute în antet. Aplicația Intrastat
     * revalidează oricum totul la import; anul NC8 se pune la generare.
     */
    protected const VERSIUNI = [
        'CountryVer' => '2022',
        'EuCountryVer' => '2022',
        'CnVer' => '',
        'ModeOfTransportVer' => '2005',
        'DeliveryTermsVer' => '2021',
        'NatureOfTransactionAVer' => '2022',
        'NatureOfTransactionBVer' => '2022',
        'CountyVer' => '1',
        'LocalityVer' => '06/2006',
        'UnitVer' => '1',
    ];

    /**
     * Natura tranzacției: achiziție / vânzare definitivă.
     *
     * [2026-09-15] Codul B se scrie întreg, cu punct — „1.1", nu „1". Așa îl
     * scrie și aplicația INS în declarațiile pe care le primește. Trimis ca „1",
     * INS răspundea „Cod Natură Tranzacţie B lipseşte" pe fiecare linie, deși
     * codul era acolo: pur și simplu nu-l găsea în nomenclatorul lui.
     *
     * Dacă vreodată se declară retururi ca livrări intracomunitare, codul lor e
     * 2.1, „Returnări de bunuri", nu acesta.
     */
    protected const NATURA_TRANZACTIEI = ['a' => '1', 'b' => '1.1'];

    /**
     * @param array{cif: string, firma: string, nume: string, prenume: string,
     *     telefon: string, email: ?string, incoterm: ?string} $antet
     * @param array{nula?: bool, declaratii?: array<int, int>} $optiuni
     *     `nula` — luna n-a avut nimic pe fluxul acesta;
     *     `declaratii` — id-urile alese în centralizator; lipsă = cele propuse.
     * @return array{nume: string, xml: string, linii: int, declaratii: int, valoare: int, nula: bool}
     */
    public function genereaza(int $luna, int $anul, string $flux, array $antet, array $optiuni = []): array
    {
        $this->verificaFluxul($flux);

        $nula = (bool) ($optiuni['nula'] ?? false);
        $felul = $this->felul($flux);

        if ($nula) {
            /*
             * Declaratia nula spune ca luna n-a avut nimic pe fluxul acesta.
             * Cand are, ea ar fi o declaratie mincinoasa, asa ca nu se face:
             * mai bine se opreste aici decat sa ajunga asa la INS.
             */
            $aleLunii = $this->candidate($luna, $anul, $flux)['ale_lunii'];

            if ($aleLunii->isNotEmpty()) {
                throw new EtransportException(sprintf(
                    'Pe %02d/%d există %d transporturi pentru %s, deci luna nu e goală. '
                        . 'Declarația nulă se depune doar când nu e nimic de declarat.',
                    $luna,
                    $anul,
                    $aleLunii->count(),
                    $felul
                ));
            }

            return $this->document($luna, $anul, $flux, $antet, [], true);
        }

        $declaratii = $this->alese($luna, $anul, $flux, $optiuni['declaratii'] ?? null);

        if ($declaratii->isEmpty()) {
            throw new EtransportException(sprintf(
                'Niciun transport de declarat pentru %s pe %02d/%d. Dacă în luna aceasta chiar nu ați avut, '
                    . 'bifați „declarație nulă”: INS o cere oricum, altfel sunteți trecut nerespondent.',
                $felul,
                $luna,
                $anul
            ));
        }

        if (trim((string) ($antet['incoterm'] ?? '')) === '') {
            throw new EtransportException('Lipsește condiția de livrare (Incoterm), cerută pe fiecare linie.');
        }

        $rezultat = $this->document(
            $luna,
            $anul,
            $flux,
            $antet,
            $this->aduna($declaratii, $flux),
            false,
            $declaratii->count()
        );

        /*
         * Se insemneaza in ce luna Intrastat a intrat fiecare transport. De aici
         * stie centralizatorul lunii urmatoare care facturi au ramas pe dinafara
         * si trebuie luate din urma.
         */
        EtransportDeclaratie::whereIn('id', $declaratii->pluck('id'))
            ->update(['intrastat_perioada' => $this->perioada($luna, $anul)]);

        return $rezultat;
    }

    /**
     * Centralizatorul: ce ar intra în declarație, înainte să se genereze ceva.
     *
     * Întoarce documentele propuse — cele ale lunii și cele mai vechi rămase
     * nedeclarate — și totalurile pe cod NC8 care ies din cele alese.
     *
     * @param array<int, int>|null $alese id-urile bifate; lipsă = propunerea
     * @return array{perioada: string, flux: string, documente: array, linii: array,
     *     totaluri: array{documente: int, linii: int, masa: int, valoare: int}}
     */
    public function centralizator(int $luna, int $anul, string $flux, ?array $alese = null): array
    {
        $this->verificaFluxul($flux);

        $candidate = $this->candidate($luna, $anul, $flux);
        $toate = $candidate['ale_lunii']->concat($candidate['intarziate']);

        // Fara o alegere anume, propunerea e: luna intreaga, fara intarziati.
        $bifate = $alese === null
            ? $candidate['ale_lunii']->pluck('id')->all()
            : array_map('intval', $alese);

        $documente = $toate->map(function (EtransportDeclaratie $d) use ($candidate, $bifate) {
            $document = $d->documente[0] ?? [];

            return [
                'id' => $d->id,
                'numar' => $document['numar'] ?? $d->referinta_interna,
                'data' => $d->data_intrastat,
                'luna' => $d->luna_intrastat,
                'data_transport' => optional($d->data_transport)->format('Y-m-d'),
                'referinta' => $d->referinta_interna,
                'stare' => $d->stare,
                'uit' => $d->uit,
                'nr_linii' => count($d->linii ?: []),
                'valoare_lei' => round(array_sum(array_column($d->linii ?: [], 'valoare_lei')), 2),
                // Factura unei luni trecute, neintrata in nicio declaratie Intrastat.
                'intarziat' => $candidate['intarziate']->contains('id', $d->id),
                'bifat' => in_array($d->id, $bifate, true),
            ];
        })->values()->all();

        // Ce nu intra, si de ce: altfel documentele lipsesc fara nicio vorba.
        $neincluse = [];

        foreach ($candidate['neincluse'] as $lasat) {
            $motiv = $lasat['motiv'];
            $declaratie = $lasat['declaratie'];

            if (!isset($neincluse[$motiv])) {
                $neincluse[$motiv] = ['motiv' => $motiv, 'nr' => 0, 'exemple' => []];
            }

            $neincluse[$motiv]['nr']++;

            if (count($neincluse[$motiv]['exemple']) < 3) {
                $neincluse[$motiv]['exemple'][] = ($declaratie->documente[0]['numar'] ?? null)
                    ?: $declaratie->referinta_interna;
            }

            if ($motiv === 'alt_flux' || $motiv === 'alta_operatiune') {
                $neincluse[$motiv]['operatiune'] = Nomenclatoare::TIPURI_OPERATIUNE[$declaratie->tip_operatiune]
                    ?? (string) $declaratie->tip_operatiune;
            }
        }

        $selectate = $toate->whereIn('id', $bifate);
        $linii = $this->aduna($selectate, $flux);

        return [
            'perioada' => $this->perioada($luna, $anul),
            'flux' => $flux,
            'documente' => $documente,
            'neincluse' => array_values($neincluse),
            'linii' => $linii,
            'totaluri' => [
                'documente' => $selectate->count(),
                'linii' => count($linii),
                'masa' => (int) array_sum(array_column($linii, 'masa')),
                'valoare' => (int) array_sum(array_column($linii, 'valoare')),
            ],
        ];
    }

    /**
     * Transporturile care pot intra în declarația lunii.
     *
     * `ale_lunii` — cele cu data documentului în luna cerută, neintrate în altă
     * declarație Intrastat. `intarziate` — cele mai vechi, rămase nedeclarate:
     * factura din 31 iulie sosită după depunerea lui iulie se ia acum.
     *
     * Cele respinse de ANAF nu intră: transportul acela s-a redepus cu altă
     * declarație, iar la socoteală ar ieși marfa de două ori.
     *
     * @return array{ale_lunii: \Illuminate\Support\Collection, intarziate: \Illuminate\Support\Collection}
     */
    public function candidate(int $luna, int $anul, string $flux): array
    {
        $this->verificaFluxul($flux);

        $perioada = $this->perioada($luna, $anul);
        $inceput = \Carbon\Carbon::create($anul, $luna, 1)->startOfMonth();

        /*
         * Plasa larga, taiata dupa aceea in PHP: luna Intrastat se citeste din
         * JSON-ul documentelor, unde baza nu poate filtra de-a dreptul. Doi ani
         * inapoi acopera orice intarziere adevarata, iar doua luni inainte prind
         * facturile lunii al caror transport a plecat mai tarziu.
         *
         * [2026-09-15] Se aduc si cele care NU intra — de pe alt flux, respinse,
         * declarate deja —, ca sa se poata spune omului de ce lipsesc. Altfel
         * ele pur si simplu nu apar, si nu are de unde sti pe ce sa se uite.
         */
        $brute = EtransportDeclaratie::where(function ($q) use ($inceput) {
            $q->whereNull('data_transport')
                ->orWhereBetween('data_transport', [
                    $inceput->copy()->subYears(2)->toDateString(),
                    $inceput->copy()->addMonths(2)->endOfMonth()->toDateString(),
                ]);
        })
            ->orderBy('id')
            ->get();

        $aleLunii = [];
        $intarziate = [];
        $neincluse = [];

        foreach ($brute as $declaratie) {
            $lunaEi = $declaratie->luna_intrastat;

            // Fara nicio data nu se poate aseza intr-o luna; se propune ca
            // intarziata, ca sa fie macar vazuta si sa i se poata pune data.
            $aLunii = $lunaEi === $perioada;
            $maiVeche = $lunaEi === null || $lunaEi < $perioada;

            if (!$aLunii && !$maiVeche) {
                continue;
            }

            $motiv = $this->deCeNuIntra($declaratie, $flux, $perioada);

            if ($motiv !== null) {
                $neincluse[] = ['declaratie' => $declaratie, 'motiv' => $motiv];

                continue;
            }

            if ($aLunii) {
                $aleLunii[] = $declaratie;
            } else {
                $intarziate[] = $declaratie;
            }
        }

        return [
            'ale_lunii' => collect($aleLunii),
            'intarziate' => collect($intarziate),
            'neincluse' => collect($neincluse),
        ];
    }

    /**
     * De ce nu intră un transport în declarația cerută. `null` = intră.
     *
     * Motivele sunt cele care se pot vedea din afară și se pot îndrepta: fluxul
     * greșit pe declarație, respingerea la ANAF, sau faptul că marfa a fost deja
     * declarată în altă lună.
     */
    protected function deCeNuIntra(EtransportDeclaratie $declaratie, string $flux, string $perioada): ?string
    {
        if ((int) $declaratie->tip_operatiune !== self::FLUXURI[$flux]) {
            return in_array((int) $declaratie->tip_operatiune, self::FLUXURI, true)
                ? 'alt_flux'
                : 'alta_operatiune';
        }

        if ($declaratie->stare === 'respinsa') {
            return 'respinsa';
        }

        if ($declaratie->intrastat_perioada !== null && $declaratie->intrastat_perioada !== $perioada) {
            return 'declarata';
        }

        return null;
    }

    /** Transporturile care intră în fișier: cele bifate, ori propunerea. */
    protected function alese(int $luna, int $anul, string $flux, ?array $alese)
    {
        $candidate = $this->candidate($luna, $anul, $flux);

        if ($alese === null) {
            return $candidate['ale_lunii'];
        }

        $ids = array_map('intval', $alese);

        return $candidate['ale_lunii']->concat($candidate['intarziate'])
            ->whereIn('id', $ids)
            ->values();
    }

    protected function verificaFluxul(string $flux): void
    {
        if (!isset(self::FLUXURI[$flux])) {
            throw new EtransportException('Fluxul cerut nu există: se alege între sosiri și expedieri.');
        }
    }

    protected function felul(string $flux): string
    {
        return $flux === 'sosiri'
            ? 'achiziții intracomunitare (sosiri)'
            : 'livrări intracomunitare (expedieri)';
    }

    protected function perioada(int $luna, int $anul): string
    {
        return sprintf('%d-%02d', $anul, $luna);
    }

    /**
     * Documentul XML, cu sau fără linii de marfă.
     *
     * Cuprinsul e același la amândouă felurile — versiunile nomenclatoarelor și
     * antetul —; declarația nulă se deosebește doar prin rădăcină și prin faptul
     * că nu are nicio linie.
     *
     * @return array{nume: string, xml: string, linii: int, declaratii: int, valoare: int, nula: bool}
     */
    protected function document(int $luna, int $anul, string $flux, array $antet, array $linii, bool $nula, int $declaratii = 0): array
    {
        $doc = new DOMDocument('1.0', 'UTF-8');
        $doc->formatOutput = true;

        $radacina = $doc->createElementNS(
            self::NAMESPACE,
            self::RADACINI[$flux][$nula ? 'nula' : 'obisnuita']
        );
        $doc->appendChild($radacina);
        $radacina->setAttribute('SchemaVersion', '1.0');

        $radacina->appendChild($this->versiunile($doc, $anul));
        $radacina->appendChild($this->antetul($doc, $luna, $anul, $antet));

        $numarLinie = 0;
        $valoareTotala = 0;

        foreach ($linii as $linie) {
            $element = $doc->createElementNS(
                self::NAMESPACE,
                $flux === 'sosiri' ? 'InsArrivalItem' : 'InsDispatchItem'
            );
            $radacina->appendChild($element);
            $element->setAttribute('OrderNr', (string) ++$numarLinie);

            $this->text($doc, $element, 'Cn8Code', $linie['cn8']);
            $this->text($doc, $element, 'InvoiceValue', (string) $linie['valoare']);
            $this->text($doc, $element, 'StatisticalValue', (string) $linie['valoare']);
            $this->text($doc, $element, 'NetMass', (string) $linie['masa']);
            // Natura tranzacției 1.1: cumpărare/vânzare definitivă.
            $this->text($doc, $element, 'NatureOfTransactionACode', self::NATURA_TRANZACTIEI['a']);
            $this->text($doc, $element, 'NatureOfTransactionBCode', self::NATURA_TRANZACTIEI['b']);
            $this->text($doc, $element, 'DeliveryTermsCode', $antet['incoterm']);
            // Transport rutier: doar el trece prin e-Transport.
            $this->text($doc, $element, 'ModeOfTransportCode', '3');
            $this->text($doc, $element, 'CountryOfOrigin', $linie['origine']);

            /*
             * [2026-09-15] Unitatea de masura suplimentara, acolo unde codul o
             * cere. Sta dupa tara de origine, cum arata schema: `CountryOfOrigin`
             * si `InsSupplUnitsInfo` inchid tipul de baza, iar tara de expediere
             * ori cea de destinatie vin dupa, din tipul derivat.
             */
            if (!empty($linie['um_suplimentara']) && !empty($linie['cantitate'])) {
                $unitati = $doc->createElementNS(self::NAMESPACE, 'InsSupplUnitsInfo');
                $element->appendChild($unitati);

                $this->text($doc, $unitati, 'SupplUnitCode', $linie['um_suplimentara']);
                $this->text($doc, $unitati, 'QtyInSupplUnits', (string) $linie['cantitate']);
            }

            if ($flux === 'sosiri') {
                $this->text($doc, $element, 'CountryOfConsignment', $linie['tara']);
            } else {
                $this->text($doc, $element, 'CountryOfDestination', $linie['tara']);
                $this->text($doc, $element, 'PartnerCountryCode', $linie['tara']);
                $this->text($doc, $element, 'PartnerVatNr', $linie['partener_cod'] ?: '-');
            }

            $valoareTotala += $linie['valoare'];
        }

        return [
            // „nula" in nume, ca fisierul sa se recunoasca dintr-o privire in dosar.
            'nume' => sprintf(
                'intrastat_%s%s_%d_%02d_%s.xml',
                $nula ? 'nula_' : '',
                $flux,
                $anul,
                $luna,
                preg_replace('/\D/', '', $antet['cif'])
            ),
            'xml' => $doc->saveXML(),
            'linii' => count($linii),
            'declaratii' => $declaratii,
            'valoare' => $valoareTotala,
            'nula' => $nula,
        ];
    }

    /**
     * Adună liniile declarațiilor pe cod NC8, țară parteneră și țară de
     * origine: Intrastat cere totaluri pe fel de marfă, nu fiecare transport.
     */
    protected function aduna($declaratii, string $flux): array
    {
        $linii = [];

        foreach ($declaratii as $declaratie) {
            $tara = strtoupper(trim((string) $declaratie->partener_tara)) ?: 'XX';

            foreach ($declaratie->linii ?: [] as $rand) {
                $cn8 = preg_replace('/\D/', '', (string) ($rand['cod_tarifar'] ?? ''));

                if (strlen($cn8) !== 8) {
                    // Fara cod NC8 intreg, linia nu are loc in Intrastat.
                    continue;
                }

                /*
                 * La sosiri, tara de origine e a marfii (din fisierul
                 * furnizorului); necunoscuta, ramane tara partenerului. La
                 * expedieri, marfa pleaca de aici.
                 */
                $origine = $flux === 'sosiri'
                    ? (strtoupper(trim((string) ($rand['tara_origine'] ?? ''))) ?: $tara)
                    : 'RO';

                $cheie = $cn8 . '|' . $tara . '|' . $origine;

                if (!isset($linii[$cheie])) {
                    $linii[$cheie] = [
                        'cn8' => $cn8,
                        'tara' => $tara,
                        'origine' => $origine,
                        'valoare' => 0,
                        'masa' => 0.0,
                        'cantitate' => 0.0,
                        'partener_cod' => trim((string) $declaratie->partener_cod),
                    ];
                }

                $linii[$cheie]['valoare'] += (int) round((float) ($rand['valoare_lei'] ?? 0));
                $linii[$cheie]['masa'] += (float) ($rand['greutate_neta'] ?? 0);
                $linii[$cheie]['cantitate'] += (float) ($rand['cantitate'] ?? 0);
            }
        }

        foreach ($linii as &$linie) {
            // INS cere numere intregi, iar sub un kilogram se scrie 1.
            $linie['masa'] = max(1, (int) round($linie['masa']));
            $linie['valoare'] = max(1, $linie['valoare']);
            // Aceeasi regula si la unitatea suplimentara: fara zecimale, minim 1.
            $linie['cantitate'] = $linie['cantitate'] > 0 ? max(1, (int) round($linie['cantitate'])) : 0;
        }

        unset($linie);

        return $this->completeazaUnitatile(array_values($linii));
    }

    /**
     * Pune pe fiecare linie unitatea de măsură suplimentară cerută de codul ei.
     *
     * [2026-09-15] Nomenclatorul Combinat cere la unele coduri o unitate în afară
     * de kilogram: bucăți (`p/st`), perechi (`pa`), metri pătrați. Fără ea, INS
     * respinge declarația cu „Cod Unitate de Măsură Suplimentară invalid", câte o
     * eroare pe fiecare asemenea linie. Codurile care n-au una rămân cum sunt.
     */
    protected function completeazaUnitatile(array $linii): array
    {
        $coduri = array_filter(array_column($linii, 'cn8'));

        if ($coduri === []) {
            return $linii;
        }

        $unitati = EtransportCodVamal::whereIn('cod', $coduri)
            ->whereNotNull('um_suplimentara')
            ->pluck('um_suplimentara', 'cod');

        foreach ($linii as &$linie) {
            $linie['um_suplimentara'] = $unitati[$linie['cn8']] ?? null;
        }

        return $linii;
    }

    protected function versiunile(DOMDocument $doc, int $anul): DOMElement
    {
        $element = $doc->createElementNS(self::NAMESPACE, 'InsCodeVersions');

        /*
         * [2026-09-15] Versiunile sunt cele dintr-o declarație pe care INS a
         * primit-o, întocmită cu aplicația lui: țări 2022, condiții de livrare
         * 2021, natura tranzacției 2022, județe „1", localități „06/2006",
         * unități „1". Nu se deduc din nimic și nu sunt scrise în niciun ghid;
         * până acum erau ghicite, iar câteva nici nu existau la INS.
         *
         * Singura care ține de anul declarației e nomenclatorul de bunuri: pe el
         * îl schimbăm în fiecare ianuarie, odată cu codurile vamale.
         */
        foreach (self::VERSIUNI as $nume => $valoare) {
            $this->text($doc, $element, $nume, $nume === 'CnVer' ? (string) $anul : $valoare);
        }

        return $element;
    }

    protected function antetul(DOMDocument $doc, int $luna, int $anul, array $antet): DOMElement
    {
        $element = $doc->createElementNS(self::NAMESPACE, 'InsDeclarationHeader');

        // Schema cere fix 10 cifre; CIF-ul se completeaza cu zerouri in fata.
        $this->text($doc, $element, 'VatNr', str_pad(preg_replace('/\D/', '', $antet['cif']), 10, '0', STR_PAD_LEFT));
        $this->text($doc, $element, 'FirmName', $antet['firma']);
        $this->text($doc, $element, 'RefPeriod', sprintf('%d-%02d', $anul, $luna));
        $this->text($doc, $element, 'CreateDt', now()->format('Y-m-d\TH:i:s'));

        $contact = $doc->createElementNS(self::NAMESPACE, 'ContactPerson');
        $element->appendChild($contact);

        $this->text($doc, $contact, 'LastName', $antet['nume']);
        $this->text($doc, $contact, 'FirstName', $antet['prenume']);

        if (!empty($antet['email'])) {
            $this->text($doc, $contact, 'Email', $antet['email']);
        }

        $this->text($doc, $contact, 'Phone', $antet['telefon']);

        return $element;
    }

    protected function text(DOMDocument $doc, DOMElement $parinte, string $nume, string $valoare): void
    {
        $element = $doc->createElementNS(self::NAMESPACE, $nume);
        $element->appendChild($doc->createTextNode($valoare));
        $parinte->appendChild($element);
    }
}
