<?php

namespace Tests\Unit;

use App\Models\AnafCertificat;
use App\Models\CertificatUtilizator;
use App\Services\Anaf\Spv\CertificatService;
use Tests\TestCase;

/**
 * Rutarea cererilor catre bridge-ul calculatorului pe care se afla certificatul.
 */
class RutareBridgeTest extends TestCase
{
    protected $certificate = [];

    protected function creeaza(array $atribute): AnafCertificat
    {
        $certificat = AnafCertificat::create(array_merge([
            'thumbprint' => strtoupper(bin2hex(random_bytes(20))),
            'cn' => 'Test',
            'activ' => true,
            'valabil_pana_la' => now()->addYear(),
        ], $atribute));

        $this->certificate[] = $certificat;

        return $certificat;
    }

    protected function tearDown(): void
    {
        foreach ($this->certificate as $certificat) {
            $certificat->utilizatori()->delete();
            $certificat->delete();
        }

        parent::tearDown();
    }

    protected function serviciu(): CertificatService
    {
        return $this->app->make(CertificatService::class);
    }

    public function test_certificatul_fortat_are_prioritate(): void
    {
        $certificat = $this->creeaza([
            'bridge_url' => 'http://192.168.1.20:8099',
            'bridge_token' => 'token-de-retea',
        ]);

        $serviciu = $this->serviciu();
        $serviciu->foloseste($certificat);

        $bridge = $serviciu->bridge();

        $this->assertSame('http://192.168.1.20:8099', $bridge['url']);
        // Codul de instalare arata carui calculator ii apartine; comenzile pleaca
        // cu jeton semnat, verificat separat.
        $this->assertSame('token-de-retea', $bridge['cod_instalare']);
        $this->assertSame($certificat->thumbprint, $bridge['thumbprint']);
    }

    /** Fara ruta proprie, certificatul foloseste bridge-ul din configuratie. */
    public function test_certificatul_fara_bridge_propriu_cade_pe_configuratie(): void
    {
        $certificat = $this->creeaza(['bridge_url' => null, 'bridge_token' => null]);

        $serviciu = $this->serviciu();
        $serviciu->foloseste($certificat);

        $bridge = $serviciu->bridge();

        $this->assertSame(config('anaf.spv.bridge.url'), $bridge['url']);
        $this->assertSame(config('anaf.spv.bridge.token'), $bridge['cod_instalare']);
        // Amprenta ramane a certificatului: acelasi bridge poate deservi mai multe tokene.
        $this->assertSame($certificat->thumbprint, $bridge['thumbprint']);
    }

    public function test_certificatul_cerut_prin_antet_este_folosit(): void
    {
        $certificat = $this->creeaza(['bridge_url' => 'http://10.0.0.5:8099']);

        request()->headers->set('X-Certificat-Id', (string) $certificat->id);

        $bridge = $this->serviciu()->bridge();

        $this->assertSame('http://10.0.0.5:8099', $bridge['url']);

        request()->headers->remove('X-Certificat-Id');
    }

    /** Doua tokene pe acelasi calculator: aceeasi ruta, amprente diferite. */
    public function test_doua_certificate_pe_acelasi_bridge_pastreaza_amprente_diferite(): void
    {
        $unu = $this->creeaza(['bridge_url' => 'http://192.168.1.30:8099', 'cn' => 'Primul']);
        $doi = $this->creeaza(['bridge_url' => 'http://192.168.1.30:8099', 'cn' => 'Al doilea']);

        $serviciu = $this->serviciu();

        $serviciu->foloseste($unu);
        $primul = $serviciu->bridge();

        $serviciu->foloseste($doi);
        $alDoilea = $serviciu->bridge();

        $this->assertSame($primul['url'], $alDoilea['url']);
        $this->assertNotSame($primul['thumbprint'], $alDoilea['thumbprint']);
    }

    /**
     * Utilizatorul primeste certificatul care i-a fost atribuit — asa poate
     * semna cu tokenul din alt calculator decat cel implicit.
     */
    public function test_utilizatorul_atasat_determina_certificatul(): void
    {
        $certificat = $this->creeaza(['bridge_url' => 'http://192.168.1.44:8099']);

        // Utilizator sintetic, ca testul sa nu depinda de atribuirile reale.
        $user = new \App\Models\User();
        $user->id = 987654;
        $user->email = 'rutare-test@exemplu.ro';

        CertificatUtilizator::create([
            'certificat_id' => $certificat->id,
            'email' => $user->email,
            'user_id' => $user->id,
        ]);

        $this->app['auth']->guard('api')->setUser($user);

        $bridge = $this->serviciu()->bridge();

        $this->assertSame('http://192.168.1.44:8099', $bridge['url']);
        $this->assertSame($certificat->thumbprint, $bridge['thumbprint']);
    }

    /**
     * Cand utilizatorul are mai multe certificate atribuite, se alege cel
     * marcat implicit — restul raman disponibile prin selectie explicita.
     */
    public function test_cu_mai_multe_atribuiri_castiga_certificatul_implicit(): void
    {
        $obisnuit = $this->creeaza(['bridge_url' => 'http://10.1.1.1:8099', 'implicit' => false]);
        $implicit = $this->creeaza(['bridge_url' => 'http://10.2.2.2:8099', 'implicit' => true]);

        $user = new \App\Models\User();
        $user->id = 987655;
        $user->email = 'rutare-multi@exemplu.ro';

        foreach ([$obisnuit, $implicit] as $certificat) {
            CertificatUtilizator::create([
                'certificat_id' => $certificat->id,
                'email' => $user->email,
                'user_id' => $user->id,
            ]);
        }

        $this->app['auth']->guard('api')->setUser($user);

        $this->assertSame('http://10.2.2.2:8099', $this->serviciu()->bridge()['url']);
    }
}
