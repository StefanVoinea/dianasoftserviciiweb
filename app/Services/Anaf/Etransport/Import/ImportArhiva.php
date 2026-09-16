<?php

namespace App\Services\Anaf\Etransport\Import;

use App\Models\EtransportDeclaratie;
use App\Models\EtransportGestiune;
use App\Services\Anaf\Etransport\EtransportException;

/**
 * Arhiva zilnică a furnizorului: câte o ciornă de declarație pe fiecare factură.
 *
 * Emporio primește de la Teddy câte un ZIP pe zi de livrare, cu câte trei
 * fișiere pe factură: T02_* — recapitulația pe coduri vamale (liniile
 * declarației), D01_* — distinta cu destinația finală (magazinul, codul lui și
 * adresa), FT1_* — detaliul de articole, care nu ne trebuie.
 *
 * La retururi (nota de credit, arhiva „RESO") recapitulația lipsește; în locul
 * ei vine T01_* — lista pe articole, din care liniile ies grupate pe cod vamal.
 * Numele fișierelor nu poartă atunci numărul documentului; el se ia dinăuntru.
 * Returul se declară ca livrare intracomunitară: traseul e întors, de la
 * magazin (codul lui e pe rândul „From" al distinctei) la punctul de frontieră.
 * Adresa magazinului se ia din ultima declarație a aceluiași magazin.
 *
 * Dintr-o arhivă ies gata completate: partenerul, liniile cu denumirile din
 * nomenclator, valoarea în lei la cursul zilei facturii, factura la documente
 * și locul de descărcare — cu județul dedus din oraș. Rămân de completat doar
 * vehiculul, transportatorul și data transportului, apoi se depun pe rând.
 */
class ImportArhiva
{
    /** PTF-ul obișnuit al camioanelor Teddy: Borș 2 - A3. Rămâne editabil. */
    protected const PTF_IMPLICIT = 38;

    /**
     * Fișierele care ne interesează și bucata de nume care le leagă.
     *
     * Ale aceleiași facturi au același nume după prefix:
     * T02_2_TEDDY_..._10053419.TXT și D01_2_TEDDY_..._10053419.TXT.
     */
    protected const TIPAR_FISIER = '/^(T01|T02|D01)_(.+)\.(?:txt|text|prn)$/i';

    /** Orașele reședință de județ, pentru județul locului de descărcare. */
    protected const ORASE = [
        'BUCURESTI' => 40, 'BUCUREŞTI' => 40, 'BUCHARESTI' => 40, 'BUCHAREST' => 40,
        'ALBA IULIA' => 1, 'ARAD' => 2, 'PITESTI' => 3,
        'BACAU' => 4, 'ORADEA' => 5, 'BISTRITA' => 6, 'BOTOSANI' => 7, 'BRASOV' => 8,
        'BRAILA' => 9, 'BUZAU' => 10, 'RESITA' => 11, 'CLUJ-NAPOCA' => 12, 'CLUJ NAPOCA' => 12,
        'CONSTANTA' => 13, 'SFANTU GHEORGHE' => 14, 'TARGOVISTE' => 15, 'CRAIOVA' => 16,
        'GALATI' => 17, 'TARGU JIU' => 18, 'MIERCUREA CIUC' => 19, 'DEVA' => 20,
        'SLOBOZIA' => 21, 'IASI' => 22, 'VOLUNTARI' => 23, 'BAIA MARE' => 24,
        'DROBETA-TURNU SEVERIN' => 25, 'TARGU MURES' => 26, 'PIATRA NEAMT' => 27,
        'SLATINA' => 28, 'PLOIESTI' => 29, 'SATU MARE' => 30, 'ZALAU' => 31, 'SIBIU' => 32,
        'SUCEAVA' => 33, 'ALEXANDRIA' => 34, 'TIMISOARA' => 35, 'TULCEA' => 36,
        'VASLUI' => 37, 'RAMNICU VALCEA' => 38, 'FOCSANI' => 39, 'CALARASI' => 51,
        'GIURGIU' => 52,
    ];

    protected $fisiere;

