<?php

namespace Tests\Unit;

use App\Models\AnafCertificat;
use App\Models\AnafDeclaratie;
use App\Models\AnafSocietate;
use App\Models\SpvMesaj;
use App\Support\ContextCompanie;
use Tests\TestCase;

/**
 * Izolarea datelor între clienții serviciului.
 *
 * Verificările nu se opresc la liste: cel mai periculos caz este accesul direct
 * după id (rutele cu route model binding), care trebuie să nu găsească nimic.
 */
class IzolareClientiTest extends TestCase
{
    protected const CLIENT_A = 900001;
    protected const CLIENT_B = 900002;

    protected function tearDown(): void
    {
        ContextCompanie::toateCompaniile(function () {
            foreach ([AnafCertificat::class, AnafSocietate::class, SpvMesaj::class, AnafDeclaratie::class] as $model) {
                $model::query()->toateCompaniile()
                    ->whereIn('company_id', [self::CLIENT_A, self::CLIENT_B])
                    ->delete();
            }
        });

        ContextCompanie::elibereaza();

        parent::tearDown();
    }

    protected function certificatPentru(int $client): AnafCertificat
    {
        return ContextCompanie::pentru($client, function () {
            return AnafCertificat::create([
                'thumbprint' => strtoupper(bin2hex(random_bytes(20))),
                'cn' => 'Certificat client',
                'valabil_pana_la' => now()->addYear(),
            ]);
        });
    }

    public function test_company_id_se_completeaza_automat_la_creare(): void
    {
        $certificat = $this->certificatPentru(self::CLIENT_A);

        $this->assertSame(self::CLIENT_A, (int) $certificat->company_id);
    }

    public function test_un_client_nu_vede_datele_altuia(): void
    {
        $this->certificatPentru(self::CLIENT_A);
        $this->certificatPentru(self::CLIENT_A);
        $this->certificatPentru(self::CLIENT_B);

        $vazuteDeA = ContextCompanie::pentru(self::CLIENT_A, function () {
            return AnafCertificat::count();
        });

        $vazuteDeB = ContextCompanie::pentru(self::CLIENT_B, function () {
            return AnafCertificat::count();
        });

        $this->assertSame(2, $vazuteDeA);
        $this->assertSame(1, $vazuteDeB);
    }

    /** Cazul critic: cererea directă a unui id care aparține altui client. */
    public function test_accesul_direct_dupa_id_este_blocat(): void
    {
        $alLuiA = $this->certificatPentru(self::CLIENT_A);

        $gasit = ContextCompanie::pentru(self::CLIENT_B, function () use ($alLuiA) {
            return AnafCertificat::find($alLuiA->id);
        });

        $this->assertNull($gasit, 'Un client nu trebuie să poată deschide datele altuia după id');
    }

    public function test_izolarea_se_aplica_tuturor_tipurilor_de_date(): void
    {
        ContextCompanie::pentru(self::CLIENT_A, function () {
            AnafSocietate::create(['cif' => 'IZOL-A', 'activ' => true]);
            SpvMesaj::create(['mesaj_id' => 'M-A-' . uniqid(), 'cif' => 'IZOL-A', 'tip' => 'RECIPISA']);
            AnafDeclaratie::create(['cui' => 'IZOL-A', 'tip' => 'D300', 'nume_fisier' => 'a.xml']);
        });

        ContextCompanie::pentru(self::CLIENT_B, function () {
            $this->assertSame(0, AnafSocietate::count(), 'Entitățile altui client sunt vizibile');
            $this->assertSame(0, SpvMesaj::count(), 'Mesajele altui client sunt vizibile');
            $this->assertSame(0, AnafDeclaratie::count(), 'Declarațiile altui client sunt vizibile');
        });
    }

    /** Administrarea serviciului are nevoie de vederea peste toți clienții. */
    public function test_administrarea_poate_vedea_toti_clientii(): void
    {
        $this->certificatPentru(self::CLIENT_A);
        $this->certificatPentru(self::CLIENT_B);

        $total = ContextCompanie::toateCompaniile(function () {
            return AnafCertificat::query()->toateCompaniile()
                ->whereIn('company_id', [self::CLIENT_A, self::CLIENT_B])
                ->count();
        });

        $this->assertSame(2, $total);
    }

    /** Același CUI poate fi administrat de doi clienți diferiți. */
    public function test_acelasi_cui_poate_exista_la_doi_clienti(): void
    {
        ContextCompanie::pentru(self::CLIENT_A, function () {
            AnafSocietate::create(['cif' => 'CUI-COMUN', 'activ' => true]);
        });

        ContextCompanie::pentru(self::CLIENT_B, function () {
            AnafSocietate::create(['cif' => 'CUI-COMUN', 'activ' => true]);
        });

        $total = AnafSocietate::query()->toateCompaniile()->where('cif', 'CUI-COMUN')->count();

        $this->assertSame(2, $total);

        AnafSocietate::query()->toateCompaniile()->where('cif', 'CUI-COMUN')->delete();
    }
}
