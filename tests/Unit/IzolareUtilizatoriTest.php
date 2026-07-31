<?php

namespace Tests\Unit;

use App\Models\AnafCertificat;
use App\Models\AnafDeclaratie;
use App\Models\CertificatUtilizator;
use App\Models\Company;
use App\Models\SpvMesaj;
use App\Models\SpvSolicitare;
use App\Models\User;
use App\Support\ContextCompanie;
use App\Support\ContextUtilizator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * Ce vede fiecare om din firma clientului.
 *
 * Utilizatorul obisnuit vede declaratiile si solicitarile depuse de el, si
 * mesajele din SPV ale certificatelor la care i s-a dat acces. Administratorul
 * firmei vede tot ce s-a lucrat pentru firma lui.
 */
class IzolareUtilizatoriTest extends TestCase
{
    protected $client;
    protected $sef;
    protected $ana;
    protected $bogdan;
    protected $certificatAnei;
    protected $certificatLuiBogdan;

    protected function setUp(): void
    {
        parent::setUp();

        $this->client = Company::create(['denumire' => 'BIROU CONTABIL SRL', 'cui' => '99000222']);

        $this->sef = $this->cont('sef.izolare@example.com', true);
        $this->ana = $this->cont('ana.izolare@example.com');
        $this->bogdan = $this->cont('bogdan.izolare@example.com');

        ContextCompanie::fixeaza($this->client->id);

        $this->certificatAnei = $this->certificat('ANA POPESCU');
        $this->certificatLuiBogdan = $this->certificat('BOGDAN IONESCU');

        CertificatUtilizator::create([
            'company_id' => $this->client->id,
            'certificat_id' => $this->certificatAnei->id,
            'email' => $this->ana->email,
            'user_id' => $this->ana->id,
            'activ' => true,
        ]);

        // Declaratii: cate una de fiecare
        $this->declaratie($this->ana, 'D112');
        $this->declaratie($this->bogdan, 'D394');

        // Solicitari: la fel
        $this->solicitare($this->ana);
        $this->solicitare($this->bogdan);

        // Mesaje SPV: cate unul pe fiecare certificat
        $this->mesaj('5100000001', $this->certificatAnei);
        $this->mesaj('5100000002', $this->certificatLuiBogdan);
    }

    protected function tearDown(): void
    {
        ContextUtilizator::faraLimitare(function () {
            AnafDeclaratie::query()->toateCompaniile()->where('company_id', $this->client->id)->delete();
            SpvSolicitare::query()->toateCompaniile()->where('company_id', $this->client->id)->delete();
            SpvMesaj::query()->toateCompaniile()->where('company_id', $this->client->id)->delete();
            CertificatUtilizator::query()->toateCompaniile()->where('company_id', $this->client->id)->delete();
            AnafCertificat::query()->toateCompaniile()->where('company_id', $this->client->id)->delete();
        });

        DB::table('company_user')->where('company_id', $this->client->id)->delete();

        foreach ([$this->sef, $this->ana, $this->bogdan] as $cont) {
            $cont->delete();
        }

        $this->client->delete();

        Auth::forgetGuards();
        ContextCompanie::elibereaza();

        parent::tearDown();
    }

    protected function cont(string $email, bool $administrator = false): User
    {
        $user = User::create([
            'name' => $email,
            'email' => $email,
            'password' => Hash::make('parola-de-proba'),
            'user_type' => 'user',
            'blocat' => 'Nu',
        ]);

        $this->client->users()->attach($user->id, ['administrator' => $administrator]);

        return $user;
    }

    protected function certificat(string $cn): AnafCertificat
    {
        return AnafCertificat::create([
            'company_id' => $this->client->id,
            'thumbprint' => strtoupper(bin2hex(random_bytes(20))),
            'cn' => $cn,
            'activ' => true,
            'valabil_pana_la' => now()->addYear(),
        ]);
    }

    protected function declaratie(User $user, string $tip): AnafDeclaratie
    {
        return AnafDeclaratie::create([
            'company_id' => $this->client->id,
            'nume_fisier' => $tip . '.xml',
            'tip' => $tip,
            'cui' => '15208744',
            'user_id' => $user->id,
        ]);
    }

    protected function solicitare(User $user): SpvSolicitare
    {
        return SpvSolicitare::create([
            'company_id' => $this->client->id,
            'cif' => '15208744',
            'tip_document' => 'Fisa Rol',
            'user_id' => $user->id,
        ]);
    }

    protected function mesaj(string $id, AnafCertificat $certificat): SpvMesaj
    {
        return SpvMesaj::create([
            'company_id' => $this->client->id,
            'mesaj_id' => $id,
            'cif' => '15208744',
            'tip' => 'Situatie Sintetica',
            'certificat_id' => $certificat->id,
        ]);
    }

    protected function ca(User $user): void
    {
        Auth::guard('api')->setUser($user);
    }

    public function test_utilizatorul_vede_doar_declaratiile_lui(): void
    {
        $this->ca($this->ana);

        $ale_ei = AnafDeclaratie::get();

        $this->assertCount(1, $ale_ei);
        $this->assertSame('D112', $ale_ei->first()->tip);
    }

    public function test_utilizatorul_vede_doar_solicitarile_lui(): void
    {
        $this->ca($this->bogdan);

        $ale_lui = SpvSolicitare::get();

        $this->assertCount(1, $ale_lui);
        $this->assertSame($this->bogdan->id, (int) $ale_lui->first()->user_id);
    }

    /** Documentul altcuiva nu se gaseste nici cerut direct dupa id. */
    public function test_declaratia_altcuiva_nu_se_gaseste_dupa_id(): void
    {
        $aLuiBogdan = ContextUtilizator::faraLimitare(function () {
            return AnafDeclaratie::where('tip', 'D394')->first();
        });

        $this->ca($this->ana);

        $this->assertNull(AnafDeclaratie::find($aLuiBogdan->id));
    }

    /** Mesajele nu tin de cine le-a adus, ci de certificatul la care are drept. */
    public function test_utilizatorul_vede_mesajele_certificatului_lui(): void
    {
        $this->ca($this->ana);

        $mesaje = SpvMesaj::get();

        $this->assertCount(1, $mesaje);
        $this->assertSame($this->certificatAnei->id, (int) $mesaje->first()->certificat_id);
    }

    /** Fara niciun certificat atribuit nu are ce vedea in mesaje. */
    public function test_utilizatorul_fara_certificat_nu_vede_niciun_mesaj(): void
    {
        $this->ca($this->bogdan);

        $this->assertCount(0, SpvMesaj::get());
    }

    public function test_administratorul_firmei_vede_tot(): void
    {
        $this->ca($this->sef);

        $this->assertCount(2, AnafDeclaratie::get());
        $this->assertCount(2, SpvSolicitare::get());
        $this->assertCount(2, SpvMesaj::get());
    }

    /** Operatiile interne (salvarea unui mesaj) trebuie sa vada tot. */
    public function test_operatiile_interne_trec_peste_limitare(): void
    {
        $this->ca($this->bogdan);

        $toate = SpvMesaj::query()->toateCertificatele()->get();

        $this->assertCount(2, $toate);
    }
}
