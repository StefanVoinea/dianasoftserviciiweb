<?php

namespace Tests\Unit;

use App\Models\AnafCertificat;
use App\Models\AnafDeclaratie;
use App\Models\AnafSocietate;
use App\Models\VectorDeclaratie;
use App\Models\VectorSpv;
use App\Services\Anaf\Spv\RaportVectorLunar;
use App\Support\ContextCompanie;
use Tests\TestCase;

/**
 * Vectorul fiscal al unei luni: din obligatiile firmei se deduc declaratiile
 * ei, iar pentru fiecare se spune ce s-a intamplat.
 *
 * Depusa, poarta indexul recipisei cu data si ora depunerii. Nedepusa, poarta
 * periodicitatea obligatiei — si atentionare numai daca perioada raportata se
 * incheie chiar in luna ceruta: o declaratie trimestriala nu e „nedepusa" in
 * februarie, ci pur si simplu nu e a lunii aceleia.
 */
class RaportVectorLunarTest extends TestCase
{
    protected const COMPANIE = 994;

    protected $certificat;

    protected function setUp(): void
    {
        parent::setUp();

        ContextCompanie::fixeaza(self::COMPANIE);

        $this->certificat = AnafCertificat::create([
            'company_id' => self::COMPANIE,
            'thumbprint' => strtoupper(bin2hex(random_bytes(20))),
            'cn' => 'POPESCU ION',
            'activ' => true,
            'valabil_pana_la' => now()->addYear(),
        ]);
    }

    protected function tearDown(): void
    {
        foreach ([
            AnafDeclaratie::class, AnafSocietate::class, AnafCertificat::class,
            VectorSpv::class, VectorDeclaratie::class,
        ] as $model) {
            $model::query()->toateCompaniile()->where('company_id', self::COMPANIE)->delete();
        }

        ContextCompanie::elibereaza();

        parent::tearDown();
    }

    protected function entitate(string $cif): AnafSocietate
    {
        return AnafSocietate::create([
            'company_id' => self::COMPANIE,
            'cif' => $cif,
            'denumire' => 'FIRMA ' . $cif,
            'tip' => 'pj',
            'activ' => true,
            'scos_din_uz' => false,
            'certificat_id' => $this->certificat->id,
            'vector_la' => now(),
        ]);
    }

    protected function obligatie(string $cui, string $cod, string $perfisc, string $inceput, ?string $sfarsit = null): VectorSpv
    {
        return VectorSpv::create([
            'company_id' => self::COMPANIE,
            'cui' => $cui,
            'cod_imp' => $cod,
            'semnificatie' => 'Obligatia ' . $cod,
            'perfisc' => $perfisc,
            'data_inceput' => $inceput,
            'data_sfarsit' => $sfarsit,
        ]);
    }

    /** Din obligatia de TVA lunara reiese D300 pe fiecare luna. */
    public function test_declaratia_depusa_poarta_recipisa_cu_data_si_ora(): void
    {
        $this->entitate('15208744');
        $this->obligatie('15208744', '300', 'Lunar', '2023-01-01');

        AnafDeclaratie::create([
            'company_id' => self::COMPANIE,
            'cui' => '15208744',
            'tip' => 'D300',
            'luna' => 3,
            'anul' => 2026,
            'nume_fisier' => 'd300.pdf',
            'pas' => 'finalizat',
            'index_recipisa' => '113371090',
            'data_depunere' => '2026-04-15 08:49:17',
        ]);

        $raport = (new RaportVectorLunar())->pentruLuna(3, 2026);

        $celula = $raport['randuri'][0]['celule']['D300'];

        $this->assertTrue($celula['depusa']);
        $this->assertSame('113371090', $celula['index_recipisa']);
        $this->assertSame('15.04.2026', $celula['data_depunere']);
        $this->assertSame('08:49:17', $celula['ora_depunere']);
        $this->assertFalse($celula['atentionare']);
    }

