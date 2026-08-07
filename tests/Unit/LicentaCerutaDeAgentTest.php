<?php

namespace Tests\Unit;

use App\Models\AnafCertificat;
use App\Support\ContextCompanie;
use Tests\TestCase;

/**
 * Programul de la client isi cere singur licenta, indata dupa instalare.
 *
 * Pana acum licenta pleca numai dinspre server: la salvarea certificatului in
 * aplicatie, sau noaptea, cu comanda planificata. Un calculator instalat azi
 * ramanea deci nelicentiat pana a doua zi dimineata — pornit, legat prin tunel,
 * si totusi nefolositor, fiindca programul local refuza orice comanda fara
 * licenta si spune „Programul nu are licenta valida pe acest calculator".
 *
 * Nu se poate face invers — serverul sa i-o duca in clipa inrolarii — fiindca
 * agentul asteapta chiar raspunsul la inrolare: comanda ar sta in coada pana
 * i-ar expira rabdarea.
 */
class LicentaCerutaDeAgentTest extends TestCase
{
    protected const COMPANIE = 991;
    protected const COD = 'cod-de-instalare-991';
    protected const MASINA = 'A1B2C3D4E5F60718293A4B5C6D7E8F90';

    protected $certificat;

    protected function setUp(): void
    {
        parent::setUp();

        ContextCompanie::fixeaza(self::COMPANIE);

        $this->certificat = AnafCertificat::create([
            'company_id' => self::COMPANIE,
            'thumbprint' => strtoupper(bin2hex(random_bytes(20))),
            'cn' => 'FILIMON ELENA-CARMEN',
            'activ' => true,
            'valabil_pana_la' => now()->addYear(),
            'bridge_token' => self::COD,
            'mod_legatura' => 'tunel',
        ]);
    }

    protected function tearDown(): void
    {
        AnafCertificat::query()->toateCompaniile()->where('company_id', self::COMPANIE)->delete();
        ContextCompanie::elibereaza();

        parent::tearDown();
    }

    protected function cere(array $date = null, string $cod = self::COD)
    {
        return $this->withHeader('Authorization', 'Bearer ' . $cod)
            ->postJson('/api/punte/agent/licenta', $date === null ? ['masina' => self::MASINA] : $date);
    }

    /** Cu codul lui si cu amprenta calculatorului, agentul primeste licenta. */
    public function test_agentul_primeste_licenta_pentru_calculatorul_lui(): void
    {
        $raspuns = $this->cere();

        $raspuns->assertStatus(200);
        $raspuns->assertJsonStructure(['date' => ['masina', 'expira', 'certificat'], 'semnatura']);

        $this->assertSame(self::MASINA, $raspuns->json('date.masina'));
        $this->assertSame($this->certificat->id, $raspuns->json('date.certificat'));
    }

    /** Ce s-a dat se tine minte, ca aplicatia sa stie ca statia e licentiata. */
    public function test_data_licentei_se_scrie_la_certificat(): void
    {
        $this->assertNull($this->certificat->licenta_pana_la);

        $this->cere()->assertStatus(200);

        $this->assertNotNull($this->certificat->fresh()->licenta_pana_la);
        $this->assertTrue($this->certificat->fresh()->licenta_pana_la->isFuture());
    }

    /** Fara amprenta calculatorului licenta n-ar fi legata de nimic. */
    public function test_fara_amprenta_nu_se_emite_nimic(): void
    {
        $this->cere(['masina' => ''])->assertStatus(422);

        $this->assertNull($this->certificat->fresh()->licenta_pana_la);
    }

    /** Un cod strain nu scoate licente pentru calculatoare straine. */
    public function test_codul_necunoscut_nu_primeste_licenta(): void
    {
        $this->cere(null, 'cod-inventat')->assertStatus(401);
    }

    /**
     * Certificatele scoase din uz nu dau licenta: un calculator pe care au ramas
     * numai ele n-are ce lucra, si e mai bine sa se vada asta decat sa para
     * pregatit.
     */
    public function test_certificatul_scos_din_uz_nu_licentiaza_calculatorul(): void
    {
        $this->certificat->update(['activ' => false]);

        $this->cere()->assertStatus(409);

        $this->assertNull($this->certificat->fresh()->licenta_pana_la);
    }

    /**
     * Pe acelasi calculator pot sta mai multe tokene, dar programul e unul
     * singur: licenta e a lui, deci se tine minte la toate certificatele de
     * acolo — si numai la cele in lucru.
     */
    public function test_licenta_se_tine_minte_la_toate_certificatele_in_lucru(): void
    {
        $alt = AnafCertificat::create([
            'company_id' => self::COMPANIE,
            'thumbprint' => strtoupper(bin2hex(random_bytes(20))),
            'cn' => 'AL DOILEA TOKEN',
            'activ' => true,
            'valabil_pana_la' => now()->addYear(),
            'bridge_token' => self::COD,
            'mod_legatura' => 'tunel',
        ]);

        $scos = AnafCertificat::create([
            'company_id' => self::COMPANIE,
            'thumbprint' => strtoupper(bin2hex(random_bytes(20))),
            'cn' => 'BLUMENFIELD (SEAP)',
            'activ' => false,
            'valabil_pana_la' => now()->addYear(),
            'bridge_token' => self::COD,
            'mod_legatura' => 'tunel',
        ]);

        $this->cere()->assertStatus(200);

        $this->assertNotNull($alt->fresh()->licenta_pana_la);
        $this->assertNull($scos->fresh()->licenta_pana_la, 'certificatul scos din uz n-are ce primi');
    }

    /** Agentul stie sa-si ceara licenta: pasul e chiar in programul lui. */
    public function test_agentul_are_pasul_de_licentiere(): void
    {
        $agent = file_get_contents(base_path('spv-bridge/agent.php'));
        $functii = file_get_contents(base_path('spv-bridge/agent-functii.php'));

        $this->assertStringContainsString('agent_licentiaza($config)', $agent);
        $this->assertStringContainsString('function agent_licentiaza', $functii);
        $this->assertStringContainsString('/api/punte/agent/licenta', $functii);
        $this->assertStringContainsString("'/identitate'", $functii);
    }
}
