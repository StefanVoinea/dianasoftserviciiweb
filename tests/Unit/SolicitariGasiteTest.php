<?php

namespace Tests\Unit;

use App\Models\AnafCertificat;
use App\Models\AnafSocietate;
use App\Models\SpvSolicitare;
use App\Services\Anaf\Spv\CertificatService;
use App\Services\Anaf\Spv\SolicitareService;
use App\Support\ContextCompanie;
use Tests\TestCase;

/**
 * Nu tot ce sta in SPV a fost cerut din aplicatie: cererile facute de pe
 * site-ul ANAF, sau inainte de a fi folosita aplicatia, isi au raspunsul acolo
 * fara ca aici sa existe randul lor. Se inscriu la citirea mesajelor, ca fila de
 * solicitari sa arate tot ce are raspuns.
 */
class SolicitariGasiteTest extends TestCase
{
    protected const COMPANIE = 993;

    protected function setUp(): void
    {
        parent::setUp();

        ContextCompanie::fixeaza(self::COMPANIE);

        $certificat = AnafCertificat::create([
            'company_id' => self::COMPANIE,
            'thumbprint' => strtoupper(bin2hex(random_bytes(20))),
            'cn' => 'POPESCU ION',
            'activ' => true,
            'valabil_pana_la' => now()->addYear(),
        ]);

        $this->app->make(CertificatService::class)->foloseste($certificat);
    }

    protected function tearDown(): void
    {
        SpvSolicitare::query()->toateCompaniile()->totiUtilizatorii()
            ->where('company_id', self::COMPANIE)->delete();
        AnafSocietate::query()->toateCompaniile()->where('company_id', self::COMPANIE)->delete();
        AnafCertificat::query()->toateCompaniile()->where('company_id', self::COMPANIE)->delete();

        ContextCompanie::elibereaza();

        parent::tearDown();
    }

    protected function serviciu(): SolicitareService
    {
        return $this->app->make(SolicitareService::class);
    }

    /** Mesajele asa cum vin de la ANAF. */
    protected function mesaje(): array
    {
        return [
            [
                'id' => '912947094',
                'cif' => '15208744',
                'tip' => 'RASPUNS SOLICITARE',
                'detalii' => 'duplicat VECTOR FISCAL pentru CUI 15208744',
                'id_solicitare' => '189146133',
            ],
            [
                'id' => '912947096',
                'cif' => '1720913216197',
                'tip' => 'RASPUNS SOLICITARE',
                'detalii' => 'Obligatii de plata pentru CNP 1720913216197',
                'id_solicitare' => '189146148',
            ],
            // Nu e raspuns la o solicitare: n-are ce cauta in fila aceea.
            [
                'id' => '890312729',
                'cif' => '15208744',
                'tip' => 'EXTRAS DE CONT',
                'detalii' => 'Extras de cont pe iunie',
            ],
        ];
    }

    public function test_raspunsurile_gasite_isi_capata_randul(): void
    {
        $this->assertSame(2, $this->serviciu()->inregistreazaCeleGasite($this->mesaje()));

        $inscrise = SpvSolicitare::query()->totiUtilizatorii()->get()->keyBy('id_solicitare');

        $this->assertCount(2, $inscrise);
        $this->assertSame('VECTOR FISCAL', $inscrise['189146133']->tip_document);
        $this->assertSame('15208744', $inscrise['189146133']->cif);

        // Si CNP-ul e un cod de contribuabil: si persoana are spatiu privat.
        $this->assertSame('Obligatii de plata', $inscrise['189146148']->tip_document);
        $this->assertSame('1720913216197', $inscrise['189146148']->cif);
    }

    /** Raman „in asteptare": documentul se aduce la urmatoarea preluare. */
    public function test_solicitarea_inscrisa_asteapta_preluarea(): void
    {
        $this->serviciu()->inregistreazaCeleGasite($this->mesaje());

        $solicitare = SpvSolicitare::query()->totiUtilizatorii()
            ->where('id_solicitare', '189146133')->first();

        $this->assertSame('trimisa', $solicitare->stare);
        $this->assertNull($solicitare->data_afisare);
        $this->assertTrue(SpvSolicitare::inAsteptare()->where('id_solicitare', '189146133')->exists());
    }