    /** Nedepusa la vremea ei: periodicitatea si atentionarea. */
    public function test_declaratia_datorata_si_nedepusa_se_atentioneaza(): void
    {
        $this->entitate('15208744');
        $this->obligatie('15208744', '300', 'Lunar', '2023-01-01');

        $raport = (new RaportVectorLunar())->pentruLuna(3, 2026);

        $celula = $raport['randuri'][0]['celule']['D300'];

        $this->assertFalse($celula['depusa']);
        $this->assertSame('Lunar', $celula['periodicitate']);
        $this->assertTrue($celula['atentionare']);
        // TVA-ul aduce cu el si D394, si D406 — trei declaratii nedepuse.
        $this->assertSame(3, $raport['randuri'][0]['lipsa']);
    }

    /**
     * TVA-ul nu inseamna doar decontul.
     *
     * Codul 300 din vector cere fiecare perioada fiscala cu D300, D394 si —
     * de cand SAF-T e obligatoriu — D406, toate pe aceeasi periodicitate.
     * D394 si D406 n-au cod propriu in vector; raportul le deducea gresit
     * doar cand firma avea codul lor, adica niciodata.
     */
    public function test_tva_ul_aduce_cu_el_d394_si_d406(): void
    {
        $this->entitate('15208744');
        $this->obligatie('15208744', '300', 'Trimestrial', '2023-01-01');

        $raport = (new RaportVectorLunar())->pentruLuna(3, 2026);

        $celule = $raport['randuri'][0]['celule'];

        foreach (['D300', 'D394', 'D406'] as $tip) {
            $this->assertArrayHasKey($tip, $celule);
            $this->assertSame('Trimestrial', $celule[$tip]['periodicitate'], $tip . ' urmează decontul');
            $this->assertTrue($celule[$tip]['atentionare'], $tip . ' era a trimestrului încheiat în martie');
        }
    }

    /**
     * Declaratiile care nu se pot citi din vector se invata din istoric.
     *
     * D390 se depune doar in lunile cu operatiuni intracomunitare — vectorul
     * n-o arata. Daca firma a depus-o, ea ramane urmarita, cu periodicitatea
     * dedusa din chiar depunerile ei: doua luni alaturate inseamna lunar.
     */
    public function test_declaratia_depusa_vreodata_se_invata_din_istoric(): void
    {
        $this->entitate('15208744');
        $this->obligatie('15208744', '300', 'Lunar', '2023-01-01');

        foreach ([1, 2] as $luna) {
            AnafDeclaratie::create([
                'company_id' => self::COMPANIE,
                'cui' => '15208744',
                'tip' => 'D390',
                'luna' => $luna,
                'anul' => 2026,
                'nume_fisier' => 'd390.xml',
                'pas' => 'finalizat',
                'index_recipisa' => '55500' . $luna,
                'data_depunere' => sprintf('2026-%02d-15 09:00:00', $luna + 1),
            ]);
        }

        $raport = (new RaportVectorLunar())->pentruLuna(3, 2026);

        $celula = $raport['randuri'][0]['celule']['D390'];

        $this->assertTrue($celula['din_istoric']);
        $this->assertSame('Lunar', $celula['periodicitate']);
        $this->assertTrue($celula['atentionare'], 'în martie nu e depusă');

        // Iar in februarie se vede recipisa depunerii de atunci.
        $inFebruarie = (new RaportVectorLunar())->pentruLuna(2, 2026);

        $this->assertSame('555002', $inFebruarie['randuri'][0]['celule']['D390']['index_recipisa']);
    }

