<?php

namespace App\Services\Anaf\Declaratii;

/**
 * Identifica tipul declaratiei si metadatele (CUI, denumire, perioada) din XML-ul ANAF.
 *
 * Ordinea de detectie:
 *   1. namespace-ul declaratiei — "mfp:anaf:dgti:<cod>:declaratie:vN" contine chiar
 *      codul folosit de DUKIntegrator; e sursa cea mai sigura, pentru ca radacinile
 *      sunt inconsecvente (D110 -> "D110", D100 -> "declaratie100", A4200 -> "mReg");
 *   2. numele elementului radacina, pentru exceptiile care nu respecta nicio regula;
 *   3. regulile generice (declaratieNNN -> DNNN, bilantNNNN -> SNNNN, litera+cifre).
 *
 * Rezultatul e validat mereu fata de TIPURI — lista validatoarelor instalate
 * (dist/lib/*Validator.jar), actualizabila cu `php artisan anaf:duk-update`.
 */
class DeclaratieXml
{
    /** Tipurile suportate de DUKIntegrator 1.4.18.3.3. */
    public const TIPURI = [
        'A4200', 'A4201', 'A4202', 'A4203', 'B230', 'B900', 'C168', 'C310', 'C801', 'C802',
        'D017', 'D085', 'D092', 'D10', 'D100', 'D101', 'D101G', 'D104', 'D106', 'D107',
        'D108', 'D110', 'D112', 'D114', 'D119', 'D120', 'D130', 'D163', 'D169', 'D169n',
        'D177', 'D179', 'D180', 'D200', 'D201', 'D204', 'D205', 'D207', 'D208', 'D212',
        'D213', 'D214', 'D216', 'D220', 'D221', 'D223', 'D230', 'D300', 'D301', 'D307',
        'D311', 'D318', 'D390', 'D392', 'D393', 'D394', 'D395', 'D397', 'D398', 'D399',
        'D401', 'D402', 'D403', 'D406', 'D407', 'D5', 'D6', 'D600', 'D603', 'D7',
        'D700', 'D710', 'D8', 'D9', 'DAC6', 'F3000', 'F4101', 'F4102', 'F4103', 'F4105',
        'F4109', 'F7000', 'L153', 'M500', 'N012', 'N014', 'P1000', 'P2000', 'P4000', 'P5000',
        'R404', 'R405', 'S1001', 'S1002', 'S1003', 'S1004', 'S1005', 'S1006', 'S1007', 'S1008',
        'S1009', 'S1010', 'S1011', 'S1012', 'S1013', 'S1014', 'S1015', 'S1016', 'S1017', 'S1018',
        'S1019', 'S1020', 'S1021', 'S1022', 'S1023', 'S1024', 'S1025', 'S1026', 'S1027', 'S1028',
        'S1029', 'S1030', 'S1031', 'S1032', 'S1033', 'S1034', 'S1035', 'S1036', 'S1037', 'S1038',
        'S1039', 'S1040', 'S1041', 'S1042', 'S1043', 'S1044', 'S1045', 'S1046', 'S1047', 'S1048',
        'S1049', 'S1050', 'S1051', 'S1052', 'S1053', 'S1054', 'S1055', 'S1056', 'S1057', 'S1058',
        'S1059', 'S1060', 'S1061', 'S1070', 'S1072', 'S1073', 'S1074', 'S1075', 'S1076', 'S1077',
        'S1078', 'S1100', 'S1110', 'S1120', 'S1121', 'S1122', 'S1123', 'S1124', 'S1125', 'S1126',
        'S1127', 'S1128', 'T100', 'T101',
    ];

    /** Câte elemente se străbat cel mult căutând identificarea sau perioada. */
    protected const ELEMENTE_CAUTATE = 2000;

    /** Radacini care nu respecta nicio regula generica (cheile sunt lowercase). */
    protected const RADACINI = [
        'auditfile' => 'D406',      // SAF-T
        'declaratieunica' => 'D112',
        'declaratiad5' => 'D5',
        'declaratiet100' => 'T100',
        'declaratiet101' => 'T101',
        'patrimoniu' => 'P2000',
        'mreg' => 'A4200',          // aparate de marcat
    ];

