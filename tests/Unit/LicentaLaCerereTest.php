<?php

namespace Tests\Unit;

use App\Models\AnafCertificat;
use App\Services\Anaf\Bridge\LicentiereBridge;
use App\Services\Anaf\Spv\CertificatService;
use App\Services\Anaf\Spv\Transport\BridgeTransport;
use App\Support\ContextCompanie;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

/**
 * Cand programul local spune ca n-are licenta, serverul i-o da si reia comanda.
 *
 * Se intampla dupa o reinstalare, dupa schimbarea calculatorului sau cand
 * licenta a expirat cat timp statia a stat inchisa. Pana acum, omul primea
 * „Deschideti fila «Certificate digitale»" si trebuia sa apese acolo un buton
 * pe care tot serverul il apasa — iar in mijlocul unei lucrari de zeci de firme
 * asta insemna sa ia totul de la capat.
 */
class LicentaLaCerereTest extends TestCase
{
    protected const COMPANIE = 990;

    protected $certificat;

    protected function setUp(): void
    {
        parent::setUp();

        ContextCompanie::fixeaza(self::COMPANIE);

        $this->certificat = AnafCertificat::create([
            'company_id' => self::COMPANIE,
            'thumbprint' => strtoupper(bin2hex(random_bytes(20))),
            'cn' => 'Certificat fără licență',
            'activ' => true,
            'bridge_url' => 'http://127.0.0.1:8099',
            'bridge_token' => 'cod-de-proba',
            'valabil_pana_la' => now()->addYear(),
        ]);
    }

    protected function tearDown(): void
    {
        AnafCertificat::query()->toateCompaniile()->where('company_id', self::COMPANIE)->delete();
        ContextCompanie::elibereaza();

        parent::tearDown();
    }

    public function test_licenta_se_trimite_si_comanda_se_reia(): void
    {
        $raspunsuri = [
            Http::response(['eroare' => 'Programul nu are licență validă pe acest calculator.'], 403),
            Http::response(['mesaje' => []], 200),
        ];

        Http::fake(['*' => Http::sequence($raspunsuri)]);

        $trimiteri = 0;

        $this->mock(LicentiereBridge::class, function ($mock) use (&$trimiteri) {
            $mock->shouldReceive('reinnoieste')->andReturnUsing(function () use (&$trimiteri) {
                $trimiteri++;

                return ['emisa' => true, 'expira' => now()->addMonth()->toIso8601String(), 'motiv' => null];
            });
        });

        $raspuns = $this->transport()->get('/listaMesaje', ['zile' => 1]);

        $this->assertSame(1, $trimiteri, 'Licența trebuia trimisă o dată.');
        $this->assertSame(200, $raspuns->status(), 'Comanda trebuia reluată după licențiere.');
    }

    /** Daca licenta tot nu se poate da, se intoarce refuzul, nu se invarte. */
    public function test_fara_licenta_emisa_ramane_refuzul(): void
    {
        Http::fake(['*' => Http::response(['eroare' => 'Programul nu are licență validă pe acest calculator.'], 403)]);

        $this->mock(LicentiereBridge::class, function ($mock) {
            $mock->shouldReceive('reinnoieste')->andReturn([
                'emisa' => false,
                'expira' => null,
                'motiv' => 'calculatorul nu a răspuns',
            ]);
        });

        $raspuns = $this->transport()->get('/listaMesaje', ['zile' => 1]);

        $this->assertSame(403, $raspuns->status());
    }

    /** Un refuz care nu e despre licenta se intoarce asa cum e. */
    public function test_alt_refuz_nu_cheama_licentierea(): void
    {
        Http::fake(['*' => Http::response(['eroare' => 'Cod de acces invalid.'], 403)]);

        $this->mock(LicentiereBridge::class, function ($mock) {
            $mock->shouldNotReceive('reinnoieste');
        });

        $this->assertSame(403, $this->transport()->get('/listaMesaje')->status());
    }

    protected function transport(): BridgeTransport
    {
        $certificate = app(CertificatService::class);
        $certificate->foloseste($this->certificat);

        return new BridgeTransport(config('anaf.spv'), $certificate);
    }
}
