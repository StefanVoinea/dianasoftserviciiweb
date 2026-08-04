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
}