    public function __construct(?ImportFisiere $fisiere = null)
    {
        $this->fisiere = $fisiere ?: new ImportFisiere();
    }

    /** Gestiunile companiei, pe codul furnizorului; se încarcă la primul import. */
    protected $gestiuni;

    /** Ce e de spus despre ciorna în lucru (adresă lipsă etc.); iese ca avertisment. */
    protected $note = [];

    /**
     * [2026-09-16] Marfa de retur se declară în două etape, deci în două
     * declarații pe fiecare factură.
     *
     * Marfa se strânge din magazine la depozitul transportatorului — transport
     * pe teritoriul național, TTN —, iar de acolo pleacă din țară — livrare
     * intracomunitară, LIC. Sunt două transporturi deosebite, fiecare cu UIT-ul
     * lui, chiar dacă marfa și factura sunt aceleași.
     */
    protected $marfaRetur = false;

    /**
     * Citește arhiva și face câte o ciornă pe fiecare factură din ea.
     *
     * @return array{ciorne: array<int, array{id: int, factura: string, magazin: ?string}>, avertismente: array<int, string>, gestiuni_noi: array<int, array{cod_furnizor: string, denumire_furnizor: ?string}>}
     */
    public function importa(string $caleArhiva, ?string $cifDeclarant, ?int $userId = null, bool $marfaRetur = false): array
    {
        return $this->importaFisiere(
            [['nume' => basename($caleArhiva), 'cale' => $caleArhiva]],
            $cifDeclarant,
            $userId,
            $marfaRetur
        );
    }

    /**
     * [2026-09-16] Același import, dar pe un teanc de fișiere: tot ce s-a găsit
     * într-un dosar.
     *
     * Furnizorul nu trimite totul la fel. Unele zile vin ca arhivă, altele ca
     * fișiere răzlețe puse în dosar, iar de la alți furnizori vine un Excel cu
     * detaliile facturii. Aici se iau toate deodată: arhivele se desfac,
     * fișierele text se adună pe facturi ca și cum ar fi venit dintr-o arhivă,
     * iar fiecare Excel își face ciorna lui.
     *
     * @param array<int, array{nume: string, cale: string}> $fisiere
     * @return array{ciorne: array, avertismente: array<int, string>, gestiuni_noi: array}
     */
    public function importaFisiere(array $fisiere, ?string $cifDeclarant, ?int $userId = null, bool $marfaRetur = false): array
    {
        $this->marfaRetur = $marfaRetur;

        $rezultat = ['ciorne' => [], 'avertismente' => [], 'gestiuni_noi' => []];
        $grupuri = [];
        $excele = [];

        foreach ($fisiere as $fisier) {
            $nume = basename($fisier['nume']);
            $extensie = strtolower(pathinfo($nume, PATHINFO_EXTENSION));

            if ($extensie === 'zip') {
                $this->desfaArhiva($fisier, $grupuri, $rezultat);
            } elseif (in_array($extensie, ['txt', 'text', 'prn'], true)) {
                if (preg_match(self::TIPAR_FISIER, $nume, $gasit)) {
                    $grupuri[$gasit[2]][strtoupper($gasit[1])] = (string) file_get_contents($fisier['cale']);
                } else {
                    $rezultat['avertismente'][] = '„' . $nume . '" nu e T02, T01 sau D01; sărit.';
                }
            } elseif (in_array($extensie, ['xls', 'xlsx', 'ods'], true)) {
                $excele[] = $fisier;
            } else {
                $rezultat['avertismente'][] = '„' . $nume . '": fel de fișier necunoscut; sărit.';
            }
        }

        if ($grupuri === [] && $excele === []) {
            throw new EtransportException(
                'Nu s-a găsit nimic de citit: se așteaptă arhive ZIP, fișiere T02_*, T01_* și D01_*, ori Excel'
                . ' cu detaliile facturii.'
            );
        }

        $facturi = [];

        foreach ($grupuri as $stem => $bucati) {
            $facturi[$this->numarulFacturii($stem, $bucati)] = $bucati;
        }

        ksort($facturi);

        foreach ($facturi as $factura => $bucati) {
            // Importul facut a doua oara nu dubleaza ciornele.
            $referinte = [
                'Factura ' . $factura,
                'Retur ' . $factura,
                'Retur ' . $factura . ' (TTN)',
                'Retur ' . $factura . ' (LIC)',
            ];

            if (EtransportDeclaratie::whereIn('referinta_interna', $referinte)->exists()) {
                $rezultat['avertismente'][] = 'Factura ' . $factura . ' era deja adusă; sărită.';

                continue;
            }

            if (!isset($bucati['T02']) && !isset($bucati['T01'])) {
                $rezultat['avertismente'][] = 'Factura ' . $factura
                    . ': arhiva nu are recapitulația T02 (nici lista T01) — ciorna s-a făcut fără linii;'
                    . ' completați-le manual sau importați-le din fișier.';
            }

            $this->note = [];

            try {
                $declaratii = $this->ciorna((string) $factura, $bucati, $cifDeclarant, $userId);
            } catch (\Exception $e) {
                $rezultat['avertismente'][] = 'Factura ' . $factura . ': ' . $e->getMessage();

                continue;
            }

            foreach ($this->note as $nota) {
                $rezultat['avertismente'][] = 'Factura ' . $factura . ': ' . $nota;
            }

            foreach ($declaratii as $declaratie) {
                $locMagazin = $declaratie->loc_magazin;

                $rezultat['ciorne'][] = [
                    'id' => $declaratie->id,
                    'factura' => $declaratie->referinta_interna,
                    'magazin' => $locMagazin['magazin_denumire'] ?? null,
                ];

                // Un cod de magazin nestiut inca: utilizatorul e intrebat cum se numeste gestiunea.
                $codMagazin = mb_strtoupper((string) ($locMagazin['magazin_cod'] ?? ''));

                if ($codMagazin !== ''
                    && !isset($this->gestiunile()[$codMagazin])
                    && !isset($rezultat['gestiuni_noi'][$codMagazin])) {
                    $rezultat['gestiuni_noi'][$codMagazin] = [
                        'cod_furnizor' => $codMagazin,
                        'denumire_furnizor' => $locMagazin['magazin_denumire'] ?? null,
                    ];
                }
            }
        }

        foreach ($excele as $excel) {
            $this->ciornaDinExcel($excel, $cifDeclarant, $userId, $rezultat);
        }

        $rezultat['gestiuni_noi'] = array_values($rezultat['gestiuni_noi']);

        return $rezultat;
    }

