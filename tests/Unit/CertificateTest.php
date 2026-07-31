<?php

namespace Tests\Unit;

use App\Models\AnafCertificat;
use App\Models\CertificatAbonat;
use App\Services\Anaf\Spv\CertificatService;
use Tests\TestCase;

class CertificateTest extends TestCase
{
    public function test_serviciul_este_inregistrat(): void
    {
        $this->assertInstanceOf(CertificatService::class, $this->app->make(CertificatService::class));
    }

    public function test_configurarea_avertizarii_este_definita(): void
    {
        $this->assertGreaterThan(0, config('anaf.certificate.zile_avertizare'));
        $this->assertGreaterThan(0, config('anaf.certificate.reamintire_zile'));
    }

    /** @dataProvider valabilitati */
    public function test_zilele_ramase_si_starea_de_expirare(int $zileOffset, int $asteptat, bool $expirat): void
    {
        $certificat = new AnafCertificat();
        $certificat->setRawAttributes([
            'valabil_pana_la' => now()->addDays($zileOffset)->toDateTimeString(),
        ], true);

        $this->assertSame($asteptat, $certificat->zile_ramase);
        $this->assertSame($expirat, $certificat->expirat);
    }

    public function valabilitati(): array
    {
        return [
            'valabil mult timp' => [145, 145, false],
            'expiră în fereastra de avertizare' => [20, 20, false],
            'expiră azi' => [0, 0, false],
            'deja expirat' => [-5, -5, true],
        ];
    }

    /**
     * Un certificat poate fi folosit de mai multi utilizatori din retea, legati
     * dupa email — chiar daca adresa nu are inca un cont in aplicatie.
     */
    public function test_utilizatorii_sunt_legati_de_certificat_dupa_email(): void
    {
        $certificat = AnafCertificat::create([
            'thumbprint' => 'TEST' . uniqid(),
            'cn' => 'Certificat comun',
            'valabil_pana_la' => now()->addDays(60),
        ]);

        $certificat->utilizatori()->create(['email' => 'unu@test.ro', 'nume' => 'Unu']);
        $certificat->utilizatori()->create(['email' => 'doi@test.ro', 'user_id' => null]);

        $this->assertCount(2, $certificat->fresh()->utilizatori);
        $this->assertSame(
            ['doi@test.ro', 'unu@test.ro'],
            $certificat->fresh()->utilizatori->pluck('email')->sort()->values()->all()
        );

        $certificat->utilizatori()->delete();
        $certificat->delete();
    }

    public function test_aceeasi_adresa_nu_poate_fi_atasata_de_doua_ori_la_acelasi_certificat(): void
    {
        $certificat = AnafCertificat::create([
            'thumbprint' => 'TEST' . uniqid(),
            'cn' => 'Certificat comun',
            'valabil_pana_la' => now()->addDays(60),
        ]);

        \App\Models\CertificatUtilizator::updateOrCreate(
            ['certificat_id' => $certificat->id, 'email' => 'unu@test.ro'],
            ['nume' => 'Prima dată']
        );
        \App\Models\CertificatUtilizator::updateOrCreate(
            ['certificat_id' => $certificat->id, 'email' => 'unu@test.ro'],
            ['nume' => 'A doua oară']
        );

        $this->assertCount(1, $certificat->fresh()->utilizatori);
        $this->assertSame('A doua oară', $certificat->fresh()->utilizatori->first()->nume);

        $certificat->utilizatori()->delete();
        $certificat->delete();
    }

    public function test_certificatul_fara_data_de_expirare_nu_e_considerat_expirat(): void
    {
        $certificat = new AnafCertificat();
        $certificat->setRawAttributes(['valabil_pana_la' => null], true);

        $this->assertNull($certificat->zile_ramase);
        $this->assertFalse($certificat->expirat);
    }

    /**
     * Abonatii fara certificat ales primesc avertizari pentru toate certificatele,
     * cei legati de un certificat doar pentru acela.
     */
    public function test_destinatarii_includ_abonatii_globali_si_pe_cei_ai_certificatului(): void
    {
        CertificatAbonat::query()->delete();
        $certificat = AnafCertificat::create([
            'thumbprint' => 'TEST' . uniqid(),
            'cn' => 'Test',
            'valabil_pana_la' => now()->addDays(10),
        ]);
        $altul = AnafCertificat::create([
            'thumbprint' => 'TEST' . uniqid(),
            'cn' => 'Altul',
            'valabil_pana_la' => now()->addDays(10),
        ]);

        CertificatAbonat::create(['email' => 'global@test.ro', 'certificat_id' => null]);
        CertificatAbonat::create(['email' => 'alocat@test.ro', 'certificat_id' => $certificat->id]);
        CertificatAbonat::create(['email' => 'strain@test.ro', 'certificat_id' => $altul->id]);
        CertificatAbonat::create(['email' => 'inactiv@test.ro', 'certificat_id' => null, 'activ' => false]);

        $destinatari = CertificatAbonat::pentruCertificat($certificat->id);

        $this->assertContains('global@test.ro', $destinatari);
        $this->assertContains('alocat@test.ro', $destinatari);
        $this->assertNotContains('strain@test.ro', $destinatari);
        $this->assertNotContains('inactiv@test.ro', $destinatari);

        CertificatAbonat::query()->delete();
        $certificat->delete();
        $altul->delete();
    }
}
