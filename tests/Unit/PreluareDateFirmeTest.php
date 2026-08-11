<?php

namespace Tests\Unit;

use App\Models\AnafSocietate;
use App\Models\SpvSolicitare;
use App\Services\Anaf\Spv\SocietatiService;
use App\Services\Anaf\Spv\SolicitareService;
use App\Services\Anaf\Spv\SpvClient;
use App\Support\ContextCompanie;
use App\Support\ContextUtilizator;
use Tests\TestCase;

/**
 * Preluarea datelor de firma se face pe transe.
 *
 * Fiecare apel catre ANAF are pauza lui impusa, iar un birou cu zeci de firme
 * inseamna sute de apeluri: intr-o singura cerere web ele nu incap, oricat de
 * rabdator ar fi serverul. De aceea interfata trimite firmele in transe si
 * arata la cate s-a ajuns.
 */
class PreluareDateFirmeTest extends TestCase
{
    protected const COMPANIE = 992;

    protected function tearDown(): void
    {
        ContextUtilizator::faraLimitare(function () {
            AnafSocietate::query()->toateCompaniile()->where('company_id', self::COMPANIE)->delete();
            SpvSolicitare::query()->toateCompaniile()->where('company_id', self::COMPANIE)->delete();
        });

        ContextCompanie::elibereaza();

        parent::tearDown();
    }

    /** Cu lista de CIF-uri data, se lucreaza doar pe firmele acelea. */
    public function test_solicitarile_pleaca_doar_pentru_firmele_din_transa(): void
    {
        ContextCompanie::pentru(self::COMPANIE, function () {
            foreach (['TRANSA-1', 'TRANSA-2', 'TRANSA-3'] as $cif) {
                AnafSocietate::create(['cif' => $cif, 'tip' => 'pj', 'activ' => true]);
            }

            $cerute = [];

            $this->mock(SolicitareService::class, function ($mock) use (&$cerute) {
                $mock->shouldReceive('citesteDenumirileDinIdentificare')
                    ->andReturn(['citite' => 0, 'denumiri' => 0, 'cu_document' => []]);
                $mock->shouldReceive('solicita')->andReturnUsing(function ($cif) use (&$cerute) {
                    $cerute[] = $cif;

                    return new SpvSolicitare();
                });
            });

            $rezultat = $this->app->make(SocietatiService::class)->solicitaDocumente(
                ['DATE IDENTIFICARE'],
                null,
                ['TRANSA-1', 'TRANSA-3']
            );

            $this->assertSame(['TRANSA-1', 'TRANSA-3'], $cerute);
            $this->assertSame(2, $rezultat['trimise']);
        });
    }

    /** Firma care are deja documentul nu se mai intreaba a doua oara. */
    public function test_firma_cu_datele_stiute_nu_se_mai_solicita(): void
    {
        ContextCompanie::pentru(self::COMPANIE, function () {
            AnafSocietate::create([
                'cif' => 'STIUTA',
                'tip' => 'pj',
                'activ' => true,
                'date_identificare_la' => now()->subDays(3),
            ]);

            AnafSocietate::create(['cif' => 'NESTIUTA', 'tip' => 'pj', 'activ' => true]);

            $cerute = [];

            $this->mock(SolicitareService::class, function ($mock) use (&$cerute) {
                $mock->shouldReceive('citesteDenumirileDinIdentificare')
                    ->andReturn(['citite' => 0, 'denumiri' => 0, 'cu_document' => []]);
                $mock->shouldReceive('solicita')->andReturnUsing(function ($cif) use (&$cerute) {
                    $cerute[] = $cif;

                    return new SpvSolicitare();
                });
            });

            $rezultat = $this->app->make(SocietatiService::class)->solicitaDocumente(
                ['DATE IDENTIFICARE'],
                null,
                ['STIUTA', 'NESTIUTA']
            );

            $this->assertSame(['NESTIUTA'], $cerute, 'Firma cu datele știute nu trebuia întrebată din nou.');
            $this->assertSame(1, $rezultat['trimise']);
            $this->assertSame(1, $rezultat['sarite']);
        });
    }

