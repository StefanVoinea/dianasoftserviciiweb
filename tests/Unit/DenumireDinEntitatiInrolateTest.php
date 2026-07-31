<?php

namespace Tests\Unit;

use App\Http\Controllers\Api\DeclaratiiController;
use App\Models\AnafCertificat;
use App\Models\AnafDeclaratie;
use App\Models\AnafSocietate;
use App\Services\Anaf\Spv\CertificatService;
use App\Support\ContextCompanie;
use Illuminate\Http\Request;
use Tests\TestCase;

/**
 * La incarcare, denumirea firmei se ia din Entitati inrolate, iar semnarea si
 * depunerea merg pe calculatorul unde se afla certificatul cu care a fost
 * inrolata firma.
 */
class DenumireDinEntitatiInrolateTest extends TestCase
{
    protected const COMPANIE = 993;

    /** CUI inrolat, cu certificat propriu pe alt calculator. */
    protected const CUI_INROLAT = '15208744';

    /** CUI care nu apare in Entitati inrolate. */
    protected const CUI_STRAIN = '44556677';

    protected $certificat;

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

        AnafSocietate::create([
            'company_id' => self::COMPANIE,
            'cif' => self::CUI_INROLAT,
            'denumire' => 'DIANA SOFT SRL',
            'denumire_sursa' => 'date_identificare',
            'certificat_id' => $this->certificat->id,
        ]);
    }

    protected function tearDown(): void
    {
        AnafDeclaratie::query()->toateCompaniile()->where('company_id', self::COMPANIE)->delete();
        AnafSocietate::query()->toateCompaniile()->where('company_id', self::COMPANIE)->delete();
        AnafCertificat::query()->toateCompaniile()->where('company_id', self::COMPANIE)->delete();

        ContextCompanie::elibereaza();

        parent::tearDown();
    }

    protected function controller(): DeclaratiiController
    {
        return $this->app->make(DeclaratiiController::class);
    }

    /** Apeleaza o metoda ocolita a controlerului. */
    protected function cheama(DeclaratiiController $controller, string $metoda, array $argumente = [])
    {
        $reflectie = new \ReflectionMethod($controller, $metoda);
        $reflectie->setAccessible(true);

        return $reflectie->invokeArgs($controller, $argumente);
    }

    protected function meta(string $cui, string $denumire): array
    {
        return [
            'cui' => $cui,
            'den_firma' => $denumire,
            'tip' => 'D394',
            'luna' => 6,
            'anul' => 2026,
            'rectificativa' => false,
        ];
    }

    public function test_denumirea_vine_din_entitati_inrolate_nu_din_declaratie(): void
    {
        $campuri = $this->cheama($this->controller(), 'campuriDin', [
            $this->meta(self::CUI_INROLAT, 'DIANASOFT'),
        ]);

        $this->assertSame('DIANA SOFT SRL', $campuri['den_firma']);
        $this->assertSame($this->certificat->id, $campuri['certificat_id']);
    }

    public function test_fara_entitate_inrolata_ramane_denumirea_din_declaratie(): void
    {
        $campuri = $this->cheama($this->controller(), 'campuriDin', [
            $this->meta(self::CUI_STRAIN, 'FIRMA NECUNOSCUTA SRL'),
        ]);

        $this->assertSame('FIRMA NECUNOSCUTA SRL', $campuri['den_firma']);
        $this->assertNull($campuri['certificat_id']);
    }

    /** Entitatea exista, dar fara denumire: nu are ce sa inlocuiasca. */
    public function test_entitatea_fara_denumire_nu_sterge_denumirea_din_declaratie(): void
    {
        AnafSocietate::create([
            'company_id' => self::COMPANIE,
            'cif' => self::CUI_STRAIN,
            'denumire' => null,
        ]);

        $campuri = $this->cheama($this->controller(), 'campuriDin', [
            $this->meta(self::CUI_STRAIN, 'FIRMA NECUNOSCUTA SRL'),
        ]);

        $this->assertSame('FIRMA NECUNOSCUTA SRL', $campuri['den_firma']);
    }

    public function test_tabelul_arata_care_declaratie_nu_are_entitate_inrolata(): void
    {
        AnafDeclaratie::create([
            'company_id' => self::COMPANIE,
            'nume_fisier' => 'inrolata.xml',
            'tip' => 'D394',
            'cui' => self::CUI_INROLAT,
            'den_firma' => 'DIANA SOFT SRL',
        ]);

        AnafDeclaratie::create([
            'company_id' => self::COMPANIE,
            'nume_fisier' => 'straina.xml',
            'tip' => 'D394',
            'cui' => self::CUI_STRAIN,
            'den_firma' => 'FIRMA NECUNOSCUTA SRL',
        ]);

        $randuri = $this->controller()->index(new Request())->getData(true)['data'];

        $dupaCui = array_column($randuri, null, 'cui');

        $this->assertTrue($dupaCui[self::CUI_INROLAT]['inrolata']);
        $this->assertSame('POPESCU ION', $dupaCui[self::CUI_INROLAT]['certificat_inrolare']);

        $this->assertFalse($dupaCui[self::CUI_STRAIN]['inrolata']);
        $this->assertNull($dupaCui[self::CUI_STRAIN]['certificat_inrolare']);
    }

    /** Semnarea trebuie sa plece spre calculatorul unde se afla acel token. */
    public function test_declaratia_este_dirijata_catre_bridge_ul_certificatului_de_inrolare(): void
    {
        $declaratie = AnafDeclaratie::create([
            'company_id' => self::COMPANIE,
            'nume_fisier' => 'proba.xml',
            'tip' => 'D394',
            'cui' => self::CUI_INROLAT,
        ]);

        $controller = $this->controller();
        $this->cheama($controller, 'folosesteCertificatulEntitatii', [$declaratie]);

        $bridge = $this->app->make(CertificatService::class)->bridge();

        $this->assertSame('http://192.168.1.44:8099', $bridge['url']);
        $this->assertSame('token-contabil', $bridge['cod_instalare']);
        $this->assertSame($this->certificat->thumbprint, $bridge['thumbprint']);
    }

    /** Fara entitate inrolata se ramane pe certificatul ales in aplicatie. */
    public function test_declaratia_neinrolata_nu_schimba_certificatul_ales(): void
    {
        $declaratie = AnafDeclaratie::create([
            'company_id' => self::COMPANIE,
            'nume_fisier' => 'straina.xml',
            'tip' => 'D394',
            'cui' => self::CUI_STRAIN,
        ]);

        $altul = AnafCertificat::create([
            'company_id' => self::COMPANIE,
            'thumbprint' => strtoupper(bin2hex(random_bytes(20))),
            'cn' => 'IONESCU MARIA',
            'activ' => true,
            'valabil_pana_la' => now()->addYear(),
        ]);

        $serviciu = $this->app->make(CertificatService::class);
        $serviciu->foloseste($altul);

        $this->cheama($this->controller(), 'folosesteCertificatulEntitatii', [$declaratie]);

        $this->assertSame($altul->id, $serviciu->idCurent());
    }
}