    /** Felul perioadei scris in declaratie (D406: L/T/A) bate ghicitul din distante. */
    public function test_perioada_tip_din_declaratie_da_periodicitatea(): void
    {
        $entitate = $this->entitate('15208744');
        $entitate->update(['vector_la' => now()]);
        // Fara TVA in vector: D406 exista doar in istoric, cu o singura depunere.
        $this->obligatie('15208744', '602', 'Lunar', '2018-01-01');

        AnafDeclaratie::create([
            'company_id' => self::COMPANIE,
            'cui' => '15208744',
            'tip' => 'D406',
            'luna' => 12,
            'anul' => 2025,
            'perioada_tip' => 'T',
            'nume_fisier' => 'd406.xml',
            'pas' => 'finalizat',
            'index_recipisa' => '777001',
            'data_depunere' => '2026-01-25 09:00:00',
        ]);

        $raport = (new RaportVectorLunar())->pentruLuna(3, 2026);

        $celula = $raport['randuri'][0]['celule']['D406'];

        $this->assertTrue($celula['din_istoric']);
        $this->assertSame('Trimestrial', $celula['periodicitate']);
    }

    /**
     * Deductia se pastreaza in tabela vector_declaratii.
     *
     * Ce a aflat aplicatia despre firma — declaratiile, periodicitatea,
     * valabilitatea — sta scris, ca omul sa-l poata vedea si indrepta din
     * fereastra de actualizare, nu doar bănui din raport.
     */
    public function test_deductia_se_salveaza_in_tabel(): void
    {
        $this->entitate('15208744');
        $this->obligatie('15208744', '300', 'Lunar', '2023-01-01');

        (new RaportVectorLunar())->pentruLuna(3, 2026);

        $salvate = VectorDeclaratie::where('cui', '15208744')->where('sursa', 'dedusa')->get();

        $this->assertSame(
            ['D300', 'D394', 'D406'],
            $salvate->pluck('tip')->sort()->values()->all()
        );

        $d300 = $salvate->firstWhere('tip', 'D300');

        $this->assertSame('Lunar', $d300->perfisc);
        $this->assertSame('2023-01-01', $d300->data_inceput->format('Y-m-d'));
        $this->assertNull($d300->data_sfarsit);

        // A doua intocmire nu dubleaza randurile.
        (new RaportVectorLunar())->pentruLuna(3, 2026);

        $this->assertSame(3, VectorDeclaratie::where('cui', '15208744')->where('sursa', 'dedusa')->count());
    }

    /**
     * Ce a scris omul intra in raport: Bilant semestrial, de pilda.
     *
     * Bilantul nu are cod in vectorul SPV si nu se depune prin aplicatie, deci
     * nu se poate deduce de nicaieri. Adaugat manual, cu periodicitatea lui,
     * apare in raport si se atentioneaza la lunile in care se incheie
     * semestrul — iunie si decembrie — nu in fiecare luna.
     */
    public function test_declaratia_adaugata_manual_intra_in_raport(): void
    {
        $this->entitate('15208744');
        $this->obligatie('15208744', '602', 'Lunar', '2018-01-01');

        VectorDeclaratie::create([
            'company_id' => self::COMPANIE,
            'cui' => '15208744',
            'tip' => 'BILANT',
            'perfisc' => 'Semestrial',
            'sursa' => 'manuala',
            'data_inceput' => '2020-01-01',
        ]);

        $inIunie = (new RaportVectorLunar())->pentruLuna(6, 2026);
        $celula = $inIunie['randuri'][0]['celule']['BILANT'];

        $this->assertTrue($celula['manuala']);
        $this->assertSame('Semestrial', $celula['periodicitate']);
        $this->assertTrue($celula['atentionare'], 'semestrul se încheie în iunie');

        $inMai = (new RaportVectorLunar())->pentruLuna(5, 2026);

        $this->assertFalse($inMai['randuri'][0]['celule']['BILANT']['atentionare'], 'mai nu încheie un semestru');
    }

