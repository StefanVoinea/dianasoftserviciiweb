<?php

namespace Tests\Unit;

use App\Services\Anaf\Spv\VectorFiscalParser;
use Tests\TestCase;

class VectorFiscalParserTest extends TestCase
{
    /**
     * Randurile au formatul din PDF-ul ANAF: periodicitatea e lipita de
     * semnificatie, iar datele sunt zz/ll/aaaa (nu zz.ll.aaaa).
     *
     * @dataProvider randuriVector
     */
    public function test_randurile_vectorului_sunt_parsate(string $linie, array $asteptat): void
    {
        $parser = new class extends VectorFiscalParser {
            public function test(string $linie): ?array
            {
                return $this->parseazaRand($linie);
            }
        };

        $this->assertSame($asteptat, $parser->test($linie));
    }

    public function randuriVector(): array
    {
        return [
            'obligatie incheiata' => [
                "100\t31/12/2010 TrimestrialaProfit\t01/01/2010",
                [
                    'cod_imp' => '100',
                    'semnificatie' => 'Profit',
                    'perfisc' => 'Trimestrial',
                    'data_inceput' => '2010-01-01',
                    'data_sfarsit' => '2010-12-31',
                ],
            ],
            'obligatie in vigoare (fara data sfarsit)' => [
                "120  /  / TrimestrialaMicrointreprinderi\t01/01/2011",
                [
                    'cod_imp' => '120',
                    'semnificatie' => 'Microintreprinderi',
                    'perfisc' => 'Trimestrial',
                    'data_inceput' => '2011-01-01',
                    'data_sfarsit' => null,
                ],
            ],
            'semnificatie cu diacritice si spatii' => [
                "480  /  / LunaraContribuţie asiguratorie\t01/01/2018",
                [
                    'cod_imp' => '480',
                    'semnificatie' => 'Contribuţie asiguratorie',
                    'perfisc' => 'Lunar',
                    'data_inceput' => '2018-01-01',
                    'data_sfarsit' => null,
                ],
            ],
            'tva lunar' => [
                "300\t31/12/2025 LunaraTva\t01/01/2023",
                [
                    'cod_imp' => '300',
                    'semnificatie' => 'Tva',
                    'perfisc' => 'Lunar',
                    'data_inceput' => '2023-01-01',
                    'data_sfarsit' => '2025-12-31',
                ],
            ],

            /*
             * Citit pe calculatorul clientului, acelasi rand vine cu coloanele
             * in ordinea de pe hartie. Documentul nu mai urca pe server, deci
             * randurile trebuie recunoscute si asa.
             */
            'citit la client, obligatie incheiata' => [
                '100 Profit 01/01/2010 31/12/2010 Trimestriala',
                [
                    'cod_imp' => '100',
                    'semnificatie' => 'Profit',
                    'perfisc' => 'Trimestrial',
                    'data_inceput' => '2010-01-01',
                    'data_sfarsit' => '2010-12-31',
                ],
            ],
            'citit la client, obligatie in vigoare' => [
                '120 Microintreprinderi 01/01/2011  /  / Trimestriala',
                [
                    'cod_imp' => '120',
                    'semnificatie' => 'Microintreprinderi',
                    'perfisc' => 'Trimestrial',
                    'data_inceput' => '2011-01-01',
                    'data_sfarsit' => null,
                ],
            ],
            'citit la client, semnificatie cu diacritice' => [
                '480 Contribuţie asiguratorie 01/01/2018  /  / Lunara',
                [
                    'cod_imp' => '480',
                    'semnificatie' => 'Contribuţie asiguratorie',
                    'perfisc' => 'Lunar',
                    'data_inceput' => '2018-01-01',
                    'data_sfarsit' => null,
                ],
            ],
        ];
    }

    /** @dataProvider randuriIgnorate */
    public function test_liniile_care_nu_sunt_obligatii_sunt_ignorate(string $linie): void
    {
        $parser = new class extends VectorFiscalParser {
            public function test(string $linie): ?array
            {
                return $this->parseazaRand($linie);
            }
        };

        $this->assertNull($parser->test($linie));
    }

    public function randuriIgnorate(): array
    {
        return [
            'antet tabel' => ["DATA_SFARSITCOD_IMP\tPERFISCDATA_INCEPUTSEMNIFICATIE"],
            'antet tabel citit la client' => ['COD_IMP SEMNIFICATIE DATA_INCEPUT DATA_SFARSIT PERFISC'],
            'titlu document' => ['DATE PRIVIND SOCIETATEA DIANA SOFT SRL CE ARE CUI-ul 15208744'],
            'data raportului' => ['28/07/2026 10.54 AM'],
            'linie goala' => [''],
        ];
    }

    /**
     * Documentul de date identificare pune valoarea INAINTEA etichetei.
     *
     * E un tabel pe doua coloane, iar extractorul de PDF il desface pe randuri
     * in ordinea in care sunt desenate: intai numele, apoi cuvantul "Denumire".
     * Textul de mai jos e chiar cel scos dintr-un document adevarat.
     */
    public function test_denumirea_se_ia_de_deasupra_etichetei(): void
    {
        $text = $this->dateIdentificare();

        $this->assertSame(
            'CRISTI & DANA INSTAL SRL',
            (new VectorFiscalParser())->citesteDenumire($text, '22489650')
        );
    }

