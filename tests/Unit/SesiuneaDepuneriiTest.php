<?php

namespace Tests\Unit;

use App\Models\AnafCertificat;
use App\Services\Anaf\Declaratii\DepunereService;
use App\Services\Anaf\Spv\CertificatService;
use App\Support\ContextCompanie;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

/**
 * Sesiunea deschisă la ANAF pentru depunere se ține, nu se reface.
 *
 * Autentificarea nu e un apel, ci trei — pagina, poarta F5 și provocarea ei —,
 * și începe prin a arunca coșul de cookies. Făcută înaintea fiecărei
 * declarații, costa de trei ori mai mult decât depunerea însăși și arunca de
 * fiecare dată sesiunea abia deschisă.
 */
class SesiuneaDepuneriiTest extends TestCase
{
    protected const COMPANIE = 994;

    protected $certificat;

    protected function setUp(): void
    {
        parent::setUp();

        ContextCompanie::fixeaza(self::COMPANIE);
        Cache::flush();

        $this->certificat = AnafCertificat::create([
            'company_id' => self::COMPANIE,
            'thumbprint' => strtoupper(bin2hex(random_bytes(20))),
            'cn' => 'CONTABIL SEF',
            'activ' => true,
            'valabil_pana_la' => now()->addYear(),
            'bridge_url' => 'http://10.0.0.1:8099',
        ]);

        $this->app->make(CertificatService::class)->foloseste($this->certificat);
    }

    protected function tearDown(): void
    {
        AnafCertificat::query()->toateCompaniile()->where('company_id', self::COMPANIE)->delete();

        Cache::flush();
        ContextCompanie::elibereaza();

        parent::tearDown();
    }

    protected function serviciu(): DepunereService
    {
        return new DepunereService(
            config('anaf.declaratii'),
            $this->app->make(CertificatService::class)
        );
    }

    /** Un PDF de probă, cât să existe pe disc. */
    protected function pdf(): string
    {
        $cale = tempnam(sys_get_temp_dir(), 'dep') . '.pdf';
        file_put_contents($cale, '%PDF-1.4 proba');

        return $cale;
    }

    /** Răspunsul ANAF la o depunere izbutită: indicele de încărcare. */
    protected function izbanda(): string
    {
        return '<html><b style="color: #000000">1198828676</b></html>';
    }

    public function test_a_doua_depunere_nu_mai_deschide_o_sesiune(): void
    {
        Http::fake([
            '*/decl/login' => Http::response(['success' => true], 200),
            '*/decl/upload' => Http::response($this->izbanda(), 200),
        ]);

        $serviciu = $this->serviciu();
        $pdf = $this->pdf();

        $serviciu->autentificare();
        $serviciu->depune($pdf);

        $serviciu->autentificare();
        $serviciu->depune($pdf);

        @unlink($pdf);

        // O singură intrare pentru amândouă depunerile.
        Http::assertSentCount(3);
    }

    /**
     * Sesiunea stinsă înainte de vreme se vede din răspuns.
     *
     * Poarta ANAF nu spune că sesiunea a căzut: întoarce chiar formularul de
     * intrare, cu stare 200. Fără să-l recunoască, depunerea s-ar fi încheiat
     * cu „răspuns ANAF neinterpretabil" — iar declarația ar fi părut respinsă.
     */
    public function test_sesiunea_stinsa_se_deschide_din_nou_si_se_incearca_iar(): void
    {
        $apeluri = 0;

        Http::fake([
            '*/decl/login' => Http::response(['success' => true], 200),
            '*/decl/upload' => function () use (&$apeluri) {
                $apeluri++;

                // Întâi poarta F5, apoi depunerea adevărată.
                return $apeluri === 1
                    ? Http::response('<html><form action="/my.policy"></form></html>', 200)
                    : Http::response($this->izbanda(), 200);
            },
        ]);

        $serviciu = $this->serviciu();
        $pdf = $this->pdf();

        $serviciu->autentificare();
        $rezultat = $serviciu->depune($pdf);

        @unlink($pdf);

        $this->assertSame('1198828676', $rezultat['index_recipisa']);
        $this->assertSame(2, $apeluri, 'depunerea trebuia încercată din nou');
    }

    /** După o intrare eșuată nu rămâne minciuna că sesiunea e deschisă. */
    public function test_intrarea_esuata_nu_lasa_in_urma_o_sesiune_inchipuita(): void
    {
        Http::fake([
            '*/decl/login' => Http::sequence()
                ->push(['eroare' => 'nu merge'], 502)
                ->push(['success' => true], 200),
        ]);

        $serviciu = $this->serviciu();

        try {
            $serviciu->autentificare();
            $this->fail('autentificarea eșuată trebuia să se plângă');
        } catch (\Exception $e) {
            // Se aștepta.
        }

        // A doua chemare trebuie să încerce din nou, nu să creadă că e intrată.
        $serviciu->autentificare();

        Http::assertSentCount(2);
    }
}