    /**
     * Radacini folosite de mai multe declaratii — tipul poate fi stabilit doar
     * din namespace ("msj" e comun pentru A4201, A4202 si A4203).
     */
    protected const RADACINI_AMBIGUE = ['msj'];

    /**
     * Atributele in care declaratiile ANAF tin codul fiscal al contribuabilului,
     * in ordinea in care sunt cautate. Numele sunt luate din validatoarele
     * DUKIntegrator (dist/lib/*Validator.jar), nu ghicite.
     *
     * Sunt lasate dinadins afara atributele altor persoane din declaratie:
     * "cif_audi" (auditor), "cif_intocmit" (cel care a completat), "cifR"
     * (reprezentant), "cif_i" (intermediar), "cuiP"/"cifP" din D394 (partener).
     * La fel si falsii prieteni "cifra", "cifre", "cifra_afaceri".
     */
    protected const ATRIBUTE_CUI = [
        'cui', 'cif',                                   // aproape toate declaratiile
        'cuiplatitor', 'cod_fiscal', 'den_cui',         // variante mai vechi
        'cif_c', 'cifcontribuabil',                     // D200, D201, D213, D214, D230, D600
        'cif_entitate', 'cifentitate',                  // B230, D200, D212, D230
        'cif_declarant',                                // D114
        'cif_uat', 'cui_primarie', 'cui_institutie', 'cif_institutie', // B900, D100, L153, M500, P1000, P2000
        'cif_operator',                                 // S1001
        'cuipi',                                        // C310, F4102
    ];

    /**
     * Atributele cu denumirea contribuabilului. Se citesc din aceeasi sectiune
     * din care a venit codul fiscal, deci "den" inseamna aici denumirea firmei,
     * nu a vreunui partener.
     */
    protected const ATRIBUTE_DENUMIRE = [
        'den_firma', 'denumire', 'den',                 // D394, D300, D112
        'nume_ip', 'denplatitor', 'den_soc',            // variante mai vechi, C801
        'den_entitate', 'denentitate',                  // B230, D200, D212, D230
        'den_declarant',                                // D114
        'den_uat', 'den_institutie', 'denumire_firma',  // B900, D100, L153, M500, P2000
        'den_operator', 'den_solicitant',               // S1001, C802
        'denpi',                                        // C310, F4102
    ];