    /**
     * Desface o arhivă în fișierele ei, adăugându-le la grupurile de facturi.
     *
     * Ce nu e T02, T01 sau D01 se lasă acolo: arhiva mai poartă și facturi PDF,
     * detalii de articole și alte fișiere care nu ne trebuie.
     */
    protected function desfaArhiva(array $fisier, array &$grupuri, array &$rezultat): void
    {
        $arhiva = new \ZipArchive();

        if ($arhiva->open($fisier['cale']) !== true) {
            $rezultat['avertismente'][] = 'Arhiva „' . basename($fisier['nume']) . '" nu a putut fi deschisă; sărită.';

            return;
        }

        $gasiteAici = 0;

        for ($i = 0; $i < $arhiva->numFiles; $i++) {
            $nume = basename($arhiva->getNameIndex($i));

            if (!preg_match(self::TIPAR_FISIER, $nume, $gasit)) {
                continue;
            }

            $grupuri[$gasit[2]][strtoupper($gasit[1])] = $arhiva->getFromIndex($i);
            $gasiteAici++;
        }

        $arhiva->close();

        if ($gasiteAici === 0) {
            $rezultat['avertismente'][] = 'Arhiva „' . basename($fisier['nume'])
                . '" nu are fișiere T02_*, T01_* sau D01_*; sărită.';
        }
    }