    /** Vectorul fiscal si datele de identificare se socotesc fiecare in parte. */
    public function test_fiecare_document_se_socoteste_separat(): void
    {
        ContextCompanie::pentru(self::COMPANIE, function () {
            AnafSocietate::create([
                'cif' => 'JUMATATE',
                'tip' => 'pj',
                'activ' => true,
                'date_identificare_la' => now()->subDay(),
            ]);

            $cerute = [];

            $this->mock(SolicitareService::class, function ($mock) use (&$cerute) {
                $mock->shouldReceive('citesteDenumirileDinIdentificare')
                    ->andReturn(['citite' => 0, 'denumiri' => 0, 'cu_document' => []]);
                $mock->shouldReceive('solicita')->andReturnUsing(function ($cif, $tip) use (&$cerute) {
                    $cerute[] = $tip;

                    return new SpvSolicitare();
                });
            });

            $this->app->make(SocietatiService::class)->solicitaDocumente(
                ['DATE IDENTIFICARE', 'VECTOR FISCAL'],
                null,
                ['JUMATATE']
            );

            $this->assertSame(['VECTOR FISCAL'], $cerute);
        });
    }

    /**
     * Recitirea documentelor vechi se cere anume, nu la fiecare transa.
     *
     * Nu se mai recitesc toate solicitarile, ci numai ultimul document „DATE
     * IDENTIFICARE" al fiecarei firme: recitirea tuturor tinea minute intregi si
     * nu aducea nimic in plus pentru denumire.
     */
    public function test_documentele_vechi_se_recitesc_o_singura_data(): void
    {
        ContextCompanie::pentru(self::COMPANIE, function () {
            $recitiri = 0;

            $this->mock(SolicitareService::class, function ($mock) use (&$recitiri) {
                $mock->shouldReceive('citesteDenumirileDinIdentificare')
                    ->andReturnUsing(function () use (&$recitiri) {
                        $recitiri++;

                        return ['citite' => 7, 'denumiri' => 3, 'cu_document' => []];
                    });
            });

            $serviciu = $this->app->make(SocietatiService::class);

            $primul = $serviciu->solicitaDocumente(['DATE IDENTIFICARE'], null, ['NIMENI'], true);
            $urmatorul = $serviciu->solicitaDocumente(['DATE IDENTIFICARE'], null, ['NIMENI'], false);

            $this->assertSame(1, $recitiri);
            $this->assertSame(7, $primul['reinterpretate']);
            $this->assertSame(0, $urmatorul['reinterpretate']);
        });
    }

    /** Cu limita pusa, se preiau cel mult atatea raspunsuri; restul asteapta. */
    public function test_raspunsurile_se_preiau_cate_atatea_cat_s_a_cerut(): void
    {
        ContextCompanie::pentru(self::COMPANIE, function () {
            $mesaje = [];

            foreach (range(1, 5) as $i) {
                SpvSolicitare::create([
                    'cif' => 'LOT-' . $i,
                    'tip_document' => 'DATE IDENTIFICARE',
                    'id_solicitare' => 'ID-' . $i,
                    'stare' => 'trimisa',
                    'data_solicitarii' => now(),
                ]);

                $mesaje[] = ['id' => (string) $i, 'id_solicitare' => 'ID-' . $i, 'tip' => 'RASPUNS SOLICITARE'];
            }

            $this->mock(SpvClient::class, function ($mock) use ($mesaje) {
                $mock->shouldReceive('listaMesaje')->andReturn(['mesaje' => $mesaje]);
                // Descarcarea esueaza dinadins: se numara doar cate s-au incercat.
                $mock->shouldReceive('descarcare')->andThrow(new \App\Services\Anaf\Spv\SpvException('proba'));
            });

            $rezultat = $this->app->make(SolicitareService::class)->preiaRaspunsuri(60, 2);

            $this->assertSame(5, $rezultat['verificate']);
            $this->assertSame(5, $rezultat['ramase'], 'Nicio descărcare n-a reușit, deci toate mai așteaptă.');
            $this->assertCount(
                2,
                $rezultat['erori'],
                'S-au numărat încercările, nu izbânzile: lotul trebuia să se oprească după două.'
            );
        });
    }
}