    /** Pe acelasi tip, cuvantul omului bate deductia. */
    public function test_randul_manual_bate_deductia_pe_acelasi_tip(): void
    {
        $this->entitate('15208744');
        // Vectorul spune TVA lunar; omul stie ca firma a trecut la trimestrial.
        $this->obligatie('15208744', '300', 'Lunar', '2023-01-01');

        VectorDeclaratie::create([
            'company_id' => self::COMPANIE,
            'cui' => '15208744',
            'tip' => 'D300',
            'perfisc' => 'Trimestrial',
            'sursa' => 'manuala',
        ]);

        $raport = (new RaportVectorLunar())->pentruLuna(2, 2026);
        $celula = $raport['randuri'][0]['celule']['D300'];

        $this->assertSame('Trimestrial', $celula['periodicitate']);
        $this->assertFalse($celula['atentionare'], 'trimestrul nu se încheie în februarie');
    }

    /** Randul manual iesit din valabilitate nu se mai ia in seama. */
    public function test_valabilitatea_randului_manual_se_respecta(): void
    {
        $this->entitate('15208744');
        $this->obligatie('15208744', '602', 'Lunar', '2018-01-01');

        VectorDeclaratie::create([
            'company_id' => self::COMPANIE,
            'cui' => '15208744',
            'tip' => 'D394',
            'perfisc' => 'Lunar',
            'sursa' => 'manuala',
            'data_inceput' => '2020-01-01',
            'data_sfarsit' => '2025-12-31',
        ]);

        $raport = (new RaportVectorLunar())->pentruLuna(3, 2026);

        $this->assertArrayNotHasKey('D394', $raport['randuri'][0]['celule'], 'valabilitatea s-a încheiat în 2025');

        $inTermen = (new RaportVectorLunar())->pentruLuna(11, 2025);

        $this->assertArrayHasKey('D394', $inTermen['randuri'][0]['celule']);
    }

    /** Depunerea anului trecut nu trece drept a lunii cu acelasi numar de acum. */
    public function test_depunerea_anului_trecut_nu_se_ia_drept_a_anului_cerut(): void
    {
        $this->entitate('15208744');
        $this->obligatie('15208744', '300', 'Lunar', '2023-01-01');

        AnafDeclaratie::create([
            'company_id' => self::COMPANIE,
            'cui' => '15208744',
            'tip' => 'D300',
            'luna' => 3,
            'anul' => 2025,
            'nume_fisier' => 'd300.pdf',
            'pas' => 'finalizat',
            'index_recipisa' => '888001',
            'data_depunere' => '2025-04-15 09:00:00',
        ]);

        $raport = (new RaportVectorLunar())->pentruLuna(3, 2026);

        $celula = $raport['randuri'][0]['celule']['D300'];

        $this->assertFalse($celula['depusa'], 'martie 2025 nu e martie 2026');
        $this->assertTrue($celula['atentionare']);
    }

    /**
     * Obligatia trimestriala nu e a fiecarei luni.
     *
     * In februarie, o declaratie trimestriala nedepusa nu se atentioneaza —
     * perioada ei se incheie abia in martie. Coloana ramane, cu periodicitatea,
     * ca omul sa vada ca obligatia exista.
     */
    public function test_obligatia_trimestriala_nu_se_atentioneaza_in_lunile_dintre_termene(): void
    {
        $this->entitate('15208744');
        $this->obligatie('15208744', '100', 'Trimestrial', '2018-04-01');

        $raport = (new RaportVectorLunar())->pentruLuna(2, 2026);

        $celula = $raport['randuri'][0]['celule']['D100'];

        $this->assertFalse($celula['datorata']);
        $this->assertFalse($celula['atentionare'], 'perioada trimestrului se încheie abia în martie');

        // Iar in martie, aceeasi obligatie nedepusa chiar se atentioneaza.
        $inMartie = (new RaportVectorLunar())->pentruLuna(3, 2026);

        $this->assertTrue($inMartie['randuri'][0]['celule']['D100']['atentionare']);
    }

