<?php

namespace Tests\Unit;

use App\Http\Controllers\Api\AdministrareController;
use App\Models\Company;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * Statisticile clientilor, pe module.
 *
 * Ele spun cat foloseste fiecare client ce a cumparat, iar modulele se numara
 * deosebit fiindca se si vand deosebit.
 */
class StatisticiClientiTest extends TestCase
{
    /** @var int */
    protected $client;

    protected function setUp(): void
    {
        parent::setUp();

        $this->client = Company::create(['denumire' => 'Proba Statistici SRL', 'cui' => '99999999'])->id;
    }

    protected function tearDown(): void
    {
        DB::table('etransport_notificari')->where('company_id', $this->client)->delete();
        DB::table('etransport_declaratii')->where('company_id', $this->client)->delete();
        Company::where('id', $this->client)->delete();

        parent::tearDown();
    }

    /** @return array rândul clientului de probă din statistici */
    protected function randulClientului(): array
    {
        $raspuns = $this->app->make(AdministrareController::class)->statistici()->getData(true);

        foreach ($raspuns['data'] as $rand) {
            if ($rand['id'] === $this->client) {
                return $rand;
            }
        }

        $this->fail('clientul de probă nu apare în statistici');
    }

    /**
     * Transporturile urmarite se numara si cand n-a fost trimis niciunul.
     *
     * Dispecerul se foloseste in doua feluri: unii declara transportul de aici,
     * altii il declara in alta parte si vin numai sa-l urmareasca. Numarate doar
     * cele trimise, tocmai clientii aceia ar parea ca nu folosesc modulul deloc
     * — si asa s-a si intamplat pe datele adevarate, la primul chip al filei.
     */
    public function test_transporturile_urmarite_se_numara_si_fara_trimiteri(): void
    {
        DB::table('etransport_notificari')->insert([
            ['company_id' => $this->client, 'uit' => 'U1', 'data_creare' => now()->subDays(2)],
            ['company_id' => $this->client, 'uit' => 'U2', 'data_creare' => now()->subDays(3)],
            ['company_id' => $this->client, 'uit' => 'U3', 'data_creare' => now()->subMonthNoOverflow()->startOfMonth()],
        ]);

        $rand = $this->randulClientului();

        $this->assertSame(0, $rand['transporturi'], 'nu s-a trimis niciun transport din aplicație');
        $this->assertSame(3, $rand['urmarite']);
        $this->assertSame(2, $rand['urmarite_luna_curenta']);
        $this->assertSame(1, $rand['urmarite_luna_anterioara']);
        $this->assertNotNull($rand['ultimul_transport'], 'urmărirea e și ea o folosire a modulului');
    }

    /** Se numara numai transporturile care au primit UIT de la ANAF. */
    public function test_se_numara_numai_transporturile_cu_uit(): void
    {
        DB::table('etransport_declaratii')->insert([
            [
                'company_id' => $this->client, 'stare' => 'validata', 'cif_declarant' => '123',
                'uit' => 'UIT1', 'depusa_la' => now()->subDay(),
            ],
            // Ciorna: n-a plecat nicaieri, deci nu spune nimic despre folosire.
            [
                'company_id' => $this->client, 'stare' => 'ciorna', 'cif_declarant' => '123',
                'uit' => null, 'depusa_la' => null,
            ],
        ]);

        $rand = $this->randulClientului();

        $this->assertSame(1, $rand['transporturi']);
        $this->assertSame(1, $rand['transporturi_luna_curenta']);
        $this->assertSame(1, $rand['declaranti']);
    }

    /** Un client fara nicio treaba nu strica fila: apare cu zerouri. */
    public function test_clientul_fara_treaba_apare_cu_zerouri(): void
    {
        $rand = $this->randulClientului();

        $this->assertSame(0, $rand['declaratii']);
        $this->assertSame(0, $rand['transporturi']);
        $this->assertSame(0, $rand['urmarite']);
        $this->assertNull($rand['ultimul_transport']);
    }
}