    /**
     * Ciorna dintr-un Excel cu detaliile facturii.
     *
     * Excelul aduce doar marfa: cod vamal, cantități, greutăți și valori. N-are
     * nici partener, nici destinație, nici număr de factură, așa că ciorna se
     * numește după fișier și se completează în formular.
     */
    protected function ciornaDinExcel(array $fisier, ?string $cifDeclarant, ?int $userId, array &$rezultat): void
    {
        $nume = basename($fisier['nume']);
        $referinta = pathinfo($nume, PATHINFO_FILENAME);

        if (EtransportDeclaratie::where('referinta_interna', $referinta)->exists()) {
            $rezultat['avertismente'][] = '„' . $nume . '" era deja adus; sărit.';

            return;
        }

        try {
            $citit = $this->fisiere->importa([['nume' => $nume, 'cale' => $fisier['cale']]]);
        } catch (\Exception $e) {
            $rezultat['avertismente'][] = '„' . $nume . '": ' . $e->getMessage();

            return;
        }

        if ($citit['linii'] === []) {
            $rezultat['avertismente'][] = '„' . $nume . '" nu are nicio linie de citit; sărit.';

            return;
        }

        $linii = [];

        foreach ($citit['linii'] as $linie) {
            $linie['scop_operatiune'] = 101;
            $linie['valoare_lei'] = null;
            $linii[] = $linie;
        }

        $declaratie = EtransportDeclaratie::create([
            'stare' => 'ciorna',
            'cif_declarant' => $cifDeclarant,
            'referinta_interna' => $referinta,
            'tip_operatiune' => 10,
            'transportator_tara' => 'RO',
            'loc_start' => ['tip' => 'ptf', 'cod_ptf' => self::PTF_IMPLICIT],
            'loc_final' => ['tip' => 'adresa'],
            'linii' => $linii,
            'valuta' => $citit['antet']['valuta'] ?? 'EUR',
            'fisiere_importate' => [$nume],
            'user_id' => $userId,
        ]);

        $rezultat['ciorne'][] = [
            'id' => $declaratie->id,
            'factura' => $referinta,
            'magazin' => null,
        ];

        $rezultat['avertismente'][] = '„' . $nume . '": Excelul aduce doar marfa;'
            . ' completați partenerul, documentul și locul de descărcare.';
    }

    /**
     * Numărul facturii: din coada numelui („..._10053419.TXT"), cum vine la
     * livrări; la retururi numele nu-l poartă și se citește din antetul
     * fișierelor — „Documents: 10074615 of ...", „Doc number: ..." sau, în
     * distinta D01, „Number ......: 10074615 del ...".
     */
    protected function numarulFacturii(string $stem, array $bucati): string
    {
        if (preg_match('/_(\d+)$/', $stem, $gasit)) {
            return $gasit[1];
        }

        foreach (['T02', 'T01', 'D01'] as $tip) {
            if (!isset($bucati[$tip])) {
                continue;
            }

            if (preg_match('/^\s*(?:Doc number|Documents|Numero documento|Number)\s*\.*\s*:\s*(\d+)\s+(?:of|del)\b/im', $bucati[$tip], $gasit)) {
                return $gasit[1];
            }
        }

        return $stem;
    }

