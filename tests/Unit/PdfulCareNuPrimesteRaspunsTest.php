<?php

namespace Tests\Unit;

use App\Models\AnafCertificat;
use App\Services\Anaf\Declaratii\DeclaratieException;
use App\Services\Anaf\Declaratii\PdfDeclaratie;
use App\Services\Anaf\Spv\CertificatService;
use App\Support\ContextCompanie;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

/**
 * PDF-ul trimis spre citire la un calculator care nu răspunde.
 *
 * XML-ul unei declarații stă înăuntrul PDF-ului, iar biblioteca care îl scoate
 * de acolo e pe calculatorul clientului, lângă token. Când acela e închis sau
 * programul e oprit, cererea așteaptă un minut și moare cu „cURL error 28".
 *
 * Lăsată să urce, poticnirea dărâma toată încărcarea: și celelalte fișiere din
 * același teanc, care n-aveau nicio vină. Omul rămânea, după un minut de
 * așteptare, cu un mesaj de bibliotecă în față, iar la noi pleca și un email de
 * alertă pentru un calculator închis la client.
 */
class PdfulCareNuPrimesteRaspunsTest extends TestCase
{
    protected const COMPANIE = 995;

    protected $certificat;

    protected function setUp(): void
    {
        parent::setUp();

        ContextCompanie::fixeaza(self::COMPANIE);

        $this->certificat = AnafCertificat::create([
            'company_id' => self::COMPANIE,
            'thumbprint' => strtoupper(bin2hex(random_bytes(20))),
            'cn' => 'POPA ROXANA',
            'activ' => true,
            'valabil_pana_la' => now()->addYear(),
            'bridge_url' => 'http://10.0.0.9:8099',
        ]);

        $this->app->make(CertificatService::class)->foloseste($this->certificat);
    }

    protected function tearDown(): void
    {
        AnafCertificat::query()->toateCompaniile()->where('company_id', self::COMPANIE)->delete();
        ContextCompanie::elibereaza();

        parent::tearDown();
    }

    /** Un PDF de probă pe disc: conținutul nu contează, numai că există. */
    protected function pdf(): string
    {
        $cale = tempnam(sys_get_temp_dir(), 'decl') . '.pdf';
        file_put_contents($cale, '%PDF-1.4 proba');

        return $cale;
    }

    protected function cititorul(): PdfDeclaratie
    {
        return new PdfDeclaratie(
            ['timeout' => 60] + config('anaf.declaratii'),
            $this->app->make(CertificatService::class)
        );
    }

    /** @test */
    public function asteptarea_fara_raspuns_se_spune_pe_intelesul_omului()
    {
        Http::fake(function () {
            throw new ConnectionException(
                'cURL error 28: Operation timed out after 60002 milliseconds with 0 bytes received'
            );
        });

        $cale = $this->pdf();

        try {
            $this->cititorul()->citeste($cale);
            $this->fail('trebuia să se plângă');
        } catch (DeclaratieException $e) {
            $this->assertStringContainsString('nu a răspuns', $e->getMessage());
            $this->assertStringContainsString('60 de secunde', $e->getMessage());
            $this->assertStringContainsString('tokenul e conectat', $e->getMessage());
            $this->assertStringNotContainsString('cURL', $e->getMessage(), 'omul n-are ce face cu vorba bibliotecii');
        } finally {
            @unlink($cale);
        }
    }

    /**
     * Miezul: e o pană a fișierului, nu a întregii încărcări.
     *
     * Bucla care ia fișierele unul câte unul prinde DeclaratieException și trece
     * la următorul; orice altceva o oprește de tot.
     *
     * @test
     */
    public function poticnirea_e_de_felul_pe_care_incarcarea_stie_sa_l_treaca()
    {
        Http::fake(function () {
            throw new ConnectionException('cURL error 28: Operation timed out');
        });

        $cale = $this->pdf();

        $this->expectException(DeclaratieException::class);

        try {
            $this->cititorul()->citeste($cale);
        } finally {
            @unlink($cale);
        }
    }

    /** Când programul răspunde, dar cu un refuz, se spune tot pe înțeles. */
    /** @test */
    public function refuzul_programului_local_ramane_spus_cum_era()
    {
        Http::fake(['*' => Http::response(['eroare' => 'PDF stricat'], 422)]);

        $cale = $this->pdf();

        try {
            $this->cititorul()->citeste($cale);
            $this->fail('trebuia să se plângă');
        } catch (DeclaratieException $e) {
            $this->assertStringContainsString('PDF stricat', $e->getMessage());
        } finally {
            @unlink($cale);
        }
    }

    /** Un PDF care nu există se spune înainte de orice drum la client. */
    /** @test */
    public function pdf_ul_lipsa_se_spune_fara_sa_se_mai_intrebe_nimeni()
    {
        Http::fake();

        $this->expectException(DeclaratieException::class);

        $this->cititorul()->citeste(sys_get_temp_dir() . '/nu-exista-' . uniqid() . '.pdf');
    }
}
