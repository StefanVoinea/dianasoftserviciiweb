<?php

namespace Tests\Unit;

use App\Models\AnafCertificat;
use App\Models\AnafDeclaratie;
use App\Services\Anaf\Arhiva\ArhivaException;
use App\Services\Anaf\Arhiva\ArhivaService;
use App\Services\Anaf\Declaratii\RecipisaService;
use App\Services\Anaf\Spv\CertificatService;
use App\Support\ContextCompanie;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * [2026-11-25] Recipisa care n-a încăput în arhiva clientului nu rămâne uitată.
 *
 * Pe 20.11.2026 s-au depus 32 de D112 deodată. La ultimele șapte recipise,
 * drumul spre calculatorul cu tokenul a răspuns 429 („bate mai rar la ușă").
 * Scrierea în arhivă s-a oprit din prima, recipisele au rămas doar pe server,
 * declarațiile s-au încheiat — și nimeni n-a mai încercat. Omul nu le-a găsit
 * în dosarele firmelor și le-a adus cu mâna.
 */
class RecipiseleRamasePeServerTest extends TestCase
{
    protected const COMPANIE = 987;

    protected $fisiere = [];

    protected function setUp(): void
    {
        parent::setUp();

        ContextCompanie::fixeaza(self::COMPANIE);

        // Fara asteptare intre incercari, ca proba sa nu stea degeaba.
        config(['anaf.arhiva.ragaz_429' => 0, 'anaf.arhiva.activa' => true, 'anaf.arhiva.sterge_de_pe_server' => true]);

        $certificat = AnafCertificat::create([
            'company_id' => self::COMPANIE,
            'thumbprint' => strtoupper(bin2hex(random_bytes(20))),
            'cn' => 'POPA ROXANA',
            'activ' => true,
            'valabil_pana_la' => now()->addYear(),
            'bridge_url' => 'http://192.168.1.44:8099',
            'bridge_token' => 'token-contabil',
        ]);

        $this->app->make(CertificatService::class)->foloseste($certificat);
    }

    protected function tearDown(): void
    {
        foreach ($this->fisiere as $fisier) {
            Storage::delete($fisier);
        }

        AnafDeclaratie::query()->toateCompaniile()->where('company_id', self::COMPANIE)->delete();
        AnafCertificat::query()->toateCompaniile()->where('company_id', self::COMPANIE)->delete();

        ContextCompanie::elibereaza();

        parent::tearDown();
    }

    /** O declarație încheiată, cu recipisa rămasă doar pe server. */
    protected function declaratie(string $index): AnafDeclaratie
    {
        $cale = 'declaratii/recipise/proba_' . $index . '.pdf';
        Storage::put($cale, '%PDF-1.4 recipisa ' . $index);
        $this->fisiere[] = $cale;

        return AnafDeclaratie::create([
            'company_id' => self::COMPANIE,
            'nume_fisier' => 'proba.pdf',
            'tip' => 'D112',
            'cui' => '48846501',
            'den_firma' => 'FIROM EQUIP S.R.L.',
            'luna' => 10,
            'anul' => 2026,
            'pas' => 'finalizat',
            'index_recipisa' => $index,
            'cale_recipisa' => $cale,
            'data_recipisa' => now()->subDays(5),
        ]);
    }

    public function test_dupa_429_scrierea_in_arhiva_se_mai_incearca(): void
    {
        Http::fake(['*' => Http::sequence()
            ->push('{"message":"Too Many Attempts."}', 429)
            ->push('{"message":"Too Many Attempts."}', 429)
            ->push(['cale' => 'F (1)/D112/recipisa.pdf'], 200),
        ]);

        $cale = $this->app->make(ArhivaService::class)->pune('%PDF', 'F (1)', 'D112', 'recipisa.pdf');

        $this->assertSame('F (1)/D112/recipisa.pdf', $cale);
        Http::assertSentCount(3);
    }

    public function test_429_fara_sfarsit_nu_tine_lucrarea_in_loc(): void
    {
        Http::fake(['*' => Http::response('{"message":"Too Many Attempts."}', 429)]);

        try {
            $this->app->make(ArhivaService::class)->pune('%PDF', 'F (1)', 'D112', 'recipisa.pdf');
            $this->fail('Scrierea ar fi trebuit sa esueze.');
        } catch (ArhivaException $e) {
            $this->assertStringContainsString('429', $e->getMessage());
        }

        // Prima cerere si cele trei reincercari, apoi se lasa.
        Http::assertSentCount(4);
    }

    public function test_recipisa_ramasa_pe_server_ajunge_in_arhiva_la_verificarea_urmatoare(): void
    {
        $declaratie = $this->declaratie('1318017638');

        Http::fake([
            '*/arhiva' => Http::response(['cale' => 'FIROM EQUIP S.R.L (48846501)/D112/recipisa_1318017638.pdf'], 200),
            '*/arhiva/copiaza' => Http::response(['cale' => 'Toate/recipisa_1318017638.pdf'], 200),
            '*' => Http::response('', 200),
        ]);

        $duse = $this->app->make(RecipisaService::class)->arhiveazaRamase();

        $declaratie->refresh();

        $this->assertSame(1, $duse);
        $this->assertSame('FIROM EQUIP S.R.L (48846501)/D112/recipisa_1318017638.pdf', $declaratie->arhiva_recipisa);
        // Odata ajunsa la client, copia de pe server nu mai ramane.
        $this->assertNull($declaratie->cale_recipisa);
        $this->assertFalse(Storage::exists('declaratii/recipise/proba_1318017638.pdf'));
    }

    public function test_cu_arhiva_care_nu_raspunde_recipisa_ramane_pe_server(): void
    {
        $declaratie = $this->declaratie('1318017644');
        $this->declaratie('1318017618');

        Http::fake(['*' => Http::response(['eroare' => 'Programul local nu a răspuns.'], 504)]);

        $duse = $this->app->make(RecipisaService::class)->arhiveazaRamase();

        $declaratie->refresh();

        $this->assertSame(0, $duse);
        $this->assertNull($declaratie->arhiva_recipisa);
        $this->assertNotNull($declaratie->cale_recipisa);
        $this->assertTrue(Storage::exists($declaratie->cale_recipisa));
        // La primul esec se opreste: a doua recipisa nu se mai incearca acum.
        Http::assertSentCount(1);
    }
}
