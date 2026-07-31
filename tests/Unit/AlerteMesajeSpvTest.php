<?php

namespace Tests\Unit;

use App\Mail\AlertaMesajSpvEmail;
use App\Models\AlertaMesajSpv;
use App\Models\AnafCertificat;
use App\Models\AnafSocietate;
use App\Models\SpvMesaj;
use App\Services\Anaf\Spv\AlerteMesaje;
use App\Support\ContextCompanie;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

/**
 * Instiintarile pe email cand intra in SPV un document urmarit.
 *
 * Se verifica cine primeste si, mai ales, cine nu: alerta legata de un
 * certificat nu are voie sa prinda firmele altui certificat.
 */
class AlerteMesajeSpvTest extends TestCase
{
    protected const COMPANIE = 989;

    protected $certificatA;
    protected $certificatB;

    protected function setUp(): void
    {
        parent::setUp();

        Mail::fake();
        ContextCompanie::fixeaza(self::COMPANIE);

        $this->certificatA = $this->certificat('POPESCU ION');
        $this->certificatB = $this->certificat('IONESCU MARIA');

        // Firme inrolate: doua pe certificatul A, una pe B
        $this->societate('15208744', 'DIANA SOFT SRL', $this->certificatA);
        $this->societate('33486455', 'ALFA CONSTRUCT SRL', $this->certificatA);
        $this->societate('44556677', 'BETA COM SRL', $this->certificatB);
    }

    protected function tearDown(): void
    {
        AlertaMesajSpv::query()->toateCompaniile()->where('company_id', self::COMPANIE)->delete();
        SpvMesaj::query()->toateCompaniile()->where('company_id', self::COMPANIE)->delete();
        AnafSocietate::query()->toateCompaniile()->where('company_id', self::COMPANIE)->delete();
        AnafCertificat::query()->toateCompaniile()->where('company_id', self::COMPANIE)->delete();

        ContextCompanie::elibereaza();

        parent::tearDown();
    }

    protected function certificat(string $cn): AnafCertificat
    {
        return AnafCertificat::create([
            'company_id' => self::COMPANIE,
            'thumbprint' => strtoupper(bin2hex(random_bytes(20))),
            'cn' => $cn,
            'activ' => true,
            'valabil_pana_la' => now()->addYear(),
        ]);
    }

    protected function societate(string $cif, string $denumire, AnafCertificat $certificat): AnafSocietate
    {
        return AnafSocietate::create([
            'company_id' => self::COMPANIE,
            'cif' => $cif,
            'denumire' => $denumire,
            'certificat_id' => $certificat->id,
        ]);
    }

    protected function alerta(array $atribute = []): AlertaMesajSpv
    {
        return AlertaMesajSpv::create(array_merge([
            'company_id' => self::COMPANIE,
            'email' => 'contabil@example.com',
            'activ' => true,
        ], $atribute));
    }

    protected function mesaj(array $atribute = []): SpvMesaj
    {
        return SpvMesaj::create(array_merge([
            'company_id' => self::COMPANIE,
            'mesaj_id' => (string) random_int(1000000, 9999999),
            'cif' => '15208744',
            'tip' => 'SOMATIE',
            'certificat_id' => $this->certificatA->id,
        ], $atribute));
    }

    protected function anunta(SpvMesaj $mesaj): int
    {
        return $this->app->make(AlerteMesaje::class)->pentruMesajNou($mesaj);
    }

    public function test_alerta_pe_tip_trimite_emailul(): void
    {
        $this->alerta(['tip_document' => 'SOMATIE']);

        $this->assertSame(1, $this->anunta($this->mesaj()));

        Mail::assertSent(AlertaMesajSpvEmail::class, function (AlertaMesajSpvEmail $email) {
            return $email->hasTo('contabil@example.com')
                && $email->denumire === 'DIANA SOFT SRL';
        });
    }

    /** Alt tip de document nu are ce cauta in alerta. */
    public function test_alt_tip_de_document_nu_declanseaza_alerta(): void
    {
        $this->alerta(['tip_document' => 'SOMATIE']);

        $this->assertSame(0, $this->anunta($this->mesaj(['tip' => 'RECIPISA'])));

        Mail::assertNothingSent();
    }

    /** ANAF scrie tipurile si cu litere mari, si cu mici. */
    public function test_potrivirea_tipului_nu_tine_cont_de_litere(): void
    {
        $this->alerta(['tip_document' => 'somatie']);

        $this->assertSame(1, $this->anunta($this->mesaj(['tip' => 'SOMATIE PLATA'])));
    }

    /** Fara tip scris, alerta prinde orice document. */
    public function test_alerta_fara_tip_prinde_orice_document(): void
    {
        $this->alerta();

        $this->assertSame(1, $this->anunta($this->mesaj(['tip' => 'Situatie Sintetica'])));
    }

    /** Legata de un certificat, alerta prinde doar firmele inrolate lui. */
    public function test_alerta_pe_certificat_nu_prinde_firmele_altui_certificat(): void
    {
        $this->alerta(['certificat_id' => $this->certificatA->id]);

        // Firma inrolata certificatului A: da
        $this->assertSame(1, $this->anunta($this->mesaj(['cif' => '33486455'])));

        // Firma inrolata certificatului B, mesaj venit pe certificatul B: nu
        $this->assertSame(0, $this->anunta($this->mesaj([
            'cif' => '44556677',
            'certificat_id' => $this->certificatB->id,
        ])));
    }

    /** Cu firma aleasa, doar ea conteaza. */
    public function test_alerta_pe_o_firma_anume_prinde_doar_firma_aceea(): void
    {
        $this->alerta(['certificat_id' => $this->certificatA->id, 'cif' => '15208744']);

        $this->assertSame(1, $this->anunta($this->mesaj(['cif' => '15208744'])));
        $this->assertSame(0, $this->anunta($this->mesaj(['cif' => '33486455'])));
    }

    public function test_alerta_oprita_nu_trimite_nimic(): void
    {
        $this->alerta(['activ' => false]);

        $this->assertSame(0, $this->anunta($this->mesaj()));
        Mail::assertNothingSent();
    }

    /** Se tine socoteala cate au plecat si cand a fost ultima. */
    public function test_se_retine_cate_alerte_au_plecat(): void
    {
        $alerta = $this->alerta(['tip_document' => 'SOMATIE']);

        $this->anunta($this->mesaj());
        $this->anunta($this->mesaj());

        $alerta = $alerta->fresh();

        $this->assertSame(2, $alerta->trimise);
        $this->assertNotNull($alerta->ultima_alerta_la);
    }
}
