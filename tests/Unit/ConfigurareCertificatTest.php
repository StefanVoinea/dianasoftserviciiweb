<?php

namespace Tests\Unit;

use App\Http\Controllers\Api\CertificateController;
use App\Models\AnafCertificat;
use App\Support\ContextCompanie;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

/**
 * Salvarea configurarii unui certificat: caile de pe calculatorul clientului.
 *
 * Regulile erau scrise ca tipare, cu bare oblice indoite de patru ori, si
 * amandoua ieșisera stricate: una nu mai putea fi citita deloc — salvarea se
 * oprea cu „preg_match(): Unknown modifier ']'", adica eroare de server — iar
 * cealalta nu mai potrivea nicio cale adevarata.
 */
class ConfigurareCertificatTest extends TestCase
{
    protected const COMPANIE = 991;

    protected $certificat;

    protected function setUp(): void
    {
        parent::setUp();

        ContextCompanie::fixeaza(self::COMPANIE);

        $this->certificat = AnafCertificat::create([
            'company_id' => self::COMPANIE,
            'thumbprint' => strtoupper(bin2hex(random_bytes(20))),
            'cn' => 'Certificat de configurat',
            'valabil_pana_la' => now()->addYear(),
        ]);
    }

    protected function tearDown(): void
    {
        AnafCertificat::query()->toateCompaniile()->where('company_id', self::COMPANIE)->delete();
        ContextCompanie::elibereaza();

        parent::tearDown();
    }

    /** Caile intregi se salveaza, fara sa cada nimic pe drum. */
    public function test_caile_intregi_se_salveaza(): void
    {
        $raspuns = $this->salveaza([
            'arhiva_cale' => 'D:\Documente fiscale',
            'monitorizare_cale' => '\\\\server\Declarații de semnat',
            'monitorizare_activa' => true,
        ]);

        $this->assertSame(200, $raspuns->getStatusCode());

        $proaspat = $this->certificat->fresh();

        $this->assertSame('D:\Documente fiscale', $proaspat->arhiva_cale);
        $this->assertSame('\\\\server\Declarații de semnat', $proaspat->monitorizare_cale);
    }

    /** O cale pe jumatate primeste un raspuns despre cale, nu 500. */
    public function test_calea_pe_jumatate_e_respinsa_cu_mesaj(): void
    {
        try {
            $this->salveaza(['arhiva_cale' => 'Documente fiscale']);
            $this->fail('Trebuia respinsă: calea nu e scrisă întreagă.');
        } catch (ValidationException $e) {
            $this->assertStringContainsString(
                'întreagă',
                implode(' ', $e->validator->errors()->get('arhiva_cale'))
            );
        }
    }

    public function test_salturile_din_cale_sunt_oprite(): void
    {
        try {
            $this->salveaza(['monitorizare_cale' => 'D:\Declarații\..\Altundeva']);
            $this->fail('Trebuia respinsă: calea are salturi „..".');
        } catch (ValidationException $e) {
            $this->assertStringContainsString(
                '..',
                implode(' ', $e->validator->errors()->get('monitorizare_cale'))
            );
        }
    }

    protected function salveaza(array $date)
    {
        return (new CertificateController())->update(new Request($date), $this->certificat);
    }
}