    /**
     * Contributiile sociale merg toate in D112.
     *
     * Firma cu salariati are in vector o mana de coduri 4xx si venitul din
     * salarii (602) — toate se declara intr-o singura D112, nu in sase coloane.
     */
    public function test_contributiile_se_strang_intr_o_singura_coloana_d112(): void
    {
        $this->entitate('15208744');
        $this->obligatie('15208744', '412', 'Lunar', '2018-01-01');
        $this->obligatie('15208744', '432', 'Lunar', '2018-01-01');
        $this->obligatie('15208744', '480', 'Lunar', '2018-01-01');
        $this->obligatie('15208744', '602', 'Lunar', '2018-01-01');

        $raport = (new RaportVectorLunar())->pentruLuna(3, 2026);

        $this->assertSame(['D112'], array_keys($raport['randuri'][0]['celule']));
        $this->assertCount(4, $raport['randuri'][0]['celule']['D112']['obligatii']);
    }

    /**
     * Obligatia incheiata nu mai naste declaratii.
     *
     * Vectorul pastreaza istoricul: firma trecuta de la micro la profit are
     * si codul 120, cu data de sfarsit. Pentru lunile de dupa, doar 100 conteaza.
     */
    public function test_obligatia_incheiata_nu_se_mai_cere(): void
    {
        $this->entitate('15208744');
        $this->obligatie('15208744', '120', 'Trimestrial', '2018-01-01', '2018-03-31');
        $this->obligatie('15208744', '100', 'Trimestrial', '2018-04-01');

        $raport = (new RaportVectorLunar())->pentruLuna(3, 2026);

        $celule = $raport['randuri'][0]['celule'];

        $this->assertArrayHasKey('D100', $celule);
        $this->assertSame(['100 Obligatia 100'], $celule['D100']['obligatii']);
    }

    /** Entitatea fara vector preluat se arata deoparte, cu motivul ei. */
    public function test_entitatea_fara_vector_se_arata_deoparte(): void
    {
        $entitate = $this->entitate('15208744');
        $entitate->update(['vector_la' => null]);

        $raport = (new RaportVectorLunar())->pentruLuna(3, 2026);

        $this->assertSame([], $raport['randuri']);
        $this->assertCount(1, $raport['fara_vector']);
        $this->assertStringContainsString('nu a fost încă preluat', $raport['fara_vector'][0]['motiv']);
    }

    /** Codul necunoscut nu se pierde: se arata ca obligatie fara declaratie stiuta. */
    public function test_codul_necunoscut_se_arata_ca_atare(): void
    {
        $this->entitate('15208744');
        $this->obligatie('15208744', '999', 'Lunar', '2018-01-01');

        $raport = (new RaportVectorLunar())->pentruLuna(3, 2026);

        $this->assertSame([], array_keys($raport['randuri'][0]['celule']));
        $this->assertSame('999', $raport['randuri'][0]['alte_obligatii'][0]['cod']);
    }

    /** Dintre depunerile aceleiasi perioade ramane ultima — rectificativa. */
    public function test_rectificativa_bate_depunerea_initiala(): void
    {
        $this->entitate('15208744');
        $this->obligatie('15208744', '300', 'Lunar', '2023-01-01');

        foreach ([
            ['index_recipisa' => '111', 'data_depunere' => '2026-04-10 09:00:00', 'rectificativa' => false],
            ['index_recipisa' => '222', 'data_depunere' => '2026-04-20 10:00:00', 'rectificativa' => true],
        ] as $depunere) {
            AnafDeclaratie::create(array_merge([
                'company_id' => self::COMPANIE,
                'cui' => '15208744',
                'tip' => 'D300',
                'luna' => 3,
                'anul' => 2026,
                'nume_fisier' => 'd300.pdf',
                'pas' => 'finalizat',
            ], $depunere));
        }

        $raport = (new RaportVectorLunar())->pentruLuna(3, 2026);

        $celula = $raport['randuri'][0]['celule']['D300'];

        $this->assertSame('222', $celula['index_recipisa']);
        $this->assertTrue($celula['rectificativa']);
    }
}
