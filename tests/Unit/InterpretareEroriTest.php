<?php

namespace Tests\Unit;

use App\Models\AnafDeclaratie;
use App\Services\Anaf\Declaratii\InterpretareErori;
use Tests\TestCase;

/**
 * Traducerea erorilor DUKIntegrator.
 *
 * Mesajele folosite aici sunt cele produse chiar de validatorul ANAF, obținute
 * validând declarații stricate intenționat — nu formulări presupuse.
 */
class InterpretareEroriTest extends TestCase
{
    protected function serviciu(): InterpretareErori
    {
        return $this->app->make(InterpretareErori::class);
    }

    protected function declaratie(): AnafDeclaratie
    {
        return new AnafDeclaratie([
            'tip' => 'D394',
            'cui' => '15208744',
            'luna' => 6,
            'anul' => 2026,
        ]);
    }

    public function test_fara_erori_nu_se_inventeaza_probleme(): void
    {
        $rezultat = $this->serviciu()->interpreteaza(null, $this->declaratie());

        $this->assertSame([], $rezultat['probleme']);
        $this->assertStringContainsString('nu are erori', $rezultat['rezumat']);
    }

    public function test_codul_fiscal_invalid_este_explicat_cu_cel_asteptat(): void
    {
        $erori = "E: validari globale\n eroare atribut: cui: CUI invalid ('99999999')";

        $rezultat = $this->serviciu()->interpreteaza($erori, $this->declaratie());

        $this->assertCount(1, $rezultat['probleme']);
        $problema = $rezultat['probleme'][0];

        $this->assertSame('cui', $problema['camp']);
        $this->assertSame('eroare', $problema['severitate']);
        $this->assertSame('validari globale', $problema['sectiune']);
        $this->assertStringContainsString('cifra lui de control', $problema['explicatie']);

        // Indicatia trebuie sa fie concreta: ce se cauta in fisier si cu ce se inlocuieste.
        $this->assertSame('cui="99999999"', $problema['cauta']);
        $this->assertStringContainsString('fără prefixul RO', $problema['de_corectat']);
        $this->assertStringContainsString('15208744', $problema['de_corectat']);
    }

    public function test_atributul_lipsa_este_explicat(): void
    {
        $erori = "E: validari globale\n eroare atribut: sistemTVA: atributul trebuie sa existe";

        $problema = $this->serviciu()->interpreteaza($erori, $this->declaratie())['probleme'][0];

        $this->assertSame('sistemTVA', $problema['camp']);
        $this->assertStringContainsString('lipsește cu totul', $problema['explicatie']);
        $this->assertStringContainsString('sistemTVA="..."', $problema['de_corectat']);
    }

    /** Regula de corelare are in mesaj chiar valoarea corecta; ea trebuie scoasa in fata. */
    public function test_regula_de_corelare_arata_valoarea_asteptata(): void
    {
        $erori = "E: validari globale\n eroare regula: R17: atributul totalPlata_A (999999) trebuie sa fie egal cu Suma(informatii.nrCui<i>) + Suma(rezumat2.baza[L + A + AI]) (150025)";

        $problema = $this->serviciu()->interpreteaza($erori, $this->declaratie())['probleme'][0];

        $this->assertSame('totalPlata_A', $problema['camp']);
        $this->assertStringContainsString('R17', $problema['explicatie']);
        $this->assertStringContainsString('999999', $problema['explicatie']);
        $this->assertStringContainsString('150025', $problema['explicatie']);
        $this->assertStringContainsString('150025', $problema['de_corectat']);
    }

    public function test_valoarea_din_afara_listei_este_explicata(): void
    {
        $erori = "E: validari globale\n eroare atribut: tip_D394: valoarea 'X' nu se afla in lista";

        $problema = $this->serviciu()->interpreteaza($erori, $this->declaratie())['probleme'][0];

        $this->assertSame('tip_D394', $problema['camp']);
        $this->assertStringContainsString('doar câteva valori', $problema['explicatie']);
    }

    public function test_textul_prea_lung_spune_cat_are_si_cat_se_accepta(): void
    {
        $erori = "E: validari globale\n eroare atribut: den: sir mai lung de 200 caractere ('" . str_repeat('A', 400) . "')";

        $problema = $this->serviciu()->interpreteaza($erori, $this->declaratie())['probleme'][0];

        $this->assertSame('den', $problema['camp']);
        $this->assertStringContainsString('400', $problema['explicatie']);
        $this->assertStringContainsString('200', $problema['explicatie']);
    }

