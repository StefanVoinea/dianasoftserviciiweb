<?php

/*
 * Regulile decontului de TVA, scoase din aplicatia ANAF „D300_2026".
 *
 * De ce exista: ANAF a publicat o aplicatie care scoate D300 dintr-un SAF-T,
 * dar toata prelucrarea ei sta intr-un singur „main" cu fereastra Swing, iar
 * exportul se face din butoane. Nu are cum sa fie chemata de pe un server, asa
 * cum e chemat DUKIntegrator. Regulile ei, insa, sunt curat asezate: 61 de
 * multimi de coduri TVA si cateva sute de adunari peste randurile decontului.
 *
 * Fisierul acesta le muta in PHP, mecanic. Nu se scrie nimic de mana: ce iese
 * aici este exact ce face aplicatia ANAF, linie cu linie, ca sa se poata da
 * socoteala pentru fiecare cifra din decont si sa se poata reface cand ANAF
 * scoate versiunea urmatoare.
 *
 * Izvoarele sunt cinci, toate ale ANAF (vezi tools/d300/README.md pentru cum se
 * scot si se decompileaza):
 *
 *   --parsing      Parsing3.java     socoteala decontului
 *   --raport       raport2026.jrxml  la ce rand de pe formular cade fiecare camp
 *   --pdf          Pdf_vN.java       ce atribut din XML tine fiecare rand
 *   --validator    Declaratie300.java  ce randuri se aduna in totaluri
 *   --nomenclator  RO_SAFT_...xlsx   ce inseamna fiecare cod de taxa
 *
 * Ce nu se genereaza: citirea XML-ului si scrierea declaratiei. Ele sunt scrise
 * de mana, in DecontDinSaft si DecontXml, pentru ca acolo aplicatia ANAF tine si
 * fereastra, si raportul Jasper.
 */

if (PHP_SAPI !== 'cli') {
    exit("Se ruleaza din linia de comanda.\n");
}

// Pentru citirea documentatiei SAF-T (xlsx) se foloseste PhpSpreadsheet.
require dirname(__DIR__, 2) . '/vendor/autoload.php';

/*
 * Izvoarele. Primul e de nelipsit; celelalte adauga cate ceva, si se spune la
 * sfarsit ce a iesit din fiecare.
 */
$izvoare = ['parsing' => null, 'raport' => null, 'pdf' => null, 'validator' => null, 'nomenclator' => null, 'formular' => null];

foreach (array_slice($argv, 1) as $arg) {
    if (preg_match('/^--(\w+)=(.+)$/', $arg, $p) === 1 && array_key_exists($p[1], $izvoare)) {
        $izvoare[$p[1]] = $p[2];

        continue;
    }

    // Vechiul apel, cu pozitii: primul e Parsing3.java, al doilea raportul.
    if ($izvoare['parsing'] === null) {
        $izvoare['parsing'] = $arg;
    } elseif ($izvoare['raport'] === null) {
        $izvoare['raport'] = $arg;
    }
}

if (!$izvoare['parsing'] || !is_file($izvoare['parsing'])) {
    exit(
        "Apel: php tools/d300/genereaza.php --parsing=<Parsing3.java> [--raport=<raport2026.jrxml>]\n"
        . "                                  [--pdf=<Pdf_vN.java>] [--validator=<Declaratie300.java>]\n"
        . "                                  [--nomenclator=<RO_SAFT_SchemaDefCod.xlsx>]\n"
        . "                                  [--formular=<D300_soft_A.pdf>]\n\n"
        . "Vezi tools/d300/README.md pentru de unde se iau.\n"
    );
}

$catre = dirname(__DIR__, 2) . '/app/Services/Anaf/Declaratii/D300';

if (!is_dir($catre) && !mkdir($catre, 0775, true) && !is_dir($catre)) {
    exit("Nu am putut face dosarul $catre\n");
}

$generator = new GeneratorD300(
    file($izvoare['parsing'], FILE_IGNORE_NEW_LINES),
    basename($izvoare['parsing'])
);

$scrise = [];

$scrise['CoduriD300.php'] = $generator->coduri();
$scrise['ReguliD300.php'] = $generator->reguli();

/*
 * Randurile decontului: unde cade fiecare camp pe formular si sub ce atribut
 * intra in XML. Cere si raportul (campul -> randul), si generatorul de PDF
 * (randul -> atributul si denumirea lui).
 */
if ($izvoare['raport'] && is_file($izvoare['raport'])) {
    if (!$izvoare['pdf'] || !is_file($izvoare['pdf'])) {
        exit("Pentru randurile decontului trebuie si --pdf=<Pdf_vN.java>; vezi README.\n");
    }

    $scrise['RanduriD300.php'] = $generator->randuri(
        file_get_contents($izvoare['raport']),
        file_get_contents($izvoare['pdf']),
        $izvoare['validator'] && is_file($izvoare['validator']) ? file_get_contents($izvoare['validator']) : '',
        basename($izvoare['raport']),
        basename($izvoare['pdf'])
    );
}

// Codurile TVA, cu descrierea si cota lor, din documentatia SAF-T.
if ($izvoare['nomenclator'] && is_file($izvoare['nomenclator'])) {
    $scrise['NomenclatorTva.php'] = $generator->nomenclator($izvoare['nomenclator']);
}

/*
 * Formularul inteligent al ANAF („soft A"): unde sta fiecare rand si fiecare
 * camp in datele lui, ca sa se poata scrie un fisier de incarcat in el.
 */
if ($izvoare['formular'] && is_file($izvoare['formular'])) {
    $scrise['FormularD300.php'] = $generator->formular(
        file_get_contents($izvoare['formular']),
        basename($izvoare['formular'])
    );
}

echo "Gata:\n";

foreach ($scrise as $nume => $continut) {
    file_put_contents($catre . '/' . $nume, $continut);

    echo '  ' . $catre . '/' . $nume . "\n";
}

/**
 * Trecerea din java in php.
 *
 * Textul decompilat e simplu: „if" peste multimi si steaguri, adunari peste
 * randuri, si cate o variabila de o clipa („rezultat") pentru semnul sumei.
 * Nimic altceva — de aceea trecerea se poate face cu increderea ca nu se pierde
 * nimic pe drum.
 */
class GeneratorD300
{
    /** Steagurile liniei, asa cum le numeste aplicatia ANAF. */
    protected const STEAGURI = [
        'Not442' => '$not442',
        'Not3532' => '$not3532',
        'Is4426' => '$is4426',
        'Is4427' => '$is4427',
        'Is4428' => '$is4428',
        'Is35326' => '$is35326',
        'Is35327' => '$is35327',
        'Is35328' => '$is35328',
        'IsBASE' => '$areBaza',
    ];

