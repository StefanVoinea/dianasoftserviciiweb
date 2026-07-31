<?php

namespace Tests\Unit;

use App\Models\EtransportNotificare;
use App\Services\Anaf\Etransport\EtransportClient;
use App\Services\Anaf\Etransport\EtransportSincronizare;
use App\Support\ContextCompanie;
use Tests\TestCase;

class EtransportTest extends TestCase
{
    protected function tearDown(): void
    {
        EtransportNotificare::query()->toateCompaniile()->where('cod_decl', 'TEST-ETR')->delete();
        ContextCompanie::elibereaza();

        parent::tearDown();
    }

    public function test_serviciile_sunt_inregistrate(): void
    {
        $this->assertInstanceOf(EtransportClient::class, $this->app->make(EtransportClient::class));
        $this->assertInstanceOf(EtransportSincronizare::class, $this->app->make(EtransportSincronizare::class));
    }

    public function test_configurarea_foloseste_endpointul_cu_certificat(): void
    {
        $this->assertStringContainsString('webserviceapl.anaf.ro', config('anaf.etransport.base_url'));
        $this->assertSame('ETRANSP', config('anaf.etransport.standard'));
        $this->assertSame(60, config('anaf.etransport.zile_max'));
    }

    /** @dataProvider operatiuni */
    public function test_tipurile_de_operatiune_din_documentatie(int $cod, string $prefix): void
    {
        $this->assertArrayHasKey($cod, EtransportNotificare::OPERATIUNI);
        $this->assertStringStartsWith($prefix, EtransportNotificare::OPERATIUNI[$cod]);
    }

    public function operatiuni(): array
    {
        return [
            'achiziție intracomunitară' => [10, 'AIC'],
            'livrare intracomunitară' => [20, 'LIC'],
            'transport național' => [30, 'TTN'],
            'import' => [40, 'IMP'],
            'export' => [50, 'EXP'],
        ];
    }

    public function test_tipurile_de_notificare_sunt_cele_din_documentatie(): void
    {
        $this->assertSame(
            ['NOT', 'COR', 'DEL', 'CON', 'MVH'],
            array_keys(EtransportNotificare::TIPURI)
        );
    }

    public function test_notificarea_cu_erori_este_marcata(): void
    {
        $eronata = new EtransportNotificare(['stare' => 'ERR']);
        $valida = new EtransportNotificare(['stare' => 'OK']);

        $this->assertTrue($eronata->are_erori);
        $this->assertFalse($valida->are_erori);
    }

    /** Reinterogarea aceleiași perioade nu trebuie să dubleze notificările. */
    public function test_notificarile_nu_se_dubleaza_la_reinterogare(): void
    {
        ContextCompanie::pentru(900050, function () {
            $client = $this->mock(EtransportClient::class, function ($mock) {
                $mock->shouldReceive('lista')->andReturn([
                    'mesaje' => [
                        [
                            'uit' => 'UIT-TEST-1', 'tip' => 'NOT', 'stare' => 'OK',
                            'cod_decl' => 'TEST-ETR', 'id_incarcare' => '111',
                            'tip_op' => 30, 'data_transp' => '2026-07-28',
                        ],
                        [
                            'uit' => 'UIT-TEST-2', 'tip' => 'NOT', 'stare' => 'ERR',
                            'cod_decl' => 'TEST-ETR', 'id_incarcare' => '112',
                            'mesaje' => [['tip' => 'ERR', 'mesaj' => 'Câmp lipsă']],
                        ],
                    ],
                ]);
            });

            $serviciu = $this->app->make(EtransportSincronizare::class);

            $prima = $serviciu->preia(30, 'TEST-ETR');
            $aDoua = $serviciu->preia(30, 'TEST-ETR');

            $this->assertSame(2, $prima['noi']);
            $this->assertSame(1, $prima['cu_erori']);
            $this->assertSame(0, $aDoua['noi'], 'Notificările s-au dublat la reinterogare');
            $this->assertSame(2, EtransportNotificare::where('cod_decl', 'TEST-ETR')->count());
        });
    }
}