    public function test_cnp_invalid_este_explicat(): void
    {
        $erori = "E: validari globale\n eroare atribut: cifR: CNP invalid ('1234567890123')";

        $problema = $this->serviciu()->interpreteaza($erori, $this->declaratie())['probleme'][0];

        $this->assertSame('cifR', $problema['camp']);
        $this->assertStringContainsString('13 cifre', $problema['de_corectat']);
    }

    public function test_valoarea_in_afara_intervalului_este_explicata(): void
    {
        $erori = "E: informatii (1)\n eroare atribut: nrFacturi: valoarea '-5' nu se incadreaza in intervalul cerut";

        $problema = $this->serviciu()->interpreteaza($erori, $this->declaratie())['probleme'][0];

        $this->assertSame('nrFacturi', $problema['camp']);
        $this->assertSame('informatii (1)', $problema['sectiune']);
        $this->assertStringContainsString('interval', $problema['explicatie']);
    }

    public function test_atributul_necunoscut_este_explicat(): void
    {
        $erori = "E: informatii (1)\n eroare atribut: atributInexistent: atribut necunoscut ('atributInexistent') in namespace='mfp:anaf:dgti:d394:declaratie:v5'";

        $problema = $this->serviciu()->interpreteaza($erori, $this->declaratie())['probleme'][0];

        $this->assertSame('atributInexistent', $problema['camp']);
        $this->assertStringContainsString('nu îl', $problema['explicatie']);
        $this->assertStringContainsString('Șterge', $problema['de_corectat']);
    }

    /**
     * Eroarea de namespace apare deseori cand luna sau anul sunt gresite —
     * explicatia trebuie sa spuna asta, altfel utilizatorul umbla la xmlns
     * degeaba.
     */
    public function test_eroarea_de_namespace_trimite_intai_la_perioada(): void
    {
        $erori = "F: validari globale\n va rugam sa verificati daca folositi versiunea corecta de PDF inteligent sau daca XML-ul creat contine namespace-ul conform schemei XSD (pentru perioada de raportare)\n eroare structura: namespace ('mfp:anaf:dgti:d394:declaratie:v5') lipsa sau incorect la sectiunea declaratie394. Valoarea corecta este xmlns='mfp:anaf:dgti:d394:declaratie:v1'";

        $rezultat = $this->serviciu()->interpreteaza($erori, $this->declaratie());

        // Randul de indicatie generica nu trebuie sa apara ca problema separata.
        $this->assertCount(1, $rezultat['probleme']);

        $problema = $rezultat['probleme'][0];
        $this->assertSame('blocant', $problema['severitate']);
        $this->assertStringContainsString('luna 6/2026', $problema['explicatie']);
        $this->assertStringContainsString('luna sau anul sunt greșite', $problema['explicatie']);
        $this->assertStringContainsString('Verifică întâi luna și anul', $problema['de_corectat']);
    }

    public function test_fisierul_nevalid_ca_xml_este_explicat(): void
    {
        $erori = "1.\nErori la validare fisier; cod eroare=-5";

        $rezultat = $this->serviciu()->interpreteaza($erori, $this->declaratie());

        $explicate = array_values(array_filter($rezultat['probleme'], function ($p) {
            return $p['explicatie'] !== null;
        }));

        $this->assertNotEmpty($explicate);
        $this->assertStringContainsString('nu a putut fi citit ca XML', $explicate[0]['explicatie']);
    }

    /** Mai multe erori deodata: fiecare isi pastreaza sectiunea. */
    public function test_erorile_multiple_isi_pastreaza_sectiunea(): void
    {
        $erori = "E: informatii (1)\n eroare atribut: nrFacturi: valoarea '-5' nu se incadreaza in intervalul cerut\n"
            . "E: validari globale\n eroare regula: R131: nrFacturi (-5) > 0 daca si numai daca exista serieFacturi cu tip 2 (1)";

        $rezultat = $this->serviciu()->interpreteaza($erori, $this->declaratie());

        $this->assertCount(2, $rezultat['probleme']);
        $this->assertSame('informatii (1)', $rezultat['probleme'][0]['sectiune']);
        $this->assertSame('validari globale', $rezultat['probleme'][1]['sectiune']);
        $this->assertStringContainsString('R131', $rezultat['probleme'][1]['explicatie']);
        $this->assertStringContainsString('2 probleme', $rezultat['rezumat']);
        $this->assertStringContainsString('D394', $rezultat['rezumat']);
    }