    /** Ce tine de tranzactie, nu de linie: ramane in starea purtata mai departe. */
    protected const IN_STARE = [
        'Conditie1' => "\$s['conditie1']",
        'Conditie2' => "\$s['conditie2']",
        'Conditie3' => "\$s['conditie3']",
        'IsTA' => "\$s['isTa']",
        'IsCod1' => "\$s['isCod1']",
        'CA_DA' => "\$s['CA_DA']",
        'OpeningCreditBalance' => "\$s['OpeningCreditBalance']",
        'OpeningDebitBalance' => "\$s['OpeningDebitBalance']",
        'FinalPayment4423' => "\$s['FinalPayment4423']",
    ];

    /** Sumele liniei: ele vin ca argumente, nu din stare. */
    protected const ALE_LINIEI = [
        'CA' => '$ca',
        'DA' => '$da',
        'TA' => '$ta',
        'BASE' => '$baza',
        'TaxCode' => '$cod',
    ];

    /** @var array<int, string> */
    protected $java;

    /** @var string */
    protected $sursa;

    /** @var array<string, array<int, string>> */
    protected $seturi = [];

    /** Numele variabilei de o clipa din blocul in lucru. */
    protected $temporara;

    public function __construct(array $java, string $sursa)
    {
        $this->java = $java;
        $this->sursa = $sursa;
        $this->seturi = $this->citesteSeturile();
    }

    /** Cele 61 de multimi de coduri TVA, ca liste de cautat cu isset. */
    public function coduri(): string
    {
        $php = $this->antet('CoduriD300', $this->descriereCoduri());

        foreach ($this->seturi as $nume => $coduri) {
            $php .= "    /** " . count($coduri) . " coduri. */\n";
            $php .= '    public const ' . strtoupper($nume) . " = [\n";

            // Cheile sunt codurile: cautarea se face cu isset, nu cu in_array.
            foreach (array_chunk($coduri, 6) as $bucata) {
                $php .= '        ' . implode(', ', array_map(function ($cod) {
                    return "'$cod' => true";
                }, $bucata)) . ",\n";
            }

            $php .= "    ];\n\n";
        }

        return rtrim($php, "\n") . "\n}\n";
    }

    /** Adunarile peste randurile decontului. */
    public function reguli(): string
    {
        $php = $this->antet('ReguliD300', $this->descriereReguli());

        $metode = $this->metodaCodTaxa() . "\n" . $this->metodaSumaTaxa() . "\n" . $this->metodaFinal();

        // Randurile atinse si codurile din switch se strang din chiar codul
        // iesit, ca sa nu ramana vreunul pe dinafara cand ANAF mai adauga unul.
        $php .= $this->listaRandurilor($metode) . "\n"
            . $this->listaCodurilorFaraTaxa($metode) . "\n"
            . $metode;

        return rtrim($php, "\n") . "\n}\n";
    }

    /**
     * Randurile decontului: unde cade fiecare camp si sub ce nume intra in XML.
     *
     * Se leaga doua izvoare ale ANAF:
     *
     *   - raportul (raport2026.jrxml) spune la ce rand de pe formular se
     *     tipareste fiecare camp: la stanga randului sta numarul lui („5",
     *     „12.1"), iar campul cu valoarea sta pe aceeasi inaltime, mai la
     *     dreapta. Se potrivesc dupa inaltime, si numai in cuprinsul aceleiasi
     *     benzi — altfel randuri de pe pagini deosebite se potrivesc intre ele;
     *   - generatorul de PDF (Pdf_vN din D300Pdf.jar) spune ce atribut din XML
     *     tine fiecare rand si cum se cheama randul pe formular.
     *
     * Legatura aceasta e de neocolit, fiindca numarul randului si numarul din
     * numele atributului nu sunt acelasi lucru: randul 17 sta in „R64", randul
     * 19 in „R17". Cand ANAF a mai adaugat randuri la mijloc, a pastrat numele
     * vechi si le-a dat celor noi nume din coada.
     *
     * Un camp poate fi tiparit in doua locuri — o data la taxa colectata, o
     * data la cea deductibila, unde valoarea se copiaza. Se ia primul.
     */
    public function randuri(string $raport, string $pdf, string $validator, string $sursaRaport, string $sursaPdf): string
    {
        $formular = $this->randurileFormularului($pdf);
        $campuri = $this->campurileRaportului($raport);

        $php = "<?php\n\nnamespace App\Services\Anaf\Declaratii\D300;\n\n"
            . "/*\n * FIȘIER GENERAT — nu se scrie de mână.\n *\n"
            . " * Scos din raportul ANAF $sursaRaport și din generatorul lui de PDF\n"
            . " * ($sursaPdf), cu tools/d300/genereaza.php.\n */\n\n"
            . "/**\n * Randurile decontului: numarul de pe formular, denumirea si atributul\n"
            . " * din XML-ul D300.\n *\n"
            . " * Numarul randului nu e acelasi lucru cu numarul din numele atributului:\n"
            . " * randul 17 sta in „R64”, randul 19 in „R17”. Asa a ramas de cand ANAF a\n"
            . " * adaugat randuri la mijlocul formularului.\n */\n"
            . "class RanduriD300\n{\n"
            . "    /** Campul din decont => randul de pe formular, atributul din XML, denumirea. */\n"
            . "    public const RANDURI = [\n";

        /*
         * Raportul nu tipareste toate campurile: cele care se copiaza dintr-un
         * rand in altul — randul 20 e randul 5, vazut din partea deducerii — se
         * scriu acolo cu campul celuilalt. Ele lipsesc astfel din legatura, dar
         * declaratia le cere: validatorul ANAF cantareste chiar egalitatea
         * dintre ele („R18_1 = R5_1").
         *
         * Pentru astea se merge pe potrivirea de nume: „RD20_1_TVA" cade la
         * randul 20.1, coloana taxei. Se ia numai daca randul exista pe formular,
         * are coloana ceruta si nu e deja luat de alt camp — asa incat o
         * potrivire gresita sa fie sarita, nu scrisa aiurea.
         */
        foreach ($this->campurileDecontului() as $camp) {
            if (isset($campuri[$camp])) {
                continue;
            }

            $rand = $this->randulDinNume($camp);

            if ($rand !== null && isset($formular[$rand])) {
                $campuri[$camp] = $rand;
            }
        }

        $puse = 0;
        $luate = [];

        foreach ($campuri as $camp => $rand) {
            if (!isset($formular[$rand])) {
                continue;
            }

            $coloana = substr($camp, -5) === '_BAZA' ? 'baza' : 'tva';
            $atribut = $formular[$rand][$coloana];

            if ($atribut === null || isset($luate[$atribut])) {
                continue;
            }

            $luate[$atribut] = $camp;

            $php .= "        '$camp' => ['rand' => '$rand', 'atribut' => '$atribut', 'denumire' => '"
                . str_replace("'", "\'", $formular[$rand]['denumire']) . "'],\n";
            $puse++;
        }

        $php .= "    ];\n\n" . $this->totalurile($validator) . "}\n";

        fwrite(STDERR, "  randuri legate: $puse din " . count($campuri) . " campuri tiparite\n");

        return $php;
    }

