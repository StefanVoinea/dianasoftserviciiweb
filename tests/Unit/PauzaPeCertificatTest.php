<?php

namespace Tests\Unit;

use App\Models\AnafCertificat;
use App\Services\Anaf\Spv\CertificatService;
use App\Services\Anaf\Spv\Contracts\SpvTransport;
use App\Services\Anaf\Spv\SpvClient;
use App\Support\ContextCompanie;
use Illuminate\Http\Client\Response;
use Tests\TestCase;

/**
 * Pauza cerută de ANAF între două apeluri se ține pe fiecare certificat.
 *
 * ANAF numără apelurile pe certificatul care le face, deci două tokene ale
 * aceluiași client n-au nicio treabă unul cu altul. Ținută pe toate laolaltă,
 * pauza încetinea de două ori un client cu două tokene — degeaba.
 */
class PauzaPeCertificatTest extends TestCase
{
    protected const COMPANIE = 993;

    /** Cât se așteaptă în probe: destul cât să se vadă, puțin cât să nu supere. */
    protected const RAGAZ_MS = 300;

    protected $unul;
    protected $altul;

    protected function setUp(): void
    {
        parent::setUp();

        ContextCompanie::fixeaza(self::COMPANIE);

        $this->unul = $this->certificat('CONTABIL SEF');
        $this->altul = $this->certificat('COLEGUL');
    }

    protected function tearDown(): void
    {
        AnafCertificat::query()->toateCompaniile()->where('company_id', self::COMPANIE)->delete();

        ContextCompanie::elibereaza();

        parent::tearDown();
    }

    protected function certificat(string $nume): AnafCertificat
    {
        return AnafCertificat::create([
            'company_id' => self::COMPANIE,
            'thumbprint' => strtoupper(bin2hex(random_bytes(20))),
            'cn' => $nume,
            'activ' => true,
            'valabil_pana_la' => now()->addYear(),
            'bridge_url' => 'http://10.0.0.1:8099',
        ]);
    }

    /** Un transport care nu pleacă nicăieri: aici se măsoară doar așteptarea. */
    protected function transport(): SpvTransport
    {
        return new class implements SpvTransport {
            public function get($path, array $query = []): Response
            {
                return new Response(new \GuzzleHttp\Psr7\Response(200, [], json_encode(['mesaje' => []])));
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

    protected function client(bool $peCertificat = true): SpvClient
    {
        return new SpvClient(
            $this->transport(),
            [
                'throttle_ms' => self::RAGAZ_MS,
                'throttle_pe_certificat' => $peCertificat,
                'zile_max' => 60,
            ],
            $this->app->make(CertificatService::class)
        );
    }

    /** Cât ține o lucrare, în milisecunde. */
    protected function cat(callable $lucrarea): float
    {
        $inceput = microtime(true);
        $lucrarea();

        return (microtime(true) - $inceput) * 1000;
    }

    /** Două apeluri cu același token își așteaptă rândul, ca până acum. */
    public function test_acelasi_token_isi_asteapta_randul(): void
    {
        $certificate = $this->app->make(CertificatService::class);
        $client = $this->client();

        $certificate->foloseste($this->unul);

        $tinut = $this->cat(function () use ($client) {
            $client->listaMesaje(1);
            $client->listaMesaje(1);
        });

        $this->assertGreaterThanOrEqual(self::RAGAZ_MS * 0.9, $tinut);
    }

    /** Două tokene deosebite nu se așteaptă unul pe altul. */
    public function test_doua_tokene_nu_se_asteapta_unul_pe_altul(): void
    {
        $certificate = $this->app->make(CertificatService::class);
        $client = $this->client();

        $tinut = $this->cat(function () use ($client, $certificate) {
            $certificate->foloseste($this->unul);
            $client->listaMesaje(1);

            $certificate->foloseste($this->altul);
            $client->listaMesaje(1);
        });

        $this->assertLessThan(self::RAGAZ_MS * 0.5, $tinut);
    }

    /**
     * Pus pe „false", se lucrează ca înainte: o singură socoteală pentru toate.
     *
     * Rămâne acolo pentru ziua în care s-ar dovedi că ANAF numără altfel — pe
     * adresă, de pildă —, ca întoarcerea să fie o linie în `.env`, nu o punere
     * la loc a codului.
     */
    public function test_socoteala_se_poate_tine_si_pe_toate_laolalta(): void
    {
        $certificate = $this->app->make(CertificatService::class);
        $client = $this->client(false);

        $tinut = $this->cat(function () use ($client, $certificate) {
            $certificate->foloseste($this->unul);
            $client->listaMesaje(1);

            $certificate->foloseste($this->altul);
            $client->listaMesaje(1);
        });

        $this->assertGreaterThanOrEqual(self::RAGAZ_MS * 0.9, $tinut);
    }

    /** Fără evidență de certificate, toate apelurile împart aceeași socoteală. */
    public function test_fara_certificat_stiut_se_asteapta_ca_pana_acum(): void
    {
        $client = new SpvClient(
            $this->transport(),
            ['throttle_ms' => self::RAGAZ_MS, 'zile_max' => 60],
            null
        );

        $tinut = $this->cat(function () use ($client) {
            $client->listaMesaje(1);
            $client->listaMesaje(1);
        });

        $this->assertGreaterThanOrEqual(self::RAGAZ_MS * 0.9, $tinut);
    }
}