    public function analizeaza(string $caleXml): array
    {
        if (!is_file($caleXml)) {
            throw new DeclaratieException('Fișierul XML nu există: ' . $caleXml);
        }

        $anterior = libxml_use_internal_errors(true);
        $xml = simplexml_load_file($caleXml);
        libxml_use_internal_errors($anterior);

        if ($xml === false) {
            throw new DeclaratieException('Fișierul nu este un XML valid.');
        }

        $radacina = $xml->getName();
        $atribute = $this->atributeleIdentificarii($xml);

        $cui = $this->primul($atribute, self::ATRIBUTE_CUI);
        $denumire = $this->primul($atribute, self::ATRIBUTE_DENUMIRE);

        // Declaratiile mai noi (ex. D406/SAF-T) tin identificarea in elemente imbricate.
        if ($cui === null || $denumire === null) {
            $antet = $this->antetul($xml);

            if ($cui === null) {
                $cui = $this->cautaElement(
                    $antet,
                    ['taxregistrationnumber', 'registrationnumber', 'companyid', 'cui', 'cif']
                );
            }

            if ($denumire === null) {
                $denumire = $this->cautaElement($antet, ['name', 'companyname', 'denumire']);
            }
        }

        $luna = $this->numar($this->primul($atribute, ['luna_r', 'luna', 'perioada_r']));
        $anul = $this->numar($this->primul($atribute, ['an_r', 'an', 'anul']));

        /*
         * D406/SAF-T ține perioada în antet, în elemente, nu în atribute. Ea
         * contează: validatorul ANAF are câte o versiune de reguli și de
         * nomenclatoare pentru fiecare perioadă, iar denumirea fișierului din
         * arhiva clientului o poartă și ea.
         */
        $tipPerioada = null;

        if ($luna === null || $anul === null) {
            $antet = $this->antetul($xml);

            // <SelectionStartDate>2026-06-01</SelectionStartDate>
            $inceput = $this->cautaElement($antet, ['selectionstartdate']);

            if ($inceput !== null && preg_match('/^(\d{4})-(\d{2})/', $inceput, $m)) {
                $anul = $anul ?? (int) $m[1];
                $luna = $luna ?? (int) $m[2];
            }

            if ($luna === null) {
                $luna = $this->numar($this->cautaElement($antet, ['periodstart', 'period']));
            }

            if ($anul === null) {
                $anul = $this->numar($this->cautaElement($antet, ['periodstartyear', 'fiscalyear']));
            }

            $tipPerioada = $this->tipPerioada($this->cautaElement($antet, ['headercomment']));
        }

        return [
            'tip' => $this->tipDinRadacina($radacina, $this->namespaceImplicit($xml)),
            'radacina' => $radacina,
            'cui' => $cui !== null ? ltrim(preg_replace('/^RO/i', '', trim($cui))) : null,
            'den_firma' => $denumire,
            'luna' => $luna,
            'anul' => $anul,
            'perioada_tip' => $tipPerioada,
            'rectificativa' => $this->primul($atribute, ['d_rec', 'rectificativa']) === '1',
        ];
    }

    /**
     * Intregeste identificarea din numele fisierului, cand XML-ul n-a spus-o.
     *
     * Unele programe de contabilitate scot PDF-uri (mai ales D406/SAF-T) al
     * caror XML atasat nu tine CUI-ul si perioada unde le cauta analiza — dar
     * numele fisierului le poarta: „FIRMA_D406_47587115_31-07-2026_Inf-semnat.pdf".
     * Fara CUI, declaratia nu se leaga de firma, recipisa nu se potriveste, iar
     * omul vede liniute in loc de firma.
     */
    public function completeazaDinNume(array $meta, string $numeFisier): array
    {
        $tip = preg_quote((string) ($meta['tip'] ?? ''), '/');

        if (empty($meta['cui']) && $tip !== ''
            && preg_match('/_' . $tip . '_(\d{2,10})(?:_|\.|$)/i', $numeFisier, $gasit)) {
            $meta['cui'] = $gasit[1];
        }

        // 31-07-2026 sau 31.07.2026: ziua din urma a perioadei raportate.
        if ((empty($meta['luna']) || empty($meta['anul']))
            && preg_match('/(\d{1,2})[-.](\d{1,2})[-.](\d{4})/', $numeFisier, $gasit)) {
            $meta['luna'] = $meta['luna'] ?? (int) $gasit[2];
            $meta['anul'] = $meta['anul'] ?? (int) $gasit[3];
        }

        return $meta;
    }

    public function tipDinRadacina(string $radacina, ?string $namespace = null): string
    {
        $tip = $this->tipDinNamespace($namespace);

        if ($tip !== null) {
            return $tip;
        }

        $cheie = strtolower($radacina);

        if (in_array($cheie, self::RADACINI_AMBIGUE, true)) {
            throw new DeclaratieException(
                'Elementul rădăcină "' . $radacina . '" este folosit de mai multe declarații, '
                . 'iar fișierul nu are namespace-ul ANAF care să o identifice.'
            );
        }

        if (isset(self::RADACINI[$cheie])) {
            return self::RADACINI[$cheie];
        }

        if ($cheie === 'form1') {
            throw new DeclaratieException('Fișierul este în format pentru import în softA, nu este o declarație validă.');
        }

        // declaratie100 -> D100, declaratie301 -> D301
        if (preg_match('/^declaratie(\d+)$/i', $radacina, $m)) {
            $tip = $this->canonic('D' . $m[1]);
        // Bilant1046 -> S1046
        } elseif (preg_match('/^bilant(\d+)$/i', $radacina, $m)) {
            $tip = $this->canonic('S' . $m[1]);
        // d107, f4101, C801 — deja in formatul ANAF
        } elseif (preg_match('/^[a-z]+\d+[a-z]*$/i', $radacina)) {
            $tip = $this->canonic($radacina);
        }

        if ($tip === null) {
            throw new DeclaratieException(
                'Tip de declarație nesuportat (element rădăcină: ' . $radacina . ').'
                . ' Rulați `php artisan anaf:duk-update` dacă declarația este nouă.'
            );
        }

        return $tip;
    }

