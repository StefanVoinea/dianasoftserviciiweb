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

    /** Cadenta dosarului urmarit se salveaza, iar una din afara listei e respinsa. */
    public function test_cadenta_monitorizarii_se_salveaza_doar_din_lista(): void
    {
        $this->salveaza(['monitorizare_cadenta' => 15]);

        $this->assertSame(15, (int) $this->certificat->fresh()->monitorizare_cadenta);

        try {
            $this->salveaza(['monitorizare_cadenta' => 7]);
            $this->fail('Trebuia respinsă: 7 minute nu e în lista de cadențe.');
        } catch (ValidationException $e) {
            $this->assertArrayHasKey('monitorizare_cadenta', $e->validator->errors()->toArray());
        }
    }

    /**
     * Randul certificatului la verificare vine dupa cadenta lui.
     *
     * Planificatorul bate din minut in minut; fara scadenta pe certificat, un
     * dosar cu cadenta de 30 de minute ar fi intrebat de 30 de ori degeaba.
     */
    public function test_scadenta_monitorizarii_urmeaza_cadenta(): void
    {
        $this->certificat->update([
            'monitorizare_activa' => true,
            'monitorizare_cale' => 'D:\Declarații de semnat',
            'monitorizare_cadenta' => 15,
        ]);

        $certificat = $this->certificat->fresh();

        // Neverificat vreodata: ii vine randul acum.
        $this->assertTrue($certificat->monitorizareaEsteScadenta());

        $certificat->update(['monitorizare_la' => now()->subMinutes(5)]);
        $this->assertFalse($certificat->fresh()->monitorizareaEsteScadenta());

        $certificat->update(['monitorizare_la' => now()->subMinutes(15)]);
        $this->assertTrue($certificat->fresh()->monitorizareaEsteScadenta());

        // Oprit din buton, nu se mai verifica oricat ar fi trecut.
        $certificat->update(['monitorizare_activa' => false]);
        $this->assertFalse($certificat->fresh()->monitorizareaEsteScadenta());
    }

    protected function salveaza(array $date)
    {
        return (new CertificateController())->update(new Request($date), $this->certificat);
    }
}
