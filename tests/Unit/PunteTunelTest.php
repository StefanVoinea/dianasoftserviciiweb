<?php

namespace Tests\Unit;

use App\Http\Controllers\Api\PunteController;
use App\Models\AnafCertificat;
use App\Models\BridgeComanda;
use App\Services\Anaf\Bridge\Licente;
use App\Services\Anaf\Bridge\Punte;
use App\Services\Anaf\Spv\CertificatService;
use App\Support\Aplicatia;
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

    /**
     * O comandă dusă la capăt stinge vestea că tokenul își așteaptă codul.
     *
     * E cea mai bună dovadă că fereastra s-a închis: cheia a fost dată, fie că
     * omul a scris codul de la distanță, fie că s-a dus până la calculator.
     * Fără asta, vestea rămânea agățată în baza de date, iar fila din browser și
     * telefonul cereau codul ceasuri după ce fereastra nu mai era pe niciun
     * ecran.
     */
    public function test_comanda_dusa_la_capat_stinge_vestea_pinului(): void
    {
        $this->certificat->update([
            'pin_stare' => 'asteapta',
            'pin_motiv' => 'Token Logon',
            'pin_verificat_la' => now(),
        ]);

        $comanda = $this->punte()->pune(
            $this->certificat,
            Request::create('/api/punte/1/certificate', 'GET'),
            '/certificate'
        );

        $raspuns = Request::create('/api/punte/agent/rezultat/' . $comanda->id, 'POST', [], [], [], [], '{"cn":"POPESCU ION"}');
        $raspuns->headers->set('Authorization', 'Bearer cod-de-instalare');
        $raspuns->headers->set('X-Status', '200');

        (new PunteController($this->punte()))->rezultat($raspuns, $comanda);

        $this->assertNull(
            $this->certificat->fresh()->pin_stare,
            'Programul local a răspuns, deci nu-l mai ține nicio fereastră.'
        );
    }

    /**
     * Cât omul e întrebat de codul tokenului, ceasul așteptării nu curge.
     *
     * Altfel lucrarea pica tocmai după ce el scria codul: fereastra se închidea,
     * cheia se dădea, iar răspunsul sosea la o aplicație care nu mai aștepta. În
     * lanțul acesta încap zeci de secunde numai până se bagă de seamă că s-a
     * deschis fereastra, plus cât îi trebuie omului să o vadă și să scrie.
     */
    public function test_asteptarea_se_lungeste_cat_tokenul_isi_cere_codul(): void
    {
        $this->certificat->update([
            'pin_stare' => 'asteapta',
            'pin_verificat_la' => now(),
        ]);

        $comanda = $this->punte()->pune(
            $this->certificat,
            Request::create('/api/punte/1/certificate', 'GET'),
            '/certificate'
        );

        $deCand = microtime(true);
        $rezultat = $this->punte()->asteapta($comanda, 1);
        $cat = microtime(true) - $deCand;

        $this->assertNull($rezultat, 'Nimeni n-a răspuns; până la urmă tot se încheie.');

        $this->assertGreaterThan(
            1.8,
            $cat,
            'Cu tokenul care își cere codul, o secundă de răbdare trebuie să se dea din nou.'
        );
    }

    /** Vestea veche nu mai lungește nimic: fereastra aceea nu mai e. */
    public function test_vestea_veche_nu_lungeste_asteptarea(): void
    {
        $this->certificat->update([
            'pin_stare' => 'asteapta',
            'pin_verificat_la' => now()->subHour(),
        ]);

        $comanda = $this->punte()->pune(
            $this->certificat,
            Request::create('/api/punte/1/certificate', 'GET'),
            '/certificate'
        );

        $deCand = microtime(true);
        $this->punte()->asteapta($comanda, 1);

        $this->assertLessThan(1.8, microtime(true) - $deCand);
    }

    /**
     * Codul se cere acolo unde s-a apăsat butonul, nu în toate părțile.
     *
     * Vestea vine de la agent, care nu știe cine a pornit lucrarea. Drumul se
     * urmează însă înapoi pe comanda aflată atunci în lucru: ea a cerut cheia,
     * deci ea a deschis fereastra.
     */
    public function test_codul_se_cere_de_unde_a_plecat_comanda(): void
    {
        $cerere = Request::create('/api/punte/1/certificate', 'GET');
        $cerere->headers->set(Aplicatia::ANTETUL, Aplicatia::MOBIL);
        app()->instance('request', $cerere);

        $comanda = $this->punte()->pune($this->certificat, $cerere, '/certificate');
        $comanda->update(['stare' => 'luata']);

        $vestea = Request::create('/api/punte/agent/pin-asteapta', 'POST');
        $vestea->headers->set('Authorization', 'Bearer cod-de-instalare');
        $vestea->headers->set('X-Pin-Titlu', base64_encode('Token Logon'));

        (new PunteController($this->punte()))->pinAsteapta($vestea);

        $tokenul = $this->certificat->fresh();

        $this->assertSame('asteapta', $tokenul->pin_stare);
        $this->assertSame(Aplicatia::MOBIL, $tokenul->pin_cerut_din);
    }

    /**
     * Fără nicio comandă în lucru, codul se cere oriunde.
     *
     * Agentul duce comenzile mai multora, iar fereastra poate rămâne de la o
     * lucrare pornită de la sine. Atunci e mai bine întrebat oricine e prin
     * preajmă decât lăsat tokenul să aștepte.
     */
    public function test_fara_comanda_in_lucru_codul_se_cere_oriunde(): void
    {
        $vestea = Request::create('/api/punte/agent/pin-asteapta', 'POST');
        $vestea->headers->set('Authorization', 'Bearer cod-de-instalare');

        (new PunteController($this->punte()))->pinAsteapta($vestea);

        $this->assertSame(Aplicatia::FUNDAL, $this->certificat->fresh()->pin_cerut_din);
    }

    /** Comanda ține minte de unde a plecat, ca acolo să se ceară și codul. */
    public function test_comanda_tine_minte_de_unde_a_plecat(): void
    {
        $cerere = Request::create('/api/punte/1/certificate', 'GET');
        $cerere->headers->set(Aplicatia::ANTETUL, Aplicatia::MOBIL);
        app()->instance('request', $cerere);

        $comanda = $this->punte()->pune($this->certificat, $cerere, '/certificate');

        $this->assertSame(Aplicatia::MOBIL, $comanda->cerut_din);
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

    /** Pânda agentului lasă urmă: altfel n-am ști cui are rost să-i trimitem. */
    public function test_panda_agentului_se_tine_minte(): void
    {
        $this->assertNull($this->certificat->agent_vazut_la);
        $this->assertFalse($this->punte()->agentulEsteTreaz($this->certificat));

        $this->punte()->urmatoarea($this->certificat, 1);

        $this->assertNotNull($this->certificat->fresh()->agent_vazut_la);
        $this->assertTrue($this->punte()->agentulEsteTreaz($this->certificat->fresh()));
    }

    /** Un agent care n-a mai dat semne de mult nu mai e socotit treaz. */
    public function test_agentul_tacut_de_mult_nu_mai_e_socotit_treaz(): void
    {
        $this->certificat->update([
            'agent_vazut_la' => now()->subSeconds(Punte::AGENT_TREAZ_SECUNDE + 30),
        ]);

        $this->assertFalse($this->punte()->agentulEsteTreaz($this->certificat->fresh()));
    }

    /**
     * Un calculator nou se prezintă singur: prin tunel serverul n-are cum să-l
     * caute, deci agentul îi trimite certificatele de pe tokenul de acolo.
     */
    public function test_agentul_isi_inroleaza_singur_certificatele(): void
    {
        $lista = [[
            'thumbprint' => strtoupper(bin2hex(random_bytes(20))),
            'cn' => 'IONESCU MARIA',
            'subiect' => 'CN=IONESCU MARIA',
            'emitent' => 'CN=certSIGN Qualified CA',
            'valabil_pana_la' => now()->addYear()->toDateTimeString(),
        ]];

        $inrolate = $this->app->make(CertificatService::class)->inroleazaDinAgent($lista, 'cod-nou');

        $this->assertCount(1, $inrolate);
        $this->assertSame('tunel', $inrolate[0]->mod_legatura);
        $this->assertSame('cod-nou', $inrolate[0]->bridge_token);
        $this->assertNull($inrolate[0]->bridge_url, 'adresa din rețea nu mai înseamnă nimic prin tunel');
    }

    /** Certificatele auto-semnate din magazinul Windows nu intră în evidență. */
    public function test_certificatele_nesemnate_de_o_autoritate_raman_afara(): void
    {
        $lista = [[
            'thumbprint' => strtoupper(bin2hex(random_bytes(20))),
            'cn' => 'PROGRAM OARECARE',
            'subiect' => 'CN=PROGRAM OARECARE',
            'emitent' => 'CN=PROGRAM OARECARE',
        ]];

        $this->assertSame([], $this->app->make(CertificatService::class)->inroleazaDinAgent($lista, 'cod-nou'));
    }

    /**
     * Documentul dintr-o cerere multipart trece intreg prin tunel.
     *
     * getContent() e gol la multipart — PHP consuma corpul in $_POST/$_FILES —
     * iar fara refacerea lui, arhivarea pleca fara document si programul local
     * raspundea „Cererea nu contine documentul de arhivat".
     */
    public function test_cererea_multipart_isi_pastreaza_documentul_prin_tunel(): void
    {
        $caleTemporara = tempnam(sys_get_temp_dir(), 'pdf');
        file_put_contents($caleTemporara, '%PDF-1.4 continut de proba');

        $incarcat = new \Symfony\Component\HttpFoundation\File\UploadedFile(
            $caleTemporara,
            'D112_15208744_2026-06_semnata.pdf',
            'application/pdf',
            null,
            true
        );

        $cerere = Request::create(
            '/api/punte/1/arhiva',
            'POST',
            ['firma' => 'DIANA SOFT SRL (15208744)', 'dosar' => 'D112', 'nume' => 'doc.pdf'],
            [],
            ['fisier' => $incarcat],
            ['CONTENT_TYPE' => 'multipart/form-data; boundary=granita-veche']
        );

        $comanda = $this->punte()->pune($this->certificat, $cerere, '/arhiva');

        $this->assertNotNull($comanda->corp_fisier, 'corpul multipart trebuie refacut, nu pierdut');

        $corp = Storage::get($comanda->corp_fisier);

        $this->assertStringContainsString('%PDF-1.4 continut de proba', $corp);
        $this->assertStringContainsString('name="fisier"; filename="D112_15208744_2026-06_semnata.pdf"', $corp);
        $this->assertStringContainsString('name="firma"', $corp);
        $this->assertStringContainsString('DIANA SOFT SRL (15208744)', $corp);

        // Granita din antet e cea noua, chiar cea folosita in corp.
        $this->assertMatchesRegularExpression('/boundary=(punte[0-9a-f]+)/', $comanda->antete['content-type'], 'antetul poarta granita noua');
        preg_match('/boundary=(punte[0-9a-f]+)/', $comanda->antete['content-type'], $granita);
        $this->assertStringContainsString('--' . $granita[1] . '--', $corp);

        @unlink($caleTemporara);
    }

    /** Jetonul de înrolare spune al cui client e kitul și nu poate fi ticluit. */
    public function test_jetonul_de_inrolare_spune_clientul(): void
    {
        $licente = $this->app->make(Licente::class);
        $licente->pregatesteCheile();

        $this->assertSame(self::COMPANIE, $licente->clientulDinJeton($licente->jetonInrolare(self::COMPANIE)));
        $this->assertNull($licente->clientulDinJeton('i1.ticluit.semnatura'));
        $this->assertNull($licente->clientulDinJeton(''));
    }

    /**
     * Fara token conectat, magazinul Windows tot trimite certificate — dar
     * auto-semnate, ale unor programe. Inrolarea nu are voie sa raspunda
     * „bine" cand n-a inrolat nimic: agentul ar crede ca s-a legat, iar pe
     * urma s-ar mira ca serverul nu-i recunoaste codul.
     */
    public function test_inrolarea_fara_certificat_calificat_spune_ca_tokenul_lipseste(): void
    {
        $licente = $this->app->make(Licente::class);
        $licente->pregatesteCheile();

        $cerere = Request::create('/api/punte/agent/inrolare', 'POST', [
            'certificate' => [[
                'thumbprint' => strtoupper(bin2hex(random_bytes(20))),
                'cn' => 'PROGRAM OARECARE',
                'subiect' => 'CN=PROGRAM OARECARE',
                'emitent' => 'CN=PROGRAM OARECARE',
            ]],
        ]);
        $cerere->headers->set('X-Inrolare', $licente->jetonInrolare(self::COMPANIE));
        $cerere->headers->set('Authorization', 'Bearer cod-fara-token');

        $raspuns = (new \App\Http\Controllers\Api\PunteController($this->punte()))
            ->inrolare($cerere, $licente, $this->app->make(CertificatService::class));

        $this->assertSame(422, $raspuns->getStatusCode());
        $this->assertStringContainsString('tokenul nu este conectat', $raspuns->getContent());

        // Esecul se vede si in jurnalul clientului, nu doar in fereastra agentului.
        $insemnare = \App\Models\AnafJurnal::query()->toateCompaniile()
            ->where('company_id', self::COMPANIE)
            ->where('actiune', 'certificat_inrolare')
            ->orderByDesc('id')
            ->first();

        $this->assertNotNull($insemnare, 'eșecul înrolării trebuie consemnat în jurnal');
        $this->assertFalse((bool) $insemnare->reusit);
        $this->assertStringContainsString('tokenul nu era conectat', $insemnare->descriere);

        \App\Models\AnafJurnal::query()->toateCompaniile()->where('company_id', self::COMPANIE)->delete();
    }
}
