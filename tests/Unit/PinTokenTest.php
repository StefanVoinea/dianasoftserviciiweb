<?php

namespace Tests\Unit;

use App\Models\AnafCertificat;
use App\Models\User;
use App\Support\ContextCompanie;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

/**
 * Tokenul se deblocheaza la intrarea in aplicatie, nu la prima lucrare.
 *
 * Citirea certificatului din magazinul Windows nu cere niciodata PIN: ea atinge
 * doar partea publica. PIN-ul se cere abia cand cheia privata e chiar folosita —
 * la semnare, sau la intrarea in SPV cu certificat.
 *
 * Asta se afla pana acum pe pielea omului: prima lucrare care avea nevoie de
 * cheie se impiedica de o fereastra deschisa pe calculatorul cu tokenul, adesea
 * pe alt ecran decat al lui, si adesea in mijlocul unei descarcari de zeci de
 * documente. Acum se cere dinadins o semnatura mica, la intrare: daca driverul
 * are PIN-ul in minte, proba se face pe loc si nu se vede nimic; daca nu-l are,
 * fereastra se deschide atunci, cand nu asteapta nimeni nimic dupa ea.
 *
 * Proba e deci si declansatorul — nu se poate afla fara sa se forteze.
 */
class PinTokenTest extends TestCase
{
    protected const COMPANIE = 995;

    protected $certificat;
    protected $omul;

    protected function setUp(): void
    {
        parent::setUp();

        /*
         * Middleware-ul cere ca omul sa fie arondat la o firma care exista cu
         * adevarat: fara randul din „companies", cererea se opreste cu 403 si
         * proba n-ar mai ajunge sa fie facuta.
         */
        DB::table('companies')->insert([
            'id' => self::COMPANIE,
            'denumire' => 'FIRMA DE PROBĂ 995',
        ]);

        $this->omul = User::create([
            'name' => 'Contabilul',
            'email' => 'pin995@example.test',
            'password' => bcrypt('proba'),
        ]);

        DB::table('company_user')->insert([
            'user_id' => $this->omul->id,
            'company_id' => self::COMPANIE,
            'administrator' => true,
            // Fara modulul SPV, middleware-ul opreste cererea inainte de proba.
            'module' => json_encode(['spv']),
        ]);

        ContextCompanie::fixeaza(self::COMPANIE);

        $this->certificat = AnafCertificat::create([
            'company_id' => self::COMPANIE,
            'thumbprint' => strtoupper(bin2hex(random_bytes(20))),
            'cn' => 'POPESCU ION',
            'activ' => true,
            'valabil_pana_la' => now()->addYear(),
            'bridge_url' => 'http://127.0.0.1:8099',
            'bridge_token' => 'cod-de-proba',
            'mod_legatura' => 'direct',
        ]);
    }

    protected function tearDown(): void
    {
        AnafCertificat::query()->toateCompaniile()->where('company_id', self::COMPANIE)->delete();
        DB::table('company_user')->where('company_id', self::COMPANIE)->delete();
        DB::table('companies')->where('id', self::COMPANIE)->delete();
        User::where('email', 'pin995@example.test')->delete();
        ContextCompanie::elibereaza();

        parent::tearDown();
    }

    /** @return array starea intoarsa pentru certificatul de proba */
    protected function proba(): array
    {
        $raspuns = $this->cere();

        $raspuns->assertStatus(200);

        return $raspuns->json('data.0') ?: [];
    }

    /** Cererea, asa cum vine de la fila: autentificata si cu societatea aleasa. */
    protected function cere()
    {
        return $this->actingAs($this->omul, 'api')
            ->withHeader('AuthorizationHeader', (string) self::COMPANIE)
            ->postJson('/api/anaf-certificate/verifica-pin', ['certificat' => $this->certificat->id]);
    }

    /** Cu PIN-ul deja dat, proba trece tacut si se tine minte. */
    public function test_pinul_deja_dat_se_tine_minte(): void
    {
        Http::fake(['*/pin' => Http::response(['gata' => true, 'cerut' => false, 'secunde' => 0.08], 200)]);

        $stare = $this->proba();

        $this->assertSame('gata', $stare['stare']);
        $this->assertSame('gata', $this->certificat->fresh()->pin_stare);
        $this->assertNotNull($this->certificat->fresh()->pin_verificat_la);
    }

