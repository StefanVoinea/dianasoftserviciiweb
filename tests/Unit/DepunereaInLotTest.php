<?php

namespace Tests\Unit;

use App\Http\Controllers\Api\DeclaratiiController;
use App\Models\AnafDeclaratie;
use App\Models\Company;
use App\Models\User;
use App\Services\Anaf\Declaratii\DepunereService;
use App\Support\ContextCompanie;
use App\Support\ContextUtilizator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Mai multe declarații se depun dintr-o singură cerere.
 *
 * Pe rând, fiecare declarație însemna o cerere a ei: la patruzeci de
 * declarații, patruzeci de porniri ale aplicației și tot atâtea autentificări,
 * pentru o lucrare care e, de fapt, una singură. Iar sesiunea deschisă la ANAF
 * se arunca între ele.
 */
class DepunereaInLotTest extends TestCase
{
    protected $client;
    protected $omul;

    protected function setUp(): void
    {
        parent::setUp();

        $this->client = Company::create(['denumire' => 'BIROU LOT SRL', 'cui' => '99000555']);

        $this->omul = User::create([
            'name' => 'Depunător',
            'email' => 'lot.depunere@example.com',
            'password' => Hash::make(bin2hex(random_bytes(8))),
        ]);

        $this->client->users()->attach($this->omul->id, [
            'administrator' => true,
            'poate_semna' => true,
            'poate_depune' => true,
        ]);

        ContextCompanie::fixeaza($this->client->id);
        Auth::login($this->omul);

        Storage::put('anaf/lot_semnat.pdf', '%PDF-1.4 proba');
    }

    protected function tearDown(): void
    {
        Auth::logout();

        ContextUtilizator::faraLimitare(function () {
            AnafDeclaratie::query()->toateCompaniile()->where('company_id', $this->client->id)->delete();
        });

        $this->client->users()->detach();
        $this->client->delete();
        $this->omul->delete();

        Storage::delete('anaf/lot_semnat.pdf');
        ContextCompanie::elibereaza();

        parent::tearDown();
    }

    protected function declaratie(string $cui): AnafDeclaratie
    {
        return AnafDeclaratie::create([
            'company_id' => $this->client->id,
            'nume_fisier' => 'D112.xml',
            'tip' => 'D112',
            'cui' => $cui,
            'pas' => 'semnat',
            'semnat' => true,
            'cale_pdf_semnat' => 'anaf/lot_semnat.pdf',
            'user_id' => $this->omul->id,
        ]);
    }

    /**
     * Rândurile trimise filei, în ordinea lucrului.
     *
     * Se citesc din generator, nu din răspunsul care curge: acela își golește
     * toate tampoanele ca să ajungă rândurile pe fir de îndată, deci din el
     * n-ar mai fi nimic de prins nici măcar aici.
     */
    protected function pasii(array $iduri, DepunereService $depunere): array
    {
        $declaratii = AnafDeclaratie::whereIn('id', $iduri)
            ->get()
            ->sortBy(function (AnafDeclaratie $declaratie) use ($iduri) {
                return array_search($declaratie->id, $iduri, true);
            })
            ->values();

        return iterator_to_array(
            (new DeclaratiiController())->pasiiDepunerii($declaratii, $depunere),
            false
        );
    }

    public function test_toate_declaratiile_pleaca_dintr_o_singura_cerere(): void
    {
        $unu = $this->declaratie('15208744');
        $doi = $this->declaratie('28909699');

        $depuse = [];

        $depunere = $this->createMock(DepunereService::class);
        $depunere->method('depune')->willReturnCallback(function () use (&$depuse) {
            $depuse[] = count($depuse) + 1;

            return ['index_recipisa' => '11988286' . count($depuse), 'eroare' => null, 'raspuns' => ''];
        });

        $pasi = $this->pasii([$unu->id, $doi->id], $depunere);

        $this->assertSame('inceput', $pasi[0]['tip']);
        $this->assertSame(2, $pasi[0]['total']);

        $this->assertSame('pas', $pasi[1]['tip']);
        $this->assertSame(1, $pasi[1]['facute']);
        $this->assertTrue($pasi[1]['reusit']);

        $gata = end($pasi);
        $this->assertSame('gata', $gata['tip']);
        $this->assertSame(2, $gata['depuse']);
        $this->assertSame([], $gata['erori']);

        $this->assertSame('depus', $unu->fresh()->pas);
        $this->assertSame('depus', $doi->fresh()->pas);
    }

    /** Ordinea aleasă de om se păstrează: el a ales-o uitându-se la tabel. */
    public function test_ordinea_ceruta_se_pastreaza(): void
    {
        $unu = $this->declaratie('11111111');
        $doi = $this->declaratie('22222222');

        $cerute = [];

        $depunere = $this->createMock(DepunereService::class);
        $depunere->method('depune')->willReturnCallback(function () use (&$cerute) {
            return ['index_recipisa' => '119882867', 'eroare' => null, 'raspuns' => ''];
        });

        $pasi = $this->pasii([$doi->id, $unu->id], $depunere);

        foreach ($pasi as $pas) {
            if (($pas['tip'] ?? '') === 'pas') {
                $cerute[] = $pas['ce'];
            }
        }

        $this->assertSame(['D112 22222222', 'D112 11111111'], $cerute);
    }

    /**
     * O declarație respinsă nu oprește lotul.
     *
     * Ele sunt lucrări deosebite: una respinsă de ANAF n-are nicio treabă cu
     * următoarea, iar omul care a apăsat o dată pentru patruzeci nu vrea să
     * apese încă o dată pentru treizeci și nouă.
     */
    public function test_o_declaratie_respinsa_nu_opreste_lotul(): void
    {
        $unu = $this->declaratie('33333333');
        $doi = $this->declaratie('44444444');

        $cate = 0;

        $depunere = $this->createMock(DepunereService::class);
        $depunere->method('depune')->willReturnCallback(function () use (&$cate) {
            $cate++;

            return $cate === 1
                ? ['index_recipisa' => null, 'eroare' => 'CUI greșit', 'raspuns' => '']
                : ['index_recipisa' => '119882868', 'eroare' => null, 'raspuns' => ''];
        });

        $pasi = $this->pasii([$unu->id, $doi->id], $depunere);
        $gata = end($pasi);

        $this->assertSame(1, $gata['depuse']);
        $this->assertCount(1, $gata['erori']);
        $this->assertStringContainsString('CUI greșit', $gata['erori'][0]);

        $this->assertSame('eroare_depunere', $unu->fresh()->pas);
        $this->assertSame('depus', $doi->fresh()->pas);
    }

    /** Fără dreptul de depunere, lotul nici nu pleacă. */
    public function test_fara_drept_lotul_nu_pleaca(): void
    {
        $simplu = User::create([
            'name' => 'Fără drept',
            'email' => 'lot.fara.drept@example.com',
            'password' => Hash::make(bin2hex(random_bytes(8))),
        ]);

        $this->client->users()->attach($simplu->id, [
            'administrator' => false,
            'poate_semna' => false,
            'poate_depune' => false,
        ]);

        Auth::login($simplu);

        $unu = $this->declaratie('55555555');

        $depunere = $this->createMock(DepunereService::class);
        $depunere->expects($this->never())->method('depune');

        $cerere = Request::create('/api/declaratii/depune/flux', 'POST', ['id' => [$unu->id]]);
        $raspuns = (new DeclaratiiController())->depuneFlux($cerere, $depunere);

        $this->assertSame(403, $raspuns->getStatusCode());

        Auth::login($this->omul);
        $simplu->delete();
    }
}
