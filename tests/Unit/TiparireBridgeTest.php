<?php

namespace Tests\Unit;

use App\Models\AnafCertificat;
use App\Services\Anaf\Declaratii\ConcatenareService;
use App\Services\Anaf\Declaratii\DeclaratieException;
use App\Services\Anaf\Spv\CertificatService;
use App\Support\ContextCompanie;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

/**
 * Tiparirea se face pe calculatorul omului, prin programul local: documentul
 * unit nu se mai intoarce la aplicatie, ci iese pe hartie acolo.
 */
class TiparireBridgeTest extends TestCase
{
    protected const COMPANIE = 987;

    protected $certificat;

    /** @var array<int, string> */
    protected $fisiere = [];

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
            'bridge_url' => 'http://192.168.1.44:8099',
            'bridge_token' => 'token-contabil',
        ]);

        $this->app->make(CertificatService::class)->foloseste($this->certificat);
    }

    protected function tearDown(): void
    {
        foreach ($this->fisiere as $cale) {
            @unlink($cale);
        }

        AnafCertificat::query()->toateCompaniile()->where('company_id', self::COMPANIE)->delete();
        ContextCompanie::elibereaza();

        parent::tearDown();
    }

    protected function pdf(): string
    {
        $cale = tempnam(sys_get_temp_dir(), 'tip') . '.pdf';
        file_put_contents($cale, '%PDF-1.4 proba');
        $this->fisiere[] = $cale;

        return $cale;
    }

    protected function serviciu(): ConcatenareService
    {
        return $this->app->make(ConcatenareService::class);
    }

    /** Valoarea unui camp din corpul multipart. */
    protected static function camp(string $corp, string $nume): ?string
    {
        $tipar = '/name="' . preg_quote($nume, '/') . '"(?:\r\n[^\r\n]+)*\r\n\r\n(.*?)\r\n--/s';

        return preg_match($tipar, $corp, $potrivire) ? $potrivire[1] : null;
    }

    public function test_imprimanta_aleasa_este_trimisa_programului_local(): void
    {
        Http::fake([
            '192.168.1.44:8099/concateneaza' => Http::response([
                'tiparit' => true,
                'imprimanta' => 'HP LaserJet MFP M234dw',
                'pagini' => 2,
            ], 200),
        ]);

        $rezultat = $this->serviciu()->tipareste(
            [$this->pdf(), $this->pdf()],
            [],
            'HP LaserJet MFP M234dw'
        );

        $this->assertSame('HP LaserJet MFP M234dw', $rezultat['imprimanta']);

        Http::assertSent(function (Request $cerere) {
            $corp = $cerere->body();

            return $cerere->url() === 'http://192.168.1.44:8099/concateneaza'
                && self::camp($corp, 'imprimanta') === 'HP LaserJet MFP M234dw'
                && self::camp($corp, 'tipareste') === '1';
        });
    }

    /** Filigranul merge mai departe si la tiparire, nu doar la descarcare. */
    public function test_filigranul_se_trimite_si_la_tiparire(): void
    {
        Http::fake([
            '192.168.1.44:8099/concateneaza' => Http::response(['tiparit' => true, 'imprimanta' => 'X'], 200),
        ]);

        $this->serviciu()->tipareste([$this->pdf()], ['DIANA SOFT SRL'], 'X');

        Http::assertSent(function (Request $cerere) {
            return self::camp($cerere->body(), 'watermark[0]') === 'DIANA SOFT SRL';
        });
    }

    /** Fara program de tiparit pe acel calculator, esecul trebuie sa se vada. */
    public function test_esecul_tiparirii_este_spus_pe_intelesul_omului(): void
    {
        Http::fake([
            '192.168.1.44:8099/concateneaza' => Http::response([
                'eroare' => 'Tipărirea a eșuat.',
                'detalii' => 'Nu este instalat un program care să tipărească PDF-uri.',
            ], 500),
        ]);

        $this->expectException(DeclaratieException::class);
        $this->expectExceptionMessage('Nu este instalat un program');

        $this->serviciu()->tipareste([$this->pdf()], [], 'X');
    }

    /** Un raspuns fara confirmare nu trece drept tiparire reusita. */
    public function test_raspunsul_fara_confirmare_este_tratat_ca_esec(): void
    {
        Http::fake([
            '192.168.1.44:8099/concateneaza' => Http::response(['tiparit' => false], 200),
        ]);

        $this->expectException(DeclaratieException::class);

        $this->serviciu()->tipareste([$this->pdf()], [], 'X');
    }

    /** Descarcarea obisnuita ramane neschimbata: intoarce PDF-ul, nu JSON. */
    public function test_unirea_fara_tiparire_intoarce_documentul(): void
    {
        Http::fake(['192.168.1.44:8099/concateneaza' => Http::response('%PDF-1.4 unit', 200)]);

        $this->assertSame('%PDF-1.4 unit', $this->serviciu()->uneste([$this->pdf()]));

        Http::assertSent(function (Request $cerere) {
            return self::camp($cerere->body(), 'tipareste') === null;
        });
    }
}