    /* ------------------------------------------------------------------ */
    /* Livrarea pe bucati                                                  */
    /* ------------------------------------------------------------------ */

    /**
     * Interpretarea se da pe pasi, ca interfata sa poata arata fiecare problema
     * de indata ce e gata. Numarul total trebuie stiut din primul pas, altfel
     * nu s-ar putea spune „3 din 5".
     */
    public function test_pasii_vin_in_ordine_cu_totalul_anuntat_din_start(): void
    {
        $erori = "E: validari globale\n eroare atribut: cui: CUI invalid ('99999999')\n"
            . "E: informatii (1)\n eroare atribut: den: atribut prezent dar vid nepermis";

        $pasi = iterator_to_array($this->serviciu()->pasCuPas($erori, $this->declaratie()), false);

        $this->assertSame(['inceput', 'problema', 'problema', 'gata'], array_column($pasi, 'tip'));
        $this->assertSame(2, $pasi[0]['total']);
        $this->assertSame(0, $pasi[1]['index']);
        $this->assertSame(1, $pasi[2]['index']);
        $this->assertSame('cui', $pasi[1]['data']['camp']);
        $this->assertStringContainsString('2 probleme', $pasi[3]['rezumat']);
    }

    /** Randurile care nu sunt probleme nu intra in total. */
    public function test_totalul_nu_numara_randurile_ajutatoare(): void
    {
        $erori = "F: validari globale\n"
            . " va rugam sa verificati daca folositi versiunea corecta de PDF inteligent sau daca XML-ul creat contine namespace-ul conform schemei XSD (pentru perioada de raportare)\n"
            . " eroare structura: namespace ('mfp:anaf:dgti:d394:declaratie:v5') lipsa sau incorect la sectiunea declaratie394. Valoarea corecta este xmlns='mfp:anaf:dgti:d394:declaratie:v1'";

        $pasi = iterator_to_array($this->serviciu()->pasCuPas($erori, $this->declaratie()), false);

        $this->assertSame(1, $pasi[0]['total']);
        $this->assertCount(3, $pasi);
    }

    public function test_fara_erori_pasii_spun_ca_nu_e_nimic_de_corectat(): void
    {
        $pasi = iterator_to_array($this->serviciu()->pasCuPas(null, $this->declaratie()), false);

        $this->assertSame(['inceput', 'gata'], array_column($pasi, 'tip'));
        $this->assertSame(0, $pasi[0]['total']);
        $this->assertStringContainsString('nu are erori', $pasi[1]['rezumat']);
    }

    /* ------------------------------------------------------------------ */
    /* Locul din fisier care trebuie corectat                              */
    /* ------------------------------------------------------------------ */

    public function test_linia_si_coloana_sunt_numarate_ca_in_notepad(): void
    {
        $xml = "<?xml version=\"1.0\"?>\n"
            . "<declaratie394 luna=\"6\" an=\"2026\">\n"
            . "  <informatii cui=\"99999999\" den=\"Firma\" />\n"
            . "</declaratie394>";

        $erori = "E: validari globale\n eroare atribut: cui: CUI invalid ('99999999')";

        $problema = $this->serviciu()->interpreteaza($erori, $this->declaratie(), $xml)['probleme'][0];

        $this->assertNotNull($problema['locatie']);

        // Randul 3, iar cui= incepe dupa doua spatii si „<informatii "
        $this->assertSame(3, $problema['locatie']['linie']);
        $this->assertSame(15, $problema['locatie']['coloana']);
        $this->assertSame('<informatii cui="99999999" den="Firma" />', $problema['locatie']['text']);
        $this->assertFalse($problema['locatie']['trunchiat']);
        $this->assertSame(1, $problema['locatie']['aparitii']);
    }