    /**
     * O ciornă dintr-o factură: liniile din T02 (sau din T01, la retururi),
     * destinația din D01.
     *
     * Unele arhive vin fără T02 la anumite facturi: ciorna se face atunci
     * doar cu destinația și factura, iar liniile le pune omul.
     */
    /** @return array<int, EtransportDeclaratie> una singură, ori cele două ale unui retur */
    protected function ciorna(string $factura, array $bucati, ?string $cifDeclarant, ?int $userId): array
    {
        $citit = ['linii' => [], 'antet' => []];
        $tipLinii = isset($bucati['T02']) ? 'T02' : (isset($bucati['T01']) ? 'T01' : null);

        if ($tipLinii !== null) {
            // Raportul se citeste cu parserul lui obisnuit, dintr-un fisier trecator.
            $cale = tempnam(sys_get_temp_dir(), 'etr');
            file_put_contents($cale, $bucati[$tipLinii]);

            try {
                $citit = $this->fisiere->importa([['nume' => $tipLinii . '_' . $factura . '.txt', 'cale' => $cale]]);
            } finally {
                @unlink($cale);
            }

            if ($citit['linii'] === []) {
                throw new EtransportException(
                    ($tipLinii === 'T02' ? 'recapitulația T02' : 'lista pe articole T01') . ' nu are nicio linie de citit.'
                );
            }
        }

        $antet = $citit['antet'];
        // Bifa „marfă retur" e mai tare decât ce se citește din fișiere.
        $retur = $this->marfaRetur || $this->esteRetur($bucati);

        /*
         * Magazinul: la livrari e destinatia din blocul „Destinazione" al
         * distinctei; la retururi e expeditorul, cu codul pe randul „From", iar
         * adresa lui se ia din ultima declaratie a aceluiasi magazin.
         */
        $magazin = $retur
            ? $this->magazinulReturului($bucati)
            : (isset($bucati['D01']) ? $this->destinatia($bucati['D01']) : []);

        // Cand gestiunea e stiuta, denumirea magazinului se ia din ea, nu de la furnizor.
        $gestiune = isset($magazin['magazin_cod'])
            ? ($this->gestiunile()[mb_strtoupper($magazin['magazin_cod'])] ?? null)
            : null;

        if ($gestiune !== null) {
            $magazin['magazin_denumire'] = $gestiune->denumire;
        }

        $dataFacturii = $antet['document_data']
            ?? (isset($bucati['D01']) ? $this->dataDinD01($bucati['D01']) : null);
        $curs = $dataFacturii ? (float) cursBNR($dataFacturii, $antet['valuta'] ?? 'EUR') : 0.0;

        $linii = [];

        foreach ($citit['linii'] as $linie) {
            $linie['scop_operatiune'] = 101;
            $linie['valoare_lei'] = $curs > 0 && $linie['valoare'] !== null
                ? round($linie['valoare'] * $curs, 2)
                : null;

            $linii[] = $linie;
        }

        // Livrare: de la frontiera la magazin (AIC). Retur: de la magazin la frontiera (LIC).
        $ptf = ['tip' => 'ptf', 'cod_ptf' => self::PTF_IMPLICIT];
        $adresaMagazin = ['tip' => 'adresa'] + $magazin;

        $comun = [
            'stare' => 'ciorna',
            'cif_declarant' => $cifDeclarant,
            'transportator_tara' => 'RO',
            'documente' => [[
                'tip' => 20,
                'numar' => $factura,
                'data' => $dataFacturii ?: '',
                'observatii' => '',
            ]],
            'linii' => $linii,
            'valuta' => $antet['valuta'] ?? 'EUR',
            'curs' => $curs ?: null,
            'fisiere_importate' => ['arhiva: factura ' . $factura],
            'user_id' => $userId,
        ];

        $partenerStrain = [
            'partener_tara' => $antet['partener_tara'] ?? 'IT',
            'partener_cod' => $antet['partener_cod'] ?? null,
            'partener_denumire' => $antet['partener_denumire'] ?? null,
        ];

        /*
         * [2026-09-16] Marfa de retur face doua drumuri, deci doua declaratii:
         * din magazin la depozitul transportatorului, pe teritoriul national,
         * si de acolo afara din tara. Fiecare isi are UIT-ul ei.
         */
        if ($this->marfaRetur) {
            $depozit = $this->adresaDepozitului();

            return [
                EtransportDeclaratie::create($comun + [
                    'referinta_interna' => 'Retur ' . $factura . ' (TTN)',
                    'tip_operatiune' => 30,
                    // Pe drumul din tara marfa ramane a clientului: el e si partener.
                    'partener_tara' => 'RO',
                    'partener_cod' => $cifDeclarant,
                    'partener_denumire' => $this->denumireaClientului(),
                    'loc_start' => $adresaMagazin,
                    'loc_final' => ['tip' => 'adresa'] + $depozit,
                ]),
                EtransportDeclaratie::create($comun + $partenerStrain + [
                    'referinta_interna' => 'Retur ' . $factura . ' (LIC)',
                    'tip_operatiune' => 20,
                    // Din depozit pleaca afara din tara; magazinul ramane scris,
                    // ca declaratia sa se stie a carui magazin e.
                    'loc_start' => ['tip' => 'adresa'] + $depozit + $magazin,
                    'loc_final' => $ptf,
                ]),
            ];
        }

        return [
            EtransportDeclaratie::create($comun + $partenerStrain + [
                'referinta_interna' => ($retur ? 'Retur ' : 'Factura ') . $factura,
                'tip_operatiune' => $retur ? 20 : 10,
                'loc_start' => $retur ? $adresaMagazin : $ptf,
                'loc_final' => $retur ? $ptf : $adresaMagazin,
            ]),
        ];
    }

