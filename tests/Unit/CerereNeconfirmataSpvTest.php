<?php

namespace Tests\Unit;

use App\Models\SpvSolicitare;
use App\Services\Anaf\Spv\CertificatService;
use App\Services\Anaf\Spv\Contracts\SpvTransport;
use App\Services\Anaf\Spv\SolicitareService;
use App\Services\Anaf\Spv\SpvClient;
use App\Services\Anaf\Spv\SpvStorage;
use App\Services\Anaf\Spv\VectorFiscalParser;
use App\Support\ContextCompanie;
use Illuminate\Http\Client\Response;
use Tests\TestCase;

/**
 * Cererea care a ajuns la ANAF, dar al carei raspuns s-a rupt pe drum (curl 56):
 * nu se trimite a doua oara (ANAF o inscria de doua ori), i se scrie totusi
 * randul, fara numar, iar cand raspunsul ei apare in SPV se leaga de rand in loc
 * sa primeasca unul nou.
 */
class CerereNeconfirmataSpvTest extends TestCase
{
    protected const COMPANIE = 985;

    /** De cate ori a plecat cererea spre ANAF. */
    public static $plecate = 0;

    protected function setUp(): void
    {
        parent::setUp();
        ContextCompanie::fixeaza(self::COMPANIE);
        self::$plecate = 0;
    }

    protected function tearDown(): void
    {
        SpvSolicitare::query()->toateCompaniile()->where('company_id', self::COMPANIE)->where('cif', '15208744')->delete();
        ContextCompanie::elibereaza();
        parent::tearDown();
    }

    /** Transportul care raspunde ca programul local cand legatura cu ANAF s-a rupt la primire. */
    protected function transportRupt(): SpvTransport
    {
        return new class implements SpvTransport {
            public function get($path, array $query = []): Response
            {
                CerereNeconfirmataSpvTest::$plecate++;

                return new Response(new \GuzzleHttp\Psr7\Response(502, [], json_encode([
                    'eroare' => 'Apelul către ANAF a eșuat: legătura s-a rupt în timpul primirii [curl 56]',
                    'detalii' => 'curl: (56) Recv failure',
                ])));
            }

            public function descarcaInArhiva(string $id, array $destinatie): array
            {
                return [];
            }

            public function descarcaLotInArhiva(array $documente, int $pauzaMs): array
            {
                return [];
            }
        };
    }

    protected function serviciu(): SolicitareService
    {
        $client = new SpvClient($this->transportRupt(), ['throttle_ms' => 0, 'zile_max' => 60]);

        return new SolicitareService($client, $this->app->make(SpvStorage::class), $this->app->make(VectorFiscalParser::class), $this->app->make(CertificatService::class));
    }

    public function test_cererea_rupta_la_raspuns_nu_se_retrimite_si_isi_primeste_numarul_din_spv(): void
    {
        $serviciu = $this->serviciu();

        $solicitare = $serviciu->solicita('15208744', 'Fisa Rol', [], null);

        $this->assertSame(1, self::$plecate, 'cererea pleaca o singura data: reluata, ANAF ar inscrie-o inca o data');
        $this->assertTrue((bool) $solicitare->getAttribute('neconfirmata'));
        $this->assertNull($solicitare->id_solicitare);
        $this->assertSame('trimisa', $solicitare->stare);
        $this->assertStringContainsString('ajuns la ANAF', (string) $solicitare->detalii);
        $this->assertNotNull($solicitare->data_solicitarii, 'se stie cand a fost ceruta: a plecat de aici');

        // raspunsul apare in SPV: se leaga de randul fara numar, nu se scrie altul
        $adaugate = $serviciu->inregistreazaCeleGasite([[
            'id' => '936513112', 'id_solicitare' => '77001', 'cif' => '15208744', 'tip' => 'RASPUNS SOLICITARE',
            'detalii' => 'Document Fisa Rol pentru CIF=15208744 (cod arondare 12)', 'data_creare' => '202610121200',
        ]]);

        $this->assertSame(0, $adaugate);
        $this->assertSame('77001', (string) $solicitare->fresh()->id_solicitare);
        $this->assertSame(1, SpvSolicitare::query()->totiUtilizatorii()->where('cif', '15208744')->where('tip_document', 'Fisa Rol')->count());

        // un raspuns de alt fel nu se leaga de ea
        $orfana = $serviciu->solicita('15208744', 'VECTOR FISCAL', [], null);
        $adaugate = $serviciu->inregistreazaCeleGasite([[
            'id' => '936513113', 'id_solicitare' => '77002', 'cif' => '15208744', 'tip' => 'RASPUNS SOLICITARE',
            'detalii' => 'duplicat Situatie Sintetica pentru CUI 15208744', 'data_creare' => '202610121200',
        ]]);
        $this->assertSame(1, $adaugate, 'raspunsul de alt fel isi face randul lui');
        $this->assertNull($orfana->fresh()->id_solicitare);
    }
}