    /**
     * Randul vine desfacut in trei, ca interfata sa poata scrie partea gresita
     * cu alta culoare. Impartirea se face pe pozitia gasita, nu prin cautare in
     * text — altfel s-ar colora prima aparitie, nu cea reclamata.
     */
    public function test_randul_este_impartit_in_jurul_partii_gresite(): void
    {
        $xml = '<op1 cuiP="9999999" denP="Alfa" cuiP2="9999999" />';
        $erori = "E: validari globale\n eroare atribut: cuiP: CUI invalid ('9999999')";

        $locatie = $this->serviciu()->interpreteaza($erori, $this->declaratie(), $xml)['probleme'][0]['locatie'];

        $this->assertSame('<op1 ', $locatie['inainte']);
        $this->assertSame('cuiP="9999999"', $locatie['potrivire']);
        $this->assertSame(' denP="Alfa" cuiP2="9999999" />', $locatie['dupa']);

        // Cele trei bucati trebuie sa refaca exact randul aratat.
        $this->assertSame(
            $locatie['text'],
            $locatie['inainte'] . $locatie['potrivire'] . $locatie['dupa']
        );
    }

    /** Si pe randurile lungi, partea colorata trebuie sa ramana intreaga. */
    public function test_partea_gresita_ramane_intreaga_pe_randurile_lungi(): void
    {
        $umplutura = str_repeat('<Detaliu cota="19" />', 60);
        $xml = $umplutura . '<informatii cui="99999999" />' . $umplutura;

        $erori = "E: validari globale\n eroare atribut: cui: CUI invalid ('99999999')";

        $locatie = $this->serviciu()->interpreteaza($erori, $this->declaratie(), $xml)['probleme'][0]['locatie'];

        $this->assertTrue($locatie['trunchiat']);
        $this->assertSame('cui="99999999"', $locatie['potrivire']);
        $this->assertStringStartsWith('…', $locatie['inainte']);
        $this->assertStringEndsWith('…', $locatie['dupa']);
    }

    /** Valoarea de pe primul rand: coloana se numara de la 1, nu de la 0. */
    public function test_prima_coloana_este_unu(): void
    {
        $xml = "cui=\"99999999\"\n";
        $erori = "E: validari globale\n eroare atribut: cui: CUI invalid ('99999999')";

        $locatie = $this->serviciu()->interpreteaza($erori, $this->declaratie(), $xml)['probleme'][0]['locatie'];

        $this->assertSame(1, $locatie['linie']);
        $this->assertSame(1, $locatie['coloana']);
    }

    /**
     * Declaratiile generate automat au uneori tot fisierul pe un singur rand;
     * atunci se arata doar bucata din jurul valorii, nu tot randul.
     */
    public function test_randul_foarte_lung_se_arata_pe_bucati(): void
    {
        $umplutura = str_repeat('<Detaliu cota="19" baza="100" />', 60);
        $xml = '<declaratie394>' . $umplutura . '<informatii cui="99999999" />' . $umplutura . '</declaratie394>';

        $erori = "E: validari globale\n eroare atribut: cui: CUI invalid ('99999999')";

        $locatie = $this->serviciu()->interpreteaza($erori, $this->declaratie(), $xml)['probleme'][0]['locatie'];

        $this->assertSame(1, $locatie['linie']);
        $this->assertTrue($locatie['trunchiat']);
        $this->assertLessThan(200, mb_strlen($locatie['text']));

        // Bucata aratata trebuie sa contina chiar valoarea gresita.
        $this->assertStringContainsString('cui="99999999"', $locatie['text']);
    }

    /** Cand aceeasi valoare apare de mai multe ori, utilizatorul trebuie avertizat. */
    public function test_aparitiile_multiple_sunt_numarate(): void
    {
        $xml = "<a cui=\"99999999\" />\n<b cui=\"99999999\" />\n";
        $erori = "E: validari globale\n eroare atribut: cui: CUI invalid ('99999999')";

        $locatie = $this->serviciu()->interpreteaza($erori, $this->declaratie(), $xml)['probleme'][0]['locatie'];

        $this->assertSame(1, $locatie['linie']);
        $this->assertSame(2, $locatie['aparitii']);
    }

    /** Fara fisier, explicatia ramane valabila; doar locul lipseste. */
    public function test_fara_xml_nu_se_inventeaza_o_pozitie(): void
    {
        $erori = "E: validari globale\n eroare atribut: cui: CUI invalid ('99999999')";

        $problema = $this->serviciu()->interpreteaza($erori, $this->declaratie())['probleme'][0];

        $this->assertNull($problema['locatie']);
        $this->assertNotNull($problema['explicatie']);
    }

    /** Daca valoarea nu se regaseste in fisier, nu se arata o pozitie gresita. */
    public function test_valoarea_negasita_nu_produce_pozitie(): void
    {
        $xml = '<declaratie394 cui="15208744" />';
        $erori = "E: validari globale\n eroare atribut: cui: CUI invalid ('99999999')";

        $problema = $this->serviciu()->interpreteaza($erori, $this->declaratie(), $xml)['probleme'][0];

        $this->assertNull($problema['locatie']);
    }