    /**
     * Magazinul din care pleacă marfa de retur.
     *
     * În distinta unui retur el e expeditorul, cu codul pe rândul „From". Când
     * bifa „marfă retur" e pusă pe o arhivă de livrare, rândul acela lipsește:
     * atunci magazinul e cel din blocul „Destinazione", adică tot el, doar că
     * scris ca destinatar al livrării de atunci.
     *
     * @return array<string, mixed>
     */
    protected function magazinulReturului(array $bucati): array
    {
        $cod = $this->magazinulDinD01($bucati['D01'] ?? '');

        if ($cod !== null) {
            return $this->adresaMagazinului($cod);
        }

        $destinatie = isset($bucati['D01']) ? $this->destinatia($bucati['D01']) : [];

        if ($destinatie !== []) {
            return $destinatie;
        }

        // Nici „From", nici „Destinazione": se spune omului si ramane gol.
        return $this->adresaMagazinului(null);
    }

    /**
     * Depozitul transportatorului, de unde marfa de retur pleacă din țară.
     *
     * Se ia din ultima declarație de transport național a clientului: acolo a
     * fost scris ultima oară. Fără una, ciorna rămâne cu locul gol și se spune
     * omului să-l completeze.
     *
     * @return array<string, mixed>
     */
    protected function adresaDepozitului(): array
    {
        $anterioara = EtransportDeclaratie::where('tip_operatiune', 30)
            ->whereNotNull('loc_final')
            ->orderByDesc('id')
            ->first();

        $depozit = $anterioara ? (array) $anterioara->loc_final : [];

        // Magazinul de pe declaratia veche n-are ce cauta pe cea noua.
        unset($depozit['tip'], $depozit['magazin_cod'], $depozit['magazin_denumire']);

        if ($depozit === []) {
            $this->note[] = 'nu există nicio declarație de transport național din care să iau adresa'
                . ' depozitului transportatorului; completați-o pe cele două ciorne.';
        }

        return $depozit;
    }

    /** Denumirea clientului, partener pe drumul dinăuntrul țării. */
    protected function denumireaClientului(): ?string
    {
        $companie = \App\Support\ContextCompanie::curenta();

        return $companie ? optional(\App\Models\Company::find($companie))->denumire : null;
    }

    /**
     * Retur, nu livrare: distinta e o notă de credit, sau liniile vin din
     * lista T01 în lipsa recapitulației T02 (așa vin arhivele „RESO").
     */
    protected function esteRetur(array $bucati): bool
    {
        if (isset($bucati['D01']) && preg_match('/CREDIT\s+NOTE/i', $bucati['D01'])) {
            return true;
        }

        return isset($bucati['T01']) && !isset($bucati['T02']);
    }

    /**
     * Codul magazinului care trimite returul, de pe rândul distinctei:
     * „From  S.C. EMPORIO COM SRL                     NEG0001474".
     */
    protected function magazinulDinD01(string $continut): ?string
    {
        if (preg_match('/^\s*From\s+.+?\s{2,}(NEG\w+)/im', $continut, $gasit)) {
            return mb_strtoupper($gasit[1]);
        }

        return null;
    }

    /**
     * Adresa magazinului, din ultima declarație care l-a avut ca destinație
     * (sau ca plecare, la un retur anterior). Fără una, ciorna pornește doar
     * cu codul și denumirea, iar adresa o pune omul.
     *
     * @return array<string, mixed>
     */
    protected function adresaMagazinului(?string $cod): array
    {
        if ($cod === null) {
            $this->note[] = 'distinta nu spune de la ce magazin pleacă returul; completați adresa de plecare.';

            return [];
        }

        $anterioara = EtransportDeclaratie::where(function ($q) use ($cod) {
            $q->where('loc_final->magazin_cod', $cod)->orWhere('loc_start->magazin_cod', $cod);
        })->orderByDesc('id')->first();

        if ($anterioara === null) {
            $this->note[] = 'magazinul ' . $cod . ' nu are nicio declarație anterioară din care să-i iau adresa;'
                . ' completați adresa de plecare.';

            return ['magazin_cod' => $cod];
        }

        $adresa = $anterioara->loc_magazin;
        unset($adresa['tip']);

        return ['magazin_cod' => $cod] + $adresa;
    }