    /** "mfp:anaf:dgti:d300:declaratie:v10" -> D300 (d406t -> D406). */
    protected function tipDinNamespace(?string $namespace): ?string
    {
        if ($namespace === null || !preg_match('/^mfp:anaf:[a-z]*:?([a-z0-9]+):/i', $namespace, $m)) {
            return null;
        }

        $cod = $m[1];

        return $this->canonic($cod) ?? $this->canonic(rtrim($cod, 'tT'));
    }

    /** Numele oficial al tipului, daca e suportat de DUKIntegrator. */
    protected function canonic(string $tip): ?string
    {
        static $indice = null;

        if ($indice === null) {
            $indice = [];

            foreach (self::TIPURI as $suportat) {
                $indice[strtoupper($suportat)] = $suportat;
            }
        }

        return $indice[strtoupper($tip)] ?? null;
    }

    protected function namespaceImplicit(\SimpleXMLElement $xml): ?string
    {
        $spatii = $xml->getDocNamespaces(false);

        return $spatii[''] ?? null;
    }

    /**
     * Atributele sectiunii care identifica contribuabilul, peste cele ale radacinii.
     *
     * Unele declaratii tin identificarea chiar pe elementul radacina (D300,
     * D394), altele pe prima sectiune (D112 o tine pe <angajator>, impreuna cu
     * luna si anul). Se cauta deci in ordinea documentului prima sectiune cu un
     * atribut de cod fiscal cunoscut si se citeste tot de acolo — denumirea si
     * perioada trebuie luate din aceeasi sectiune cu CUI-ul, altfel s-ar
     * amesteca datele contribuabilului cu ale altcuiva din declaratie.
     *
     * @return array<string, string> atributele, cu numele scrise cu litere mici
     */
    protected function atributeleIdentificarii(\SimpleXMLElement $xml): array
    {
        $radacina = dom_import_simplexml($xml);
        $aleRadacinii = $this->atributele($radacina);

        if ($this->primul($aleRadacinii, self::ATRIBUTE_CUI) !== null) {
            return $aleRadacinii;
        }

        foreach ($this->sectiuniDeSus($radacina) as $element) {
            $atribute = $this->atributele($element);

            if ($this->primul($atribute, self::ATRIBUTE_CUI) !== null) {
                return array_merge($aleRadacinii, $atribute);
            }
        }

        return $aleRadacinii;
    }

    /**
     * Primele sectiuni ale documentului, in latime.
     *
     * Identificarea sta mereu sus — pe radacina sau pe prima sectiune — deci nu
     * are rost sa fie citit tot fisierul. Cautarea e marginita dinadins: un
     * D406/SAF-T are sute de mii de elemente, iar parcurgerea lui intreaga ar
     * tine cererea in loc minute bune, fara sa gaseasca nimic in plus.
     *
     * Se merge din frate in frate, nu prin getElementsByTagName(): acela
     * intoarce o lista vie, a carei parcurgere costa patratic.
     *
     * @return \Generator<\DOMElement>
     */
    protected function sectiuniDeSus(\DOMElement $radacina, int $adancimeMaxima = 3, int $maximElemente = 300): \Generator
    {
        $nivel = [$radacina];
        $vazute = 0;

        for ($adancime = 0; $adancime < $adancimeMaxima && $nivel !== []; $adancime++) {
            $urmator = [];

            foreach ($nivel as $parinte) {
                for ($copil = $parinte->firstChild; $copil !== null; $copil = $copil->nextSibling) {
                    if (!$copil instanceof \DOMElement) {
                        continue;
                    }

                    yield $copil;

                    if (++$vazute >= $maximElemente) {
                        return;
                    }

                    $urmator[] = $copil;
                }
            }

            $nivel = $urmator;
        }
    }