    /**
     * Randurile care se aduna din celelalte, dupa regulile validatorului ANAF.
     *
     * Totalurile nu se iau din raport, ci din chiar validatorul care le
     * cantareste (Declaratie300 din D300Validator.jar): el le socoteste in
     * atribute — „R17_1 = R1_1 + R2_1 + …" —, iar o declaratie ale carei
     * totaluri nu ies dupa formula lui e respinsa. Regulile poarta nume: R65 si
     * R66 pentru totalul taxei colectate, R99 si R100 pentru cel al taxei
     * deductibile, si asa mai departe.
     */
    protected function totalurile(string $validator): string
    {
        $totaluri = [];

        if ($validator !== '') {
            // „if (this._R17_1_f != (l = this._R1_1_f + …))" — totalul si termenii
            preg_match_all(
                '/if \(this\._(R[0-9_]+)_f != \(\w+ = ([^;]*?)\)\)/',
                $validator,
                $gasite,
                PREG_SET_ORDER
            );

            // „l = this._R18_1_f + …;" urmat de „if (this._R27_1_f != l)"
            preg_match_all(
                '/\w+ = (this\._R[0-9_]+_f[^;]*?);\s*if \(this\._(R[0-9_]+)_f != \w+\)/',
                $validator,
                $celelalte,
                PREG_SET_ORDER
            );

            foreach ($celelalte as $g) {
                $gasite[] = [$g[0], $g[2], $g[1]];
            }

            foreach ($gasite as $g) {
                $termeni = [];
                preg_match_all('/this\._(R[0-9_]+)_f/', $g[2], $bucati);

                foreach ($bucati[1] as $termen) {
                    $termeni[] = $termen;
                }

                // Suma de control aduna toate randurile; ea se face altfel.
                if (count($termeni) < 2 || count($termeni) > 40 || isset($totaluri[$g[1]])) {
                    continue;
                }

                $totaluri[$g[1]] = $termeni;
            }
        }

        $diferente = $this->diferentele($validator);
        $egalitati = $this->egalitatile($validator);

        $php = "    /**\n     * Randurile care se aduna din celelalte, in atribute.\n     *\n"
            . "     * Formulele sunt ale validatorului ANAF, nu ale noastre: el le\n"
            . "     * cantareste, iar o declaratie care nu iese dupa ele e respinsa.\n     */\n";
        $php .= "    public const TOTALURI = [\n";

        foreach ($totaluri as $atribut => $termeni) {
            $php .= "        '$atribut' => ['" . implode("', '", $termeni) . "'],\n";
        }

        $php .= "    ];\n\n";

        $php .= "    /**\n     * Soldurile de la sfarsit: o scadere taiata la zero.\n     *\n"
            . "     * „R41_2 = max(R37_2 - R40_2, 0)” — tot dupa regulile validatorului:\n"
            . "     * ce iese in plus se cere de la stat, ce iese in minus se plateste.\n     */\n";
        $php .= "    public const DIFERENTE = [\n";

        foreach ($diferente as $atribut => $termeni) {
            $php .= "        '$atribut' => ['" . $termeni[0] . "', '" . $termeni[1] . "'],\n";
        }

        $php .= "    ];\n\n";

        $php .= "    /**\n     * Randurile care trebuie sa fie deopotriva cu altele.\n     *\n"
            . "     * Randul 20 e randul 5 vazut din partea deducerii: aceeasi achizitie,\n"
            . "     * o data la taxa datorata, o data la cea de dedus. Validatorul cere sa\n"
            . "     * fie scrise amandoua, si la fel.\n     */\n";
        $php .= "    public const EGALITATI = [\n";

        foreach ($egalitati as $atribut => $dupa) {
            $php .= "        '$atribut' => '$dupa',\n";
        }

        fwrite(
            STDERR,
            '  totaluri: ' . count($totaluri) . ', diferente: ' . count($diferente)
                . ', egalitati: ' . count($egalitati) . "\n"
        );

        return $php . "    ];\n";
    }

    /**
     * Randurile care se copiaza unul din altul.
     *
     * „R18_1 = R5_1": randul 20 al formularului e randul 5 vazut din partea
     * deducerii. Aplicatia ANAF le tine pe amandoua in datele ei, dar declaratia
     * le cere scrise deopotriva — iar cine le lasa razlete e respins.
     *
     * @return array<string, string>
     */
    protected function egalitatile(string $validator): array
    {
        if ($validator === '') {
            return [];
        }

        preg_match_all(
            '/if \(this\._(R[0-9_]+)_f != this\._(R[0-9_]+)_f\)/',
            $validator,
            $gasite,
            PREG_SET_ORDER
        );

        $egalitati = [];

        foreach ($gasite as $g) {
            $egalitati[$g[1]] = $g[2];
        }

        return $egalitati;
    }

    /**
     * Soldurile de la sfarsitul decontului.
     *
     * Ele nu se aduna, ci se scad si se taie la zero: „R41_2 = max(R37_2 -
     * R40_2, 0)". Asa iese ori suma de recuperat, ori taxa de plata — niciodata
     * amandoua.
     *
     * @return array<string, array<int, string>>
     */
    protected function diferentele(string $validator): array
    {
        if ($validator === '') {
            return [];
        }

        preg_match_all(
            '/this\._(R[0-9_]+)_f != Math\.max\(\w+ = this\._(R[0-9_]+)_f - this\._(R[0-9_]+)_f, 0L\)/',
            $validator,
            $gasite,
            PREG_SET_ORDER
        );

        $diferente = [];

        foreach ($gasite as $g) {
            $diferente[$g[1]] = [$g[2], $g[3]];
        }

        return $diferente;
    }

