<?php

namespace Tests\Unit;

use App\Models\AnafCertificat;
use App\Models\SpvMesaj;
use App\Services\Anaf\Spv\CertificatService;
use App\Services\Anaf\Spv\SpvStorage;
use App\Support\ContextCompanie;
use Tests\TestCase;

/**
 * Doua tokene pe acelasi calculator.
 *
 * Se intampla des la contabili: un token al firmei si unul al altei firme, sau
 * unul pentru SPV si altul pentru SEAP. Programul local le stie pe amandoua —
 * amprenta vine cu fiecare cerere, iar prajiturile de sesiune stau deoparte,
 * pentru fiecare certificat ale lui.
 *
 * Doua locuri nu tineau insa seama de asta, si amandoua se vedeau la fel din
 * afara: „merge numai daca schimb certificatul implicit".
 */
class DouaTokenuriTest extends TestCase
{
    protected const COMPANIE = 997;

    protected $unul;
    protected $altul;

    protected function setUp(): void
    {
        parent::setUp();

        ContextCompanie::fixeaza(self::COMPANIE);

        $this->unul = AnafCertificat::create([
            'company_id' => self::COMPANIE,
            'thumbprint' => 'AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA1',
            'cn' => 'TOKENUL DINTÂI',
            'activ' => true,
            'implicit' => true,
            'valabil_pana_la' => now()->addYear(),
            'bridge_url' => 'http://127.0.0.1:8099',
            'bridge_token' => 'cod-de-proba',
            'mod_legatura' => 'direct',
        ]);

        $this->altul = AnafCertificat::create([
            'company_id' => self::COMPANIE,
            'thumbprint' => 'BBBBBBBBBBBBBBBBBBBBBBBBBBBBBBBBBBBBBBB2',
            'cn' => 'TOKENUL AL DOILEA',
            'activ' => true,
            'implicit' => false,
            'valabil_pana_la' => now()->addYear(),
            'bridge_url' => 'http://127.0.0.1:8099',
            'bridge_token' => 'cod-de-proba',
            'mod_legatura' => 'direct',
        ]);
    }

    protected function tearDown(): void
    {
        SpvMesaj::query()->toateCompaniile()->where('company_id', self::COMPANIE)->delete();
        AnafCertificat::query()->toateCompaniile()->where('company_id', self::COMPANIE)->delete();
        ContextCompanie::elibereaza();

        parent::tearDown();
    }

    protected function mesajul(AnafCertificat $certificat): SpvMesaj
    {
        return SpvMesaj::create([
            'company_id' => self::COMPANIE,
            'mesaj_id' => (string) random_int(1000000, 9999999),
            'cif' => '15208744',
            'tip' => 'DECIZIE',
            'certificat_id' => $certificat->id,
        ]);
    }

    /**
     * Documentul se cere cu certificatul lui, nu cu cel implicit.
     *
     * ANAF il da numai celui care are drepturi pe firma aceea. Cerut cu
     * certificatul celuilalt token, nu vine — iar pe un calculator cu doua
     * tokene asta inseamna ca jumatate din documente nu se pot aduce.
     */
    public function test_documentul_se_cere_cu_certificatul_lui(): void
    {
        $certificate = $this->app->make(CertificatService::class);
        $depozit = new SpvStorage($certificate);

        $metoda = new \ReflectionMethod($depozit, 'folosesteCertificatulMesajului');
        $metoda->setAccessible(true);
        $metoda->invoke($depozit, $this->mesajul($this->altul));

        $this->assertSame(
            $this->altul->id,
            $certificate->activ()->id,
            'documentul celui de-al doilea token nu are ce căuta pe certificatul implicit'
        );
    }

    /** Mesajele vechi, fara certificat, raman pe cel hotarat de aplicatie. */
    public function test_mesajul_fara_certificat_ramane_pe_cel_implicit(): void
    {
        $certificate = $this->app->make(CertificatService::class);
        $depozit = new SpvStorage($certificate);

        $mesaj = SpvMesaj::create([
            'company_id' => self::COMPANIE,
            'mesaj_id' => (string) random_int(1000000, 9999999),
            'cif' => '15208744',
            'tip' => 'DECIZIE',
        ]);

        $metoda = new \ReflectionMethod($depozit, 'folosesteCertificatulMesajului');
        $metoda->setAccessible(true);
        $metoda->invoke($depozit, $mesaj);

        $this->assertSame($this->unul->id, $certificate->activ()->id);
    }

    /** Certificatul scos din uz nu se impune, chiar daca mesajul e al lui. */
    public function test_certificatul_scos_din_uz_nu_se_impune(): void
    {
        $this->altul->update(['activ' => false]);

        $certificate = $this->app->make(CertificatService::class);
        $depozit = new SpvStorage($certificate);

        $metoda = new \ReflectionMethod($depozit, 'folosesteCertificatulMesajului');
        $metoda->setAccessible(true);
        $metoda->invoke($depozit, $this->mesajul($this->altul));

        $this->assertSame($this->unul->id, $certificate->activ()->id);
    }

    /**
     * Ce s-a aflat despre PIN se tine minte pentru fiecare token in parte.
     *
     * O singura insemnare le amesteca: deschisa cheia celui dintai, al doilea
     * parea si el deschis, iar apelul lui la ANAF pleca fara ca PIN-ul sa fi
     * fost cerut — asa ca fereastra se deschidea tocmai in mijlocul strangerii
     * de mana, unde nimeni nu asteapta dupa om.
     */
    public function test_starea_pinului_se_tine_pentru_fiecare_token(): void
    {
        $server = file_get_contents(base_path('spv-bridge/server.php'));

        $inceput = strpos($server, 'function asigura_cheia');
        $sfarsit = strpos($server, 'function spv_cere');
        $bucata = substr($server, $inceput, $sfarsit - $inceput);

        $this->assertStringContainsString("substr(\$config['thumbprint'], 0, 16)", $bucata);
        $this->assertStringNotContainsString(
            "DIRECTORY_SEPARATOR . 'pin-stare.json'",
            $bucata,
            'o singură însemnare amestecă tokenele'
        );
    }

    /**
     * Programul local stie de amandoua: amprenta vine cu fiecare cerere, iar
     * prajiturile de sesiune stau deoparte pentru fiecare certificat.
     */
    public function test_programul_local_tine_tokenele_deosebite(): void
    {
        $server = file_get_contents(base_path('spv-bridge/server.php'));

        $this->assertStringContainsString('HTTP_X_THUMBPRINT', $server);
        $this->assertStringContainsString("'/cookies-' . \$sufix", $server, 'sesiunile ANAF nu se amestecă');
    }
}