    /** @return array<string, string> */
    protected function atributele(\DOMElement $element): array
    {
        $atribute = [];

        foreach ($element->attributes as $atribut) {
            // Numele local: unele declaratii scriu atributele cu prefix de namespace.
            $atribute[strtolower($atribut->localName ?: $atribut->nodeName)] = $atribut->nodeValue;
        }

        return $atribute;
    }

    /**
     * Felul perioadei raportate, asa cum il asteapta validatorul ANAF.
     *
     * D406 il tine in <HeaderComment>: L (lunar), T (trimestrial), A (anual).
     * Alte valori se lasa deoparte — validatorul le-ar respinge oricum, iar noi
     * am ghici in locul contabilului.
     */
    protected function tipPerioada(?string $valoare): ?string
    {
        $tip = strtoupper(trim((string) $valoare));

        return in_array($tip, ['L', 'T', 'A'], true) ? $tip : null;
    }

    /**
     * Antetul declaratiei, cand exista.
     *
     * La D406 tot ce o identifica — firma si perioada — sta in <Header>, iar
     * restul fisierului are sute de mii de elemente. Cautarea porneste de acolo.
     *
     * Se umbla prin DOM, pe numele local: SAF-T-ul are namespace implicit
     * (xmlns="mfp:anaf:dgti:d406..."), iar children() al SimpleXML nu intoarce
     * copiii dintr-un namespace implicit — <Header> nu se gasea niciodata, si
     * declaratia intra fara CUI.
     */
    protected function antetul(\SimpleXMLElement $xml): \DOMElement
    {
        $radacina = dom_import_simplexml($xml);

        for ($copil = $radacina->firstChild; $copil !== null; $copil = $copil->nextSibling) {
            if ($copil instanceof \DOMElement && strcasecmp($copil->localName, 'header') === 0) {
                return $copil;
            }
        }

        return $radacina;
    }

    /**
     * Prima valoare non-goala a unui element cu unul dintre numele cautate.
     *
     * Numele se compara pe partea locala, fara namespace. Cautarea are un
     * buget de elemente: intr-un SAF-T, o cautare fara raspuns ar strabate tot
     * fisierul si ar tine cererea in loc minute bune.
     */
    private function cautaElement(
        \DOMElement $element,
        array $nume,
        int $adancime = 0,
        ?\stdClass $buget = null
    ): ?string {
        if ($adancime > 4) {
            return null;
        }

        $buget = $buget ?: (object) ['ramas' => self::ELEMENTE_CAUTATE];

        for ($copil = $element->firstChild; $copil !== null; $copil = $copil->nextSibling) {
            if (!$copil instanceof \DOMElement) {
                continue;
            }

            if (--$buget->ramas < 0) {
                return null;
            }

            if (in_array(strtolower($copil->localName), $nume, true)) {
                $valoare = trim($copil->textContent);

                if ($valoare !== '') {
                    return $valoare;
                }
            }

            $gasit = $this->cautaElement($copil, $nume, $adancime + 1, $buget);

            if ($gasit !== null) {
                return $gasit;
            }
        }

        return null;
    }

    private function primul(array $atribute, array $chei): ?string
    {
        foreach ($chei as $cheie) {
            if (isset($atribute[$cheie]) && $atribute[$cheie] !== '') {
                return $atribute[$cheie];
            }
        }

        return null;
    }

    private function numar(?string $valoare): ?int
    {
        return $valoare !== null && is_numeric($valoare) ? (int) $valoare : null;
    }
}