    /**
     * Randurile formularului, din generatorul de PDF: numar, atribute, denumire.
     *
     * „drawLine" deseneaza randurile cu doua coloane, „drawLine1" pe cele care
     * au numai taxa.
     */
    protected function randurileFormularului(string $pdf): array
    {
        $randuri = [];

        foreach (explode("\n", $pdf) as $linie) {
            $t = trim($linie);

            if (strpos($t, 'this.drawLine') !== 0) {
                continue;
            }

            $bucati = explode('"', $t);

            if (count($bucati) < 4) {
                continue;
            }

            $rand = $bucati[1];
            $denumire = $this->curataDenumirea($bucati[3]);

            preg_match_all('/getValue\("(\w+)"\)/', $t, $campuri);
            $campuri = $campuri[1];

            $doarTaxa = strpos($t, 'this.drawLine1(') === 0 || strpos($t, ', null,') !== false;

            $randuri[$rand] = [
                'baza' => $doarTaxa ? null : ($campuri[0] ?? null),
                'tva' => $doarTaxa ? ($campuri[0] ?? null) : ($campuri[1] ?? null),
                'denumire' => $denumire,
            ];
        }

        return $randuri;
    }

    /** Denumirea randului, asa cum se poate citi. */
    protected function curataDenumirea(string $denumire): string
    {
        // In generatorul ANAF, „|" taie randul pe formular; aici e doar spatiu.
        $denumire = str_replace('|', ' ', $denumire);

        // Diacriticele scrise ca secvente java
        $denumire = preg_replace_callback('/\\u([0-9a-fA-F]{4})/', function ($p) {
            return mb_chr(hexdec($p[1]), 'UTF-8');
        }, $denumire);

        return trim(preg_replace('/\s+/', ' ', $denumire));
    }

    /** Campul din raport => randul de pe formular. */
    protected function campurileRaportului(string $raport): array
    {
        $campuri = [];

        foreach ($this->benzile($raport) as $corp) {
            $elemente = $this->elementeleBenzii($corp);

            $numere = array_filter($elemente, function ($e) {
                return $e['tip'] === 'staticText' && $e['x'] < 60 && $e['w'] <= 30
                    && preg_match('/^\d+(\.\d+)?$/', $e['text']) === 1;
            });

            foreach ($elemente as $e) {
                if ($e['tip'] !== 'textField' || preg_match('/^\$F\{(RD[0-9_A-Z]+)\}$/', $e['text'], $p) !== 1) {
                    continue;
                }

                $numar = $this->numarulDinDreptul($e, $numere);

                if ($numar !== null && !isset($campuri[$p[1]])) {
                    $campuri[$p[1]] = $numar;
                }
            }
        }

        return $campuri;
    }

    /**
     * Numele campurilor din antetul formularului, pe intelesul nostru.
     *
     * Cheia e ce stim noi, valoarea e numele campului din formular. Calea pana
     * la el se afla din chiar formularul ANAF, ca sa nu fie scrisa de mana.
     */
    protected const ANTETUL_FORMULARULUI = [
        // Unde numele se repeta — „den" e si al firmei, si al bancii — se scrie
        // si bucata de cale dinaintea lui.
        'denumire' => 'denumire/den',
        'banca' => 'banca/den',
        'cif' => 'cif',
        'adresa' => 'str',
        'telefon' => 'telefon',
        'fax' => 'fax',
        'email' => 'email',
        'iban' => 'iban',
        'caen' => 'caen',
        'pro_rata' => 'proRata',
        'an' => 'an_r',
        'luna' => 'luna_r',
        'tip_decont' => 'tipDecont',
        'total_plata' => 'totalPlata_A',
        'nr_evid' => 'nr_evid',
        'temei' => 'temeiLegal',
        'prin_reprezentant' => 'd_reprezentant',
        'nume_declarant' => 'nume',
        'prenume_declarant' => 'prenume',
        'functie_declarant' => 'smnFnc',
    ];

    /**
     * Formularul inteligent al ANAF, desfacut in campurile lui.
     *
     * PDF-ul e un formular XFA: datele lui stau intr-un arbore de subformulare,
     * iar fiecare rand al decontului e un subformular („r5", „r12_1") cu doua
     * casute — „c2" pentru baza si „c3" pentru taxa. Un fisier de incarcat in el
     * trebuie sa aiba aceeasi asezare, altfel Acrobat n-are unde pune cifrele.
     *
     * De aceea asezarea nu se scrie de mana: se citeste din chiar PDF-ul pe care
     * il da ANAF.
     */
    public function formular(string $pdf, string $sursa): string
    {
        $sablon = $this->sablonulFormularului($pdf);

        if ($sablon === null) {
            exit("Nu am gasit formularul XFA in $sursa.\n");
        }

        $campuri = $this->campurileFormularului($sablon);

        $randuri = [];
        $antet = [];

        foreach ($campuri as $cale => $liber) {
            $bucati = explode('/', $cale);
            $nume = array_pop($bucati);
            $parinte = end($bucati);

            // Randurile decontului: „…/comert/r5/c2"
            if (preg_match('/^r(\d+(?:_\d+)*)$/', (string) $parinte, $p) === 1) {
                $rand = str_replace('_', '.', $p[1]);

                if (!isset($randuri[$rand])) {
                    $randuri[$rand] = ['cale' => implode('/', $bucati), 'baza' => null, 'tva' => null];
                }

                if ($liber && $nume === 'c2') {
                    $randuri[$rand]['baza'] = $nume;
                }

                if ($liber && $nume === 'c3') {
                    $randuri[$rand]['tva'] = $nume;
                }

                continue;
            }

            if (!$liber) {
                continue;
            }

            foreach (self::ANTETUL_FORMULARULUI as $alNostru => $alLor) {
                $sePotriveste = strpos($alLor, '/') === false
                    ? $nume === $alLor
                    : substr($cale, -strlen('/' . $alLor)) === '/' . $alLor;

                if ($sePotriveste && !isset($antet[$alNostru])) {
                    $antet[$alNostru] = $cale;
                }
            }
        }

        // Randurile fara nicio casuta de completat n-au ce cauta in harta.
        $randuri = array_filter($randuri, function ($rand) {
            return $rand['baza'] !== null || $rand['tva'] !== null;
        });

        ksort($randuri, SORT_NATURAL);

        $php = "<?php\n\nnamespace App\\Services\\Anaf\\Declaratii\\D300;\n\n"
            . "/*\n * FIȘIER GENERAT — nu se scrie de mână.\n *\n"
            . " * Scos din formularul inteligent al ANAF ($sursa),\n"
            . " * cu tools/d300/genereaza.php.\n */\n\n"
            . "/**\n * Unde stau datele in formularul inteligent al ANAF („soft A”).\n *\n"
            . " * Formularul e XFA: fiecare rand al decontului e un subformular cu doua\n"
            . " * casute — „c2” pentru baza si „c3” pentru taxa —, asezate intr-un arbore.\n"
            . " * Un fisier de incarcat in el trebuie sa aiba aceeasi asezare.\n */\n"
            . "class FormularD300\n{\n";

        $php .= "    /** Randul de pe formular => unde sta si ce casute are. */\n";
        $php .= "    public const RANDURI = [\n";

        foreach ($randuri as $rand => $unde) {
            $php .= "        '$rand' => ['cale' => '" . $unde['cale'] . "', 'baza' => "
                . ($unde['baza'] ? "'" . $unde['baza'] . "'" : 'null') . ", 'tva' => "
                . ($unde['tva'] ? "'" . $unde['tva'] . "'" : 'null') . "],\n";
        }

        $php .= "    ];\n\n";

        $php .= "    /** Campurile din antet: ce stim noi => unde sta in formular. */\n";
        $php .= "    public const ANTET = [\n";

        foreach ($antet as $alNostru => $cale) {
            $php .= "        '$alNostru' => '$cale',\n";
        }

        fwrite(STDERR, '  formular: ' . count($randuri) . ' randuri, ' . count($antet) . " campuri de antet\n");

        return $php . "    ];\n}\n";
    }

