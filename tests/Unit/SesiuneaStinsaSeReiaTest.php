<?php

namespace Tests\Unit;

use App\Models\AnafCertificat;
use App\Services\Anaf\Spv\CertificatService;
use App\Services\Anaf\Spv\SesiuneStinsa;
use App\Services\Anaf\Spv\SpvClient;
use App\Services\Anaf\Spv\SpvException;
use App\Services\Anaf\Spv\Transport\BridgeTransport;
use App\Support\ContextCompanie;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

/**
 * Când legătura cu ANAF moare fiindcă tokenul și-a cerut PIN-ul, se mai încearcă o dată.
 *
 * Driverele de token care nu au „single logon" cer codul la fiecare folosire —
 * deci îl cer și în mijlocul strângerii de mână cu ANAF. Cât omul îl scrie,
 * sesiunea securizată se stinge (SEC_E_CONTEXT_EXPIRED, curl 56) și apelul
 * cade. Dar când vorba asta ajunge la noi, codul e deja scris și tokenul
 * dezlegat: a doua încercare trece.
 *
 * Deosebirea față de reluarea care exista dinainte: aceea se făcea numai dacă
 * PIN-ul trecuse prin aplicație. Codul scris în fereastra driverului nu trece
 * pe nicăieri, deci aplicația nu avea de unde ști, iar omul primea o eroare
 * lungă pentru ceva ce se dezlegase singur.
 */
class SesiuneaStinsaSeReiaTest extends TestCase
{
    protected const COMPANIE = 996;

    protected $certificat;

    protected function setUp(): void
    {
        parent::setUp();

        ContextCompanie::fixeaza(self::COMPANIE);

        $this->certificat = AnafCertificat::create([
            'company_id' => self::COMPANIE,
            'thumbprint' => strtoupper(bin2hex(random_bytes(20))),
            'cn' => 'IONESCU MARIA',
            'activ' => true,
            'valabil_pana_la' => now()->addYear(),
            'bridge_url' => 'http://10.0.0.2:8099',
        ]);

        $this->app->make(CertificatService::class)->foloseste($this->certificat);
    }

    protected function tearDown(): void
    {
        AnafCertificat::query()->toateCompaniile()->where('company_id', self::COMPANIE)->delete();

        ContextCompanie::elibereaza();

        parent::tearDown();
    }

    protected function client(): SpvClient
    {
        $setari = ['throttle_ms' => 0, 'pin_asteptare_secunde' => 1, 'pin_pas_secunde' => 1] + config('anaf.spv');

        return new SpvClient(
            new BridgeTransport($setari, $this->app->make(CertificatService::class)),
            $setari,
            $this->app->make(CertificatService::class)
        );
    }

    /** Vorba programului local când sesiunea s-a stins sub el. */
    protected function panaDeSesiune(): array
    {
        return [
            'eroare' => 'Apelul către ANAF a eșuat: legătura s-a rupt în timp ce se primea răspunsul;'
                . ' sesiunea securizată s-a stins înainte de capătul răspunsului (SEC_E_CONTEXT_EXPIRED).',
            'detalii' => 'Tokenul a fost întrebat chiar acum: cheia merge, dar driverul a cerut PIN-ul din nou.'
                . ' [curl 56] curl: (56) Recv failure: Connection was reset',
        ];
    }

    /** @test */
    public function apelul_se_reia_si_lucrarea_merge_mai_departe()
    {
        $cerute = 0;

        Http::fake(function () use (&$cerute) {
            $cerute++;

            return $cerute === 1
                ? Http::response($this->panaDeSesiune(), 502)
                : Http::response(['mesaje' => [['id' => '7']]], 200);
        });

        $iesit = $this->client()->listaMesaje(1);

        $this->assertSame(['mesaje' => [['id' => '7']]], $iesit);
        $this->assertSame(2, $cerute, 'apelul trebuia încercat din nou');
    }

    /**
     * A doua oară la fel înseamnă altceva: atunci eroarea merge la om.
     *
     * Altfel am bate la nesfârșit într-o ușă închisă, iar omul n-ar afla
     * niciodată că are de bifat „Enable single logon" în driverul tokenului.
     */
    /** @test */
    public function daca_si_a_doua_oara_cade_la_fel_eroarea_ajunge_la_om()
    {
        $cerute = 0;

        Http::fake(function () use (&$cerute) {
            $cerute++;

            return Http::response($this->panaDeSesiune(), 502);
        });

        try {
            $this->client()->listaMesaje(1);
            $this->fail('trebuia să se plângă');
        } catch (SpvException $e) {
            $this->assertStringContainsString('SEC_E_CONTEXT_EXPIRED', $e->getMessage());
        }

        $this->assertSame(2, $cerute, 'se reia o singură dată, nu la nesfârșit');
    }

    /** O pană adevărată nu se reia: n-are de ce să treacă a doua oară. */
    /** @test */
    public function o_pana_obisnuita_nu_se_reia()
    {
        $cerute = 0;

        Http::fake(function () use (&$cerute) {
            $cerute++;

            return Http::response(['eroare' => 'Certificatul nu mai e înrolat pentru firma cerută.'], 502);
        });

        try {
            $this->client()->listaMesaje(1);
            $this->fail('trebuia să se plângă');
        } catch (SpvException $e) {
            $this->assertStringContainsString('înrolat', $e->getMessage());
        }

        $this->assertSame(1, $cerute, 'n-avea de ce să încerce a doua oară');
    }

    /** @test */
    public function documentul_adus_in_arhiva_se_cere_si_el_a_doua_oara()
    {
        $cerute = 0;

        Http::fake(function () use (&$cerute) {
            $cerute++;

            return $cerute === 1
                ? Http::response($this->panaDeSesiune(), 502)
                : Http::response([
                    'cale' => 'C:\\arhiva\\FIRMA\\mesaj.pdf',
                    'extensie' => 'pdf',
                    'marime' => 1024,
                    'hash' => 'abc',
                ], 200);
        });

        $iesit = $this->client()->descarcaInArhiva('7', ['firma' => 'FIRMA SRL', 'dosar' => 'Mesaje']);

        $this->assertSame('C:\\arhiva\\FIRMA\\mesaj.pdf', $iesit['cale']);
        $this->assertSame(2, $cerute, 'documentul trebuia cerut din nou');
    }

    /**
     * Semnele după care se cunoaște pățania.
     *
     * @dataProvider vorbeleSesiuniiStinse
     */
    public function test_semnele_se_cunosc(string $vorba, bool $asteptat): void
    {
        $this->assertSame($asteptat, SesiuneStinsa::da(['eroare' => $vorba]));
    }

    /** @return array<string, array{0: string, 1: bool}> */
    public function vorbeleSesiuniiStinse(): array
    {
        return [
            'vorba Windows' => ['sesiunea s-a stins (SEC_E_CONTEXT_EXPIRED)', true],
            'codul curl' => ['Apelul a eșuat [curl 56] ceva', true],
            'curl pe fața lui' => ['curl: (56) Recv failure: Connection was reset', true],
            'legătura resetată' => ['Connection was reset', true],
            'altă pană' => ['curl 60: certificatul serverului nu este de încredere', false],
            'nimic' => ['', false],
        ];
    }

    /** Când n-a venit JSON, se caută în răspunsul întreg. */
    public function test_se_cauta_si_in_raspunsul_care_nu_e_json(): void
    {
        $this->assertTrue(SesiuneStinsa::da(null, 'curl: (56) Recv failure: Connection was reset'));
        $this->assertFalse(SesiuneStinsa::da(null, 'pagină de eroare oarecare'));
    }
}
