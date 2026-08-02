<?php

namespace Tests\Unit;

use App\Models\AnafCertificat;
use App\Models\Company;
use App\Services\Anaf\Bridge\Punte;
use App\Services\Anaf\Spv\CertificatService;
use App\Services\Anaf\Spv\Transport\BridgeTransport;
use App\Support\ContextCompanie;
use Tests\TestCase;

/**
 * Cat asteapta aplicatia raspunsul programului local.
 *
 * Prin tunel, in acelasi rastimp incap si drumul comenzii pana la calculatorul
 * clientului, si apelul lui catre ANAF — care are el singur voie sa tina un
 * minut. Cu rabdarea de la legatura directa, cel care intreaba renunta primul,
 * iar omul primeste „cURL error 28" in locul raspunsului care tocmai venea.
 */
class RabdareTunelTest extends TestCase
{
    protected $client;

    protected function setUp(): void
    {
        parent::setUp();

        $this->client = Company::create(['denumire' => 'FIRMA RABDARE SRL', 'cui' => '99000888']);
        ContextCompanie::fixeaza($this->client->id);
    }

    protected function tearDown(): void
    {
        AnafCertificat::query()->toateCompaniile()->where('company_id', $this->client->id)->delete();
        $this->client->delete();
        ContextCompanie::elibereaza();

        parent::tearDown();
    }

    public function test_la_tunel_se_asteapta_mai_mult_decat_la_legatura_directa(): void
    {
        $direct = $this->rabdarea($this->certificat('directa'));
        $tunel = $this->rabdarea($this->certificat('tunel'));

        $this->assertSame((int) config('anaf.spv.timeout'), $direct);
        $this->assertSame((int) config('anaf.spv.timeout_tunel'), $tunel);
        $this->assertGreaterThan($direct, $tunel, 'Prin tunel trebuie așteptat mai mult, nu la fel.');
    }

    /**
     * Cererea chiar se poate construi si trimite.
     *
     * Verificarea rabdarii se face pe metoda, dar reglajele se pun pe cererea
     * insasi — iar una dintre ele nu exista in versiunea de Laravel de aici.
     * Fara apelul acesta, lipsa s-ar fi vazut abia pe serverul de productie.
     */
    public function test_cererea_se_trimite_cu_reglajele_puse(): void
    {
        \Illuminate\Support\Facades\Http::fake([
            '*' => \Illuminate\Support\Facades\Http::response('{"mesaje":[]}', 200),
        ]);

        $certificate = app(CertificatService::class);
        $certificate->foloseste($this->certificat('directa'));

        $raspuns = (new BridgeTransport(config('anaf.spv'), $certificate))
            ->get('/listaMesaje', ['zile' => 30]);

        $this->assertSame(200, $raspuns->status());

        \Illuminate\Support\Facades\Http::assertSent(function ($cerere) {
            return strpos($cerere->url(), '/spv/listaMesaje') !== false;
        });
    }

    /** Ragazul puntii trebuie sa incapa in rabdarea celui care intreaba. */
    public function test_puntea_raspunde_inainte_ca_apelantul_sa_renunte(): void
    {
        $this->assertLessThan(
            (int) config('anaf.spv.timeout_tunel'),
            Punte::ASTEPTARE_SECUNDE,
            'Puntea așteaptă mai mult decât cel care a cerut — răspunsul ei n-ar mai ajunge la nimeni.'
        );
    }

    protected function certificat(string $mod): AnafCertificat
    {
        return AnafCertificat::create([
            'company_id' => $this->client->id,
            'thumbprint' => strtoupper(bin2hex(random_bytes(20))),
            'cn' => 'PROBA RABDARE ' . $mod,
            'activ' => true,
            'mod_legatura' => $mod,
            'bridge_url' => $mod === 'tunel' ? null : 'http://192.168.1.50:8099',
            'bridge_token' => 'cod-de-proba',
            'valabil_pana_la' => now()->addYear(),
        ]);
    }

    /** Rabdarea aleasa de transport pentru certificatul dat. */
    protected function rabdarea(AnafCertificat $certificat): int
    {
        $certificate = app(CertificatService::class);
        $certificate->foloseste($certificat);

        $transport = new BridgeTransport(config('anaf.spv'), $certificate);

        $metoda = new \ReflectionMethod($transport, 'rabdarea');
        $metoda->setAccessible(true);

        return $metoda->invoke($transport);
    }
}