    /**
     * La SAF-T valoarea gresita sta intre etichete, nu intr-un atribut, iar
     * validatorul lasa atunci numele campului gol: „eroare atribut: : ...".
     * Calea sectiunii spune despre ce element e vorba.
     */
    public function test_mesajul_fara_nume_de_camp_este_explicat_si_localizat(): void
    {
        $xml = "<AuditFile>\n"
            . "  <Account>\n"
            . "    <AccountID>2837.10</AccountID>\n"
            . "  </Account>\n"
            . '</AuditFile>';

        $erori = "E: MasterFiles (1) sectiune GeneralLedgerAccounts (1) sectiune Account (1) sectiune AccountID (1)\n"
            . " eroare atribut: : numar intreg eronat: '2837.10'";

        $problema = $this->serviciu()->interpreteaza($erori, $this->declaratie(), $xml)['probleme'][0];

        $this->assertStringContainsString('număr întreg', $problema['explicatie']);

        // Se cauta si se evidentiaza elementul intreg, nu doar valoarea.
        $this->assertSame('<AccountID>2837.10</AccountID>', $problema['cauta']);
        $this->assertSame('<AccountID>2837.10</AccountID>', $problema['locatie']['potrivire']);
        $this->assertSame(3, $problema['locatie']['linie']);
    }

    /** Elementul gol se scrie in mai multe feluri; se incearca toate. */
    public function test_elementul_gol_este_gasit_desi_nu_e_atribut(): void
    {
        $xml = "<Payment>\n  <PaymentRefNo></PaymentRefNo>\n</Payment>";

        $erori = "E: SourceDocuments (1) sectiune Payments (1) sectiune Payment (1) sectiune PaymentRefNo (1)\n"
            . ' eroare atribut: : atribut prezent dar vid nepermis';

        $problema = $this->serviciu()->interpreteaza($erori, $this->declaratie(), $xml)['probleme'][0];

        $this->assertSame('<PaymentRefNo></PaymentRefNo>', $problema['cauta']);
        $this->assertSame(2, $problema['locatie']['linie']);
    }

    /**
     * Antetul spune a cata repetare a sectiunii este; fara asta s-ar arata mereu
     * prima aparitie, care de multe ori nu e cea gresita.
     */
    public function test_indicele_sectiunii_duce_la_repetarea_potrivita(): void
    {
        $xml = "<declaratie394>\n"
            . "<op1 cuiP=\"15208744\" />\n"
            . "<op1 cuiP=\"9999999\" />\n"
            . '</declaratie394>';

        $erori = "E: op1 (2)\n eroare regula: R218.2: daca tip_partener = 1 atunci cuiP (9999999) trebuie sa fie un CUI valid";

        $problema = $this->serviciu()->interpreteaza($erori, $this->declaratie(), $xml)['probleme'][0];

        $this->assertSame(3, $problema['locatie']['linie'], 'Trebuie a doua secțiune op1, nu prima');
    }

    public function test_mesajul_scurt_de_cod_invalid_este_explicat(): void
    {
        $xml = "<op1 cuiP=\"15208744\" />\n<op1 cuiP=\"9999999\" />";
        $erori = "E: op1 (2)\n cuiP invalid";

        $problema = $this->serviciu()->interpreteaza($erori, $this->declaratie(), $xml)['probleme'][0];

        $this->assertSame('cuiP', $problema['camp']);
        $this->assertStringContainsString('cifrei de control', $problema['explicatie']);
        $this->assertSame(2, $problema['locatie']['linie']);

        // Mesajul scurt nu poarta valoarea, dar potrivirea se intregeste pana
        // la ghilimeaua de inchidere: se evidentiaza campul intreg.
        $this->assertSame('cuiP="9999999"', $problema['locatie']['potrivire']);
    }

    /* ------------------------------------------------------------------ */
    /* Sabloanele citite din DecValidation.jar                             */
    /* ------------------------------------------------------------------ */