    /** Cererile plecate din aplicatie nu se scriu a doua oara. */
    public function test_solicitarea_cunoscuta_nu_se_adauga_din_nou(): void
    {
        SpvSolicitare::create([
            'company_id' => self::COMPANIE,
            'cif' => '15208744',
            'tip_document' => 'VECTOR FISCAL',
            'id_solicitare' => '189146133',
            'stare' => 'trimisa',
        ]);

        $this->assertSame(1, $this->serviciu()->inregistreazaCeleGasite($this->mesaje()));
        $this->assertSame(2, SpvSolicitare::query()->totiUtilizatorii()->count());
    }

    /**
     * Randul e al celui care a citit mesajele. Fara stapan, un utilizator
     * obisnuit nu l-ar vedea: fila arata numai ce e al lui.
     */
    public function test_randul_e_al_celui_care_a_citit_mesajele(): void
    {
        $this->serviciu()->inregistreazaCeleGasite($this->mesaje(), 4242);

        $this->assertSame(
            4242,
            SpvSolicitare::query()->totiUtilizatorii()->where('id_solicitare', '189146133')->first()->user_id
        );
    }

    /** Denumirea firmei vine din Entitati inrolate, daca e stiuta. */
    public function test_randul_poarta_denumirea_firmei(): void
    {
        AnafSocietate::create([
            'company_id' => self::COMPANIE,
            'cif' => '15208744',
            'denumire' => 'DIANA SOFT SRL',
        ]);

        $this->serviciu()->inregistreazaCeleGasite($this->mesaje());

        $this->assertSame(
            'DIANA SOFT SRL',
            SpvSolicitare::query()->totiUtilizatorii()->where('id_solicitare', '189146133')->first()->den_firma
        );
    }

    /**
     * ANAF nu scrie textul la fel de fiecare data: „duplicat X pentru CUI n",
     * „X pentru CNP n", „Document X pentru CIF=n (cod arondare ...)".
     *
     * @dataProvider felurileTextului
     */
    public function test_felul_documentului_se_citeste_din_orice_forma_a_textului(
        string $detalii,
        string $tip,
        string $cif
    ): void {
        $this->serviciu()->inregistreazaCeleGasite([[
            'id' => '5000',
            'cif' => '15208744',
            'tip' => 'RASPUNS SOLICITARE',
            'detalii' => $detalii,
            'id_solicitare' => '5000',
        ]]);

        $solicitare = SpvSolicitare::query()->totiUtilizatorii()->where('id_solicitare', '5000')->first();

        $this->assertSame($tip, $solicitare->tip_document);
        $this->assertSame($cif, $solicitare->cif);
    }

    public function felurileTextului(): array
    {
        return [
            'duplicat, cu CUI' => [
                'duplicat VECTOR FISCAL pentru CUI 15208744',
                'VECTOR FISCAL',
                '15208744',
            ],
            'fara prefix, cu CNP' => [
                'Obligatii de plata pentru CNP 1720913216197',
                'Obligatii de plata',
                '1720913216197',
            ],
            'cu „Document" si CIF=' => [
                'Document Fisa Rol pentru CIF=15208744 (cod arondare 4021)',
                'Fisa Rol',
                '15208744',
            ],
            'fel nemaiintalnit, scris cum vine' => [
                'Adeverinta ceva nou pentru CUI 15208744',
                'Adeverinta ceva nou',
                '15208744',
            ],
        ];
    }

    /** Cand textul nu spune ce s-a cerut, randul se scrie oricum. */
    public function test_fara_detalii_lamurite_randul_tot_se_scrie(): void
    {
        $adaugate = $this->serviciu()->inregistreazaCeleGasite([[
            'id' => '999',
            'cif' => '15208744',
            'tip' => 'RASPUNS SOLICITARE',
            'detalii' => 'raspuns',
            'id_solicitare' => '777',
        ]]);

        $this->assertSame(1, $adaugate);

        $solicitare = SpvSolicitare::query()->totiUtilizatorii()->where('id_solicitare', '777')->first();

        $this->assertSame('Document SPV', $solicitare->tip_document);
        $this->assertSame('15208744', $solicitare->cif);
    }
}