    /**
     * Cand fereastra s-a deschis chiar acum, starea e tot „gata": ce conteaza e
     * ca de aici incolo cheia se poate folosi, nu cine a scris PIN-ul si cand.
     */
    public function test_pinul_scris_acum_lasa_tokenul_gata(): void
    {
        Http::fake(['*/pin' => Http::response(['gata' => true, 'cerut' => true, 'secunde' => 7.4], 200)]);

        $this->assertSame('gata', $this->proba()['stare']);
    }

    /** Fereastra inchisa fara PIN: se spune limpede, ca omul sa stie ce urmeaza. */
    public function test_pinul_neintrodus_se_vede_ca_atare(): void
    {
        Http::fake(['*/pin' => Http::response([
            'gata' => false,
            'cerut' => true,
            'motiv' => 'Operațiunea a fost anulată de utilizator',
        ], 200)]);

        $stare = $this->proba();

        $this->assertSame('refuzat', $stare['stare']);
        $this->assertStringContainsString('anulat', $this->certificat->fresh()->pin_motiv);
    }

    /** Tokenul neconectat nu e acelasi lucru cu PIN-ul refuzat. */
    public function test_tokenul_neconectat_se_deosebeste_de_pinul_refuzat(): void
    {
        Http::fake(['*/pin' => Http::response([
            'gata' => false,
            'cerut' => false,
            'motiv' => 'tokenul nu este conectat la acest calculator',
        ], 200)]);

        $this->assertSame('refuzat', $this->proba()['stare']);
        $this->assertStringContainsString('nu este conectat', $this->certificat->fresh()->pin_motiv);
    }

    /**
     * Kiturile mai vechi nu cunosc proba. Nu e o defectiune — doar nu se stie,
     * si atunci nu se scrie nimic langa certificat.
     */
    public function test_kitul_vechi_nu_stie_de_proba_si_nu_se_inventeaza_nimic(): void
    {
        Http::fake(['*/pin' => Http::response(['eroare' => 'ruta necunoscută'], 404)]);

        $this->assertSame('', $this->proba()['stare']);
        $this->assertNull($this->certificat->fresh()->pin_stare);
    }

    /** Certificatul scos din uz nu se probeaza: nu se lucreaza cu el nicaieri. */
    public function test_certificatul_scos_din_uz_nu_se_probeaza(): void
    {
        $this->certificat->update(['activ' => false]);

        Http::fake(['*/pin' => Http::response(['gata' => true, 'cerut' => false], 200)]);

        $raspuns = $this->cere();

        $raspuns->assertStatus(200);
        $this->assertSame([], $raspuns->json('data'));
        $this->assertNull($this->certificat->fresh()->pin_stare);
    }

    /** Programul de la client stie sa faca proba, si o duce si in kit. */
    public function test_programul_de_la_client_are_proba(): void
    {
        $server = file_get_contents(base_path('spv-bridge/server.php'));

        $this->assertStringContainsString("\$calea === '/pin'", $server);
        $this->assertStringContainsString('pin-test.ps1', $server);
        $this->assertFileExists(base_path('spv-bridge/pin-test.ps1'));
    }

    /**
     * Proba se cere singura la intrarea in aplicatie — acolo e rostul ei. Pusa
     * doar pe un buton, ar fi ramas nefolosita si fereastra ar fi aparut tot in
     * mijlocul primei lucrari.
     */
    public function test_proba_porneste_singura_la_intrarea_in_aplicatie(): void
    {
        $magazin = file_get_contents(base_path('resources/js/src/store/app/index.js'));

        $this->assertStringContainsString('verifica-pin', $magazin);
        $this->assertStringContainsString('deblocheazaTokenul', $magazin);
        $this->assertStringContainsString('societateaCurenta', $magazin);
    }

    /** PIN-ul nu trece prin aplicatie si nu se pastreaza nicaieri. */
    public function test_pinul_nu_ajunge_niciodata_la_noi(): void
    {
        $script = file_get_contents(base_path('spv-bridge/pin-test.ps1'));

        // Scriptul nu cere PIN-ul ca parametru si nu-l trimite nicaieri: el
        // ramane intre om si driverul tokenului.
        $this->assertStringNotContainsString('$Pin', $script);
        $this->assertStringContainsString('SignData', $script, 'proba se face semnand, nu întrebând');

        $coloane = \Illuminate\Support\Facades\Schema::getColumnListing('anaf_certificate');

        $this->assertNotContains('pin', $coloane);
        $this->assertContains('pin_stare', $coloane);
    }
}