    /**
     * Fiecare sablon de mesaj al validatorului trebuie sa aiba explicatie si
     * indicatie de corectare. Mesajele sunt scrise exact ca in validator, cu
     * marcajele @0@, @1@ inlocuite.
     *
     * @dataProvider sabloaneleValidatorului
     */
    public function test_fiecare_sablon_al_validatorului_este_explicat(string $linie, string $asteptatInExplicatie): void
    {
        $rezultat = $this->serviciu()->interpreteaza("E: validari globale\n" . $linie, $this->declaratie());

        $this->assertCount(1, $rezultat['probleme'], 'Mesajul nu a produs exact o problemă: ' . $linie);

        $problema = $rezultat['probleme'][0];

        $this->assertNotNull($problema['explicatie'], 'Fără explicație pentru: ' . $linie);
        $this->assertNotNull($problema['de_corectat'], 'Fără indicație de corectare pentru: ' . $linie);
        $this->assertStringContainsString($asteptatInExplicatie, $problema['explicatie']);
        $this->assertFalse($rezultat['netradus']);
    }

    public static function sabloaneleValidatorului(): array
    {
        return [
            'CIF invalid' => [
                "eroare atribut: cui: Cod identificare fiscala (CIF) invalid ('123')",
                'cifra lui de control',
            ],
            'IBAN invalid' => [
                "eroare atribut: iban: cod IBAN invalid ('RO99')",
                '24 de caractere',
            ],
            'email invalid' => [
                "eroare atribut: email: Email invalid ('a@@b')",
                'nu are o formă corectă',
            ],
            'atribut vid' => [
                'eroare atribut: den: atribut prezent dar vid nepermis',
                'nu are nicio valoare',
            ],
            'atribut nepermis aici' => [
                "eroare atribut: cota: atributul cu valoarea: '19' nu trebuie sa existe aici",
                'nu are ce căuta în secțiunea',
            ],
            'data cu format gresit' => [
                "eroare atribut: dataDoc: data calendaristica eronata (format: yyyy-mm-dd): '15.03.2026'",
                'nu este scrisă în formatul cerut',
            ],
            'data inexistenta' => [
                "eroare atribut: dataDoc: data calendaristica eronata: '2026-02-30'",
                'nu există în calendar',
            ],
            'valoare nenumerica' => [
                "eroare atribut: baza: valoare nenumerica ('1.234,56')",
                'acceptă doar cifre',
            ],
            'continut lipsa' => [
                'eroare atribut: total: valoarea din content trebuie sa existe',
                'este goală',
            ],
            'continut nepermis' => [
                "eroare structura: continut nepermis ('text ratacit') in sau dupa sectiunea informatii",
                'nu se scrie text liber',
            ],
            'element in loc gresit' => [
                "eroare structura: elementul 'Detaliu' (namespace = 'mfp:anaf') nu poate sa apara in descendeta caii XML '/declaratie394/informatii'",
                'este pus în locul greșit',
            ],
            'sectiune obligatorie lipsa' => [
                "eroare structura: lipsa sectiune obligatorie inainte de sfarsitul sectiunii 'declaratie394'",
                'schema o cere obligatoriu',
            ],
            'ordine incorecta' => [
                "eroare structura: ordine incorecta a descendentilor elementului 'declaratie394': elementul 'informatii' trebuie sa preceada elementul 'rezumat1'",
                'ordinea etichetelor contează',
            ],
            'depasire maxOccurs' => [
                "eroare structura: depasire maxOccurs secventa ('3') sau ordine incorecta a descendentilor elementului 'declaratie394': elementul 'informatii' trebuie sa preceada elementul 'rezumat1'",
                'nu sunt în ordinea',
            ],
            'nivel incorect' => [
                "eroare structura: sectiunea 'Detaliu' apare pe un nivel incorect",
                'alt nivel de imbricare',
            ],
            'sectiune repetata' => [
                "eroare structura: sectiunea 'informatii' nu poate sa apara de mai multe ori in acest context",
                'apare de mai multe ori',
            ],
            'perioada prea veche' => [
                "eroare structura: Perioada de raportare incorecta ('1.2010') sau anterioara perioadei de raportare minime pentru acest tip de document (1.2017)",
                'mai veche decât prima perioadă',
            ],
            'parsare esuata' => [
                "eroare structura: Eroare fatala de parsare: 'unexpected end of file'",
                'nu a putut fi citit ca XML',
            ],
            'luna in afara intervalului' => [
                'eroare regula: R2: atributul luna (13) trebuie sa fie in intervalul 1 - 12',
                'sunt acceptate doar valorile',
            ],
            'camp scos din declaratie' => [
                'eroare regula: R55: Incepand cu perioada 1.2017 atributul bazaL_PF (1500) trebuie sa fie egal cu 0',
                'legea l-a scos din declarație',
            ],
            'sectiune de rezumat lipsa' => [
                'eroare regula: R80: Nu exista sectiune Rezumat2 pentru cota = 19 pentru agregarea valorilor din Op1',
                'acea secțiune lipsește',
            ],
            'sectiune dublata' => [
                'eroare regula: R81: Exista deja o sectiune Rezumat1 cu tip_partener=1, cota=19, document_N=0',
                'apare de două ori',
            ],
            'mod de calcul eronat' => [
                'eroare regula: R90: VALOAREA TOTALA A TVA AFERENTA LIVRARILOR DE BUNURI / PRESTARILOR DE SERVICII EFECTUATE - mod de calcul eronat',
                'nu corespunde cu suma pozițiilor',
            ],
            'combinatie repetata' => [
                'eroare regula: R120: cvatrupla (tip_partener (1), cota (19), tip (2), cuiP (123)) trebuie sa fie unica pe declaratie',
                'trebuie să fie unică',
            ],
            'camp care nu trebuie completat' => [
                'eroare regula: R44: (1500) nu trebuie completat',
                'nu trebuie completat în situația declarată',
            ],
            // Prins de sablonul mai precis, care numeste si campul — de dorit.
            'valoare calculata diferita' => [
                'eroare regula: R33: atributul baza (1000) trebuie sa fie egal cu valoarea calculata (1200)',
                'din celelalte câmpuri rezultă 1200',
            ],
            'valoare calculata fara nume de camp' => [
                'eroare regula: R34: totalul sectiunii (1000) trebuie sa fie egal cu valoarea calculata (1200)',
                'nu este egală cu cea pe care validatorul',
            ],
        ];
    }