    /**
     * Sablonul XFA, scos din fluxurile PDF-ului.
     *
     * Un PDF isi tine partile in fluxuri, de obicei indesate; sablonul e cel in
     * care se gaseste subformularul de la radacina.
     */
    protected function sablonulFormularului(string $pdf): ?string
    {
        preg_match_all('/stream\r?\n/', $pdf, $inceputuri, PREG_OFFSET_CAPTURE);

        foreach ($inceputuri[0] as $inceput) {
            $de_la = $inceput[1] + strlen($inceput[0]);
            $pana_la = strpos($pdf, 'endstream', $de_la);

            if ($pana_la === false) {
                continue;
            }

            $brut = substr($pdf, $de_la, $pana_la - $de_la);
            $continut = @gzuncompress($brut);

            if ($continut === false) {
                $continut = @gzinflate($brut) ?: $brut;
            }

            if (strpos($continut, '<subform name="form1"') !== false) {
                return $continut;
            }
        }

        return null;
    }

    /**
     * Toate casutele formularului, cu calea si cu voia de a fi completate.
     *
     * Casutele „protected" sunt scrise de formular insusi — numarul randului,
     * denumirea lui — si nu se completeaza din afara.
     *
     * @return array<string, bool> calea => se poate completa
     */
    protected function campurileFormularului(string $sablon): array
    {
        $cale = [];
        $campuri = [];

        preg_match_all('/<(\/?)(subform|field)\b([^>]*?)(\/?)>/', $sablon, $gasite, PREG_SET_ORDER);

        foreach ($gasite as $g) {
            [$tot, $inchide, $fel, $atribute, $gol] = $g;

            $nume = preg_match('/name="([^"]*)"/', $atribute, $p) === 1 ? $p[1] : null;

            if ($fel === 'subform') {
                if ($inchide) {
                    array_pop($cale);
                } elseif ($gol === '') {
                    $cale[] = $nume;
                }

                continue;
            }

            if ($inchide || $nume === null) {
                continue;
            }

            $plin = implode('/', array_filter($cale)) . '/' . $nume;

            if (!isset($campuri[$plin])) {
                $campuri[$plin] = strpos($atribute, 'access="protected"') === false;
            }
        }

        return $campuri;
    }

    /**
     * Codurile TVA, cu denumirea si cota lor, din documentatia SAF-T.
     *
     * Documentul ANAF („RO_SAFT_SchemaDefCod") tine codurile pe foi, dupa felul
     * operatiunii: livrari, achizitii cu deducere intreaga, cu jumatate, fara,
     * si notele contabile. Toate au aceleasi coloane: corespondentul din D300,
     * codul din SAF-T, denumirea si cota.
     *
     * De aici nu se ia socoteala decontului — ea vine din regulile aplicatiei
     * ANAF —, ci vorbele: ca sa se poata spune omului „codul 301301, achizitii
     * scutite fara drept de deducere", nu doar „301301".
     */
    public function nomenclator(string $cale): string
    {
        $citire = \PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile($cale);
        $citire->setReadDataOnly(true);

        $coduri = [];

        foreach ($citire->listWorksheetInfo($cale) as $info) {
            $nume = $info['worksheetName'];

            if (!preg_match('/^(Livrari|Achizitii|TVA_NoteContabile)/u', $nume)) {
                continue;
            }

            $citire->setLoadSheetsOnly([$nume]);
            $foaie = $citire->load($cale)->getActiveSheet();

            foreach ($this->codurileFoii($foaie->toArray(null, false, false, false)) as $cod => $date) {
                // Un cod scris pe mai multe foi ramane cu prima lui denumire.
                $coduri += [$cod => $date];
            }
        }

        ksort($coduri);

        $php = "<?php\n\nnamespace App\Services\Anaf\Declaratii\D300;\n\n"
            . "/*\n * FIȘIER GENERAT — nu se scrie de mână.\n *\n"
            . " * Scos din documentația SAF-T a ANAF (" . basename($cale) . "),\n"
            . " * cu tools/d300/genereaza.php.\n */\n\n"
            . "/**\n * Codurile de taxă din SAF-T, pe înțelesul omului.\n *\n"
            . " * " . count($coduri) . " coduri, cu denumirea și cota fiecăruia. Se folosesc\n"
            . " * la lămurirea decontului, nu la socoteala lui.\n */\n"
            . "class NomenclatorTva\n{\n"
            . "    /** Codul => [denumirea, cota]. */\n"
            . "    public const CODURI = [\n";

        foreach ($coduri as $cod => $date) {
            $php .= "        '$cod' => ['" . str_replace("'", "\'", $date['denumire']) . "', '"
                . $date['cota'] . "'],\n";
        }

        $php .= "    ];\n\n" . $this->cautareaCodului() . "}\n";

        fwrite(STDERR, '  coduri de taxa: ' . count($coduri) . "\n");

        return $php;
    }

