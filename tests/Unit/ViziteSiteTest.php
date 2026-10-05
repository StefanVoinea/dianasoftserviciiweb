<?php

namespace Tests\Unit;

use App\Http\Controllers\Api\ViziteSiteController;
use App\Models\MarketingContact;
use App\Models\SiteVizita;
use Illuminate\Http\Request;
use Tests\TestCase;

/**
 * Vizitele paginii de prezentare spvcurier.ro.
 *
 * Pagina bate din când în când cât e deschisă. Din bătăile ei trebuie să iasă
 * un singur rând pe vizită, cu adresa de la care a venit, paginile deschise și
 * durata — iar roboții și bătăile măsluite să nu strice socoteala.
 */
class ViziteSiteTest extends TestCase
{
    protected const BROWSER = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 Chrome/140.0 Safari/537.36';

    protected function tearDown(): void
    {
        SiteVizita::query()->where('sesiune', 'like', 'proba%')->delete();
        MarketingContact::query()->where('email', 'like', '%@proba-vizite.ro')->delete();

        parent::tearDown();
    }

    /** O bătaie, trimisă ca text simplu, cum o trimite browserul. */
    protected function bate(array $date, string $ip = '203.0.113.7', string $agent = self::BROWSER)
    {
        $cerere = Request::create('/api/vizita-site', 'POST', [], [], [], [
            'REMOTE_ADDR' => $ip,
            'HTTP_USER_AGENT' => $agent,
            'CONTENT_TYPE' => 'text/plain',
        ], json_encode($date));

        return (new ViziteSiteController())->inregistreaza($cerere);
    }

    /** @test */
    public function prima_bataie_deschide_vizita_cu_adresa_ei()
    {
        $raspuns = $this->bate([
            'id' => 'proba0000000000001',
            'secunde' => 0,
            'pagini' => ['home'],
            'referrer' => 'https://www.google.com/search?q=spv+curier',
        ]);

        $this->assertSame(204, $raspuns->status());

        $vizita = SiteVizita::where('sesiune', 'proba0000000000001')->first();

        $this->assertSame('203.0.113.7', $vizita->ip);
        $this->assertSame('calculator', $vizita->dispozitiv);
        $this->assertSame('home', $vizita->pagina_intrare);
        // Coada de parametri nu se păstrează: acolo stă ce a căutat omul.
        $this->assertSame('www.google.com/search', $vizita->referrer);
    }

    /** @test */
    public function bataile_urmatoare_cresc_durata_si_aduna_paginile_pe_acelasi_rand()
    {
        $this->bate(['id' => 'proba0000000000002', 'secunde' => 0, 'pagini' => ['home']]);
        $this->bate(['id' => 'proba0000000000002', 'secunde' => 15, 'pagini' => ['home', 'tarife']]);
        $this->bate(['id' => 'proba0000000000002', 'secunde' => 42, 'pagini' => ['home', 'tarife', 'demo']]);

        $this->assertSame(1, SiteVizita::where('sesiune', 'proba0000000000002')->count());

        $vizita = SiteVizita::where('sesiune', 'proba0000000000002')->first();

        $this->assertSame(42, $vizita->durata_secunde);
        $this->assertSame(['home', 'tarife', 'demo'], $vizita->pagini);
    }

    /** O bătaie întârziată, cu mai puține secunde, nu dă durata înapoi. */
    /** @test */
    public function durata_nu_scade_si_nu_trece_de_plafon()
    {
        $this->bate(['id' => 'proba0000000000003', 'secunde' => 90]);
        $this->bate(['id' => 'proba0000000000003', 'secunde' => 30]);

        $this->assertSame(90, SiteVizita::where('sesiune', 'proba0000000000003')->value('durata_secunde'));

        $this->bate(['id' => 'proba0000000000003', 'secunde' => 999999]);

        $this->assertSame(4 * 3600, SiteVizita::where('sesiune', 'proba0000000000003')->value('durata_secunde'));
    }

    /** @test */
    public function robotii_nu_se_numara()
    {
        $raspuns = $this->bate(
            ['id' => 'proba0000000000004', 'secunde' => 0],
            '203.0.113.8',
            'Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)'
        );

        $this->assertSame(204, $raspuns->status());
        $this->assertSame(0, SiteVizita::where('sesiune', 'proba0000000000004')->count());
    }

    /** @test */
    public function vizita_fara_numar_bun_e_refuzata()
    {
        $this->assertSame(422, $this->bate(['id' => 'x'])->status());
        $this->assertSame(422, $this->bate(['id' => "proba'; drop table site_vizite"])->status());
    }

    /** Durata unei vizite nu poate fi umflată de la altă adresă. */
    /** @test */
    public function alta_adresa_nu_poate_scrie_peste_vizita()
    {
        $this->bate(['id' => 'proba0000000000005', 'secunde' => 10]);
        $this->bate(['id' => 'proba0000000000005', 'secunde' => 3000], '198.51.100.9');

        $this->assertSame(10, SiteVizita::where('sesiune', 'proba0000000000005')->value('durata_secunde'));
    }

    /** Cine vine din scrisoarea noastră e recunoscut după codul din legătură. */
    /** @test */
    public function codul_din_scrisoare_leaga_vizita_de_firma()
    {
        $contact = MarketingContact::create([
            'denumire' => 'Cabinet de Probă SRL',
            'cui' => '99000001',
            'email' => 'cabinet@proba-vizite.ro',
            'abonat' => true,
        ]);

        $this->bate(['id' => 'proba0000000000006', 'secunde' => 0, 'f' => $contact->fresh()->jeton]);
        $this->bate(['id' => 'proba0000000000007', 'secunde' => 0, 'f' => 'cod-care-nu-exista']);

        $this->assertSame($contact->id, SiteVizita::where('sesiune', 'proba0000000000006')->value('contact_id'));
        $this->assertNull(SiteVizita::where('sesiune', 'proba0000000000007')->value('contact_id'));
    }

    /** @test */
    public function fila_arata_vizitele_cifrele_si_adunarea_pe_adresa()
    {
        $adresa = '203.0.113.' . random_int(100, 250);

        $this->bate(['id' => 'proba0000000000008', 'secunde' => 20, 'pagini' => ['home']], $adresa);
        $this->bate(['id' => 'proba0000000000009', 'secunde' => 40, 'pagini' => ['home', 'spv']], $adresa);

        $date = (new ViziteSiteController())->index(
            Request::create('/api/administrare/vizite-site', 'GET', ['zile' => 1, 'ip' => $adresa])
        )->getData(true);

        $this->assertSame(2, $date['total']);
        $this->assertSame(['home', 'spv'], $date['data'][0]['pagini']);
        $this->assertSame(40, $date['data'][0]['durata_secunde']);
        $this->assertGreaterThanOrEqual(2, $date['sumar']['azi']['vizite']);

        $rand = collect($date['pe_adresa'])->firstWhere('ip', $adresa);

        $this->assertSame(2, $rand['vizite']);
        $this->assertSame(60, $rand['durata_secunde']);
    }
}