    /** Validatorul emite si atentionari, nu doar erori; ele nu blocheaza depunerea. */
    public function test_atentionarea_este_deosebita_de_eroare(): void
    {
        $erori = "W: validari globale\n atentionare regula: R99: atributul total (0) si suma baza20 + baza19 (0) nu pot fi egale cu zero";

        $problema = $this->serviciu()->interpreteaza($erori, $this->declaratie())['probleme'][0];

        $this->assertSame('atentionare', $problema['severitate']);
        $this->assertSame('R99', $problema['regula']);
    }

    /** Codul regulii trebuie sa ajunga in explicatie, oricare ar fi sablonul. */
    public function test_codul_regulii_apare_in_explicatie(): void
    {
        $erori = "E: validari globale\n eroare regula: R17: atributul totalPlata_A (999999) trebuie sa fie egal cu Suma(x) (150025)";

        $problema = $this->serviciu()->interpreteaza($erori, $this->declaratie())['probleme'][0];

        $this->assertSame('R17', $problema['regula']);
        $this->assertStringContainsString('Regula de corelare R17', $problema['explicatie']);
    }

    /** O regula fara sablon propriu primeste totusi indrumare, nu doar mesajul brut. */
    public function test_regula_necunoscuta_primeste_indrumare(): void
    {
        $erori = "E: validari globale\n eroare regula: R999: o conditie pe care nu am mai intalnit-o (5) fata de (7)";

        $rezultat = $this->serviciu()->interpreteaza($erori, $this->declaratie());
        $problema = $rezultat['probleme'][0];

        $this->assertNotNull($problema['explicatie']);
        $this->assertStringContainsString('Condiția cerută este', $problema['explicatie']);
        $this->assertNotNull($problema['de_corectat']);
        $this->assertFalse($rezultat['netradus']);
    }

    /** Un mesaj nerecunoscut se arata ca atare, nu se ascunde si nu se inventeaza. */
    public function test_mesajul_nerecunoscut_ramane_vizibil(): void
    {
        $erori = "E: validari globale\n ceva ce nu am mai vazut niciodata";

        $rezultat = $this->serviciu()->interpreteaza($erori, $this->declaratie());

        $this->assertTrue($rezultat['netradus']);
        $this->assertCount(1, $rezultat['probleme']);
        $this->assertNull($rezultat['probleme'][0]['explicatie']);
        $this->assertSame('ceva ce nu am mai vazut niciodata', $rezultat['probleme'][0]['mesaj']);
        $this->assertStringContainsString('nu am o explicație', $rezultat['rezumat']);
    }
}