    /**
     * Codurile dintr-o foaie a documentatiei.
     *
     * Capul de tabel nu sta pe un singur rand peste tot: la livrari, denumirea
     * coloanei cu codul e pe un rand, iar „Descriere cod" pe cel de sub el. De
     * aceea coloanele se strang din toate randurile de dinaintea primului cod.
     */
    protected function codurileFoii(array $randuri): array
    {
        $cap = [];
        $coduri = [];

        foreach ($randuri as $rand) {
            $rand = array_map(function ($v) {
                return trim((string) $v);
            }, $rand);

            $cod = isset($cap['cod']) ? ($rand[$cap['cod']] ?? '') : '';

            // Codurile au sase cifre; capetele de sectiune au trei.
            if (preg_match('/^\d{6}$/', $cod) === 1) {
                $coduri[$cod] = [
                    'denumire' => $this->scurteaza($rand[$cap['denumire'] ?? -1] ?? ''),
                    'cota' => preg_match('/^\d+$/', $rand[$cap['cota'] ?? -1] ?? '') === 1 ? $rand[$cap['cota']] : '',
                ];

                continue;
            }

            // Inca nu suntem la coduri: se cauta coloanele.
            foreach ($rand as $i => $celula) {
                if (mb_stripos($celula, 'Cod tax') === 0 && mb_stripos($celula, 'SAF-T') !== false) {
                    $cap['cod'] = $i;
                }

                if (mb_stripos($celula, 'Descriere cod') === 0) {
                    $cap['denumire'] = $i;
                }

                if (mb_stripos($celula, 'Cote TVA') === 0) {
                    $cap['cota'] = $i;
                }
            }
        }

        return $coduri;
    }

    /** Denumirea, taiata la cat incape intr-o propozitie. */
    protected function scurteaza(string $text): string
    {
        $text = trim(preg_replace('/\s+/u', ' ', $text));

        if (mb_strlen($text) <= 150) {
            return $text;
        }

        return mb_substr($text, 0, 147) . '...';
    }

    /** Cautarea unui cod, scrisa o data in fisierul generat. */
    protected function cautareaCodului(): string
    {
        return <<<'PHP'
    /** Ce inseamna codul, sau null daca nu e in nomenclator. */
    public static function descrie(string $cod): ?string
    {
        if (!isset(self::CODURI[$cod])) {
            return null;
        }

        [$denumire, $cota] = self::CODURI[$cod];

        return $cota === '' ? $denumire : $denumire . ' (' . $cota . '%)';
    }

PHP;
    }

    /**
     * Toate campurile decontului, asa cum le numeste aplicatia ANAF.
     *
     * Se strang din chiar sursa ei, nu numai din regulile de adunare: printre
     * ele sunt si cele care se copiaza la sfarsit („RD20_BAZA = RD5_BAZA"), pe
     * care raportul nu le tipareste, dar declaratia le cere.
     *
     * @return array<int, string>
     */
    protected function campurileDecontului(): array
    {
        preg_match_all('/(RD[0-9]+(?:_[0-9]+)*_(?:BAZA|TVA))/', implode(PHP_EOL, $this->java), $gasite);

        return array_values(array_unique($gasite[1]));
    }

    /**
     * Randul de pe formular ghicit din numele campului: „RD20_1_TVA" -> „20.1".
     *
     * Se foloseste numai la campurile pe care raportul nu le tipareste, si
     * numai cand randul iesit chiar exista pe formular cu coloana ceruta.
     */
    protected function randulDinNume(string $camp): ?string
    {
        if (preg_match('/^RD(\d+)(?:_(\d+))?_(BAZA|TVA)$/', $camp, $p) !== 1) {
            return null;
        }

        return isset($p[2]) && $p[2] !== '' ? $p[1] . '.' . $p[2] : $p[1];
    }

    /** @return array<int, string> */
    protected function benzile(string $raport): array
    {
        preg_match_all('/<band\b[^>]*>(.*?)<\/band>/s', $raport, $gasite);

        return $gasite[1];
    }

    /** Elementele cu text ale unei benzi, cu locul lor pe pagina. */
    protected function elementeleBenzii(string $corp): array
    {
        $elemente = [];

        preg_match_all('/<(staticText|textField)\b(.*?)<\/\1>/s', $corp, $gasite, PREG_SET_ORDER);

        foreach ($gasite as $g) {
            if (preg_match('/<reportElement x="(-?\d+)" y="(-?\d+)" width="(\d+)" height="(\d+)"/', $g[2], $loc) !== 1) {
                continue;
            }

            $tipar = $g[1] === 'staticText'
                ? '/<text><!\[CDATA\[(.*?)\]\]><\/text>/s'
                : '/<textFieldExpression><!\[CDATA\[(.*?)\]\]><\/textFieldExpression>/s';

            $elemente[] = [
                'tip' => $g[1],
                'x' => (int) $loc[1],
                'y' => (int) $loc[2],
                'w' => (int) $loc[3],
                'h' => (int) $loc[4],
                'text' => preg_match($tipar, $g[2], $t) === 1 ? trim($t[1]) : '',
            ];
        }

        return $elemente;
    }

    /** Numarul de rand scris in dreptul campului, pe aceeasi inaltime. */
    protected function numarulDinDreptul(array $camp, array $numere): ?string
    {
        $gasit = null;
        $celMaiAproape = PHP_INT_MAX;

        foreach ($numere as $numar) {
            $seSuprapun = $numar['y'] < $camp['y'] + $camp['h'] && $camp['y'] < $numar['y'] + $numar['h'];

            if (!$seSuprapun) {
                continue;
            }

            $departare = abs($numar['y'] - $camp['y']);

            if ($departare < $celMaiAproape) {
                $celMaiAproape = $departare;
                $gasit = $numar['text'];
            }
        }

        return $gasit;
    }

