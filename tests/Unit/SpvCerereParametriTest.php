<?php

namespace Tests\Unit;

use App\Services\Anaf\Spv\Contracts\SpvTransport;
use App\Services\Anaf\Spv\SpvClient;
use Illuminate\Http\Client\Response;
use Tests\TestCase;

/**
 * Cum se cheamă la ANAF parametrii cererilor din SPV.
 *
 * „NeconcordanteD394" e singurul raport cerut pe un interval de luni, nu pe una
 * singură: acolo luna de început se cheamă `lunai`, iar cea de sfârșit `lunas`.
 * Trimis cu `luna`, se întorcea cu „Pentru tip raport= NeconcordanteD394
 * parametrii cui, an, lunai si lunas sunt obligatorii".
 */
class SpvCerereParametriTest extends TestCase
{
    /** Ce s-a trimis la ultima cerere. */
    public static $ultimaCerere = [];

    /** Un transport care nu pleacă nicăieri: aici se citește doar ce i s-a dat. */
    protected function transport(): SpvTransport
    {
        return new class implements SpvTransport {
            public function get($path, array $query = []): Response
            {
                SpvCerereParametriTest::$ultimaCerere = $query;

                return new Response(new \GuzzleHttp\Psr7\Response(200, [], json_encode(['id_solicitare' => '1'])));
            }

            public function descarcaInArhiva(string $id, array $destinatie): array
            {
                return ['cale' => 'x.pdf', 'extensie' => 'pdf', 'marime' => 1, 'hash' => 'x'];
            }

            public function descarcaLotInArhiva(array $documente, int $pauzaMs): array
            {
                return [];
            }
        };
    }

    protected function client(): SpvClient
    {
        return new SpvClient($this->transport(), ['throttle_ms' => 0, 'zile_max' => 60]);
    }

    /** Intervalul de luni pleacă sub numele cerute de ANAF. */
    public function test_neconcordantele_d394_pleaca_cu_lunai_si_lunas(): void
    {
        $this->client()->cerere('NeconcordanteD394', '15208744', [
            'an' => 2026,
            'luna' => 1,
            'luna_sfarsit' => 8,
        ]);

        $trimis = self::$ultimaCerere;

        $this->assertSame('NeconcordanteD394', $trimis['tip']);
        $this->assertSame('15208744', $trimis['cui']);
        $this->assertSame(2026, $trimis['an']);
        $this->assertSame(1, $trimis['lunai']);
        $this->assertSame(8, $trimis['lunas']);
        $this->assertArrayNotHasKey('luna', $trimis, 'ANAF nu cunoaște „luna" la raportul acesta');
        $this->assertArrayNotHasKey('luna_sfarsit', $trimis, 'Numele nostru nu are ce căuta în cerere');
    }

    /** Toate celelalte rapoarte lunare pleacă mai departe cu „luna". */
    public function test_celelalte_rapoarte_pleaca_cu_luna(): void
    {
        $this->client()->cerere('D300', '15208744', ['an' => 2026, 'luna' => 7]);

        $trimis = self::$ultimaCerere;

        $this->assertSame(7, $trimis['luna']);
        $this->assertArrayNotHasKey('lunai', $trimis);
        $this->assertArrayNotHasKey('lunas', $trimis);
    }

    /** Parametrii negrăiți nu se trimit goi. */
    public function test_parametrii_necompletati_nu_se_trimit(): void
    {
        $this->client()->cerere('VECTOR FISCAL', '15208744', ['an' => null, 'luna' => '']);

        $this->assertSame(['tip' => 'VECTOR FISCAL', 'cui' => '15208744'], self::$ultimaCerere);
    }

    /** Configurația cere cele trei câmpuri, ca formularul să le arate pe toate. */
    public function test_configuratia_cere_anul_si_ambele_luni(): void
    {
        $this->assertSame(
            ['an', 'luna', 'luna_sfarsit'],
            config('anaf.spv.tipuri_documente.NeconcordanteD394')
        );
    }
}
