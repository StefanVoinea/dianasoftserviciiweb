<?php

namespace Tests\Unit;

use App\Models\User;
use App\Services\AccesIp;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * Adresele de la care are voie sa intre fiecare cont.
 *
 * Cea mai importanta purtare verificata aici: lista goala primeste pe oricine.
 * Fara ea, coloana noua ar fi inchis afara toate conturile existente.
 */
class AccesIpTest extends TestCase
{
    protected $conturi = [];

    protected function tearDown(): void
    {
        foreach ($this->conturi as $cont) {
            $cont->delete();
        }

        parent::tearDown();
    }

    protected function cont(?string $ipPermise): User
    {
        $user = User::create([
            'name' => 'Test acces',
            'email' => 'acces' . bin2hex(random_bytes(4)) . '@example.com',
            'password' => Hash::make('parola-de-proba'),
            'user_type' => 'user',
            'blocat' => 'Nu',
            'ip_permise' => $ipPermise,
        ]);

        $this->conturi[] = $user;

        return $user;
    }

    /** Fara lista, contul intra de oriunde. Asta e purtarea implicita. */
    public function test_contul_fara_lista_intra_de_oriunde(): void
    {
        $user = $this->cont(null);

        $this->assertFalse(AccesIp::esteLimitat($user));
        $this->assertTrue(AccesIp::arePermisiune($user, '8.8.8.8'));
        $this->assertTrue(AccesIp::arePermisiune($user, '86.120.4.15'));
    }

    /** Un sir gol sau doar spatii inseamna tot „de oriunde". */
    public function test_lista_goala_nu_inseamna_lista_de_reguli(): void
    {
        $this->assertFalse(AccesIp::esteLimitat($this->cont('')));
        $this->assertFalse(AccesIp::esteLimitat($this->cont('   ')));
        $this->assertTrue(AccesIp::arePermisiune($this->cont("\n , ; "), '8.8.8.8'));
    }

    public function test_adresa_intreaga_se_potriveste_exact(): void
    {
        $user = $this->cont('86.120.4.15');

        $this->assertTrue(AccesIp::arePermisiune($user, '86.120.4.15'));
        $this->assertFalse(AccesIp::arePermisiune($user, '86.120.4.16'));
    }

    public function test_mai_multe_adrese_despartite_prin_virgula_sau_rand(): void
    {
        $user = $this->cont("86.120.4.15,\n 5.12.30.40 ; 91.1.1.1");

        $this->assertTrue(AccesIp::arePermisiune($user, '5.12.30.40'));
        $this->assertTrue(AccesIp::arePermisiune($user, '91.1.1.1'));
        $this->assertFalse(AccesIp::arePermisiune($user, '91.1.1.2'));
    }

    public function test_intervalul_cidr_prinde_toata_reteaua(): void
    {
        $user = $this->cont('192.168.1.0/24');

        $this->assertTrue(AccesIp::arePermisiune($user, '192.168.1.1'));
        $this->assertTrue(AccesIp::arePermisiune($user, '192.168.1.254'));
        $this->assertFalse(AccesIp::arePermisiune($user, '192.168.2.1'));
    }

    public function test_inceputul_de_adresa_prinde_ce_urmeaza_dupa_el(): void
    {
        $user = $this->cont('79.112.*');

        $this->assertTrue(AccesIp::arePermisiune($user, '79.112.5.9'));
        $this->assertFalse(AccesIp::arePermisiune($user, '79.113.5.9'));
    }

    /** De pe calculatorul serverului se intra mereu: comenzi, sarcini programate. */
    public function test_de_pe_acelasi_calculator_se_intra_intotdeauna(): void
    {
        $user = $this->cont('86.120.4.15');

        $this->assertTrue(AccesIp::arePermisiune($user, '127.0.0.1'));
        $this->assertTrue(AccesIp::arePermisiune($user, '::1'));
    }

    /** Fara adresa cunoscuta nu se blocheaza nimeni: ar fi o oprire pe nimic. */
    public function test_fara_adresa_cunoscuta_nu_se_opreste(): void
    {
        $this->assertTrue(AccesIp::arePermisiune($this->cont('86.120.4.15'), null));
        $this->assertTrue(AccesIp::arePermisiune($this->cont('86.120.4.15'), ''));
    }

    public function test_regulile_scrise_gresit_sunt_semnalate(): void
    {
        $this->assertSame([], AccesIp::reguliGresite('192.168.1.10, 10.0.0.0/8, 79.112.*'));
        $this->assertSame(['casa'], AccesIp::reguliGresite('192.168.1.10, casa'));
        $this->assertSame(['10.0.0.0/99'], AccesIp::reguliGresite('10.0.0.0/99'));
    }

    /** Nimeni nu-si poate pune pe cont o lista din care lipseste chiar el. */
    public function test_nu_se_poate_salva_o_lista_care_te_inchide_afara(): void
    {
        $motiv = AccesIp::motivRefuz('86.120.4.15', true, '5.12.30.40');

        $this->assertNotNull($motiv);
        $this->assertStringContainsString('v-ar închide afară', $motiv);
    }

    public function test_lista_care_cuprinde_adresa_de_acum_se_poate_salva(): void
    {
        $this->assertNull(AccesIp::motivRefuz('86.120.4.0/24', true, '86.120.4.15'));
        // Golirea listei e mereu permisa: deschide, nu inchide.
        $this->assertNull(AccesIp::motivRefuz('', true, '5.12.30.40'));
    }

    /** Pentru contul altcuiva, oprirea nu se aplica. */
    public function test_lista_altcuiva_nu_este_verificata_fata_de_adresa_mea(): void
    {
        $this->assertNull(AccesIp::motivRefuz('86.120.4.15', false, '5.12.30.40'));
    }

    /** O regula scrisa aiurea nu trece, oricare ar fi contul. */
    public function test_regula_gresita_opreste_salvarea(): void
    {
        $motiv = AccesIp::motivRefuz('acasa la mine', false, '5.12.30.40');

        $this->assertNotNull($motiv);
        $this->assertStringContainsString('Nu înțeleg adresele', $motiv);
    }
}