    /**
     * Randurile 1—4: ele se aduna la citirea codului, nu a sumei taxei.
     *
     * Sunt operatiuni fara TVA (neimpozabile, scutite), asa ca in dreptul lor
     * nu vine niciun TaxAmount dupa care sa se ia decizia.
     */
    protected function metodaCodTaxa(): string
    {
        $linii = $this->bloc(
            $this->linia('switch (TaxCode) {'),
            1
        );

        $php = <<<'PHP'
    /**
     * Randurile 1—4, care se aduna la citirea codului de taxa.
     *
     * Ele tin operatiunile fara TVA: acolo nu vine niciun TaxAmount dupa care
     * sa se ia decizia, asa ca suma se ia din chiar linia notei contabile.
     */
    public static function laCodTaxa(array &$s, string $cod, float $ca, float $da): void
    {
        switch ($cod) {

PHP;

        $php .= $this->treci($linii, 3);
        $php .= "        }\n    }\n";

        return $php;
    }

    /**
     * Miezul: ce se aduna cand se citeste suma taxei de pe linie.
     */
    protected function metodaSumaTaxa(): string
    {
        $inceput = $this->linia('if (InTaxAmount && ');
        $linii = $this->bloc($inceput, 1);

        // Primele randuri ale blocului tin de citirea din XML, nu de socoteala.
        $linii = array_values(array_filter($linii, function ($linie) {
            $t = trim($linie);

            return !in_array($t, [
                't = CitesteNod.getElementText();',
                'InTaxAmount = false;',
                'TA = Double.parseDouble((String)t);',
            ], true);
        }));

        $php = <<<'PHP'
    /**
     * Ce aduce linia in decont, socotit cand i se citeste suma taxei.
     *
     * Semnul il da partea in care sta suma: ce e in debit intra cu plus, ce e
     * in credit cu minus (sau pe dos, la randurile de livrari). De aceea apare
     * peste tot abs(...) * (abs($da) / $da) — adica marimea sumei, cu semnul
     * partii din care vine.
     *
     * @param array $s     starea decontului, purtata de la o linie la alta
     * @param array $cont  steagurile conturilor liniei (vezi DecontDinSaft)
     */
    public static function laSumaTaxa(array &$s, string $cod, float $ca, float $da, float $ta, float $baza, bool $areBaza, array $cont): void
    {
        $not442 = $cont['not442'];
        $not3532 = $cont['not3532'];
        $is4426 = $cont['is4426'];
        $is4427 = $cont['is4427'];
        $is4428 = $cont['is4428'];
        $is35326 = $cont['is35326'];
        $is35327 = $cont['is35327'];
        $is35328 = $cont['is35328'];
        $rezultat = 0.0;


PHP;

        $php .= $this->treci($linii, 2);
        $php .= "    }\n";

        return $php;
    }

    /** Randurile care se socotesc din celelalte, dupa ce s-a citit tot fisierul. */
    protected function metodaFinal(): string
    {
        // De la primul rand socotit din celelalte pana la umplerea raportului.
        $inceput = $this->linia('RD12_BAZA = RD27_1_BAZA');
        $sfarsit = $this->linia('new RaportDTO()', $inceput);

        $linii = array_slice($this->java, $inceput, $sfarsit - $inceput);

        $php = <<<'PHP'
    /**
     * Randurile care ies din celelalte, dupa ce s-a citit tot fisierul.
     *
     * Totalurile, randurile care se copiaza (20 = 5, 21 = 6 …) si soldul de
     * TVA de la sfarsit. Unele tin de an: nomenclatorul din 2026 a adus cotele
     * de 21% si 11%, cu randurile lor.
     */
    public static function laFinal(array &$s, string $an): void
    {

PHP;

        $php .= $this->treci($linii, 2);
        $php .= "    }\n";

        return $php;
    }

    /**
     * Randurile decontului atinse de reguli, ca lista de pornit de la zero.
     *
     * Printre ele sunt si cele „_P", care se aduna in cuprinsul unei tranzactii
     * si se varsa in decont numai daca tranzactia se dovedeste a fi cea cautata.
     */
    protected function listaRandurilor(string $metode): string
    {
        preg_match_all("/\\\$s\['(RD[0-9_A-Z]+)'\]/", $metode, $gasite);

        $randuri = array_values(array_unique($gasite[1]));
        sort($randuri, SORT_NATURAL);

        $php = "    /** Randurile decontului, toate cele atinse de reguli. */\n";
        $php .= "    public const RANDURI = [\n";

        foreach (array_chunk($randuri, 5) as $bucata) {
            $php .= '        ' . implode(', ', array_map(function ($rand) {
                return "'$rand'";
            }, $bucata)) . ",\n";
        }

        return $php . "    ];\n";
    }

    /**
     * Codurile care se aduna la citirea codului de taxa, nu a sumei ei.
     *
     * Ele nu stau in nicio multime, ci in ramurile unui „switch”, asa ca de
     * acolo se si strang. Fara ele n-am putea spune, cand decontul iese gol,
     * daca vina e a codurilor din fisier sau a socotelii.
     */
    protected function listaCodurilorFaraTaxa(string $metode): string
    {
        preg_match_all("/^\\s+case '(\\d+)':/m", $metode, $gasite);

        $coduri = array_values(array_unique($gasite[1]));
        sort($coduri, SORT_NATURAL);

        $php = "    /** Codurile fara taxa, cele din „laCodTaxa”. */\n";
        $php .= "    public const CODURI_LA_COD_TAXA = [\n";

        foreach (array_chunk($coduri, 6) as $bucata) {
            $php .= '        ' . implode(', ', array_map(function ($cod) {
                return "'$cod' => true";
            }, $bucata)) . ",\n";
        }

        return $php . "    ];\n";
    }

    /** Randul (de la 0) la care incepe textul cautat, de la un rand incolo. */
    protected function linia(string $cautat, int $deLa = 0): int
    {
        foreach ($this->java as $i => $linie) {
            if ($i >= $deLa && strpos($linie, $cautat) !== false) {
                return $i;
            }
        }

        exit("Nu am gasit in {$this->sursa}: $cautat\n");
    }

    /**
     * Blocul care incepe la randul dat, pana la acolada lui de inchidere.
     *
     * Se numara acoladele: textul decompilat e asezat frumos, dar nu se poate
     * lucra pe indentare cand blocurile sunt cuibarite.
     */
    protected function bloc(int $inceput, int $sariRanduri = 0): array
    {
        $adancime = 0;
        $linii = [];

        for ($i = $inceput; $i < count($this->java); $i++) {
            $linie = $this->java[$i];
            $adancime += substr_count($linie, '{') - substr_count($linie, '}');

            if ($i >= $inceput + $sariRanduri) {
                $linii[] = $linie;
            }

            if ($adancime === 0 && $i > $inceput) {
                array_pop($linii);

                return $linii;
            }
        }

        exit("Blocul de la randul $inceput nu se inchide.\n");
    }

    /** Randurile de java, trecute in php si asezate la adancimea ceruta. */
    protected function treci(array $linii, int $adancimeDeBaza): string
    {
        $php = '';
        $adancime = $adancimeDeBaza;

        foreach ($linii as $linie) {
            $t = trim($linie);

            if ($t === '') {
                continue;
            }

            // Declaratiile scapate de la decompilare nu inseamna nimic.
            if (preg_match('/^void \w+;$/', $t)) {
                continue;
            }

            /*
             * Adancimea se tine numai dupa acolade. Acolada care inchide se
             * scade inainte de scris, ca ea sa cada in dreptul randului care a
             * deschis blocul; restul se socotesc dupa.
             */
            $inchideInainte = strpos($t, '}') === 0 ? 1 : 0;
            $adancime -= $inchideInainte;

            $php .= str_repeat('    ', $adancime) . $this->treciRandul($t) . "\n";

            $adancime += substr_count($t, '{') - (substr_count($t, '}') - $inchideInainte);
        }

        return $php;
    }

    /** Un rand de java, ca rand de php. */
    protected function treciRandul(string $t): string
    {
        if ($t === '}' || $t === '} else {') {
            return $t;
        }

        if (preg_match('/^\} else if \((.+)\) \{$/', $t, $p) === 1) {
            return '} elseif (' . $this->treciExpresia($p[1]) . ') {';
        }

        if (preg_match('/^if \((.+)\) \{$/', $t, $p) === 1) {
            return 'if (' . $this->treciExpresia($p[1]) . ') {';
        }

        // Ramurile switch-ului: „case "310301": {" sau, la cele care cad una
        // in alta, „case "310303":" fara acolada.
        if (preg_match('/^case "(\w+)": ?(\{)?$/', $t, $p) === 1) {
            return "case '{$p[1]}':" . (isset($p[2]) ? ' {' : '');
        }

        if ($t === 'break;') {
            return 'break;';
        }

        // Variabila de o clipa: „double d6 = ..." sau „double rezultat = ...".
        if (preg_match('/^double (\w+) = (.+);$/', $t, $p) === 1) {
            $this->temporara = $p[1];

            return '$rezultat = ' . $this->treciExpresia($p[2]) . ';';
        }

        if (preg_match('/^([\w.]+) (\+=|-=|=) (.+);$/', $t, $p) === 1) {
            return $this->treciNumele($p[1]) . ' ' . $p[2] . ' ' . $this->treciExpresia($p[3]) . ';';
        }

        exit("Nu stiu sa trec randul: $t\n");
    }

    /** O expresie de java, ca expresie de php. */
    protected function treciExpresia(string $e): string
    {
        // Multimile de coduri: „cod7.contains(TaxCode)"
        $e = preg_replace_callback('/\bcod(\d+)\.contains\(TaxCode\)/', function ($p) {
            $set = 'cod' . $p[1];

            if (!isset($this->seturi[$set])) {
                exit("Multimea $set nu e definita in {$this->sursa}\n");
            }

            return 'isset(CoduriD300::' . strtoupper($set) . '[$cod])';
        }, $e);

        // Potrivirile de cod: „TaxCode.matches("3.120.")" — java cere tot sirul.
        $e = preg_replace_callback('/\bTaxCode\.matches\("([^"]+)"\)/', function ($p) {
            return "preg_match('/^" . $p[1] . "$/', \$cod) === 1";
        }, $e);

        // „AN.contentEquals("2026")"
        $e = preg_replace('/\bAN\.contentEquals\("(\d+)"\)/', "\$an === '$1'", $e);

        $e = str_replace('Math.abs(', 'abs(', $e);

        /*
         * Numele, luate unul cate unul. Ce s-a scris deja in php mai sus —
         * „CoduriD300::COD7", „preg_match(" — se sare: numele de clasa e urmat
         * de doua puncte, cel de constanta e precedat de ele, iar chemarile de
         * functie sunt urmate de paranteza.
         */
        $e = preg_replace_callback('/(?<![\w$\':])[A-Za-z_]\w*(?![\w(:])/', function ($p) {
            return $this->treciNumele($p[0]);
        }, $e);

        return $e;
    }

    /** Un nume de variabila din java, ca variabila php. */
    protected function treciNumele(string $nume): string
    {
        if (isset(self::ALE_LINIEI[$nume])) {
            return self::ALE_LINIEI[$nume];
        }

        if (isset(self::STEAGURI[$nume])) {
            return self::STEAGURI[$nume];
        }

        if (isset(self::IN_STARE[$nume])) {
            return self::IN_STARE[$nume];
        }

        if (preg_match('/^RD[0-9_A-Z]*$/', $nume) === 1) {
            return "\$s['$nume']";
        }

        // Variabila de o clipa isi schimba numele de la o decompilare la alta.
        if ($nume === $this->temporara || $nume === 'rezultat') {
            return '$rezultat';
        }

        if (in_array($nume, ['true', 'false', 'null'], true)) {
            return $nume;
        }

        exit("Nume necunoscut in {$this->sursa}: $nume\n");
    }

    /** Multimile de coduri, citite de acolo de unde sunt scrise. */
    protected function citesteSeturile(): array
    {
        $text = implode("\n", $this->java);
        $seturi = [];

        preg_match_all(
            '/HashSet<String> hashSet(\d*) = new HashSet<String>\(Arrays\.asList\(([^)]*)\)\)/',
            $text,
            $gasite,
            PREG_SET_ORDER
        );

        foreach ($gasite as $set) {
            $numar = $set[1] === '' ? 1 : (int) $set[1];
            preg_match_all('/"(\d+)"/', $set[2], $coduri);

            $seturi['cod' . $numar] = $coduri[1];
        }

        ksort($seturi, SORT_NATURAL);

        return $seturi;
    }

    protected function antet(string $clasa, string $descriere): string
    {
        return "<?php\n\nnamespace App\\Services\\Anaf\\Declaratii\\D300;\n\n"
            . "/*\n * FIȘIER GENERAT — nu se scrie de mână.\n *\n"
            . " * Scos din aplicația ANAF D300 (clasa anaf.saft.Parsing3), cu\n"
            . " * tools/d300/genereaza.php. Când ANAF scoate versiunea următoare, se\n"
            . " * generează din nou; vezi tools/d300/README.md.\n */\n\n"
            . $descriere
            . "class $clasa\n{\n";
    }

    protected function descriereCoduri(): string
    {
        $coduri = 0;

        foreach ($this->seturi as $set) {
            $coduri += count($set);
        }

        return "/**\n"
            . " * Multimile de coduri TVA dupa care se impart randurile decontului.\n"
            . " *\n"
            . " * " . count($this->seturi) . " multimi, " . $coduri . " apartenente. Un cod poate sta in mai multe:\n"
            . " * el duce operatiunea si intr-un rand de amanunt, si in totalul lui.\n"
            . " */\n";
    }

    protected function descriereReguli(): string
    {
        return "/**\n"
            . " * Randurile decontului, socotite din liniile jurnalelor SAF-T.\n"
            . " *\n"
            . " * Trei momente: la citirea codului de taxa (randurile fara TVA), la\n"
            . " * citirea sumei taxei (tot restul) si la sfarsit (totalurile).\n"
            . " */\n";
    }
}