    /**
     * Forma juridica singura nu e denumire.
     *
     * In antetul aceluiasi document, dupa CUI vine pe randul urmator doar
     * "SRL" — coada numelui, taiata de extractor. Asa s-a si inregistrat o
     * firma cu denumirea "SRL", si nimeni n-a mai gasit-o in liste.
     */
    public function test_forma_juridica_singura_nu_trece_drept_denumire(): void
    {
        $parser = new VectorFiscalParser();

        // Numai antetul, fara randul cu eticheta: nu mai are de unde lua numele.
        $doarAntet = "DATE PRIVIND SOCIETATEA
 CE ARE CUI-ul 22489650
SRL
LA DATA DE
";

        $this->assertNull($parser->citesteDenumire($doarAntet, '22489650'));
    }

    /** Vectorul fiscal isi are numele in antet, si acela ramane neatins. */
    public function test_vectorul_fiscal_citeste_ca_pana_acum(): void
    {
        $text = "DATE PRIVIND SOCIETATEA ALFA BETA SRL CE ARE CUI-ul 15208744 LA DATA DE 11/08/2026";

        $this->assertSame(
            'ALFA BETA SRL',
            (new VectorFiscalParser())->citesteDenumire($text, '15208744')
        );
    }

    /**
     * Vectorul fiscal isi are numele rupt in doua.
     *
     * Desenat, antetul arata „<nume> DATE PRIVIND SOCIETATEA CE ARE CUI-ul
     * <cif>". Scos din PDF, numele cade deasupra, iar forma juridica sub cod:
     *
     *     CRISTI & DANA INSTAL
     *     DATE PRIVIND SOCIETATEA
     *      CE ARE CUI-ul
     *     22489650
     *     SRL
     *
     * Se lua doar bucata de sub cod, si asa s-a inregistrat o firma cu numele
     * „SRL". Textul de mai jos e chiar cel scos dintr-un document adevarat.
     */
    public function test_numele_rupt_in_doua_se_strange_la_loc(): void
    {
        $text = file_get_contents(base_path('tests/fixturi/vector-fiscal.txt'));

        $this->assertSame(
            'CRISTI & DANA INSTAL SRL',
            (new VectorFiscalParser())->citesteDenumire($text, '22489650')
        );
    }

    /** Si documentul de date identificare, al aceleiasi firme. */
    public function test_datele_de_identificare_dau_acelasi_nume(): void
    {
        $text = file_get_contents(base_path('tests/fixturi/date-identificare.txt'));

        $this->assertSame(
            'CRISTI & DANA INSTAL SRL',
            (new VectorFiscalParser())->citesteDenumire($text, '22489650')
        );
    }

    /** Textul unui document adevarat de date identificare, asa cum iese din PDF. */
    protected function dateIdentificare(): string
    {
        return implode("
", [
            'CRISTI & DANA INSTAL',
            'DATE PRIVIND SOCIETATEA',
            ' CE ARE CUI-ul 22489650',
            'SRL',
            'LA DATA DE',
            '11/08/2026 1.18 PM',
            'CRISTI & DANA INSTAL SRL',
            'Denumire',
            'JUD. ARAD, MUN. ARAD, STR. ANDREI ŞAGUNA, NR.67',
            'Domiciliul Fiscal',
            'JUD. ARAD, MUN. ARAD, STR. ANDREI ŞAGUNA, NR.67',
            'Sediul Social',
        ]);
    }

    /**
     * Etichetele documentului nu sunt nume de firma.
     *
     * Ele stau pe randuri singure, exact ca valorile, iar cautarea „randul de
     * deasupra etichetei" poate cadea pe alta eticheta cand extractorul aseaza
     * altfel coloanele. La un client, o firma s-a inregistrat cu numele „Organ
     * fiscal competent" — se vedea in lista, printre firmele adevarate.
     */
    public function test_etichetele_documentului_nu_trec_drept_nume(): void
    {
        $parser = new VectorFiscalParser();
        $metoda = new \ReflectionMethod($parser, 'pareDenumire');
        $metoda->setAccessible(true);

        foreach ([
            'Organ fiscal competent',
            'Domiciliul Fiscal',
            'Sediul Social',
            'Forma de organizare',
            'Grupa contribuabil',
            'LA DATA DE',
        ] as $eticheta) {
            $this->assertFalse(
                $metoda->invoke($parser, $eticheta),
                '„' . $eticheta . '" e o etichetă, nu un nume'
            );
        }

        // Iar numele adevarate trec mai departe.
        foreach (['CRISTI & DANA INSTAL SRL', 'ÎNTREPRINDERE FAMILIALĂ POPESCU'] as $nume) {
            $this->assertTrue($metoda->invoke($parser, $nume), '„' . $nume . '" e un nume');
        }
    }

    /**
     * Ghilimelele din jur nu fac parte din nume.
     *
     * ANAF le pune la asociatii si fundatii; ele ajungeau in denumire si de
     * acolo in numele dosarului din arhiva. Cele dinauntru raman: fac parte din
     * nume.
     */
    public function test_ghilimelele_din_jur_nu_raman_in_nume(): void
    {
        $parser = new VectorFiscalParser();

        $this->assertSame(
            'ASOCIATIA GAZETA LOCALA ARAD',
            $parser->citesteDenumire("\"ASOCIATIA GAZETA LOCALA ARAD\"
Denumire
", '41004123')
        );

        $this->assertSame(
            'ASOCIATIA "NIEMALS ALLEIN"',
            $parser->citesteDenumire("ASOCIATIA \"NIEMALS ALLEIN\"
Denumire
", '36972557')
        );
    }
}