    /** Gestiunile companiei curente, pe codul furnizorului (NEG*). */
    protected function gestiunile()
    {
        if ($this->gestiuni === null) {
            $this->gestiuni = EtransportGestiune::peCodFurnizor();
        }

        return $this->gestiuni;
    }

    /**
     * Data facturii din antetul distinctei D01, când T02 lipsește:
     * „Number ......:   10053419     del  3/07/2026".
     */
    protected function dataDinD01(string $continut): ?string
    {
        if (preg_match('/(?:Number|Numero)\s*\.*\s*:\s*\d+\s+del\s+(\d{1,2})\/(\d{1,2})\/(\d{4})/i', $continut, $gasit)) {
            return sprintf('%04d-%02d-%02d', $gasit[3], $gasit[2], $gasit[1]);
        }

        return null;
    }

    /**
     * Destinația finală, din antetul distinctei D01.
     *
     * Blocul arată așa, pe trei rânduri:
     *
     *   Destinazione.:  0029818 007 S.C. EMPORIO COM SRL MAGAZIN TERRAN
     *   NEG0000548      BD GEN GH MAGHERU, NR 33, SECTOR 1,
     *                   000000     BUCURESTI     RO
     *
     * Primul rând poartă denumirea magazinului, al doilea codul lui și strada,
     * al treilea orașul — din care se deduce județul.
     *
     * @return array<string, mixed>
     */
    protected function destinatia(string $continut): array
    {
        $randuri = preg_split('/\r\n|\r|\n/', $continut);

        foreach ($randuri as $index => $rand) {
            /*
             * Distinta are doua coloane pe acelasi rand: destinatia in stanga,
             * scadentele si sconturile in dreapta, despartite de spatii multe.
             * Se ia doar coloana din stanga — taiata la 3+ spatii.
             */
            if (!preg_match('/Destinazione\.*\s*:\s*\d+\s+\d+\s+(.+?)(?:\s{3,}|\s*$)/i', $rand, $gasit)) {
                continue;
            }

            $destinatie = ['magazin_denumire' => trim($gasit[1])];
            $oras = '/^\s*(\d{5,6})?\s+(.+?)\s{2,}RO(?:\s|$)/i';

            /*
             * Randul urmator: codul magazinului si strada. Depozitele nu au
             * cod NEG — ramane doar strada, tot in coloana din stanga.
             */
            if (preg_match('/^\s*(NEG\w+)\s{2,}(.+?)(?:\s{3,}|\s*$)/i', $randuri[$index + 1] ?? '', $gasit)) {
                $destinatie['magazin_cod'] = $gasit[1];
                $destinatie['strada'] = trim($gasit[2], ' ,');
            } elseif (!preg_match($oras, $randuri[$index + 1] ?? '')
                && preg_match('/^\s+(\S.*?)(?:\s{3,}|\s*$)/', $randuri[$index + 1] ?? '', $gasit)) {
                $destinatie['strada'] = trim($gasit[1], ' ,');
            }

            // Apoi orasul: "000000     BUCURESTI     RO".
            foreach ([$index + 2, $index + 1] as $randOras) {
                if (!preg_match($oras, $randuri[$randOras] ?? '', $gasit)) {
                    continue;
                }

                $destinatie['localitate'] = trim($gasit[2]);

                $judet = self::ORASE[strtoupper(trim($gasit[2]))] ?? null;

                if ($judet !== null) {
                    $destinatie['cod_judet'] = $judet;
                }

                if (!empty($gasit[1]) && (int) $gasit[1] !== 0) {
                    $destinatie['cod_postal'] = $gasit[1];
                }

                break;
            }

            return $destinatie;
        }

        return [];
    }
}
