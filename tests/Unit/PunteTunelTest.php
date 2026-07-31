<?php

namespace Tests\Unit;

use App\Models\AnafCertificat;
use App\Models\BridgeComanda;
use App\Services\Anaf\Bridge\Licente;
use App\Services\Anaf\Bridge\Punte;
use App\Services\Anaf\Spv\CertificatService;
use App\Support\ContextCompanie;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Puntea către programele locale aflate în spatele unui router.
 *
 * Serverul stă în cloud, calculatorul cu tokenul stă în rețeaua clientului.
 * Nimeni nu deschide un port pe routerul lui, așa că nu mai sună serverul la
 * client: clientul întreabă serverul, pe 443, ce are de făcut. Comenzile stau
 * între cele două capete în tabelul acesta.
 */
class PunteTunelTest extends TestCase
{
    protected const COMPANIE = 966;

    protected $certificat;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake(config('filesystems.default'));
        ContextCompanie::fixeaza(self::COMPANIE);

        $this->certificat = AnafCertificat::create([
            'company_id' => self::COMPANIE,
            'thumbprint' => strtoupper(bin2hex(random_bytes(20))),
            'cn' => 'POPESCU ION',
            'activ' => true,
            'valabil_pana_la' => now()->addYear(),
            'bridge_token' => 'cod-de-instalare',
            'mod_legatura' => 'tunel',
        ]);
    }

    protected function tearDown(): void
    {
        BridgeComanda::where('company_id', self::COMPANIE)->delete();
        AnafCertificat::query()->toateCompaniile()->where('company_id', self::COMPANIE)->delete();
        ContextCompanie::elibereaza();

        parent::tearDown();
    }

    protected function punte(): Punte
    {
        return $this->app->make(Punte::class);
    }

    /** Certificatul prin tunel primește adresa punții, nu una din rețeaua clientului. */
    public function test_certificatul_prin_tunel_trimite_comenzile_la_punte(): void
    {
        $certificate = $this->app->make(CertificatService::class);
        $certificate->foloseste($this->certificat);

        $this->assertSame(
            rtrim(config('app.url'), '/') . '/api/punte/' . $this->certificat->id,
            $certificate->bridge()['url']
        );
    }

    /** Certificatul obișnuit rămâne pe adresa lui din rețea. */
    public function test_certificatul_direct_ramane_neschimbat(): void
    {
        $this->certificat->update(['mod_legatura' => 'direct', 'bridge_url' => 'http://192.168.1.44:8099']);

        $certificate = $this->app->make(CertificatService::class);
        $certificate->foloseste($this->certificat->fresh());

        $this->assertSame('http://192.168.1.44:8099', $certificate->bridge()['url']);
    }

    /** O comandă pusă în coadă păstrează metoda, calea, întrebarea și antetele. */
    public function test_comanda_pastreaza_tot_ce_trebuie_dus_mai_departe(): void
    {
        $cerere = Request::create('/api/punte/1/spv/listaMesaje?zile=5', 'GET');
        $cerere->headers->set('X-Thumbprint', 'ABC123');
        $cerere->headers->set('Authorization', 'Bearer jeton-semnat');

        $comanda = $this->punte()->pune($this->certificat, $cerere, '/spv/listaMesaje');

        $this->assertSame('GET', $comanda->metoda);
        $this->assertSame('/spv/listaMesaje?zile=5', $comanda->cale);
        $this->assertSame('ABC123', $comanda->antete['x-thumbprint']);
        $this->assertArrayNotHasKey('host', $comanda->antete, 'antetele legăturii noastre rămân aici');
    }

    /**
     * Legitimarea se schimbă la punte, pentru că cele două uși cer lucruri
     * diferite: puntea cere jeton semnat, programul local încă nelicențiat nu
     * știe decât codul lui de instalare.
     */
    public function test_programul_nelicentiat_primeste_codul_lui_nu_jetonul(): void
    {
        $cerere = Request::create('/api/punte/1/certificate', 'GET');
        $cerere->headers->set('Authorization', 'Bearer v1.jeton.semnat');

        $comanda = $this->punte()->pune($this->certificat, $cerere, '/certificate');

        $this->assertSame('Bearer cod-de-instalare', $comanda->antete['authorization']);
    }

    /** Unul licențiat recunoaște jetonul, deci îl primește pe el. */
    public function test_programul_licentiat_primeste_jeton_semnat(): void
    {
        $this->app->make(Licente::class)->pregatesteCheile();
        $this->certificat->update(['licenta_pana_la' => now()->addDays(20)]);

        $cerere = Request::create('/api/punte/1/certificate', 'GET');
        $cerere->headers->set('Authorization', 'Bearer v1.altceva');

        $comanda = $this->punte()->pune($this->certificat->fresh(), $cerere, '/certificate');

        $this->assertStringStartsWith('Bearer v1.', $comanda->antete['authorization']);
        $this->assertNotSame('Bearer v1.altceva', $comanda->antete['authorization'], 'se semnează unul proaspăt');
        $this->assertSame('asteapta', $comanda->stare);
    }

    /** Corpurile mari nu intră în tabel, ci în storage. */
    public function test_corpul_comenzii_se_scrie_pe_disc_nu_in_tabel(): void
    {
        $cerere = Request::create('/api/punte/1/semnare', 'POST', [], [], [], [], '%PDF-1.4 de semnat');

        $comanda = $this->punte()->pune($this->certificat, $cerere, '/semnare');

        $this->assertNotNull($comanda->corp_fisier);
        $this->assertSame('%PDF-1.4 de semnat', $comanda->corpul());
    }

    /** Agentul se legitimează cu codul de instalare al certificatului lui. */
    public function test_agentul_este_recunoscut_dupa_codul_lui(): void
    {
        $bun = Request::create('/api/punte/agent/asteapta', 'POST');
        $bun->headers->set('Authorization', 'Bearer cod-de-instalare');
        $bun->headers->set('X-Certificat', (string) $this->certificat->id);

        $this->assertSame($this->certificat->id, optional($this->punte()->certificatulAgentului($bun))->id);

        $gresit = Request::create('/api/punte/agent/asteapta', 'POST');
        $gresit->headers->set('Authorization', 'Bearer alt-cod');
        $gresit->headers->set('X-Certificat', (string) $this->certificat->id);

        $this->assertNull($this->punte()->certificatulAgentului($gresit));
    }

    /** Agentul ia comanda, iar ea nu mai e dată nimănui altcuiva. */
    public function test_comanda_luata_nu_se_mai_da_a_doua_oara(): void
    {
        $cerere = Request::create('/api/punte/1/certificate', 'GET');
        $this->punte()->pune($this->certificat, $cerere, '/certificate');

        $luata = $this->punte()->urmatoarea($this->certificat, 1);

        $this->assertNotNull($luata);
        $this->assertSame('luata', $luata->fresh()->stare);

        $this->assertNull($this->punte()->urmatoarea($this->certificat, 1), 'a doua întrebare nu mai găsește nimic');
    }

    /** Comanda unui certificat nu ajunge la agentul altuia. */
    public function test_comanda_nu_pleaca_la_agentul_altui_certificat(): void
    {
        $altul = AnafCertificat::create([
            'company_id' => self::COMPANIE,
            'thumbprint' => strtoupper(bin2hex(random_bytes(20))),
            'cn' => 'IONESCU MARIA',
            'activ' => true,
            'valabil_pana_la' => now()->addYear(),
            'bridge_token' => 'alt-cod',
            'mod_legatura' => 'tunel',
        ]);

        $this->punte()->pune($this->certificat, Request::create('/api/punte/1/certificate', 'GET'), '/certificate');

        $this->assertNull($this->punte()->urmatoarea($altul, 1));
    }

    /** Puntea e o rută publică: doar jetonul semnat de server o deschide. */
    public function test_puntea_primeste_numai_cereri_semnate_de_server(): void
    {
        $licente = $this->app->make(Licente::class);
        $licente->pregatesteCheile();

        $bun = Request::create('/api/punte/1/certificate', 'GET');
        $bun->headers->set('Authorization', 'Bearer ' . $licente->jeton());

        $this->assertTrue($this->punte()->cerereDeLaServer($bun));

        foreach (['Bearer cod-de-instalare', 'Bearer v1.aaa.bbb', ''] as $incercare) {
            $rea = Request::create('/api/punte/1/certificate', 'GET');

            if ($incercare !== '') {
                $rea->headers->set('Authorization', $incercare);
            }

            $this->assertFalse($this->punte()->cerereDeLaServer($rea), 'a trecut: ' . $incercare);
        }
    }

    /** Când agentul răspunde, aplicația primește exact ce a spus programul local. */
    public function test_raspunsul_agentului_ajunge_la_aplicatie(): void
    {
        $comanda = $this->punte()->pune(
            $this->certificat,
            Request::create('/api/punte/1/certificate', 'GET'),
            '/certificate'
        );

        $fisier = BridgeComanda::DOSAR . '/rez_proba.bin';
        Storage::put($fisier, '{"cn":"POPESCU ION"}');

        $comanda->update([
            'stare' => 'gata',
            'status' => 200,
            'rezultat_antete' => ['content-type' => 'application/json'],
            'rezultat_fisier' => $fisier,
        ]);

        $terminata = $this->punte()->asteapta($comanda, 2);

        $this->assertNotNull($terminata);
        $this->assertSame(200, $terminata->status);
        $this->assertSame('{"cn":"POPESCU ION"}', $terminata->rezultatul());
    }

    /** Calculatorul închis: aplicația nu așteaptă la nesfârșit. */
    public function test_fara_agent_asteptarea_se_incheie_si_comanda_se_curata(): void
    {
        $comanda = $this->punte()->pune(
            $this->certificat,
            Request::create('/api/punte/1/certificate', 'GET'),
            '/certificate'
        );

        $inceput = microtime(true);
        $rezultat = $this->punte()->asteapta($comanda, 1);

        $this->assertNull($rezultat);
        $this->assertLessThan(3, microtime(true) - $inceput);
        $this->assertNull(BridgeComanda::find($comanda->id), 'comanda nu rămâne agățată în coadă');
    }
}
